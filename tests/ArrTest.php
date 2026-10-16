<?php

declare(strict_types=1);

namespace Royfee\Support\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Royfee\Support\Arr;

class ArrTest extends TestCase
{
    public function testGet(): void
    {
        $array = ['name' => 'John', 'age' => 30, 'address' => ['city' => 'Beijing', 'zip' => '100000']];

        self::assertSame('John', Arr::get($array, 'name'));
        self::assertSame(30, Arr::get($array, 'age'));
        self::assertSame('Beijing', Arr::get($array, 'address.city'));
        self::assertNull(Arr::get($array, 'nonexistent'));
        self::assertSame('default', Arr::get($array, 'nonexistent', 'default'));
    }

    public function testHas(): void
    {
        $array = ['name' => 'John', 'address' => ['city' => 'Beijing']];

        self::assertTrue(Arr::has($array, 'name'));
        self::assertTrue(Arr::has($array, 'address.city'));
        self::assertFalse(Arr::has($array, 'nonexistent'));
        self::assertFalse(Arr::has($array, 'address.zip'));
        self::assertTrue(Arr::has($array, ['name', 'address.city']));
        self::assertFalse(Arr::has($array, ['name', 'nonexistent']));
    }

    public function testSet(): void
    {
        $array = [];

        Arr::set($array, 'name', 'John');
        self::assertSame(['name' => 'John'], $array);

        Arr::set($array, 'address.city', 'Beijing');
        self::assertSame('Beijing', $array['address']['city']);
    }

    public function testForget(): void
    {
        $array = ['name' => 'John', 'age' => 30, 'address' => ['city' => 'Beijing', 'zip' => '100000']];

        Arr::forget($array, 'age');
        self::assertArrayNotHasKey('age', $array);

        Arr::forget($array, 'address.city');
        self::assertArrayNotHasKey('city', $array['address']);
        self::assertArrayHasKey('zip', $array['address']);
    }

    public function testOnly(): void
    {
        $array = ['name' => 'John', 'age' => 30, 'email' => 'john@example.com'];

        self::assertSame(['name' => 'John', 'email' => 'john@example.com'], Arr::only($array, ['name', 'email']));
    }

    public function testExcept(): void
    {
        $array = ['name' => 'John', 'age' => 30, 'email' => 'john@example.com'];

        self::assertSame(['name' => 'John', 'age' => 30], Arr::except($array, ['email']));
    }

    public function testFirst(): void
    {
        $array = [1, 2, 3, 4, 5];

        self::assertSame(1, Arr::first($array));
        self::assertSame(3, Arr::first($array, function ($value) {
            return $value >= 3;
        }));
        self::assertSame('default', Arr::first([], null, 'default'));
    }

    public function testLast(): void
    {
        $array = [1, 2, 3, 4, 5];

        self::assertSame(5, Arr::last($array));
        self::assertSame(3, Arr::last($array, function ($value) {
            return $value <= 3;
        }));
        self::assertSame('default', Arr::last([], null, 'default'));
    }

    public function testFlatten(): void
    {
        self::assertSame([1, 2, 3, 4], Arr::flatten([[1, 2], [3, 4]]));
        self::assertSame([1, 2, 3, 4], Arr::flatten([[1, [2]], [3, [4]]]));
        self::assertSame([1, [2], 3, [4]], Arr::flatten([[1, [2]], [3, [4]]], 1));
    }

    public function testWhere(): void
    {
        $array = [1, 2, 3, 4, 5, 6];

        $result = Arr::where($array, function ($value) {
            return $value % 2 === 0;
        });

        self::assertSame([1 => 2, 3 => 4, 5 => 6], $result);
    }

    public function testPluck(): void
    {
        $array = [
            ['name' => 'John', 'age' => 30],
            ['name' => 'Jane', 'age' => 25],
        ];

        self::assertSame(['John', 'Jane'], Arr::pluck($array, 'name'));
        self::assertSame(['John' => 30, 'Jane' => 25], Arr::pluck($array, 'age', 'name'));
    }

    public function testGroupBy(): void
    {
        $array = [
            ['type' => 'fruit', 'name' => 'apple'],
            ['type' => 'fruit', 'name' => 'banana'],
            ['type' => 'vegetable', 'name' => 'carrot'],
        ];

        $result = Arr::groupBy($array, 'type');

        self::assertCount(2, $result['fruit']);
        self::assertCount(1, $result['vegetable']);
    }

    public function testPull(): void
    {
        $array = ['name' => 'John', 'age' => 30];

        $value = Arr::pull($array, 'name');

        self::assertSame('John', $value);
        self::assertArrayNotHasKey('name', $array);
    }

    public function testCollapse(): void
    {
        self::assertSame([1, 2, 3, 4], Arr::collapse([[1, 2], [3, 4]]));
    }

    public function testPrepend(): void
    {
        self::assertSame([1, 2, 3], Arr::prepend([2, 3], 1));
        self::assertSame(['a' => 1, 'b' => 2], Arr::prepend(['b' => 2], 1, 'a'));
    }

    public function testRandom(): void
    {
        $array = [1, 2, 3, 4, 5];

        self::assertContains(Arr::random($array), $array);
        self::assertCount(3, Arr::random($array, 3));
        self::assertSame([], Arr::random($array, 0));
    }

    public function testRandomThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Arr::random([1, 2], 5);
    }

    public function testShuffle(): void
    {
        $array = [1, 2, 3, 4, 5];

        $shuffled = Arr::shuffle($array);

        self::assertCount(count($array), $shuffled);
        self::assertEqualsCanonicalizing($array, $shuffled);
    }

    public function testSort(): void
    {
        $array = [3, 1, 4, 1, 5];

        self::assertSame([1, 1, 3, 4, 5], array_values(Arr::sort($array)));
        self::assertSame([5, 4, 3, 1, 1], array_values(Arr::sort($array, SORT_REGULAR, true)));
    }

    public function testSortRecursive(): void
    {
        $array = [
            'b' => ['z' => 1, 'a' => 2],
            'a' => ['y' => 3, 'b' => 4],
        ];

        $result = Arr::sortRecursive($array);

        self::assertSame(['a', 'b'], array_keys($result));
        self::assertSame(['a', 'z'], array_keys($result['b']));
    }

    public function testUndot(): void
    {
        $array = ['user.name' => 'John', 'user.age' => 30, 'role' => 'admin'];

        $result = Arr::undot($array);

        self::assertSame('John', $result['user']['name']);
        self::assertSame(30, $result['user']['age']);
        self::assertSame('admin', $result['role']);
    }

    public function testWrap(): void
    {
        self::assertSame(['John'], Arr::wrap('John'));
        self::assertSame([1, 2], Arr::wrap([1, 2]));
        self::assertSame([], Arr::wrap(null));
    }

    public function testIsAssoc(): void
    {
        self::assertTrue(Arr::isAssoc(['a' => 1, 'b' => 2]));
        self::assertFalse(Arr::isAssoc([1, 2, 3]));
    }

    public function testIsList(): void
    {
        self::assertTrue(Arr::isList([1, 2, 3]));
        self::assertFalse(Arr::isList(['a' => 1, 'b' => 2]));
    }

    public function testAccessible(): void
    {
        self::assertTrue(Arr::accessible([]));
        self::assertTrue(Arr::accessible(new \ArrayObject()));
        self::assertFalse(Arr::accessible('string'));
        self::assertFalse(Arr::accessible(123));
    }

    public function testExists(): void
    {
        $array = ['name' => 'John', 'age' => 30];

        self::assertTrue(Arr::exists($array, 'name'));
        self::assertFalse(Arr::exists($array, 'email'));
    }

    public function testHasAny(): void
    {
        $array = ['name' => 'John', 'age' => 30];

        self::assertTrue(Arr::hasAny($array, ['name', 'email']));
        self::assertFalse(Arr::hasAny($array, ['email', 'phone']));
    }

    public function testAdd(): void
    {
        $array = ['name' => 'John'];

        $result = Arr::add($array, 'email', 'john@example.com');
        self::assertSame('john@example.com', $result['email']);

        // Should not overwrite existing key
        $result = Arr::add($array, 'name', 'Jane');
        self::assertSame('John', $result['name']);
    }

    public function testDot(): void
    {
        $array = ['user' => ['name' => 'John', 'address' => ['city' => 'Beijing']]];

        $result = Arr::dot($array);

        self::assertSame('John', $result['user.name']);
        self::assertSame('Beijing', $result['user.address.city']);
    }

    public function testDivide(): void
    {
        $array = ['name' => 'John', 'age' => 30];

        [$keys, $values] = Arr::divide($array);

        self::assertSame(['name', 'age'], $keys);
        self::assertSame(['John', 30], $values);
    }

    public function testTake(): void
    {
        $array = [1, 2, 3, 4, 5];

        self::assertSame([1, 2, 3], Arr::take($array, 3));
        self::assertSame([4, 5], Arr::take($array, -2));
    }

    public function testMap(): void
    {
        $array = [1, 2, 3];

        $result = Arr::map($array, function ($value) {
            return $value * 2;
        });

        self::assertSame([2, 4, 6], $result);
    }

    public function testMapWithKeys(): void
    {
        $array = [
            ['name' => 'John', 'age' => 30],
            ['name' => 'Jane', 'age' => 25],
        ];

        $result = Arr::mapWithKeys($array, function ($value) {
            return [$value['name'] => $value['age']];
        });

        self::assertSame(['John' => 30, 'Jane' => 25], $result);
    }

    public function testReject(): void
    {
        $array = [1, 2, 3, 4, 5];

        $result = Arr::reject($array, function ($value) {
            return $value % 2 === 0;
        });

        self::assertSame([0 => 1, 2 => 3, 4 => 5], $result);
    }

    public function testWhereNotNull(): void
    {
        $array = ['a' => 1, 'b' => null, 'c' => 3, 'd' => null];

        $result = Arr::whereNotNull($array);

        self::assertSame(['a' => 1, 'c' => 3], $result);
    }

    public function testJoin(): void
    {
        self::assertSame('a, b, c', Arr::join(['a', 'b', 'c'], ', '));
        self::assertSame('a, b and c', Arr::join(['a', 'b', 'c'], ', ', ' and '));
        self::assertSame('a', Arr::join(['a'], ', ', ' and '));
    }

    public function testCrossJoin(): void
    {
        $result = Arr::crossJoin([1, 2], ['a', 'b']);

        self::assertCount(4, $result);
        self::assertSame([1, 'a'], $result[0]);
        self::assertSame([1, 'b'], $result[1]);
        self::assertSame([2, 'a'], $result[2]);
        self::assertSame([2, 'b'], $result[3]);
    }

    public function testKeyBy(): void
    {
        $array = [
            ['name' => 'John', 'age' => 30],
            ['name' => 'Jane', 'age' => 25],
        ];

        $result = Arr::keyBy($array, 'name');

        self::assertArrayHasKey('John', $result);
        self::assertArrayHasKey('Jane', $result);
        self::assertSame(30, $result['John']['age']);
    }

    public function testKeyByWithCallback(): void
    {
        $array = [
            ['name' => 'John', 'age' => 30],
            ['name' => 'Jane', 'age' => 25],
        ];

        $result = Arr::keyBy($array, function ($item) {
            return strtolower($item['name']);
        });

        self::assertArrayHasKey('john', $result);
        self::assertArrayHasKey('jane', $result);
    }
}
