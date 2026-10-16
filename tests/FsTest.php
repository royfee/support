<?php

declare(strict_types=1);

namespace Royfee\Support\Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use Royfee\Support\Fs;

class FsTest extends TestCase
{
    /** @var string */
    private $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'royfee_support_test_' . uniqid();
        mkdir($this->tempDir, 0755, true);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            Fs::deleteDir($this->tempDir);
        }
    }

    public function testExists(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';

        self::assertFalse(Fs::exists($file));

        file_put_contents($file, 'hello');

        self::assertTrue(Fs::exists($file));
    }

    public function testGet(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';
        file_put_contents($file, 'hello world');

        self::assertSame('hello world', Fs::get($file));
    }

    public function testGetThrowsExceptionForNonexistentFile(): void
    {
        $this->expectException(Exception::class);

        Fs::get($this->tempDir . DIRECTORY_SEPARATOR . 'nonexistent.txt');
    }

    public function testGetWithLock(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';
        file_put_contents($file, 'locked content');

        self::assertSame('locked content', Fs::get($file, true));
    }

    public function testIsFile(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';
        file_put_contents($file, 'hello');

        self::assertTrue(Fs::isFile($file));
        self::assertFalse(Fs::isFile($this->tempDir));
    }

    public function testPut(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';

        Fs::put($file, 'hello world');

        self::assertSame('hello world', file_get_contents($file));
    }

    public function testPutWithLock(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';

        Fs::put($file, 'locked content', true);

        self::assertSame('locked content', file_get_contents($file));
    }

    public function testAppend(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';
        file_put_contents($file, 'hello');

        Fs::append($file, ' world');

        self::assertSame('hello world', file_get_contents($file));
    }

    public function testDelete(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';
        file_put_contents($file, 'hello');

        self::assertTrue(Fs::delete($file));
        self::assertFileDoesNotExist($file);
    }

    public function testDeleteMultipleFiles(): void
    {
        $file1 = $this->tempDir . DIRECTORY_SEPARATOR . 'test1.txt';
        $file2 = $this->tempDir . DIRECTORY_SEPARATOR . 'test2.txt';
        file_put_contents($file1, 'hello');
        file_put_contents($file2, 'world');

        self::assertTrue(Fs::delete([$file1, $file2]));
        self::assertFileDoesNotExist($file1);
        self::assertFileDoesNotExist($file2);
    }

    public function testMakeDir(): void
    {
        $dir = $this->tempDir . DIRECTORY_SEPARATOR . 'new_dir';

        self::assertTrue(Fs::makeDir($dir));
        self::assertTrue(is_dir($dir));
    }

    public function testMakeDirRecursive(): void
    {
        $dir = $this->tempDir . DIRECTORY_SEPARATOR . 'a' . DIRECTORY_SEPARATOR . 'b' . DIRECTORY_SEPARATOR . 'c';

        self::assertTrue(Fs::makeDir($dir, 0755, true));
        self::assertTrue(is_dir($dir));
    }

    public function testMakeDirAlreadyExists(): void
    {
        self::assertTrue(Fs::makeDir($this->tempDir));
    }

    public function testIsDir(): void
    {
        self::assertTrue(Fs::isDir($this->tempDir));
        self::assertFalse(Fs::isDir($this->tempDir . DIRECTORY_SEPARATOR . 'nonexistent'));
    }

    public function testMove(): void
    {
        $source = $this->tempDir . DIRECTORY_SEPARATOR . 'source.txt';
        $dest = $this->tempDir . DIRECTORY_SEPARATOR . 'dest.txt';
        file_put_contents($source, 'hello');

        self::assertTrue(Fs::move($source, $dest));
        self::assertFileDoesNotExist($source);
        self::assertSame('hello', file_get_contents($dest));
    }

    public function testMoveCreatesTargetDirectory(): void
    {
        $source = $this->tempDir . DIRECTORY_SEPARATOR . 'source.txt';
        $dest = $this->tempDir . DIRECTORY_SEPARATOR . 'sub' . DIRECTORY_SEPARATOR . 'dest.txt';
        file_put_contents($source, 'hello');

        self::assertTrue(Fs::move($source, $dest));
        self::assertSame('hello', file_get_contents($dest));
    }

    public function testMoveNonexistentFile(): void
    {
        $dest = $this->tempDir . DIRECTORY_SEPARATOR . 'dest.txt';

        self::assertFalse(Fs::move($this->tempDir . DIRECTORY_SEPARATOR . 'nonexistent.txt', $dest));
    }

    public function testMoveDir(): void
    {
        $sourceDir = $this->tempDir . DIRECTORY_SEPARATOR . 'source_dir';
        $destDir = $this->tempDir . DIRECTORY_SEPARATOR . 'dest_dir';
        mkdir($sourceDir);
        file_put_contents($sourceDir . DIRECTORY_SEPARATOR . 'file.txt', 'hello');

        self::assertTrue(Fs::moveDir($sourceDir, $destDir));
        self::assertTrue(is_dir($destDir));
        self::assertSame('hello', file_get_contents($destDir . DIRECTORY_SEPARATOR . 'file.txt'));
    }

    public function testDeleteDir(): void
    {
        $dir = $this->tempDir . DIRECTORY_SEPARATOR . 'to_delete';
        mkdir($dir);
        file_put_contents($dir . DIRECTORY_SEPARATOR . 'file.txt', 'hello');
        mkdir($dir . DIRECTORY_SEPARATOR . 'subdir');
        file_put_contents($dir . DIRECTORY_SEPARATOR . 'subdir' . DIRECTORY_SEPARATOR . 'file2.txt', 'world');

        self::assertTrue(Fs::deleteDir($dir));
        self::assertDirectoryDoesNotExist($dir);
    }

    public function testDeleteDirNonexistent(): void
    {
        self::assertFalse(Fs::deleteDir($this->tempDir . DIRECTORY_SEPARATOR . 'nonexistent'));
    }

    public function testAllFiles(): void
    {
        file_put_contents($this->tempDir . DIRECTORY_SEPARATOR . 'file1.php', '<?php');
        file_put_contents($this->tempDir . DIRECTORY_SEPARATOR . 'file2.txt', 'hello');
        mkdir($this->tempDir . DIRECTORY_SEPARATOR . 'sub');
        file_put_contents($this->tempDir . DIRECTORY_SEPARATOR . 'sub' . DIRECTORY_SEPARATOR . 'file3.php', '<?php');

        $phpFiles = Fs::allFiles($this->tempDir, 'php');
        self::assertCount(2, $phpFiles);

        $allFiles = Fs::allFiles($this->tempDir, ['php', 'txt']);
        self::assertCount(3, $allFiles);
    }

    public function testAllFilesNonRecursive(): void
    {
        file_put_contents($this->tempDir . DIRECTORY_SEPARATOR . 'file1.php', '<?php');
        mkdir($this->tempDir . DIRECTORY_SEPARATOR . 'sub');
        file_put_contents($this->tempDir . DIRECTORY_SEPARATOR . 'sub' . DIRECTORY_SEPARATOR . 'file2.php', '<?php');

        $files = Fs::allFiles($this->tempDir, 'php', false);
        self::assertCount(1, $files);
    }

    public function testAllFilesThrowsExceptionForInvalidDirectory(): void
    {
        $this->expectException(Exception::class);

        Fs::allFiles($this->tempDir . DIRECTORY_SEPARATOR . 'nonexistent', 'php');
    }

    public function testSharedGet(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';
        file_put_contents($file, 'shared content');

        self::assertSame('shared content', Fs::sharedGet($file));
    }

    public function testPrepend(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';
        file_put_contents($file, 'world');

        Fs::prepend($file, 'hello ');

        self::assertSame('hello world', file_get_contents($file));
    }

    public function testMissing(): void
    {
        self::assertTrue(Fs::missing($this->tempDir . DIRECTORY_SEPARATOR . 'nonexistent'));
        self::assertFalse(Fs::missing($this->tempDir));
    }

    public function testCopy(): void
    {
        $source = $this->tempDir . DIRECTORY_SEPARATOR . 'source.txt';
        $dest = $this->tempDir . DIRECTORY_SEPARATOR . 'dest.txt';
        file_put_contents($source, 'hello');

        self::assertTrue(Fs::copy($source, $dest));
        self::assertSame('hello', file_get_contents($dest));
        self::assertFileExists($source); // original still exists
    }

    public function testName(): void
    {
        self::assertSame('test', Fs::name('/path/to/test.txt'));
        self::assertSame('file', Fs::name('/path/to/file.php'));
    }

    public function testBasename(): void
    {
        self::assertSame('test.txt', Fs::basename('/path/to/test.txt'));
    }

    public function testExtension(): void
    {
        self::assertSame('txt', Fs::extension('/path/to/test.txt'));
        self::assertSame('php', Fs::extension('/path/to/file.php'));
    }

    public function testDirname(): void
    {
        self::assertSame('/path/to', Fs::dirname('/path/to/test.txt'));
    }

    public function testSize(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';
        file_put_contents($file, 'hello');

        self::assertSame(5, Fs::size($file));
    }

    public function testLastModified(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';
        file_put_contents($file, 'hello');

        self::assertIsInt(Fs::lastModified($file));
        self::assertGreaterThan(0, Fs::lastModified($file));
    }

    public function testHash(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';
        file_put_contents($file, 'hello');

        self::assertSame(md5('hello'), Fs::hash($file));
        self::assertSame(sha1('hello'), Fs::hash($file, 'sha1'));
    }

    public function testMimeType(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';
        file_put_contents($file, 'hello');

        self::assertSame('text/plain', Fs::mimeType($file));
    }

    public function testIsReadable(): void
    {
        self::assertTrue(Fs::isReadable($this->tempDir));
    }

    public function testIsWritable(): void
    {
        self::assertTrue(Fs::isWritable($this->tempDir));
    }

    public function testIsEmptyDirectory(): void
    {
        $emptyDir = $this->tempDir . DIRECTORY_SEPARATOR . 'empty';
        mkdir($emptyDir);

        self::assertTrue(Fs::isEmptyDirectory($emptyDir));

        file_put_contents($emptyDir . DIRECTORY_SEPARATOR . 'file.txt', 'hello');

        self::assertFalse(Fs::isEmptyDirectory($emptyDir));
    }

    public function testEnsureDirectoryExists(): void
    {
        $dir = $this->tempDir . DIRECTORY_SEPARATOR . 'new' . DIRECTORY_SEPARATOR . 'deep';

        Fs::ensureDirectoryExists($dir);

        self::assertTrue(is_dir($dir));
    }

    public function testReplace(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';
        file_put_contents($file, 'old content');

        Fs::replace($file, 'new content');

        self::assertSame('new content', file_get_contents($file));
    }

    public function testReplaceInFile(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.txt';
        file_put_contents($file, 'hello world');

        Fs::replaceInFile('world', 'earth', $file);

        self::assertSame('hello earth', file_get_contents($file));
    }

    public function testCopyDirectory(): void
    {
        $sourceDir = $this->tempDir . DIRECTORY_SEPARATOR . 'source';
        $destDir = $this->tempDir . DIRECTORY_SEPARATOR . 'dest';
        mkdir($sourceDir);
        file_put_contents($sourceDir . DIRECTORY_SEPARATOR . 'file.txt', 'hello');
        mkdir($sourceDir . DIRECTORY_SEPARATOR . 'sub');
        file_put_contents($sourceDir . DIRECTORY_SEPARATOR . 'sub' . DIRECTORY_SEPARATOR . 'file2.txt', 'world');

        self::assertTrue(Fs::copyDirectory($sourceDir, $destDir));
        self::assertSame('hello', file_get_contents($destDir . DIRECTORY_SEPARATOR . 'file.txt'));
        self::assertSame('world', file_get_contents($destDir . DIRECTORY_SEPARATOR . 'sub' . DIRECTORY_SEPARATOR . 'file2.txt'));
    }

    public function testCleanDirectory(): void
    {
        $dir = $this->tempDir . DIRECTORY_SEPARATOR . 'to_clean';
        mkdir($dir);
        file_put_contents($dir . DIRECTORY_SEPARATOR . 'file.txt', 'hello');

        self::assertTrue(Fs::cleanDirectory($dir));
        self::assertTrue(is_dir($dir));
        self::assertTrue(Fs::isEmptyDirectory($dir));
    }

    public function testJson(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR . 'test.json';
        file_put_contents($file, '{"name":"John","age":30}');

        $result = Fs::json($file);

        self::assertSame(['name' => 'John', 'age' => 30], $result);
    }

    public function testJsonThrowsExceptionForInvalidJson(): void
    {
        $file = $this->tempDir . DIRECTORY_SEPARATOR . 'invalid.json';
        file_put_contents($file, '{invalid}');

        $this->expectException(Exception::class);

        Fs::json($file);
    }

    public function testFiles(): void
    {
        file_put_contents($this->tempDir . DIRECTORY_SEPARATOR . 'a.txt', 'a');
        file_put_contents($this->tempDir . DIRECTORY_SEPARATOR . 'b.txt', 'b');
        mkdir($this->tempDir . DIRECTORY_SEPARATOR . 'subdir');

        $files = Fs::files($this->tempDir);

        self::assertCount(2, $files);
        // Should not include directories
        foreach ($files as $file) {
            self::assertTrue(is_file($file));
        }
    }

    public function testFilesReturnsEmptyForNonExistentDirectory(): void
    {
        self::assertSame([], Fs::files($this->tempDir . DIRECTORY_SEPARATOR . 'nonexistent'));
    }

    public function testFilesWithExtensionFilter(): void
    {
        file_put_contents($this->tempDir . DIRECTORY_SEPARATOR . 'a.php', '<?php');
        file_put_contents($this->tempDir . DIRECTORY_SEPARATOR . 'b.txt', 'text');
        file_put_contents($this->tempDir . DIRECTORY_SEPARATOR . 'c.php', '<?php');

        $phpFiles = Fs::files($this->tempDir, 'php');
        self::assertCount(2, $phpFiles);

        $multiExt = Fs::files($this->tempDir, ['php', 'txt']);
        self::assertCount(3, $multiExt);
    }

    public function testDirectories(): void
    {
        mkdir($this->tempDir . DIRECTORY_SEPARATOR . 'dir1');
        mkdir($this->tempDir . DIRECTORY_SEPARATOR . 'dir2');
        file_put_contents($this->tempDir . DIRECTORY_SEPARATOR . 'file.txt', 'content');

        $dirs = Fs::directories($this->tempDir);

        self::assertCount(2, $dirs);
        // Should not include files
        foreach ($dirs as $dir) {
            self::assertTrue(is_dir($dir));
        }
    }

    public function testDirectoriesReturnsEmptyForNonExistentDirectory(): void
    {
        self::assertSame([], Fs::directories($this->tempDir . DIRECTORY_SEPARATOR . 'nonexistent'));
    }

    public function testDirectoriesExcludesHiddenByDefault(): void
    {
        mkdir($this->tempDir . DIRECTORY_SEPARATOR . 'visible');
        mkdir($this->tempDir . DIRECTORY_SEPARATOR . '.hidden');

        $dirs = Fs::directories($this->tempDir);
        self::assertCount(1, $dirs);

        $allDirs = Fs::directories($this->tempDir, false);
        self::assertCount(2, $allDirs);
    }
}
