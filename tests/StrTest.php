<?php

declare(strict_types=1);

namespace Royfee\Support\Tests;

use PHPUnit\Framework\TestCase;
use Royfee\Support\Str;

class StrTest extends TestCase
{
    public function testTitle(): void
    {
        self::assertSame('HelloWorld', Str::title('HelloWorld'));
        self::assertSame('Hello_World', Str::title('hello_world'));
        self::assertSame('Hello-World', Str::title('hello-world'));
        self::assertSame('Hello World', Str::title('hello world'));
    }

    public function testFinish(): void
    {
        self::assertSame('Hello!', Str::finish('Hello', '!'));
        self::assertSame('Hello!', Str::finish('Hello!', '!'));
        self::assertSame('Hello!', Str::finish('Hello!!!', '!'));
        self::assertSame('World/', Str::finish('World/', '/'));
        self::assertSame('World/', Str::finish('World//', '/'));
        self::assertSame('app/app', Str::finish('app', '/app'));
        self::assertSame('app/app', Str::finish('app/app', '/app'));
    }

    public function testStartsWith(): void
    {
        self::assertTrue(Str::startsWith('hello world', 'hello'));
        self::assertFalse(Str::startsWith('hello world', 'world'));
        self::assertTrue(Str::startsWith('hello world', ['hello', 'world']));
        self::assertFalse(Str::startsWith('hello world', ''));
    }

    public function testEndsWith(): void
    {
        self::assertTrue(Str::endsWith('hello world', 'world'));
        self::assertFalse(Str::endsWith('hello world', 'hello'));
        self::assertTrue(Str::endsWith('hello world', ['hello', 'world']));
        self::assertFalse(Str::endsWith('hello world', ''));
    }

    public function testSnake(): void
    {
        self::assertSame('hello_world', Str::snake('HelloWorld'));
        self::assertSame('hello_world', Str::snake('helloWorld'));
        self::assertSame('hello_world', Str::snake('hello world'));
        self::assertSame('hello-world', Str::snake('HelloWorld', '-'));
    }

    public function testCamel(): void
    {
        self::assertSame('helloWorld', Str::camel('hello_world'));
        self::assertSame('helloWorld', Str::camel('hello-world'));
        self::assertSame('helloWorld', Str::camel('hello world'));
    }

    public function testStudly(): void
    {
        self::assertSame('HelloWorld', Str::studly('hello_world'));
        self::assertSame('HelloWorld', Str::studly('hello-world'));
        self::assertSame('HelloWorld', Str::studly('hello world'));
    }

    public function testKebab(): void
    {
        self::assertSame('hello-world', Str::kebab('HelloWorld'));
        self::assertSame('hello-world', Str::kebab('helloWorld'));
    }

    public function testSlug(): void
    {
        self::assertSame('hello-world', Str::slug('Hello World'));
        self::assertSame('hello_world', Str::slug('Hello World', '_'));
        self::assertSame('hello-world', Str::slug('Hello  World'));
        // Unicode transliteration
        self::assertSame('hello-world', Str::slug('Héllo Wörld'));
        self::assertSame('cafe', Str::slug('Café'));
    }

    public function testReplaceFirst(): void
    {
        self::assertSame('hello universe world', Str::replaceFirst('world', 'universe', 'hello world world'));
        self::assertSame('hello world', Str::replaceFirst('foo', 'bar', 'hello world'));
    }

    public function testReplaceLast(): void
    {
        self::assertSame('hello world universe', Str::replaceLast('world', 'universe', 'hello world world'));
        self::assertSame('hello world', Str::replaceLast('foo', 'bar', 'hello world'));
    }

    public function testUpper(): void
    {
        self::assertSame('HELLO', Str::upper('hello'));
        self::assertSame('HELLO WORLD', Str::upper('hello world'));
    }

    public function testLower(): void
    {
        self::assertSame('hello', Str::lower('HELLO'));
        self::assertSame('hello world', Str::lower('HELLO WORLD'));
    }

    public function testLimit(): void
    {
        self::assertSame('Hello...', Str::limit('Hello World', 5));
        self::assertSame('Hello World', Str::limit('Hello World', 20));
        self::assertSame('Hello---', Str::limit('Hello World', 5, '---'));
    }

    public function testWords(): void
    {
        self::assertSame('Hello World...', Str::words('Hello World Foo Bar', 2));
        self::assertSame('Hello World Foo Bar', Str::words('Hello World Foo Bar', 10));
    }

    public function testIs(): void
    {
        self::assertTrue(Str::is('hello', 'hello'));
        self::assertTrue(Str::is('hello*', 'hello world'));
        self::assertTrue(Str::is('*world', 'hello world'));
        self::assertFalse(Str::is('hello', 'world'));
        self::assertTrue(Str::is(['hello*', 'world*'], 'hello world'));
    }

    public function testBefore(): void
    {
        self::assertSame('hello', Str::before('hello world', ' '));
        self::assertSame('hello world', Str::before('hello world', ''));
        self::assertSame('hello world', Str::before('hello world', 'foo'));
    }

    public function testAfter(): void
    {
        self::assertSame('world', Str::after('hello world', 'hello '));
        self::assertSame('hello world', Str::after('hello world', ''));
        self::assertSame('hello world', Str::after('hello world', 'foo'));
    }

    public function testBetween(): void
    {
        self::assertSame('world', Str::between('hello [world] foo', '[', ']'));
        self::assertSame('bar', Str::between('foo-bar-baz', '-', '-'));
    }

    public function testIsEmpty(): void
    {
        self::assertTrue(Str::isEmpty(''));
        self::assertTrue(Str::isEmpty('   '));
        self::assertFalse(Str::isEmpty('hello'));
    }

    public function testIsNotEmpty(): void
    {
        self::assertFalse(Str::isNotEmpty(''));
        self::assertTrue(Str::isNotEmpty('hello'));
    }

    public function testSubstr(): void
    {
        self::assertSame('llo', Str::substr('hello', 2));
        self::assertSame('ll', Str::substr('hello', 2, 2));
    }

    public function testContains(): void
    {
        self::assertTrue(Str::contains('hello world', 'world'));
        self::assertFalse(Str::contains('hello world', 'foo'));
        self::assertTrue(Str::contains('hello world', ['foo', 'world']));
    }

    public function testLength(): void
    {
        self::assertSame(5, Str::length('hello'));
        self::assertSame(11, Str::length('hello world'));
    }

    public function testRandom(): void
    {
        self::assertSame(16, strlen(Str::random()));
        self::assertSame(32, strlen(Str::random(32)));
        self::assertNotSame(Str::random(), Str::random());
    }

    public function testCharAt(): void
    {
        self::assertSame('h', Str::charAt('hello', 0));
        self::assertSame('o', Str::charAt('hello', 4));
        self::assertFalse(Str::charAt('hello', 10));
    }

    public function testAfterLast(): void
    {
        self::assertSame('world', Str::afterLast('hello beautiful world', ' '));
        self::assertSame('hello world', Str::afterLast('hello world', ''));
        self::assertSame('php', Str::afterLast('laravel php', ' '));
    }

    public function testBeforeLast(): void
    {
        self::assertSame('hello beautiful', Str::beforeLast('hello beautiful world', ' '));
        self::assertSame('hello world', Str::beforeLast('hello world', ''));
    }

    public function testContainsAll(): void
    {
        self::assertTrue(Str::containsAll('hello world', ['hello', 'world']));
        self::assertFalse(Str::containsAll('hello world', ['hello', 'foo']));
        self::assertTrue(Str::containsAll('hello world', []));
    }

    public function testDoesntContain(): void
    {
        self::assertTrue(Str::doesntContain('hello world', 'foo'));
        self::assertFalse(Str::doesntContain('hello world', 'world'));
    }

    public function testSquish(): void
    {
        self::assertSame('hello world', Str::squish('  hello   world  '));
        self::assertSame('hello world', Str::squish("hello\n\n  world"));
    }

    public function testReverse(): void
    {
        self::assertSame('olleh', Str::reverse('hello'));
        self::assertSame('cBa', Str::reverse('aBc'));
    }

    public function testPadLeft(): void
    {
        self::assertSame('  hi', Str::padLeft('hi', 4));
        self::assertSame('00hi', Str::padLeft('hi', 4, '0'));
    }

    public function testPadRight(): void
    {
        self::assertSame('hi  ', Str::padRight('hi', 4));
        self::assertSame('hi00', Str::padRight('hi', 4, '0'));
    }

    public function testPadBoth(): void
    {
        self::assertSame(' hi ', Str::padBoth('hi', 4));
        self::assertSame('0hi0', Str::padBoth('hi', 4, '0'));
    }

    public function testWrap(): void
    {
        self::assertSame('"hello"', Str::wrap('hello', '"'));
        self::assertSame('<p>hello</p>', Str::wrap('hello', '<p>', '</p>'));
    }

    public function testUnwrap(): void
    {
        self::assertSame('hello', Str::unwrap('"hello"', '"'));
        self::assertSame('hello', Str::unwrap('<p>hello</p>', '<p>', '</p>'));
    }

    public function testMask(): void
    {
        self::assertSame('138****1234', Str::mask('13812341234', '*', 3, 4));
        self::assertSame('***********', Str::mask('13812341234', '*'));
        self::assertSame('13812341234', Str::mask('13812341234', '', 3, 4));
    }

    public function testIsJson(): void
    {
        self::assertTrue(Str::isJson('{"name":"John"}'));
        self::assertTrue(Str::isJson('[1,2,3]'));
        self::assertFalse(Str::isJson(''));
        self::assertFalse(Str::isJson('{invalid}'));
        self::assertFalse(Str::isJson('hello'));
    }

    public function testIsUrl(): void
    {
        self::assertTrue(Str::isUrl('https://example.com'));
        self::assertTrue(Str::isUrl('http://example.com/path?q=1'));
        self::assertFalse(Str::isUrl('not-a-url'));
        self::assertFalse(Str::isUrl(''));
    }

    public function testRemove(): void
    {
        self::assertSame('heo world', Str::remove('ll', 'hello world'));
        self::assertSame('h', Str::remove(['e', 'l', 'o'], 'hello'));
    }

    public function testReplace(): void
    {
        self::assertSame('hi world', Str::replace('hello', 'hi', 'hello world'));
        self::assertSame('hXllX', Str::replace(['e', 'o'], 'X', 'hello'));
    }

    public function testMatch(): void
    {
        self::assertSame('world', Str::match('/hello (\w+)/', 'hello world'));
        self::assertSame('', Str::match('/foo (\w+)/', 'hello world'));
    }

    public function testUcfirst(): void
    {
        self::assertSame('Hello', Str::ucfirst('hello'));
        self::assertSame('Hello world', Str::ucfirst('hello world'));
    }

    public function testLcfirst(): void
    {
        self::assertSame('hELLO', Str::lcfirst('HELLO'));
        self::assertSame('hello World', Str::lcfirst('Hello World'));
    }

    public function testSubstrCount(): void
    {
        self::assertSame(3, Str::substrCount('hello hello hello', 'hello'));
        self::assertSame(0, Str::substrCount('hello', 'foo'));
    }

    public function testToBase64(): void
    {
        self::assertSame('aGVsbG8=', Str::toBase64('hello'));
    }

    public function testFromBase64(): void
    {
        self::assertSame('hello', Str::fromBase64('aGVsbG8='));
    }

    public function testAscii(): void
    {
        self::assertSame('u', Str::ascii('ü'));
        self::assertSame('a', Str::ascii('à'));
        self::assertSame('n', Str::ascii('ñ'));
        self::assertSame('AE', Str::ascii('Æ'));
        self::assertSame('ss', Str::ascii('ß'));
        self::assertSame('hello', Str::ascii('hello'));
    }

    public function testHeadline(): void
    {
        self::assertSame('Foo Bar', Str::headline('fooBar'));
        self::assertSame('Foo Bar', Str::headline('foo_bar'));
        self::assertSame('Foo Bar', Str::headline('foo-bar'));
        self::assertSame('Laravel Php Framework', Str::headline('laravel_php_framework'));
    }

    // ─── Chinese / Multibyte Tests ───────────────────────────────

    public function testChineseLength(): void
    {
        self::assertSame(3, Str::length('你好啊'));
        self::assertSame(5, Str::length('hello'));
        self::assertSame(7, Str::length('hello你好'));
    }

    public function testChineseSubstr(): void
    {
        self::assertSame('你好', Str::substr('你好世界', 0, 2));
        self::assertSame('世界', Str::substr('你好世界', 2));
        self::assertSame('好', Str::substr('你好世界', 1, 1));
    }

    public function testChineseCharAt(): void
    {
        self::assertSame('你', Str::charAt('你好世界', 0));
        self::assertSame('界', Str::charAt('你好世界', 3));
        self::assertFalse(Str::charAt('你好', 5));
    }

    public function testChineseReverse(): void
    {
        self::assertSame('界世好你', Str::reverse('你好世界'));
        self::assertSame('c好b', Str::reverse('b好c'));
    }

    public function testChineseUpperLower(): void
    {
        // Chinese characters should remain unchanged
        self::assertSame('你好世界', Str::upper('你好世界'));
        self::assertSame('你好世界', Str::lower('你好世界'));
        // Mixed
        self::assertSame('你好WORLD', Str::upper('你好world'));
        self::assertSame('你好world', Str::lower('你好WORLD'));
    }

    public function testChineseUcfirstLcfirst(): void
    {
        // Chinese first character has no case, rest preserved
        self::assertSame('你好世界', Str::ucfirst('你好世界'));
        self::assertSame('你好世界', Str::lcfirst('你好世界'));
        // Mixed: english + chinese
        self::assertSame('Hello世界', Str::ucfirst('hello世界'));
        self::assertSame('hELLO世界', Str::lcfirst('HELLO世界'));
    }

    public function testChineseTrim(): void
    {
        // Default whitespace trimming
        self::assertSame('你好', Str::trim('  你好  '));
        self::assertSame('你好', Str::trim("\n\t你好\r\n"));
        // Custom character trimming with Chinese
        self::assertSame('你好', Str::trim('啊啊你好啊', '啊'));
        self::assertSame('你好啊', Str::ltrim('啊啊你好啊', '啊'));
        self::assertSame('啊啊你好', Str::rtrim('啊啊你好啊', '啊'));
        // Unicode whitespace (non-breaking space \xC2\xA0)
        self::assertSame('你好', Str::trim("\xC2\xA0你好\xC2\xA0"));
        // Ideographic space (\xE3\x80\x80 = U+3000)
        self::assertSame('你好', Str::trim("\xE3\x80\x80你好\xE3\x80\x80"));
    }

    public function testChinesePad(): void
    {
        // Chinese characters with padding
        self::assertSame('  你好', Str::padLeft('你好', 4));
        self::assertSame('你好  ', Str::padRight('你好', 4));
        self::assertSame(' 你好 ', Str::padBoth('你好', 4));
        // Multi-char pad string
        self::assertSame('充充你好', Str::padLeft('你好', 4, '充'));
        self::assertSame('你好充充', Str::padRight('你好', 4, '充'));
        // Multi-char pad that needs truncation
        self::assertSame('充值你', Str::padLeft('你', 3, '充值'));
        self::assertSame('你充值', Str::padRight('你', 3, '充值'));
        // padBoth with multi-char pad (both sides use start of pad string)
        self::assertSame('充你充', Str::padBoth('你', 3, '充值'));
        // Mixed content
        self::assertSame('--hello你好', Str::padLeft('hello你好', 9, '-'));
    }

    public function testChineseContains(): void
    {
        self::assertTrue(Str::contains('你好世界', '世界'));
        self::assertTrue(Str::contains('你好世界', '你好'));
        self::assertFalse(Str::contains('你好世界', '世好'));
        self::assertTrue(Str::contains('你好世界', ['foo', '世界']));
    }

    public function testChineseStartsEndsWith(): void
    {
        self::assertTrue(Str::startsWith('你好世界', '你好'));
        self::assertFalse(Str::startsWith('你好世界', '世界'));
        self::assertTrue(Str::endsWith('你好世界', '世界'));
        self::assertFalse(Str::endsWith('你好世界', '你好'));
    }

    public function testChineseBeforeAfter(): void
    {
        self::assertSame('你好', Str::before('你好-世界', '-'));
        self::assertSame('世界', Str::after('你好-世界', '-'));
        self::assertSame('世界', Str::between('你好[世界]测试', '[', ']'));
        self::assertSame('测试', Str::afterLast('你好-世界-测试', '-'));
        self::assertSame('你好-世界', Str::beforeLast('你好-世界-测试', '-'));
    }

    public function testChineseReplaceRemove(): void
    {
        self::assertSame('你好地球', Str::replace('世界', '地球', '你好世界'));
        self::assertSame('你好', Str::remove('世界', '你好世界'));
        self::assertSame('你好地球世界', Str::replaceFirst('世界', '地球', '你好世界世界'));
        self::assertSame('你好世界地球', Str::replaceLast('世界', '地球', '你好世界世界'));
    }

    public function testChineseLimit(): void
    {
        self::assertSame('你好...', Str::limit('你好世界啊', 2));
        self::assertSame('你好世界', Str::limit('你好世界', 10));
        self::assertSame('你好世', Str::limit('你好世界啊', 3, ''));
    }

    public function testChineseMask(): void
    {
        self::assertSame('你**界', Str::mask('你好世界', '*', 1, 2));
        self::assertSame('****', Str::mask('你好世界', '*'));
    }

    public function testChineseSquish(): void
    {
        self::assertSame('你好 世界', Str::squish('  你好   世界  '));
        self::assertSame('你好 世界', Str::squish("你好\n\n  世界"));
    }

    public function testChineseIs(): void
    {
        self::assertTrue(Str::is('你好*', '你好世界'));
        self::assertTrue(Str::is('*世界', '你好世界'));
        self::assertTrue(Str::is('*好*', '你好世界'));
        self::assertFalse(Str::is('你好', '你好世界'));
    }

    public function testChineseWords(): void
    {
        // Chinese text doesn't use spaces between words, so the whole string is "one word"
        self::assertSame('你好世界', Str::words('你好世界', 1));
        // Mixed Chinese and English
        self::assertSame('hello 你好...', Str::words('hello 你好 世界 world', 2));
    }

    public function testChineseWrapUnwrap(): void
    {
        self::assertSame('「你好」', Str::wrap('你好', '「', '」'));
        self::assertSame('你好', Str::unwrap('「你好」', '「', '」'));
    }

    public function testChineseSubstrCount(): void
    {
        self::assertSame(2, Str::substrCount('你好世界你好', '你好'));
        self::assertSame(0, Str::substrCount('你好世界', '世界好'));
    }

    public function testChineseSlug(): void
    {
        // Chinese characters are not in the ASCII map, so they get stripped
        self::assertSame('hello', Str::slug('hello 你好'));
        // Mixed with transliteratable chars
        self::assertSame('hello-world', Str::slug('Hello 你好 World'));
    }

    public function testChineseAscii(): void
    {
        // Chinese characters have no ASCII equivalent, they get removed
        self::assertSame('hello', Str::ascii('hello你好'));
        self::assertSame('', Str::ascii('你好'));
        // Mixed with transliteratable chars
        self::assertSame('hello', Str::ascii('héllo你好'));
    }
}
