<?php

class PageContext
{
    public int $depthnum;
    public int $pagenum;
    public string $pageName;

    public function __construct(int $depthnum = 1, int $pagenum = 1, string $pageName = '')
    {
        $this->depthnum = $depthnum;
        $this->pagenum = $pagenum;
        $this->pageName = $pageName;
    }

    public static function fromRequest($role = null): self
    {
        $path = self::getCurrentPath();
        $map = self::buildRouteMap();

        if (isset($map[$path])) {
            $match = $map[$path];
            $context = new self($match['depth'], $match['page'], $match['title']);
        } else {
            $dir = dirname($path);
            if ($dir !== '.') {
                $index = $dir . '/index.php';
                if (isset($map[$index])) {
                    $match = $map[$index];
                    $context = new self($match['depth'], $match['page'], $match['title']);
                } else {
                    $context = new self(1, 1, '');
                }
            } else {
                $context = new self(1, 1, '');
            }
        }

        if ($role !== null && method_exists($role, 'is') && $role->is('external') && $context->depthnum === 5) {
            $externalSiteinfoPage = [
                'setting/pwd.php' => 1,
                'privacy/index.php' => 2,
                'setting/seo.php' => 3,
            ];
            if (isset($externalSiteinfoPage[$path])) {
                $context->pagenum = $externalSiteinfoPage[$path];
            }
        }

        return $context;
    }

    private static function resolveAdminPagesBase(): string
    {
        $globalBase = $GLOBALS['NO_ADMIN_PAGES_BASE'] ?? '';
        if (is_string($globalBase) && $globalBase !== '') {
            return rtrim($globalBase, '/');
        }

        if (function_exists('no_admin_pages_base_from_script')) {
            $base = no_admin_pages_base_from_script();
            return $base !== '' ? rtrim($base, '/') : '';
        }

        return '';
    }

    private static function getCurrentPath(): string
    {
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseRaw = self::resolveAdminPagesBase();
        if ($baseRaw !== '') {
            $base = $baseRaw . '/';
            $position = strpos($script, $base);
            if ($position !== false) {
                return ltrim(substr($script, $position + strlen($base)), '/');
            }
        }

        $parts = explode('/', trim($script, '/'));
        $index = array_search('pages', $parts, true);
        if ($index !== false && isset($parts[$index + 1])) {
            return implode('/', array_slice($parts, $index + 1));
        }

        return basename($script);
    }

    private static function buildRouteMap(): array
    {
        static $map = null;
        if ($map !== null) {
            return $map;
        }

        $configPath = __DIR__ . '/../../config/menu.config.php';
        $menus = is_file($configPath) ? require $configPath : [];

        $map = [];
        foreach ($menus as $depth => $menu) {
            $subs = $menu['subs'] ?? [];
            if (empty($subs)) {
                $url = $menu['url'] ?? '';
                if ($url !== '') {
                    $base = self::resolveAdminPagesBase();
                    $relative = $url;
                    if ($base !== '') {
                        $pagesBasePattern = preg_quote($base, '#');
                        $relative = preg_replace('#^' . $pagesBasePattern . '/?#', '', $url);
                    }
                    $map[$relative] = [
                        'depth' => $depth,
                        'page' => 0,
                        'title' => $menu['title'] ?? '',
                    ];
                }
                continue;
            }

            foreach ($subs as $index => $sub) {
                $url = $sub['url'] ?? '';
                if ($url !== '') {
                    $map[$url] = [
                        'depth' => $depth,
                        'page' => $index + 1,
                        'title' => $sub['title'] ?? '',
                    ];
                }
            }
        }

        foreach (self::getRouteOverrides() as $path => $context) {
            $map[$path] = $context;
        }

        return $map;
    }

    private static function getRouteOverrides(): array
    {
        return [
            'account/edit.php' => ['depth' => 6, 'page' => 1, 'title' => '계정'],
            'account/new.php' => ['depth' => 6, 'page' => 2, 'title' => '계정'],

            'board/board.add.php' => ['depth' => 1, 'page' => 1, 'title' => '게시글'],
            'board/board.list.php' => ['depth' => 1, 'page' => 1, 'title' => '게시글 관리'],
            'board/board.view.php' => ['depth' => 1, 'page' => 1, 'title' => '게시글'],
            'board/board.category.view.php' => ['depth' => 1, 'page' => 1, 'title' => '게시글'],
            'board/board.comment.view.php' => ['depth' => 1, 'page' => 1, 'title' => '게시글'],
            'board/board.manage.add.php' => ['depth' => 1, 'page' => 1, 'title' => '게시판'],
            'board/board.manage.list.php' => ['depth' => 1, 'page' => 1, 'title' => '게시판'],
            'board/board.manage.view.php' => ['depth' => 1, 'page' => 1, 'title' => '게시판'],
            'board/board.role.php' => ['depth' => 1, 'page' => 1, 'title' => '게시판'],
            'board/board.role.view.php' => ['depth' => 1, 'page' => 1, 'title' => '게시판'],

            'design/popup.edit.php' => ['depth' => 2, 'page' => 1, 'title' => '팝업 관리'],
            'design/popup.new.php' => ['depth' => 2, 'page' => 1, 'title' => '팝업 관리'],
            'design/banner.edit.php' => ['depth' => 2, 'page' => 2, 'title' => '배너 관리'],
            'design/banner.new.php' => ['depth' => 2, 'page' => 2, 'title' => '배너 관리'],

            'request/request.view.php' => ['depth' => 3, 'page' => 1, 'title' => '소개서·도입 문의'],

            'privacy/edit.php' => ['depth' => 5, 'page' => 5, 'title' => '개인정보처리방침'],
            'privacy/new.php' => ['depth' => 5, 'page' => 5, 'title' => '개인정보처리방침'],
            'setting/edit.php' => ['depth' => 5, 'page' => 4, 'title' => '메타 태그'],
            'setting/external.tag.php' => ['depth' => 5, 'page' => 4, 'title' => '사이트 메타 태그'],
            'setting/new.php' => ['depth' => 5, 'page' => 4, 'title' => '메타 태그'],
            'setting/seo.edit.php' => ['depth' => 5, 'page' => 3, 'title' => '페이지별 SEO'],
            'setting/seo.new.php' => ['depth' => 5, 'page' => 3, 'title' => '페이지별 SEO'],
            'setting/robots.php' => ['depth' => 5, 'page' => 0, 'title' => 'robots.txt 관리'], // menu entry hidden — no sub highlight
            'setting/pwd.php' => ['depth' => 5, 'page' => 5, 'title' => '비밀번호 변경'],
        ];
    }

    public function toArray(): array
    {
        return [
            'depthnum' => $this->depthnum,
            'pagenum' => $this->pagenum,
            'pageName' => $this->pageName,
        ];
    }
}
