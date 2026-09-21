<?php

class AccountModel {
    
    public static function insert($data) {
        $db = DB::getInstance();
        global $NO_SITE_UNIQUE_KEY;

        $sql = "
            INSERT INTO nb_admin (uid, upwd, uname, email, phone, active_status, role_id, sitekey, created_at)
            VALUES (:uid, :upwd, :uname, :email, :phone, :active_status, :role_id, :sitekey, :created_at)
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':uid' => $data['uid'],
            ':upwd' => $data['upwd'],
            ':uname' => $data['uname'],
            ':email' => $data['email'],
            ':phone' => $data['phone'],
            ':active_status' => $data['active_status'] ?? 'Y',
            ':role_id' => $data['role_id'] ?? 3, // 기본값: 외부인
            ':sitekey' => $NO_SITE_UNIQUE_KEY,
            ':created_at' => date('Y-m-d H:i:s'),
        ]);

        return $db->lastInsertId();
    }

    
    public static function delete($id) 
    {
        $db = DB::getInstance();
        $sql = "DELETE FROM nb_admin WHERE no = :no";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':no' => $id]);
    }

    public static function deleteMultiple(array $ids): bool
    {
        if (empty($ids)) return false;

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $db = DB::getInstance();
        $stmt = $db->prepare("DELETE FROM nb_admin WHERE no IN ($placeholders)");

        return $stmt->execute($ids);
    }

    public static function update($id, $data) 
    {
        $db = DB::getInstance();

        $fields = [
            'uid' => $data['uid'], 
            'uname' => $data['uname'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'active_status' => $data['active_status'] ?? 'Y',
            'role_id' => $data['role_id'] ?? 3,
        ];

        if (!empty($data['upwd'])) {
            $fields['upwd'] = $data['upwd'];
        }

        $set = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($fields)));
        $fields['no'] = $id;

        $sql = "UPDATE nb_admin SET $set WHERE no = :no";
        $stmt = $db->prepare($sql);
        return $stmt->execute($fields);
    }


    public static function exists(array $conditions): bool
    {
        $db = DB::getInstance();
        
        $clauses = [];
        foreach ($conditions as $key => $val) {
            $clauses[] = "$key = :$key";
        }

        $where = implode(" AND ", $clauses);
        $sql = "SELECT COUNT(*) FROM nb_admin WHERE $where";

        $stmt = $db->prepare($sql);
        $stmt->execute($conditions);
        
        return $stmt->fetchColumn() > 0;
    }

    /** 역할별 계정 수 조회 */
    public static function countByRole(int $roleId): int
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("SELECT COUNT(*) FROM nb_admin WHERE role_id = :role_id");
        $stmt->execute([':role_id' => $roleId]);
        return (int) $stmt->fetchColumn();
    }

    /** 계정의 현재 role_id 조회 */
    public static function getRoleId(int $id): ?int
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("SELECT role_id FROM nb_admin WHERE no = :no");
        $stmt->execute([':no' => $id]);
        $v = $stmt->fetchColumn();
        return $v !== false ? (int) $v : null;
    }

    public static function existsExceptSelf(array $conditions, int $excludeId): bool
    {
        $db = DB::getInstance();

        $clauses = [];
        foreach ($conditions as $key => $val) {
            $clauses[] = "$key = :$key";
        }

        $where = implode(" AND ", $clauses);
        $sql = "SELECT COUNT(*) FROM nb_admin WHERE $where AND no != :no";

        $params = $conditions;
        $params['no'] = $excludeId;

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn() > 0;
    }


}