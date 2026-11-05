<?php
require_once "../bootstrap/admin.php";
require_once "../core/Validator.php";
require_once "../Model/PopupModel.php";
$role->requireLogin();
$role->requireCanModify();

header('Content-Type: application/json');

try {
    $input = $_POST;
    $mode = $input['mode'] ?? '';
    $upload_path = $UPLOAD_DIR_POPUP;

    $validator = new Validator();

    if ($mode === 'insert') {
        $data = [
            'title'         => trim($input['title'] ?? ''),
            'popup_type'   => 1,
            'popup_path'    => '/',
            'has_link'      => (int)($input['has_link'] ?? 2),
            'link_url'      => safe_url(trim($input['link_url'] ?? ''), ['http', 'https'], true) ?: null,
            'is_target'     => (int)($input['is_target'] ?? 1),
            'is_active'     => (int)($input['is_active'] ?? 1),
            'description'   => sanitize_html_fragment(trim($input['description'] ?? '')) ?: null,
            'start_at'      => !empty(trim($input['start_at'] ?? '')) ? trim($input['start_at']) : null,
            'end_at'        => !empty(trim($input['end_at'] ?? '')) ? trim($input['end_at']) : null,
            'is_unlimited'  => (int)($input['is_unlimited'] ?? 1),
        ];

        $validator->require('title', $data['title'], '제목');

        if ($validator->fails()) {
            echo json_encode(['success' => false, 'message' => implode("\n", $validator->getErrors())]);
            exit;
        }

        $image = imageUpload($upload_path, $_FILES['popup_image'] ?? []);
        if (empty($image['saved'])) {
            $validator->require('popup_image', '', '팝업 이미지');
        } else {
            $data['popup_image'] = $image['saved'];
        }

        if ($validator->fails()) {
            echo json_encode([
                'success' => false,
                'message' => implode("\n", $validator->getErrors())
            ]);
            exit;
        }

        $data['sort_no'] = 1;
        $db = DB::getInstance();
        try {
            $db->beginTransaction();
            PopupModel::bumpSortNosOnInsert($data['popup_path']);
            $result = PopupModel::insert($data);
            if ($result) {
                $db->commit();
            } else {
                $db->rollBack();
                imageDelete($upload_path . '/' . $data['popup_image']);
            }
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            imageDelete($upload_path . '/' . $data['popup_image']);
            throw $e;
        }

        echo json_encode([
            'success' => $result,
            'message' => $result ? '팝업이 등록되었습니다.' : '등록 실패'
        ]);
        exit;
    }

    if ($mode === 'update') {
        $id = (int)($input['id'] ?? 0);
        if (!$id) throw new Exception("ID가 없습니다.");

        $data = [
            'title'         => trim($input['title'] ?? ''),
            'popup_type'   => 1,
            'popup_path'    => '/',
            'has_link'      => (int)($input['has_link'] ?? 2),
            'link_url'      => safe_url(trim($input['link_url'] ?? ''), ['http', 'https'], true) ?: null,
            'is_target'     => (int)($input['is_target'] ?? 1),
            'is_active'     => (int)($input['is_active'] ?? 1),
            'description'   => sanitize_html_fragment(trim($input['description'] ?? '')) ?: null,
            'start_at'      => !empty(trim($input['start_at'] ?? '')) ? trim($input['start_at']) : null,
            'end_at'        => !empty(trim($input['end_at'] ?? '')) ? trim($input['end_at']) : null,
            'is_unlimited'  => (int)($input['is_unlimited'] ?? 1),
        ];

        $newSortNo = (int)($input['sort_no'] ?? 0);
        $data['sort_no'] = $newSortNo;

        $validator->require('title', $data['title'], '제목');

        $existing = PopupModel::find($id);
        if (!$existing) {
            echo json_encode(['success' => false, 'message' => '데이터를 찾을 수 없습니다.']);
            exit;
        }

        $oldSortNo = (int)$existing['sort_no'];
        $uploadError = $_FILES['popup_image']['error'] ?? UPLOAD_ERR_NO_FILE;
        $hasNewImage = $uploadError === UPLOAD_ERR_OK;

        if ($uploadError !== UPLOAD_ERR_OK && $uploadError !== UPLOAD_ERR_NO_FILE) {
            echo json_encode(['success' => false, 'message' => '이미지 업로드에 실패했습니다. 파일 크기와 업로드 상태를 확인해 주세요.']);
            exit;
        }
        if (!$hasNewImage && empty($existing['popup_image'])) {
            $validator->require('popup_image', '', '팝업 이미지');
        }

        if ($validator->fails()) {
            echo json_encode([
                'success' => false,
                'message' => implode("\n", $validator->getErrors())
            ]);
            exit;
        }

        $newImage = null;
        if ($hasNewImage) {
            $image = imageUpload($upload_path, $_FILES['popup_image']);
            if (empty($image['saved'])) {
                echo json_encode(['success' => false, 'message' => '이미지 업로드에 실패했습니다.']);
                exit;
            }
            $newImage = $image['saved'];
            $data['popup_image'] = $newImage;
        }

        $db = DB::getInstance();
        try {
            $db->beginTransaction();
            if ($newSortNo !== $oldSortNo && $newSortNo > 0) {
                PopupModel::shiftSortNosForUpdate($oldSortNo, $newSortNo, $id);
            }
            $result = PopupModel::update($id, $data);
            if ($result) {
                $db->commit();
            } else {
                $db->rollBack();
            }
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            if ($newImage !== null && $newImage !== ($existing['popup_image'] ?? null)) {
                imageDelete($upload_path . '/' . $newImage);
            }
            throw $e;
        }

        if ($newImage !== null && $newImage !== ($existing['popup_image'] ?? null)) {
            if ($result && !empty($existing['popup_image'])) {
                imageDelete($upload_path . '/' . $existing['popup_image']);
            } elseif (!$result) {
                imageDelete($upload_path . '/' . $newImage);
            }
        }

        echo json_encode([
            'success' => $result,
            'message' => $result ? '수정되었습니다.' : '수정 실패'
        ]);
        exit;
    }

    if ($mode === 'delete') {
        $id = (int)($input['id'] ?? 0);
        if (!$id) throw new Exception("ID가 없습니다.");

        $existing = PopupModel::find($id);
        $db = DB::getInstance();
        try {
            $db->beginTransaction();
            $result = PopupModel::delete($id);
            if ($result) $db->commit();
            else $db->rollBack();
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            throw $e;
        }
        if ($result && !empty($existing['popup_image'])) imageDelete($upload_path . '/' . $existing['popup_image']);

        echo json_encode([
            'success' => $result,
            'message' => $result ? '삭제되었습니다.' : '삭제 실패'
        ]);
        exit;
    }

    if ($mode === 'delete_array') {
        $ids = json_decode($input['ids'] ?? '[]', true);

        if (!is_array($ids) || empty($ids)) {
            throw new Exception("삭제할 ID 목록이 없습니다.");
        }

        $existingImages = [];
        foreach ($ids as $id) {
            $existing = PopupModel::find((int)$id);
            if (!empty($existing['popup_image'])) $existingImages[] = $existing['popup_image'];
        }
        $db = DB::getInstance();
        try {
            $db->beginTransaction();
            $result = PopupModel::deleteMultiple($ids);
            if ($result) $db->commit();
            else $db->rollBack();
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            throw $e;
        }
        if ($result) {
            foreach ($existingImages as $existingImage) imageDelete($upload_path . '/' . $existingImage);
        }

        echo json_encode([
            'success' => $result,
            'message' => $result ? '선택 항목이 삭제되었습니다.' : '삭제 실패'
        ]);
        exit;
    }

    if ($mode === 'sort') {
        $id = (int)($input['id'] ?? 0);
        $newNo = (int)($input['new_no'] ?? 0);

        if (!$id || $newNo <= 0) {
            echo json_encode(['success' => false, 'message' => '잘못된 데이터']);
            exit;
        }

        $db = DB::getInstance();
        $stmt = $db->prepare("SELECT * FROM nb_popups WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $currentData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$currentData) {
            echo json_encode(['success' => false, 'message' => '존재하지 않는 항목입니다.']);
            exit;
        }

        $oldNo = (int)$currentData['sort_no'];

        if ($oldNo === $newNo) {
            echo json_encode(['success' => true, 'message' => '변경 없음']);
            exit;
        }

        $minSortNo = PopupModel::getMinSortNo();
        $maxSortNo = PopupModel::getMaxSortNo();

        if ($newNo > $maxSortNo) {
            echo json_encode(['success' => false, 'message' => '제일 높은 순서의 게시물입니다.']);
            exit;
        }

        if ($newNo < $minSortNo) {
            echo json_encode(['success' => false, 'message' => '제일 낮은 순서의 게시물입니다.']);
            exit;
        }

        $dataToUpdate = [
            'title'        => $currentData['title'],
            'popup_type'   => 1,
            'popup_path'   => '/',
            'has_link'     => (int)$currentData['has_link'],
            'link_url'     => $currentData['link_url'],
            'is_target'    => (int)$currentData['is_target'],
            'is_active'    => (int)$currentData['is_active'],
            'description'  => $currentData['description'],
            'start_at'     => $currentData['start_at'],
            'end_at'       => $currentData['end_at'],
            'is_unlimited' => (int)$currentData['is_unlimited'],
            'popup_image'  => $currentData['popup_image'],
            'sort_no'      => $newNo,
        ];

        try {
            $db->beginTransaction();
            PopupModel::shiftSortNosForUpdate($oldNo, $newNo, $id);
            $updateResult = PopupModel::update($id, $dataToUpdate);
            if ($updateResult) $db->commit();
            else $db->rollBack();
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            throw $e;
        }

        echo json_encode([
            'success' => (bool)$updateResult,
            'message' => $updateResult ? '순서가 변경되었습니다.' : '변경 실패'
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => '유효하지 않은 요청입니다.']);
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '처리 중 오류 발생: ' . $e->getMessage()
    ]);
}
