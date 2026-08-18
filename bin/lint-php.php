<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$directories = [$root.'/src', $root.'/tests'];
$files = [];

foreach ($directories as $directory) {
    if (!is_dir($directory)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    foreach ($iterator as $file) {
        if ($file->isFile() && 'php' === strtolower($file->getExtension())) {
            $files[] = $file->getPathname();
        }
    }
}

sort($files);

foreach ($files as $file) {
    $command = escapeshellarg(PHP_BINARY).' -l '.escapeshellarg($file);
    passthru($command, $exitCode);
    if (0 !== $exitCode) {
        exit($exitCode);
    }
}

printf("PHP syntax OK for %d files.\n", count($files));

exit(0);
