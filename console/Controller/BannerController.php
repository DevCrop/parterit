<?php
require_once "../bootstrap/admin.php";
require_once "../core/Validator.php";
require_once "../Model/BannerModel.php";
$role->requireLogin();
$role->requireCanModify();

header('Content-Type: application/json');

try {
    $input = $_POST;
    $mode = $input['mode'] ?? '';
    $upload_path = $UPLOAD_DIR_BANNER;

    $validator = new Validator();
    $normalizeBannerTitle = static function ($value): string {
        $clean = sanitize_html_fragment(
            trim((string)$value),
            ['br', 'strong', 'b', 'em', 'i', 'u', 'span', 'p', 'div'],
            ['span' => []]
        );

        $clean = preg_replace('/<\s*(p|div)[^>]*>/i', '', $clean);
        $clean = preg_replace('/<\s*\/\s*(p|div)\s*>/i', '<br>', $clean);
        $clean = preg_replace('/(?:\s*<br\s*\/?>\s*){2,}/i', '<br>', $clean);
        $clean = preg_replace('/^(?:\s*<br\s*\/?>\s*)+|(?:\s*<br\s*\/?>\s*)+$/i', '', $clean);

        return trim($clean);
    };
    $titleRequiredValue = static function (string $html): string {
        return trim(strip_tags(preg_replace('/<br\s*\/?>/i', "\n", $html)));
    };

    // INSERT
    if ($mode === 'insert') {
        $data = [
            'title'            => $normalizeBannerTitle($input['title'] ?? ''),
            'banner_type'      => 1,
            'hall_id'          => null,
            'has_link'         => (int)($input['has_link'] ?? 2),
            'link_url'         => safe_url(trim($input['link_url'] ?? ''), ['http', 'https'], true) ?: null,
            'is_target'        => (int)($input['is_target'] ?? 1),
            'is_active'        => (int)($input['is_active'] ?? 1),
            'description'      => sanitize_html_fragment(trim($input['description'] ?? '')) ?: null,
            'start_at'         => null,
            'end_at'           => null,
            'display_start_at' => !empty(trim($input['display_start_at'] ?? '')) ? trim($input['display_start_at']) : null,
            'display_end_at'   => !empty(trim($input['display_end_at'] ?? '')) ? trim($input['display_end_at']) : null,
            'is_unlimited'     => (int)($input['is_unlimited'] ?? 1),
            'banner_image_mobile' => null,
        ];

        $validator->require('title', $titleRequiredValue($data['title']), '제목');

        $image = imageUpload($upload_path, $_FILES['banner_image'] ?? []);
        if (empty($image['saved'])) {
            $validator->require('banner_image', '', '배너 이미지 (데스크탑)');
        } else {
            $data['banner_image'] = $image['saved'];
        }

        if ($validator->fails()) {
            echo json_encode([
                'success' => false,
                'message' => implode("\n", $validator->getErrors())
            ]);
            exit;
        }

        BannerModel::bumpSortNosOnInsert(); // 기존 전부 +1
        $data['sort_no'] = 1;               // 새 배너는 1로 고정

        $result = BannerModel::insert($data);

        echo json_encode([
            'success' => $result,
            'message' => $result ? '배너가 등록되었습니다.' : '등록 실패'
        ]);
        exit;
    }

    // UPDATE
    if ($mode === 'update') {
        $id = (int)($input['id'] ?? 0);
        if (!$id) throw new Exception("ID가 없습니다.");

        $data = [
            'title'            => $normalizeBannerTitle($input['title'] ?? ''),
            'banner_type'      => 1,
            'hall_id'          => null,
            'has_link'         => (int)($input['has_link'] ?? 2),
            'link_url'         => safe_url(trim($input['link_url'] ?? ''), ['http', 'https'], true) ?: null,
            'is_target'        => (int)($input['is_target'] ?? 1),
            'is_active'        => (int)($input['is_active'] ?? 1),
            'description'      => sanitize_html_fragment(trim($input['description'] ?? '')) ?: null,
            'start_at'         => null,
            'end_at'           => null,
            'display_start_at' => !empty(trim($input['display_start_at'] ?? '')) ? trim($input['display_start_at']) : null,
            'display_end_at'   => !empty(trim($input['display_end_at'] ?? '')) ? trim($input['display_end_at']) : null,
            'is_unlimited'     => (int)($input['is_unlimited'] ?? 1),
            'banner_image_mobile' => null,
        ];

        $newSortNo = (int)($input['sort_no'] ?? 0);
        $data['sort_no'] = $newSortNo;

        $validator->require('title', $titleRequiredValue($data['title']), '제목');

        $existing = BannerModel::find($id);
        if (!$existing) {
            echo json_encode(['success' => false, 'message' => '데이터를 찾을 수 없습니다.']);
            exit;
        }

        $oldSortNo = (int)$existing['sort_no'];
        if ($newSortNo !== $oldSortNo && $newSortNo > 0) {
            BannerModel::shiftSortNosForUpdate($oldSortNo, $newSortNo, $id);
        }

        // 새 데스크탑 이미지가 업로드되었는지 확인
        $hasNewImage = !empty($_FILES['banner_image']) &&
            isset($_FILES['banner_image']['error']) &&
            $_FILES['banner_image']['error'] === UPLOAD_ERR_OK;

        if ($hasNewImage) {
            // 기존 이미지 삭제
            if (!empty($existing['banner_image']) && file_exists($upload_path . '/' . $existing['banner_image'])) {
                imageDelete($upload_path . '/' . $existing['banner_image']);
            }

            // 새 이미지 업로드
            $image = imageUpload($upload_path, $_FILES['banner_image']);

            // imageUpload가 실패하면 exit되므로 여기까지 왔다면 성공
            // saved 값이 있는지 확인
            if (!empty($image['saved'])) {
                $data['banner_image'] = $image['saved'];
            }
        } elseif (empty($existing['banner_image'])) {
            // 새 이미지도 없고 기존 이미지도 없으면 에러
            $validator->require('banner_image', '', '배너 이미지 (데스크탑)');
        }
        // else: 새 이미지가 없고 기존 이미지가 있으면 기존 이미지 유지 (아무것도 하지 않음)

        if (!empty($existing['banner_image_mobile']) && file_exists($upload_path . '/' . $existing['banner_image_mobile'])) {
            imageDelete($upload_path . '/' . $existing['banner_image_mobile']);
        }

        if ($validator->fails()) {
            echo json_encode([
                'success' => false,
                'message' => implode("\n", $validator->getErrors())
            ]);
            exit;
        }

        $result = BannerModel::update($id, $data);

        echo json_encode([
            'success' => $result,
            'message' => $result ? '수정되었습니다.' : '수정 실패'
        ]);
        exit;
    }

    // DELETE
    if ($mode === 'delete') {
        $id = (int)($input['id'] ?? 0);
        if (!$id) throw new Exception("ID가 없습니다.");

        $existing = BannerModel::find($id);
        if ($existing) {
            if (!empty($existing['banner_image']) && file_exists($upload_path . '/' . $existing['banner_image'])) {
                imageDelete($upload_path . '/' . $existing['banner_image']);
            }
            if (!empty($existing['banner_image_mobile']) && file_exists($upload_path . '/' . $existing['banner_image_mobile'])) {
                imageDelete($upload_path . '/' . $existing['banner_image_mobile']);
            }
        }

        $result = BannerModel::delete($id);

        echo json_encode([
            'success' => $result,
            'message' => $result ? '삭제되었습니다.' : '삭제 실패'
        ]);
        exit;
    }

    // DELETE ARRAY
    if ($mode === 'delete_array') {
        $ids = json_decode($input['ids'] ?? '[]', true);

        if (!is_array($ids) || empty($ids)) {
            throw new Exception("삭제할 ID 목록이 없습니다.");
        }

        foreach ($ids as $id) {
            $existing = BannerModel::find((int)$id);
            if ($existing) {
                if (!empty($existing['banner_image']) && file_exists($upload_path . '/' . $existing['banner_image'])) {
                    imageDelete($upload_path . '/' . $existing['banner_image']);
                }
                if (!empty($existing['banner_image_mobile']) && file_exists($upload_path . '/' . $existing['banner_image_mobile'])) {
                    imageDelete($upload_path . '/' . $existing['banner_image_mobile']);
                }
            }
        }

        $result = BannerModel::deleteMultiple($ids);

        echo json_encode([
            'success' => $result,
            'message' => $result ? '선택 항목이 삭제되었습니다.' : '삭제 실패'
        ]);
        exit;
    }

    // SORT
    if ($mode === 'sort') {
        $id = (int)($input['id'] ?? 0);
        $newNo = (int)($input['new_no'] ?? 0);

        if (!$id || $newNo <= 0) {
            echo json_encode(['success' => false, 'message' => '잘못된 데이터']);
            exit;
        }

        $db = DB::getInstance();
        $stmt = $db->prepare("SELECT * FROM nb_banners WHERE id = :id");
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

        $minSortNo = BannerModel::getMinSortNo();
        $maxSortNo = BannerModel::getMaxSortNo();

        if ($newNo > $maxSortNo) {
            echo json_encode(['success' => false, 'message' => '제일 높은 순서의 게시물입니다.']);
            exit;
        }

        if ($newNo < $minSortNo) {
            echo json_encode(['success' => false, 'message' => '제일 낮은 순서의 게시물입니다.']);
            exit;
        }

        BannerModel::shiftSortNosForUpdate($oldNo, $newNo, $id);

        $dataToUpdate = [
            'title'            => $currentData['title'],
            'banner_type'      => 1,
            'has_link'         => (int)$currentData['has_link'],
            'link_url'         => $currentData['link_url'],
            'is_target'        => (int)$currentData['is_target'],
            'is_active'        => (int)$currentData['is_active'],
            'description'      => $currentData['description'],
            'start_at'         => null,
            'end_at'           => null,
            'display_start_at' => $currentData['display_start_at'] ?? null,
            'display_end_at'   => $currentData['display_end_at'] ?? null,
            'is_unlimited'     => (int)$currentData['is_unlimited'],
            'banner_image'     => $currentData['banner_image'],
            'banner_image_mobile' => null,
            'sort_no'          => $newNo,
        ];

        $updateResult = BannerModel::update($id, $dataToUpdate);

        echo json_encode([
            'success' => (bool)$updateResult,
            'message' => $updateResult ? '순서가 변경되었습니다.' : '변경 실패'
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => '유효하지 않은 요청입니다.']);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '처리 중 오류 발생: ' . $e->getMessage()
    ]);
}
