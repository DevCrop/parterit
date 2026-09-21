<?php

class SeoModel
{
    /** locale 컬럼 suffix 별 메타 필드 */
    public const LOCALE_FIELDS = ['page_title', 'meta_title', 'meta_description', 'meta_keywords'];

    /**
     * supported locales 조회 (config/functions.php 헬퍼 우선, fallback: en/ko/jp)
     */
    private static function locales(): array
    {
        if (function_exists('project_supported_locales')) {
            return project_supported_locales();
        }
        return ['en', 'ko', 'jp'];
    }

    /**
     * INSERT/UPDATE 공용: 입력 데이터에서 locale 별 컬럼 + legacy 컬럼 + robots 메타를 추출
     * legacy 컬럼은 default locale 값으로 미러링
     */
    private static function expandLocalePayload(array $data): array
    {
        $defaultLocale = function_exists('project_default_locale') ? project_default_locale() : 'en';
        $payload = [
            'path' => trim((string)($data['path'] ?? '')),
        ];

        foreach (self::locales() as $lc) {
            foreach (self::LOCALE_FIELDS as $field) {
                $col = "{$field}_{$lc}";
                $val = isset($data[$col]) ? trim((string)$data[$col]) : '';
                $payload[$col] = $val !== '' ? $val : null;
            }
        }

        // legacy 컬럼: default locale 값을 미러링하여 fallback 호환 유지
        foreach (self::LOCALE_FIELDS as $field) {
            $defaultCol = "{$field}_{$defaultLocale}";
            $payload[$field] = $payload[$defaultCol] ?? null;
        }

        // robots 메타: 비어있으면 NULL → 사이트 전역 meta_robots로 fallback
        $robots = isset($data['robots']) ? trim((string)$data['robots']) : '';
        $payload['robots'] = $robots !== '' ? $robots : null;

        // 비-locale 단일 필드: 비어있으면 NULL → 사이트 전역 값으로 fallback
        foreach (['og_image', 'og_type', 'twitter_card'] as $simpleField) {
            $val = isset($data[$simpleField]) ? trim((string)$data[$simpleField]) : '';
            $payload[$simpleField] = $val !== '' ? $val : null;
        }

        return $payload;
    }

    public static function insert($data)
    {
        $db = DB::getInstance();
        $payload = self::expandLocalePayload($data);

        $cols = array_keys($payload);
        $placeholders = array_map(static fn($c) => ":{$c}", $cols);

        $sql = "INSERT INTO nb_branch_seos (`" . implode('`, `', $cols) . "`) VALUES (" . implode(', ', $placeholders) . ")";
        $stmt = $db->prepare($sql);

        $params = [];
        foreach ($payload as $k => $v) {
            $params[":{$k}"] = $v;
        }
        return $stmt->execute($params);
    }

    public static function update($id, $data)
    {
        $db = DB::getInstance();
        $payload = self::expandLocalePayload($data);

        $sets = [];
        foreach (array_keys($payload) as $col) {
            $sets[] = "`{$col}` = :{$col}";
        }
        $sets[] = "updated_at = CURRENT_TIMESTAMP";

        $sql = "UPDATE nb_branch_seos SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = $db->prepare($sql);

        $params = [':id' => $id];
        foreach ($payload as $k => $v) {
            $params[":{$k}"] = $v;
        }
        return $stmt->execute($params);
    }

    public static function delete($id)
    {
        $db = DB::getInstance();

        $sql = "DELETE FROM nb_branch_seos WHERE id = :id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public static function deleteMultiple(array $ids): bool
    {
        if (empty($ids)) return false;

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $db = DB::getInstance();
        $stmt = $db->prepare("DELETE FROM nb_branch_seos WHERE id IN ($placeholders)");

        return $stmt->execute($ids);
    }

    /**
     * 경로로 SEO 데이터 조회
     * @param string $path 경로 (예: '/special/hospital')
     * @return array|null SEO 데이터 또는 null
     */
    public static function findByPath(string $path): ?array
    {
        $db = DB::getInstance();

        // 쿼리스트링 제거 및 정규화
        $path = parse_url($path, PHP_URL_PATH) ?: $path;
        $path = '/' . ltrim($path, '/');

        $stmt = $db->prepare("SELECT * FROM nb_branch_seos WHERE path = :path LIMIT 1");
        $stmt->execute([':path' => $path]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}
