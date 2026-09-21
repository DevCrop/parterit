<?php

class Menu
{
    public $boardPath;
    public $dirList;
    public $menuPath;
    public $boardNumList;

    /** @var array */
    private $config = [];

    /** @var array */
    private $data = [];

    /** @var string */
    private $site_name = '';

    /** @var array */
    private $menuItems = [];

    /** @var array|null */
    private $curPage;

    /** @var string */
    private $pageTitle = '';

    /** @var string */
    private $currentRequestUri = '/';

    /** @var array */
    private $requestQuery = [];

    public function __construct(array $config = [])
    {
        $this->config = $config ?: $this->resolveConfig();
        $this->setInfo();
        $this->captureRequestContext();
        $this->data = menu_read_json((string) $this->menuPath);
        $this->site_name = (string) ($this->data['site_name'] ?? '');
        $this->menuItems = $this->buildPageTree((array) ($this->data['pages'] ?? []));
        $this->markActiveTrail();
        $this->pageTitle = $this->buildPageTitle();
    }

    public function getSiteName()
    {
        return $this->site_name;
    }

    public function getCurPageIndex()
    {
        return $this->curPage['index'] ?? null;
    }

    public function getCurPageList()
    {
        $indexes = $this->getCurPageIndex();
        if (!is_array($indexes)) {
            return null;
        }

        $trail = [];
        $items = $this->menuItems;

        foreach ($indexes as $depth => $index) {
            if (!isset($items[$index])) {
                break;
            }

            $trail[] = $items[$index];
            $items = $items[$index]['pages'] ?? [];
        }

        return $trail;
    }

    public function getCurPage()
    {
        return $this->curPage ?: null;
    }

    public function getMenuItems()
    {
        return $this->menuItems;
    }

    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    private function resolveConfig(): array
    {
        global $MENU_CONFIG, $DIR_LIST, $BOARD_PATH, $BOARD_NUM_LIST, $MENU_PATH;

        if (is_array($MENU_CONFIG ?? null)) {
            return $MENU_CONFIG;
        }

        return [
            'dir_list' => is_array($DIR_LIST ?? null) ? $DIR_LIST : [],
            'board_path' => (string) ($BOARD_PATH ?? ''),
            'board_num_list' => is_array($BOARD_NUM_LIST ?? null) ? $BOARD_NUM_LIST : [],
            'menu_path' => (string) ($MENU_PATH ?? ''),
        ];
    }

    private function setInfo(): void
    {
        $this->dirList = (array) ($this->config['dir_list'] ?? []);
        $this->boardPath = (string) ($this->config['board_path'] ?? '');
        $this->boardNumList = (array) ($this->config['board_num_list'] ?? []);
        $this->menuPath = (string) ($this->config['menu_path'] ?? '');
    }

    private function captureRequestContext(): void
    {
        $this->currentRequestUri = menu_current_request_uri();
        $this->requestQuery = [];

        foreach ($_GET as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $this->requestQuery[(string) $key] = (string) $value;
            }
        }
    }

    private function buildPageTree(array $pages, array $parentIndexes = [], string $parentDir = ''): array
    {
        $built = [];

        foreach ($pages as $index => $page) {
            if (!is_array($page)) {
                continue;
            }

            $currentIndexes = $parentIndexes;
            $currentIndexes[] = $index;

            $dirname = $this->composeDirname($parentDir, (string) ($page['dirname'] ?? ''));

            $item = $page;
            $item['index'] = $currentIndexes;
            $item['dirname'] = $dirname;
            $item['pages'] = $this->buildPageTree((array) ($page['pages'] ?? []), $currentIndexes, $dirname);
            $item['path'] = $this->resolvePath($item);
            $item['target'] = menu_is_external_url((string) $item['path']) ? '_blank' : '_self';
            $item['isActive'] = $this->isPageActive($item);

            if ($item['isActive'] && $this->curPage === null) {
                $this->curPage = $item;
            }

            $built[] = $item;
        }

        return $built;
    }

    private function composeDirname(string $parentDir, string $pageDir): string
    {
        $parentDir = trim($parentDir, '/');
        $pageDir = trim($pageDir, '/');

        if ($parentDir !== '' && $pageDir !== '') {
            return $parentDir . '/' . $pageDir;
        }

        return $pageDir !== '' ? $pageDir : $parentDir;
    }

    private function resolvePath(array $page): string
    {
        if (!empty($page['ext_link'])) {
            return (string) $page['ext_link'];
        }

        if (isset($page['board_no'])) {
            $params = ['board_no' => $page['board_no']];
            if (isset($page['category_no'])) {
                $params['category_no'] = $page['category_no'];
            }

            return $this->boardPath . '?' . http_build_query($params);
        }

        $filename = trim((string) ($page['filename'] ?? ''));
        if ($filename === '') {
            if (!empty($page['pages'][0]['path'])) {
                return (string) $page['pages'][0]['path'];
            }

            return $this->getFile('index');
        }

        $path = trim((string) ($page['dirname'] ?? ''), '/');
        if ($path !== '') {
            $path .= '/';
        }

        return $this->getFile($path . $filename);
    }

    private function isPageActive(array $page): bool
    {
        $pagePath = (string) ($page['path'] ?? '');
        if ($pagePath === '') {
            return false;
        }

        $parts = parse_url($pagePath);
        $query = [];

        if (!empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        if (isset($query['board_no'])) {
            foreach ($query as $key => $value) {
                if (!array_key_exists($key, $this->requestQuery) || $this->requestQuery[$key] !== (string) $value) {
                    return false;
                }
            }

            return true;
        }

        return $this->currentRequestUri === $pagePath;
    }

    private function markActiveTrail(): void
    {
        $indexes = $this->getCurPageIndex();
        if (!is_array($indexes)) {
            return;
        }

        $items =& $this->menuItems;

        foreach ($indexes as $index) {
            if (!isset($items[$index])) {
                break;
            }

            $items[$index]['isActive'] = true;
            $items =& $items[$index]['pages'];
        }
    }

    private function buildPageTitle(): string
    {
        $title = $this->site_name;
        $trail = $this->getCurPageList();

        if (!is_array($trail) || $trail === []) {
            return $title;
        }

        $current = end($trail);
        $pageTitle = (string) ($current['title'] ?? '');

        return $pageTitle !== '' ? $pageTitle . ' | ' . $title : $title;
    }

    public function getFile($path)
    {
        return (string) ($this->dirList['pages'] ?? '') . '/' . trim((string) $path, '/') . '.php';
    }
}
