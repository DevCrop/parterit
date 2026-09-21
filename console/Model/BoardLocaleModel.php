<?php

/**
 * nb_board_locale (게시물 다국어 본문) 헬퍼.
 *
 * 한 게시물(nb_board.no = $boardId)에 대해 supported locale 마다 1 행씩 보존.
 * 컬럼: title, contents, write_name, extra1~30.
 */
class BoardLocaleModel
{
    /** locale 별로 저장되는 컬럼 (PK / FK / timestamp 제외) */
    public static function localeColumns(): array
    {
        $cols = ['title', 'contents', 'write_name'];
        for ($i = 1; $i <= 30; $i++) {
            $cols[] = "extra{$i}";
        }
        return $cols;
    }

    /** 지원 locale 목록 */
    public static function locales(): array
    {
        if (function_exists('project_supported_locales')) {
            return project_supported_locales();
        }
        return ['en', 'ko', 'jp'];
    }

    public static function defaultLocale(): string
    {
        if (function_exists('project_default_locale')) {
            return project_default_locale();
        }
        return 'en';
    }

    /**
     * 게시물 ID 로 모든 locale 행을 가져와 ['en' => row, 'ko' => row, ...] 형태 반환.
     */
    public static function findAllByBoardId(int $boardId): array
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("SELECT * FROM nb_board_locale WHERE board_id = :id");
        $stmt->execute([':id' => $boardId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($rows as $r) {
            $result[$r['locale']] = $r;
        }
        return $result;
    }

    /**
     * 단일 locale 행 조회. 없으면 null.
     */
    public static function findByBoardIdLocale(int $boardId, string $locale): ?array
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("SELECT * FROM nb_board_locale WHERE board_id = :id AND locale = :lc LIMIT 1");
        $stmt->execute([':id' => $boardId, ':lc' => $locale]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * 게시물 ID + locale 에 대해 INSERT or UPDATE.
     * $data 는 localeColumns() 에 정의된 키 일부 또는 전체.
     */
    public static function upsert(int $boardId, string $locale, array $data): bool
    {
        $db = DB::getInstance();
        $cols = self::localeColumns();

        $payload = ['board_id' => $boardId, 'locale' => $locale];
        foreach ($cols as $c) {
            $payload[$c] = array_key_exists($c, $data) ? (string)$data[$c] : null;
        }

        $allCols = array_keys($payload);
        $placeholders = array_map(static fn($c) => ":{$c}", $allCols);
        $updateAssignments = array_map(static fn($c) => "`{$c}` = VALUES(`{$c}`)", $cols);

        $sql = "INSERT INTO nb_board_locale (`" . implode('`, `', $allCols) . "`)
                VALUES (" . implode(', ', $placeholders) . ")
                ON DUPLICATE KEY UPDATE " . implode(', ', $updateAssignments);

        $stmt = $db->prepare($sql);
        $params = [];
        foreach ($payload as $k => $v) {
            $params[":{$k}"] = $v;
        }
        return $stmt->execute($params);
    }

    /**
     * 폼 입력에서 locale 별 데이터를 추출하여 모든 locale 에 대해 upsert.
     * $form 은 일반적으로 $_POST. 키 형식: title_en, contents_ko, extra1_jp, ...
     */
    public static function upsertFromForm(int $boardId, array $form): void
    {
        foreach (self::locales() as $lc) {
            $data = [];
            foreach (self::localeColumns() as $col) {
                $key = "{$col}_{$lc}";
                if (array_key_exists($key, $form)) {
                    $data[$col] = (string)$form[$key];
                }
            }
            // base64 인코딩된 contents 처리
            $encKey = "contents_encoding_{$lc}";
            if (
                isset($form[$encKey]) && $form[$encKey] === 'base64'
                && isset($data['contents'])
            ) {
                $data['contents'] = (string)base64_decode($data['contents']);
            }

            self::upsert($boardId, $lc, $data);
        }
    }

    /**
     * 게시물 삭제 시 locale 행 일괄 삭제.
     */
    public static function deleteByBoardId(int $boardId): bool
    {
        $db = DB::getInstance();
        $stmt = $db->prepare("DELETE FROM nb_board_locale WHERE board_id = :id");
        return $stmt->execute([':id' => $boardId]);
    }

    /**
     * 다중 게시물 삭제.
     */
    public static function deleteByBoardIds(array $boardIds): bool
    {
        if (empty($boardIds)) return false;
        $db = DB::getInstance();
        $placeholders = implode(',', array_fill(0, count($boardIds), '?'));
        $stmt = $db->prepare("DELETE FROM nb_board_locale WHERE board_id IN ({$placeholders})");
        return $stmt->execute(array_map('intval', $boardIds));
    }

    /**
     * 게시물 복사 시 locale 행도 함께 복제 ($srcId 의 모든 locale → $dstId 로 INSERT).
     * @param array<string,string|null> $contentsOverride locale=>새 contents 매핑 (이미지 src 재작성 후 값)
     */
    public static function copyForBoard(int $srcId, int $dstId, array $contentsOverride = []): void
    {
        $sourceRows = self::findAllByBoardId($srcId);
        foreach ($sourceRows as $locale => $row) {
            $data = [];
            foreach (self::localeColumns() as $col) {
                $data[$col] = $row[$col] ?? null;
            }
            if (array_key_exists($locale, $contentsOverride)) {
                $data['contents'] = $contentsOverride[$locale];
            }
            self::upsert($dstId, $locale, $data);
        }
    }

    /**
     * COALESCE fallback 체인을 위한 SELECT 절 helper.
     * 우선순위: locale 행 → default locale 행 → nb_board legacy 컬럼.
     *
     * 사용 예 (board.list 등):
     *   $sel = BoardLocaleModel::selectFallback('a', 'l_cur', 'l_def', ['title','contents']);
     *   ... LEFT JOIN nb_board_locale l_cur ON l_cur.board_id = a.no AND l_cur.locale = :loc_cur
     *   ... LEFT JOIN nb_board_locale l_def ON l_def.board_id = a.no AND l_def.locale = :loc_def
     */
    public static function selectFallback(string $boardAlias, string $curAlias, string $defAlias, array $fields): string
    {
        $parts = [];
        foreach ($fields as $f) {
            $parts[] = "COALESCE(NULLIF({$curAlias}.`{$f}`, ''), NULLIF({$defAlias}.`{$f}`, ''), {$boardAlias}.`{$f}`) AS `{$f}`";
        }
        return implode(",\n", $parts);
    }
}
