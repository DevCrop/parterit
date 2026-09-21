<?php

namespace Seo;

class RobotsGenerator
{
    /**
     * Build the robots.txt body.
     *
     * Resolution order for the body:
     *   1) nb_siteinfo.robots_content (admin-edited)
     *   2) default template
     *
     * Always-injected lines (security/discoverability — kept regardless of admin edits):
     *   - Disallow: /<admin_dir>/    (project_config('admin.dir'))
     *   - Sitemap: <base_url>/sitemap.xml
     */
    public function build(): string
    {
        $context = SeoContext::fromGlobals();
        $baseUrl = $context->baseUrl();

        $body = $this->loadStoredBody();
        if ($body === null || trim($body) === '') {
            $body = $this->defaultTemplate();
        }

        $body = $this->ensureAdminDisallow($body);
        $body = $this->ensureSitemapDirective($body, $baseUrl);

        return $this->normalizeLineEndings($body);
    }

    /** Default template used when admin has not configured a body yet. */
    public function defaultTemplate(): string
    {
        return implode("\n", [
            'User-agent: *',
            'Allow: /',
            '',
        ]);
    }

    /** Force-inject Disallow for the admin directory (security guard). */
    protected function ensureAdminDisallow(string $body): string
    {
        $adminDir = $this->resolveAdminDir();
        if ($adminDir === '') {
            return $body;
        }

        $needle = 'Disallow: /' . $adminDir;
        // Match "Disallow: /console" or "Disallow: /console/" — case-insensitive
        $pattern = '/^\s*Disallow:\s*\/' . preg_quote($adminDir, '/') . '\/?\s*$/im';
        if (preg_match($pattern, $body)) {
            return $body;
        }

        // Inject under the first User-agent block, or append at end if none found
        if (preg_match('/^User-agent:\s*\*/im', $body)) {
            return preg_replace(
                '/(^User-agent:\s*\*[^\n]*\n)/im',
                '$1' . $needle . '/' . "\n",
                $body,
                1
            );
        }

        return rtrim($body) . "\n\n" . "User-agent: *\n" . $needle . "/\n";
    }

    /** Append Sitemap: directive if missing. */
    protected function ensureSitemapDirective(string $body, string $baseUrl): string
    {
        $url = rtrim($baseUrl, '/') . '/sitemap.xml';
        $body = preg_replace('/^\s*Sitemap:\s*\S+\s*$/im', '', $body) ?? $body;
        return rtrim($body) . "\n\nSitemap: " . $url . "\n";
    }

    protected function resolveAdminDir(): string
    {
        if (function_exists('project_config')) {
            $dir = (string) \project_config('admin.dir', 'console');
        } else {
            $dir = 'console';
        }
        return trim($dir, "/ \t\r\n");
    }

    /** Read DB-stored robots body. Returns null on miss/error. */
    protected function loadStoredBody(): ?string
    {
        if (!class_exists('DB')) {
            return null;
        }

        global $NO_SITE_UNIQUE_KEY;
        $sitekey = $NO_SITE_UNIQUE_KEY ?? 'DKHOLD';

        try {
            $pdo = \DB::getInstance();
            $stmt = $pdo->prepare(
                'SELECT robots_content FROM nb_siteinfo WHERE sitekey = :sitekey LIMIT 1'
            );
            $stmt->execute([':sitekey' => $sitekey]);
            $value = $stmt->fetchColumn();
            return ($value === false || $value === null) ? null : (string) $value;
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function normalizeLineEndings(string $body): string
    {
        $body = str_replace(["\r\n", "\r"], "\n", $body);
        return rtrim($body, "\n") . "\n";
    }
}
