<?php

require_once "../bootstrap/admin.php";
require_once "../Model/AccountModel.php";
require_once "../core/Validator.php";

$role->requireLogin();
$role->requireCanModify();

header('Content-Type: application/json');

if (!$role->canView()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => '권한이 없습니다.']);
    exit;
}

$currentAdminNo = (int)($_SESSION['no_adm_login_no'] ?? 0);

$resolveRoleId = static function (array $input) use (&$admin_roles): int {
    $roleId = isset($input['role_id']) ? (int)$input['role_id'] : 3;

    if (!isset($admin_roles[$roleId])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => '유효하지 않은 권한입니다.'
        ]);
        exit;
    }

    return $roleId;
};

try {
    $input = $_POST;
    $mode = $input['mode'] ?? '';

    if ($mode === 'save') {
        $validator = new Validator();
        $validator->require('uid', $input['uid'] ?? '', '아이디');
        $validator->require('upwd', $input['upwd'] ?? '', '비밀번호');
        $validator->require('upwd_confirm', $input['upwd_confirm'] ?? '', '비밀번호 확인');
        $validator->passwordPolicy('upwd', $input['upwd'] ?? '', '비밀번호');
        $validator->require('uname', $input['uname'] ?? '', '이름');
        $validator->email('email', $input['email'] ?? '', '이메일');
        $validator->phone('phone', $input['phone'] ?? '', '연락처');

        if (($input['upwd'] ?? '') !== ($input['upwd_confirm'] ?? '')) {
            echo json_encode([
                'success' => false,
                'message' => '비밀번호와 비밀번호 확인이 일치하지 않습니다.'
            ]);
            exit;
        }

        if ($validator->fails()) {
            echo json_encode([
                'success' => false,
                'message' => implode("\n", $validator->getErrors())
            ]);
            exit;
        }

        if (AccountModel::exists(['uid' => $input['uid']])) {
            echo json_encode([
                'success' => false,
                'message' => '이미 사용 중인 아이디입니다.'
            ]);
            exit;
        }

        if (AccountModel::exists(['email' => $input['email']])) {
            echo json_encode([
                'success' => false,
                'message' => '이미 사용 중인 이메일입니다.'
            ]);
            exit;
        }

        if (AccountModel::exists(['phone' => $input['phone']])) {
            echo json_encode([
                'success' => false,
                'message' => '이미 사용 중인 연락처입니다.'
            ]);
            exit;
        }

        $roleId = $resolveRoleId($input);
        if (!$role->canEdit() && $roleId !== 3) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => '권한이 없습니다.'
            ]);
            exit;
        }

        $input['role_id'] = $roleId;
        $limit = Role::getRoleLimit($roleId);
        if ($limit !== null && AccountModel::countByRole($roleId) >= $limit) {
            global $admin_roles;
            $roleName = $admin_roles[$roleId]['name'] ?? '해당 권한';
            echo json_encode([
                'success' => false,
                'message' => $roleName . ' 계정은 최대 ' . $limit . '개까지 생성할 수 있습니다.'
            ]);
            exit;
        }

        $input['created_at'] = date('Y-m-d H:i:s');
        $input['upwd'] = password_hash($input['upwd'], PASSWORD_DEFAULT);
        unset($input['upwd_confirm']);

        $id = AccountModel::insert($input);

        echo json_encode([
            'success' => true,
            'message' => '계정을 등록했습니다.',
            'id' => $id
        ]);
        exit;
    }

    if ($mode === 'delete') {
        $id = $input['id'] ?? null;

        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => '삭제할 ID가 없습니다.'
            ]);
            exit;
        }

        $deleted = AccountModel::delete($id);

        echo json_encode([
            'success' => $deleted,
            'message' => $deleted ? '계정을 삭제했습니다.' : '계정 삭제에 실패했습니다.'
        ]);
        exit;
    }

    if ($mode === 'update') {
        $id = $input['no'] ?? null;

        if (!$id) {
            echo json_encode([
                'success' => false,
                'message' => '수정할 ID가 없습니다.'
            ]);
            exit;
        }

        $validator = new Validator();
        $validator->require('uid', $input['uid'] ?? '', '아이디');
        $validator->require('uname', $input['uname'] ?? '', '이름');
        $validator->email('email', $input['email'] ?? '', '이메일');
        $validator->phone('phone', $input['phone'] ?? '', '연락처');

        if (($input['upwd'] ?? '') !== '' || ($input['upwd_confirm'] ?? '') !== '') {
            $validator->passwordPolicy('upwd', $input['upwd'] ?? '', '비밀번호');
        }

        if ($validator->fails()) {
            echo json_encode([
                'success' => false,
                'message' => implode("\n", $validator->getErrors()) ?: '입력값에 오류가 있습니다.'
            ]);
            exit;
        }

        if (AccountModel::existsExceptSelf(['uid' => $input['uid']], $id)) {
            echo json_encode(['success' => false, 'message' => '이미 사용 중인 아이디입니다.']);
            exit;
        }
        if (AccountModel::existsExceptSelf(['email' => $input['email']], $id)) {
            echo json_encode(['success' => false, 'message' => '이미 사용 중인 이메일입니다.']);
            exit;
        }
        if (AccountModel::existsExceptSelf(['phone' => $input['phone']], $id)) {
            echo json_encode(['success' => false, 'message' => '이미 사용 중인 연락처입니다.']);
            exit;
        }

        $upwd = $input['upwd'] ?? '';
        $upwdConfirm = $input['upwd_confirm'] ?? '';

        if ($upwd !== '' || $upwdConfirm !== '') {
            if ($upwdConfirm === '') {
                echo json_encode([
                    'success' => false,
                    'message' => '비밀번호 확인을 입력해주세요.'
                ]);
                exit;
            }

            if ($upwd !== $upwdConfirm) {
                echo json_encode([
                    'success' => false,
                    'message' => '비밀번호와 비밀번호 확인이 일치하지 않습니다.'
                ]);
                exit;
            }

            $input['upwd'] = password_hash($upwd, PASSWORD_DEFAULT);
        } else {
            unset($input['upwd']);
        }
        unset($input['upwd_confirm']);

        $newRoleId = $resolveRoleId($input);
        $currentRoleId = AccountModel::getRoleId((int)$id);
        if ($currentRoleId === null) {
            echo json_encode([
                'success' => false,
                'message' => '계정을 찾을 수 없습니다.'
            ]);
            exit;
        }

        if ($currentAdminNo === (int)$id && $currentRoleId !== $newRoleId) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => '본인 계정의 권한은 변경할 수 없습니다.'
            ]);
            exit;
        }

        if (!$role->canEdit() && $currentRoleId !== $newRoleId) {
            http_response_code(403);
            echo json_encode([
                'success' => false,
                'message' => '권한이 없습니다.'
            ]);
            exit;
        }

        $input['role_id'] = $newRoleId;
        if ($currentRoleId !== $newRoleId) {
            $limit = Role::getRoleLimit($newRoleId);
            if ($limit !== null && AccountModel::countByRole($newRoleId) >= $limit) {
                global $admin_roles;
                $roleName = $admin_roles[$newRoleId]['name'] ?? '해당 권한';
                echo json_encode([
                    'success' => false,
                    'message' => $roleName . ' 계정은 최대 ' . $limit . '개까지 생성할 수 있습니다.'
                ]);
                exit;
            }
        }

        $updated = AccountModel::update($id, $input);

        echo json_encode([
            'success' => $updated,
            'message' => $updated ? '계정을 수정했습니다.' : '계정 수정에 실패했습니다.'
        ]);
        exit;
    }

    if ($mode === 'delete_array') {
        $ids = json_decode($input['ids'] ?? '[]', true);

        if (!is_array($ids) || empty($ids)) {
            echo json_encode(['success' => false, 'message' => '삭제할 항목이 없습니다.']);
            exit;
        }

        $result = AccountModel::deleteMultiple($ids);

        echo json_encode([
            'success' => $result,
            'message' => $result ? '선택한 계정을 삭제했습니다.' : '계정 삭제에 실패했습니다.'
        ]);
        exit;
    }

    echo json_encode([
        'success' => false,
        'message' => '유효하지 않은 요청입니다.'
    ]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => '처리 중 오류가 발생했습니다: ' . $e->getMessage()
    ]);
}
