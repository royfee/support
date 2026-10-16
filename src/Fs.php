<?php

declare(strict_types=1);

namespace Royfee\Support;

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
    public static function get(string $path, bool $lock = false): string
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
    public static function sharedGet(string $path): string
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
    public static function makeDir(string $path, int $mode = 0755, bool $recursive = false): bool
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
    public static function moveDir(string $from, string $to, bool $overwrite = false): bool
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
    public static function put(string $path, string $contents, bool $lock = false)
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
    public static function append(string $path, string $data, bool $lock = false): int
    {
        return (int) file_put_contents($path, $data, FILE_APPEND | ($lock ? LOCK_EX : 0));
    }

    /**
     * Prepend to a file.
     *
     * @param  string  $path
     * @param  string  $data
     * @return int
     */
    public static function prepend(string $path, string $data): int
    {
        if (static::exists($path)) {
            return (int) static::put($path, $data . static::get($path));
        }

        return (int) static::put($path, $data);
    }

    /**
     * Determine if a file or directory is missing.
     *
     * @param  string  $path
     * @return bool
     */
    public static function missing(string $path): bool
    {
        return ! static::exists($path);
    }

    /**
     * Copy a file to a new location.
     *
     * @param  string  $path
     * @param  string  $target
     * @return bool
     */
    public static function copy(string $path, string $target): bool
    {
        return copy($path, $target);
    }

    /**
     * Get the filename without extension from a given path.
     *
     * @param  string  $path
     * @return string
     */
    public static function name(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Get the filename from a given path.
     *
     * @param  string  $path
     * @return string
     */
    public static function basename(string $path): string
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * Get the file extension from a given path.
     *
     * @param  string  $path
     * @return string
     */
    public static function extension(string $path): string
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Get the directory name from a given path.
     *
     * @param  string  $path
     * @return string
     */
    public static function dirname(string $path): string
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * Get the size of a file in bytes.
     *
     * @param  string  $path
     * @return int
     */
    public static function size(string $path): int
    {
        return (int) filesize($path);
    }

    /**
     * Get the file's last modification time.
     *
     * @param  string  $path
     * @return int
     */
    public static function lastModified(string $path): int
    {
        return (int) filemtime($path);
    }

    /**
     * Get the hash of a file.
     *
     * @param  string  $path
     * @param  string  $algorithm
     * @return string
     */
    public static function hash(string $path, string $algorithm = 'md5'): string
    {
        return hash_file($algorithm, $path);
    }

    /**
     * Get the MIME type of a file.
     *
     * @param  string  $path
     * @return string|false
     */
    public static function mimeType(string $path)
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);

        return $finfo->file($path);
    }

    /**
     * Determine if the given path is readable.
     *
     * @param  string  $path
     * @return bool
     */
    public static function isReadable(string $path): bool
    {
        return is_readable($path);
    }

    /**
     * Determine if the given path is writable.
     *
     * @param  string  $path
     * @return bool
     */
    public static function isWritable(string $path): bool
    {
        return is_writable($path);
    }

    /**
     * Determine if the given directory is empty.
     *
     * @param  string  $directory
     * @param  bool  $ignoreDotFiles
     * @return bool
     */
    public static function isEmptyDirectory(string $directory, bool $ignoreDotFiles = false): bool
    {
        if (! static::isDir($directory)) {
            return false;
        }

        $handle = opendir($directory);

        if ($handle === false) {
            return true;
        }

        while (($entry = readdir($handle)) !== false) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            if ($ignoreDotFiles && strpos($entry, '.') === 0) {
                continue;
            }

            closedir($handle);

            return false;
        }

        closedir($handle);

        return true;
    }

    /**
     * Ensure a directory exists.
     *
     * @param  string  $path
     * @param  int  $mode
     * @param  bool  $recursive
     * @return void
     */
    public static function ensureDirectoryExists(string $path, int $mode = 0755, bool $recursive = true): void
    {
        if (! static::isDir($path)) {
            static::makeDir($path, $mode, $recursive);
        }
    }

    /**
     * Write the contents of a file, replacing it atomically if it already exists.
     *
     * @param  string  $path
     * @param  string  $content
     * @return void
     */
    public static function replace(string $path, string $content): void
    {
        $dir = dirname($path);

        if (! static::isDir($dir)) {
            static::makeDir($dir, 0777, true);
        }

        $tempPath = tempnam($dir, 'temp');

        if ($tempPath === false) {
            return;
        }

        chmod($tempPath, 0777);
        static::put($tempPath, $content);
        rename($tempPath, $path);
    }

    /**
     * Replace a given string in a file.
     *
     * @param  string|array<string>  $search
     * @param  string|array<string>  $replace
     * @param  string  $path
     * @return void
     */
    public static function replaceInFile($search, $replace, string $path): void
    {
        static::put($path, str_replace($search, $replace, static::get($path)));
    }

    /**
     * Copy a directory to another location.
     *
     * @param  string  $directory
     * @param  string  $destination
     * @return bool
     */
    public static function copyDirectory(string $directory, string $destination): bool
    {
        if (! static::isDir($directory)) {
            return false;
        }

        static::ensureDirectoryExists($destination);

        $items = new \FilesystemIterator($directory, \FilesystemIterator::SKIP_DOTS);

        foreach ($items as $item) {
            $target = $destination . DIRECTORY_SEPARATOR . $item->getBasename();

            if ($item->isDir() && ! $item->isLink()) {
                if (! static::copyDirectory($item->getPathname(), $target)) {
                    return false;
                }
            } else {
                if (! static::copy($item->getPathname(), $target)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Remove all of the files within a directory.
     *
     * @param  string  $directory
     * @return bool
     */
    public static function cleanDirectory(string $directory): bool
    {
        return static::deleteDir($directory, true);
    }

    /**
     * Read a JSON file and return its contents as an array.
     *
     * @param  string  $path
     * @param  int  $flags
     * @return array<mixed>
     * @throws Exception
     */
    public static function json(string $path, int $flags = 0): array
    {
        $content = static::get($path);
        $decoded = json_decode($content, true, 512, $flags);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Failed to decode JSON from path {$path}: " . json_last_error_msg());
        }

        return (array) $decoded;
    }

    /**
     * Get an array of files within a directory.
     *
     * @param  string  $directory
     * @param  string|array<string>|null  $extensions
     * @param  bool  $sorted
     * @return array<string>
     */
    public static function files(string $directory, $extensions = null, bool $sorted = true): array
    {
        if (! static::isDir($directory)) {
            return [];
        }

        $normalizedExtensions = null;

        if ($extensions !== null) {
            $extensions = is_array($extensions) ? $extensions : explode(',', $extensions);
            $normalizedExtensions = array_map(function ($ext) {
                return strtolower(ltrim($ext, '.'));
            }, $extensions);
        }

        $files = [];

        $items = new \FilesystemIterator($directory, \FilesystemIterator::SKIP_DOTS);

        foreach ($items as $item) {
            if ($item->isFile()) {
                if ($normalizedExtensions !== null) {
                    $ext = strtolower($item->getExtension());
                    if (! in_array($ext, $normalizedExtensions)) {
                        continue;
                    }
                }
                $files[] = $item->getPathname();
            }
        }

        if ($sorted) {
            sort($files);
        }

        return $files;
    }

    /**
     * Get an array of directories within a given directory.
     *
     * @param  string  $directory
     * @param  bool  $excludeHidden
     * @param  bool  $sorted
     * @return array<string>
     */
    public static function directories(string $directory, bool $excludeHidden = true, bool $sorted = true): array
    {
        if (! static::isDir($directory)) {
            return [];
        }

        $directories = [];

        $items = new \FilesystemIterator($directory, \FilesystemIterator::SKIP_DOTS);

        foreach ($items as $item) {
            if ($item->isDir()) {
                if ($excludeHidden && strpos($item->getFilename(), '.') === 0) {
                    continue;
                }
                $directories[] = $item->getPathname();
            }
        }

        if ($sorted) {
            sort($directories);
        }

        return $directories;
    }
}
