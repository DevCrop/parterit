<?php

declare(strict_types=1);

interface FileStoreInterface
{
    public function resolveFilePath(string $storedName, string $subDir = ''): ?string;
}
