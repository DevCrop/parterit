<?php

declare(strict_types=1);

interface FileDownloadServiceInterface
{
    public function download(int $postId, string $fieldKey): void;
}
