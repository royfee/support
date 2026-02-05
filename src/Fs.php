<?php

declare(strict_types=1);

namespace royfee\support;

use Exception;

class Fs
{
    public static function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * get file contents.
     * @param  string  $path
     * @param  bool  $lock
     * @return string
     * @throws Exception
     */
    public static function get($path, $lock = false)
    {
        if (static::isFile($path)) {
            if ($lock) {
                return static::sharedGet($path);
            } else {
                $text = file_get_contents($path);
                if ($text === false) {
                    throw new Exception("File get contents failed at path {$path}.");
                }
                return $text;
            }
        }

        throw new Exception("File does not exist at path {$path}.");
    }

    public static function isFile(string $file): bool
    {
        return is_file($file);
    }

    /**
     * Get contents of a file with shared access.
     *
     * @param  string  $path
     * @return string
     */
    public static function sharedGet($path): string
    {
        $contents = '';

        $handle = fopen($path, 'rb');

        if ($handle) {
            try {
                if (flock($handle, LOCK_SH)) {
                    clearstatcache(true, $path);

                    $contents = stream_get_contents($handle);

                    flock($handle, LOCK_UN);
                }
            } finally {
                fclose($handle);
            }
        }

        return $contents;
    }

    /**
     * Create a directory.
     *
     * @param  string  $path
     * @param  int  $mode
     * @param  bool  $recursive
     * @return bool
     */
    public static function makeDir($path, $mode = 0755, $recursive = false)
    {
        if (is_dir($path)) {
            return true;
        }

        if (!mkdir($path, $mode, $recursive)) {
            return false;
        }

        chmod($path, $mode);

        return true;
    }

    public static function isDir(string $directory): bool
    {
        return is_dir($directory);
    }

    /**
     * Move files to a new location, and create the target directory if it does not exist。
     */
    public static function move(string $source, string $destination): bool
    {
        if (!static::exists($source)) {
            return false;
        }

        $destDir = dirname($destination);

        if (!static::exists($destDir)) {
            if (!static::makeDir($destDir)) {
                return false;
            }
        }

        if (rename($source, $destination)) {
            return true;
        } elseif (copy($source, $destination)) {
            unlink($source);
            return true;
        }
        return false;
    }

    /**
     * Move a directory.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  bool  $overwrite
     * @return bool
     */
    public static function moveDir($from, $to, $overwrite = false)
    {
        if ($overwrite && static::isDir($to) && ! static::deleteDir($to)) {
            return false;
        }

        return rename($from, $to) === true;
    }
    /**
     * Get all files in a directory (including subdirectories).
     * @param string $directory
     * @param string|array<string> $extensions (ex:['php', 'html', 'js'] | 'php,html,js')
     * @param bool $recursive
     * @param int $maxDepth
     * @return array<string>
     * @throws Exception
     */
    public static function allFiles(string $directory, $extensions, $recursive = true, $maxDepth = 0): array
    {
        if (!static::isDir($directory) || !is_readable($directory)) {
            throw new Exception("Directory is unreadable: {$directory}");
        }

        $extensions = is_array($extensions) ? $extensions : explode(',', $extensions);

        $normalizedExtensions = array_map(function ($ext) {
            return strtolower(ltrim($ext, '.'));
        }, $extensions);

        $results = [];
        $directory = rtrim($directory, DIRECTORY_SEPARATOR);

        $iterator = $recursive
            ? new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            )
            : new \FilesystemIterator($directory, \FilesystemIterator::SKIP_DOTS);

        $currentDepth = 0;
        foreach ($iterator as $file) {
            /** @var \SplFileInfo $file */
            if ($file->isDir()) {
                continue;
            }

            if ($recursive && $maxDepth > 0 && $currentDepth >= $maxDepth) {
                $currentDepth++;
            }
            $extension = strtolower($file->getExtension());

            if (in_array($extension, $normalizedExtensions)) {
                $results[] = $file->getPathname();
            }

            $currentDepth++;
        }

        return $results;
    }

    public static function deleteDir(string $directory, bool $preserve = false): bool
    {
        if (! static::isDir($directory)) {
            return false;
        }

        $items = new \FilesystemIterator($directory);

        /** @var \SplFileInfo $item */
        foreach ($items as $item) {
            if ($item->isDir() && ! $item->isLink()) {
                static::deleteDir($item->getPathname());
            } else {
                static::delete($item->getPathname());
            }
        }

        unset($items);

        if (! $preserve) {
            @rmdir($directory);
        }

        return true;
    }


    /**
     * Delete the file at a given path.
     *
     * @param  string|array<string>  $paths
     * @return bool
     */
    public static function delete($paths): bool
    {
        $paths = is_array($paths) ? $paths : [$paths];

        $success = true;

        foreach ($paths as $path) {
            try {
                if (@unlink((string) $path)) {
                    clearstatcache(false, $path);
                } else {
                    $success = false;
                }
            } catch (\Exception $e) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Write the contents of a file.
     * @param  string  $path
     * @param  string  $contents
     * @param  bool  $lock
     * @return int|bool
     */
    public static function put($path, $contents, $lock = false)
    {
        return file_put_contents($path, $contents, $lock ? LOCK_EX : 0);
    }

    /**
     * Append to a file.
     *
     * @param  string  $path
     * @param  string  $data
     * @param  bool  $lock
     * @return int
     */
    public static function append($path, $data, $lock = false)
    {
        return (int) file_put_contents($path, $data, FILE_APPEND | ($lock ? LOCK_EX : 0));
    }
}
