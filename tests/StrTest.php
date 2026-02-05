<?php

namespace royfee\support\tests;

use PHPUnit\Framework\TestCase;
use Royfee\Support\Str;

class StrTest extends TestCase
{
    public function testTitle()
    {
        self::assertSame('Helloworld', Str::title('HelloWorld'));
        self::assertSame('Hello_World', Str::title('hello_world'));
        self::assertSame('Hello-World', Str::title('hello-world'));
        self::assertSame('Hello World', Str::title('hello world'));
    }

    public function testFinish()
    {
        self::assertSame('Hello!', Str::finish('Hello', '!'));
        self::assertSame('Hello!', Str::finish('Hello!', '!'));
        self::assertSame('Hello!', Str::finish('Hello!!!', '!'));
        self::assertSame('World/', Str::finish('World/', '/'));
        self::assertSame('World/', Str::finish('World//', '/'));
        self::assertSame('app/app', Str::finish('app', '/app'));
        self::assertSame('app/app', Str::finish('app/app', '/app'));
    }
}
