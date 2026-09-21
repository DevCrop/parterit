<?php

namespace Seo;

use Routing\Router;

class SitemapGenerator
{
    protected Router $router;

    /**
     * Board detail URL templates. board_id => '/path/{no}' pattern.
     * Board-specific routes may be added when the new page structure is defined.
     */
    protected array $boardUrlTemplates = [];

    public function __construct(?Router $router = null)
    {
        $this->router = $router ?: \app()->router();
    }

    public function generateXml(): string
    {
        $context = SeoContext::fromGlobals();
        $baseUrl = $context->baseUrl();
        $entries = array_merge(
            $this->collectEntries($baseUrl),
            $this->collectBoardEntries($baseUrl)
        );

        $seen = [];
        $unique = [];
        foreach ($entries as $entry) {
            if (isset($seen[$entry['loc']])) {
                continue;
            }
            $seen[$entry['loc']] = true;
            $unique[] = $entry;
        }

        usort($unique, static function (array $a, array $b): int {
            return strcmp($a['loc'], $b['loc']);
        });

        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($unique as $entry) {
            $xml[] = '  <url>';
            $xml[] = '    <loc>' . htmlspecialchars($entry['loc'], ENT_QUOTES, 'UTF-8') . '</loc>';
            if (!empty($entry['lastmod'])) {
                $xml[] = '    <lastmod>' . htmlspecialchars($entry['lastmod'], ENT_QUOTES, 'UTF-8') . '</lastmod>';
            }
            $xml[] = '  </url>';
        }

        $xml[] = '</urlset>';

        return implode("\n", $xml);
    }

    protected function collectEntries(string $baseUrl): array
    {
        $entries = [];
        $seen = [];

        foreach ($this->router->getNamedRoutes() as $name => $route) {
            if ($this->shouldSkipRoute($name, $route->getUri())) {
                continue;
            }

            if (\project_localization_enabled()) {
                foreach (\locales() as $locale) {
                    $path = \build_localized_url($route->getUri(), $locale);
                    $loc = $baseUrl . $path;
                    if (isset($seen[$loc])) {
                        continue;
                    }
                    $seen[$loc] = true;
                    $entries[] = ['loc' => $loc];
                }
                continue;
            }

            $path = \build_localized_url($route->getUri(), \project_default_locale());
            $loc = $baseUrl . $path;
            if (isset($seen[$loc])) {
                continue;
            }
            $seen[$loc] = true;
            $entries[] = ['loc' => $loc];
        }

        return $entries;
    }

    /**
     * Collect board detail URLs (BLOG/NEWS posts) with locale variants.
     * Each visible post is emitted once per supported locale; lastmod uses the
     * most recent of nb_board.regdate / nb_board_locale.updated_at.
     */
    protected function collectBoardEntries(string $baseUrl): array
    {
        $entries = [];
        $boardIds = array_keys($this->boardUrlTemplates);
        if (empty($boardIds)) {
            return $entries;
        }

        $rows = $this->fetchBoardPosts($boardIds);
        if (empty($rows)) {
            return $entries;
        }

        $locales = \project_localization_enabled() ? \locales() : [\project_default_locale()];

        foreach ($rows as $row) {
            $template = $this->boardUrlTemplates[(int) $row['board_no']] ?? null;
            if ($template === null) {
                continue;
            }
            $path = str_replace('{no}', (string) (int) $row['no'], $template);
            $lastmod = $this->formatLastmod($row['lastmod'] ?? null);

            foreach ($locales as $locale) {
                $loc = $baseUrl . \build_localized_url($path, $locale);
                $entries[] = [
                    'loc' => $loc,
                    'lastmod' => $lastmod,
                ];
            }
        }

        return $entries;
    }

    /**
     * Fetch published posts for the given board ids, with the most recent
     * locale-row updated_at folded into a single `lastmod` column.
     *
     * Returns: array of ['no', 'board_no', 'lastmod']
     */
    protected function fetchBoardPosts(array $boardIds): array
    {
        if (!class_exists('DB')) {
            return [];
        }

        global $NO_SITE_UNIQUE_KEY;
        $sitekey = $NO_SITE_UNIQUE_KEY ?? 'DKHOLD';

        try {
            $pdo = \DB::getInstance();
        } catch (\Throwable $e) {
            return [];
        }

        $placeholders = [];
        $params = [':sitekey' => $sitekey];
        foreach (array_values($boardIds) as $i => $bid) {
            $key = ':bid' . $i;
            $placeholders[] = $key;
            $params[$key] = (int) $bid;
        }

        $sql = "SELECT a.no, a.board_no, a.regdate,
                       (SELECT MAX(lc.updated_at) FROM nb_board_locale lc WHERE lc.board_id = a.no) AS locale_updated_at
                FROM nb_board a
                WHERE a.sitekey = :sitekey
                  AND a.board_no IN (" . implode(',', $placeholders) . ")
                ORDER BY a.no DESC";

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            return [];
        }

        foreach ($rows as &$row) {
            $candidates = array_filter([
                $row['locale_updated_at'] ?? null,
                $row['regdate'] ?? null,
            ]);
            $row['lastmod'] = !empty($candidates) ? max($candidates) : null;
        }

        return $rows;
    }

    /** Convert a DB datetime ('Y-m-d H:i:s') to W3C date format for sitemap lastmod. */
    protected function formatLastmod(?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }
        $ts = strtotime($value);
        if ($ts === false || $ts <= 0) {
            return null;
        }
        return date('Y-m-d', $ts);
    }

    protected function shouldSkipRoute(string $name, string $uri): bool
    {
        if ($name === 'sitemap' || $name === 'robots') {
            return true;
        }

        if (strpos($uri, '{') !== false || strpos($uri, '}') !== false) {
            return true;
        }

        return false;
    }
}
