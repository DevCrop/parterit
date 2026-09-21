<?php

if (!function_exists('partnerit_customer_stories')) {
    /**
     * 고객사례 CMS 데이터를 /partners 카드 형식으로 정규화한다.
     * extra1: 상호명, extra2: 대표 사례 여부(Y/N)
     *
     * @return list<array<string,mixed>>
     */
    function partnerit_customer_stories(): array
    {
        static $stories = null;
        if (is_array($stories)) {
            return $stories;
        }

        $stories = [];

        try {
            $board = getBoardInfoByName('고객사례');
            $boardNo = (int) ($board[0]['no'] ?? 0);
            if ($boardNo < 1) {
                return $stories;
            }

            foreach (getBoardForFront($boardNo, 100) as $row) {
                $stories[] = partnerit_customer_story_from_row($row);
            }
        } catch (Throwable $exception) {
            // CMS가 준비되지 않은 환경에서는 컴포넌트의 정적 fallback을 사용한다.
            $stories = [];
        }

        return $stories;
    }
}

if (!function_exists('partnerit_customer_story_from_row')) {
    /** @param array<string,mixed> $row */
    function partnerit_customer_story_from_row(array $row): array
    {
        $thumbnail = board_thumb_public_url((string) ($row['thumb_image'] ?? ''));
        $thumbnailWebp = board_thumb_webp_url((string) ($row['thumb_image'] ?? ''));
        $rawContents = htmlspecialchars_decode((string) ($row['contents'] ?? ''), ENT_QUOTES);
        $description = trim((string) preg_replace('/\s+/u', ' ', strip_tags($rawContents)));
        $timestamp = strtotime((string) ($row['regdate'] ?? ''));
        $id = (int) ($row['no'] ?? 0);

        return [
            'id' => $id,
            'href' => $id > 0 ? route('partners.show', ['no' => $id]) : '',
            'image' => $thumbnail,
            'image_webp' => $thumbnailWebp,
            'image_is_url' => true,
            'image_alt' => trim((string) ($row['extra1'] ?? '')) . ' 고객사례',
            'category' => trim((string) ($row['category_name'] ?? '고객사례')),
            'title' => trim((string) ($row['title'] ?? '')),
            'description' => $description,
            'contents_html' => $rawContents,
            'author' => trim((string) ($row['extra1'] ?? '')),
            'date' => $timestamp ? date('Y.m.d', $timestamp) : '',
            'featured' => strtoupper(trim((string) ($row['extra2'] ?? 'N'))) === 'Y',
        ];
    }
}

if (!function_exists('partnerit_featured_customer_story')) {
    function partnerit_featured_customer_story(array $stories): ?array
    {
        return partnerit_featured_customer_stories($stories)[0] ?? null;
    }
}

if (!function_exists('partnerit_featured_customer_stories')) {
    /** @return list<array<string,mixed>> */
    function partnerit_featured_customer_stories(array $stories): array
    {
        return array_values(array_filter(
            $stories,
            static fn(array $story): bool => !empty($story['featured'])
        ));
    }
}

if (!function_exists('partnerit_customer_story_by_id')) {
    function partnerit_customer_story_by_id(int $id): ?array
    {
        if ($id < 1) {
            return null;
        }

        foreach (partnerit_customer_stories() as $story) {
            if ((int) ($story['id'] ?? 0) === $id) {
                return $story;
            }
        }

        return null;
    }
}
