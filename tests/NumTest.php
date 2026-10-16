<?php

declare(strict_types=1);

namespace Royfee\Support\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Royfee\Support\Num;

class NumTest extends TestCase
{
    // =========================================================================
    // Range & Constraint
    // =========================================================================

    public function testClamp(): void
    {
        self::assertSame(10, Num::clamp(15, 0, 10));
        self::assertSame(0, Num::clamp(-5, 0, 10));
        self::assertSame(5, Num::clamp(5, 0, 10));
        self::assertSame(0.5, Num::clamp(0.5, 0.0, 1.0));
    }

    public function testBetween(): void
    {
        self::assertTrue(Num::between(5, 1, 10));
        self::assertTrue(Num::between(1, 1, 10));
        self::assertTrue(Num::between(10, 1, 10));
        self::assertFalse(Num::between(0, 1, 10));
        self::assertFalse(Num::between(11, 1, 10));
    }

    // =========================================================================
    // Formatting
    // =========================================================================

    public function testFormat(): void
    {
        self::assertSame('1,234,567.89', Num::format(1234567.89));
        self::assertSame('1,234,567.9', Num::format(1234567.89, 1));
        self::assertSame('1.234.567,89', Num::format(1234567.89, 2, ',', '.'));
    }

    public function testCurrency(): void
    {
        self::assertSame('$1,234.50', Num::currency(1234.5));
        self::assertSame('$1,234.50', Num::currency(1234.5, '$'));
        self::assertSame('¥1,234.50', Num::currency(1234.5, '¥'));
        self::assertSame('€1,234.5', Num::currency(1234.5, '€', 1));
    }

    public function testPercent(): void
    {
        self::assertSame('86%', Num::percent(0.856));
        self::assertSame('85.6%', Num::percent(0.856, 1));
        self::assertSame('100%', Num::percent(1));
        self::assertSame('50%', Num::percent(0.5));
    }

    public function testFileSize(): void
    {
        self::assertSame('0 B', Num::fileSize(0));
        self::assertSame('500.00 B', Num::fileSize(500));
        self::assertSame('1.00 KB', Num::fileSize(1024));
        self::assertSame('1.00 MB', Num::fileSize(1048576));
        self::assertSame('1.00 GB', Num::fileSize(1073741824));
        self::assertSame('1.50 MB', Num::fileSize(1572864));
    }

    // =========================================================================
    // Statistical Calculations
    // =========================================================================

    public function testAverage(): void
    {
        self::assertSame('2.00', Num::average([1, 2, 3]));
        self::assertSame('2.50', Num::average([1, 2, 3, 4]));
        self::assertSame('20.00', Num::average(['10', '20', '30'], 2));
        self::assertSame('0.20', Num::average(['0.1', '0.2', '0.3'], 2));
    }

    public function testAverageThrowsExceptionForEmptyArray(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Num::average([]);
    }

    public function testMedian(): void
    {
        self::assertEquals(3.0, Num::median(1, 2, 3, 4, 5));
        self::assertEquals(2.5, Num::median(1, 2, 3, 4));
        self::assertEquals(1.0, Num::median(1));
    }

    public function testMedianThrowsExceptionForEmptySet(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Num::median();
    }

    public function testSum(): void
    {
        self::assertSame('6.00', Num::sum([1, 2, 3]));
        self::assertSame('10.50', Num::sum([1.5, 2.5, 3.5, 3.0]));
        self::assertSame('0.60', Num::sum(['0.1', '0.2', '0.3'], 2));
        self::assertSame('6.00', Num::sum(['1.5', '2.5', '2.0'], 2));
    }

    public function testProduct(): void
    {
        self::assertSame(24, Num::product([2, 3, 4]));
        self::assertEquals(3.75, Num::product([1.5, 2.5]));
    }

    public function testProductThrowsExceptionForEmptyArray(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Num::product([]);
    }

    public function testMax(): void
    {
        self::assertSame(5, Num::max([3, 1, 4, 1, 5]));
        self::assertEquals(3.14, Num::max([1.5, 3.14, 2.71]));
    }

    public function testMin(): void
    {
        self::assertSame(1, Num::min([3, 1, 4, 1, 5]));
        self::assertEquals(1.5, Num::min([1.5, 3.14, 2.71]));
    }

    public function testMaxThrowsExceptionForEmptyArray(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Num::max([]);
    }

    public function testMinThrowsExceptionForEmptyArray(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Num::min([]);
    }

    public function testRound(): void
    {
        self::assertSame('3.14', Num::round(3.1415, 2));
        self::assertSame('3', Num::round(3.1415, 0));
        self::assertSame('3.14', Num::round('3.1415', 2));
        self::assertSame('3.15', Num::round('3.145', 2));
    }

    public function testFloor(): void
    {
        self::assertSame('3', Num::floor(3.9));
        self::assertSame('3.1', Num::floor(3.19, 1));
        self::assertSame('3', Num::floor('3.99', 0));
        self::assertSame('3.1', Num::floor('3.19', 1));
    }

    public function testCeil(): void
    {
        self::assertSame('4', Num::ceil(3.1));
        self::assertSame('3.2', Num::ceil(3.11, 1));
        self::assertSame('4', Num::ceil('3.01', 0));
        self::assertSame('3.2', Num::ceil('3.11', 1));
    }

    // =========================================================================
    // Type Checking
    // =========================================================================

    public function testIsEven(): void
    {
        self::assertTrue(Num::isEven(4));
        self::assertTrue(Num::isEven(0));
        self::assertFalse(Num::isEven(3));
        self::assertFalse(Num::isEven(-1));
    }

    public function testIsOdd(): void
    {
        self::assertTrue(Num::isOdd(3));
        self::assertTrue(Num::isOdd(-1));
        self::assertFalse(Num::isOdd(4));
        self::assertFalse(Num::isOdd(0));
    }

    public function testIsPositive(): void
    {
        self::assertTrue(Num::isPositive(1));
        self::assertTrue(Num::isPositive(0.1));
        self::assertFalse(Num::isPositive(0));
        self::assertFalse(Num::isPositive(-1));
    }

    public function testIsNegative(): void
    {
        self::assertTrue(Num::isNegative(-1));
        self::assertTrue(Num::isNegative(-0.1));
        self::assertFalse(Num::isNegative(0));
        self::assertFalse(Num::isNegative(1));
    }

    public function testIsZero(): void
    {
        self::assertTrue(Num::isZero(0));
        self::assertTrue(Num::isZero(0.0));
        self::assertFalse(Num::isZero(1));
        self::assertFalse(Num::isZero(-1));
    }

    public function testIsNumeric(): void
    {
        self::assertTrue(Num::isNumeric(123));
        self::assertTrue(Num::isNumeric(3.14));
        self::assertTrue(Num::isNumeric('123'));
        self::assertTrue(Num::isNumeric('3.14'));
        self::assertFalse(Num::isNumeric('abc'));
        self::assertFalse(Num::isNumeric(null));
    }

    public function testIsInt(): void
    {
        self::assertTrue(Num::isInt(3));
        self::assertTrue(Num::isInt(3.0));
        self::assertTrue(Num::isInt('3'));
        self::assertFalse(Num::isInt(3.14));
        self::assertFalse(Num::isInt('3.14'));
        self::assertFalse(Num::isInt('abc'));
    }

    public function testIsFloat(): void
    {
        self::assertTrue(Num::isFloat(3.14));
        self::assertFalse(Num::isFloat(3));
        self::assertFalse(Num::isFloat('3.14'));
    }

    // =========================================================================
    // Math Operations
    // =========================================================================

    public function testGcd(): void
    {
        self::assertSame(4, Num::gcd(12, 8));
        self::assertSame(1, Num::gcd(7, 13));
        self::assertSame(6, Num::gcd(12, 18));
        self::assertSame(5, Num::gcd(0, 5));
    }

    public function testLcm(): void
    {
        self::assertSame(12, Num::lcm(4, 6));
        self::assertSame(6, Num::lcm(2, 3));
        self::assertSame(0, Num::lcm(0, 5));
    }

    public function testFactorial(): void
    {
        self::assertSame(1, Num::factorial(0));
        self::assertSame(1, Num::factorial(1));
        self::assertSame(120, Num::factorial(5));
        self::assertSame(720, Num::factorial(6));
    }

    public function testFactorialThrowsExceptionForNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Num::factorial(-1);
    }

    public function testFibonacci(): void
    {
        self::assertSame(0, Num::fibonacci(0));
        self::assertSame(1, Num::fibonacci(1));
        self::assertSame(1, Num::fibonacci(2));
        self::assertSame(2, Num::fibonacci(3));
        self::assertSame(5, Num::fibonacci(5));
        self::assertSame(13, Num::fibonacci(7));
    }

    public function testFibonacciThrowsExceptionForNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Num::fibonacci(-1);
    }

    public function testPower(): void
    {
        self::assertEquals(1024, Num::power(2, 10));
        self::assertEquals(8, Num::power(2, 3));
        self::assertEquals(1, Num::power(5, 0));
    }

    public function testSqrt(): void
    {
        self::assertEquals(4.0, Num::sqrt(16));
        self::assertEquals(3.0, Num::sqrt(9));
        self::assertEquals(0.0, Num::sqrt(0));
    }

    public function testSqrtThrowsExceptionForNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Num::sqrt(-1);
    }

    // =========================================================================
    // Random Numbers
    // =========================================================================

    public function testRandom(): void
    {
        $result = Num::random(1, 100);

        self::assertGreaterThanOrEqual(1, $result);
        self::assertLessThanOrEqual(100, $result);
        self::assertIsInt($result);
    }

    public function testRandomFloat(): void
    {
        $result = Num::randomFloat(0, 1, 2);

        self::assertGreaterThanOrEqual(0, $result);
        self::assertLessThanOrEqual(1, $result);
        self::assertIsFloat($result);
    }

    // =========================================================================
    // Precise Calculations (bcmath)
    // =========================================================================

    public function testAdd(): void
    {
        self::assertSame('0.30', Num::add('0.1', '0.2', 2));
        self::assertSame('100.00', Num::add('99.99', '0.01', 2));
        self::assertSame('3', Num::add('1', '2', 0));
    }

    public function testSub(): void
    {
        self::assertSame('0.67', Num::sub('1.00', '0.33', 2));
        self::assertSame('0.00', Num::sub('0.1', '0.1', 2));
        self::assertSame('-0.50', Num::sub('0.5', '1.0', 2));
    }

    public function testMul(): void
    {
        self::assertSame('999.00', Num::mul('9.99', '100', 2));
        self::assertSame('0.01', Num::mul('0.1', '0.1', 2));
        self::assertSame('0.00', Num::mul('0.001', '0.001', 2));
    }

    public function testDiv(): void
    {
        self::assertSame('33.33', Num::div('100', '3', 2));
        self::assertSame('0.50', Num::div('1', '2', 2));
        self::assertSame('100.00', Num::div('10', '0.1', 2));
    }

    public function testDivThrowsExceptionForZero(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Num::div('100', '0', 2);
    }

    public function testComp(): void
    {
        self::assertSame(0, Num::comp('0.30', '0.30', 2));
        self::assertSame(1, Num::comp('0.31', '0.30', 2));
        self::assertSame(-1, Num::comp('0.29', '0.30', 2));
    }

    public function testEq(): void
    {
        self::assertTrue(Num::eq('0.1', '0.10', 2));
        self::assertTrue(Num::eq('1.00', '1.00', 2));
        self::assertFalse(Num::eq('0.1', '0.2', 2));
    }

    public function testGt(): void
    {
        self::assertTrue(Num::gt('1.01', '1.00', 2));
        self::assertFalse(Num::gt('1.00', '1.00', 2));
        self::assertFalse(Num::gt('0.99', '1.00', 2));
    }

    public function testLt(): void
    {
        self::assertTrue(Num::lt('0.99', '1.00', 2));
        self::assertFalse(Num::lt('1.00', '1.00', 2));
        self::assertFalse(Num::lt('1.01', '1.00', 2));
    }

    public function testGte(): void
    {
        self::assertTrue(Num::gte('1.00', '1.00', 2));
        self::assertTrue(Num::gte('1.01', '1.00', 2));
        self::assertFalse(Num::gte('0.99', '1.00', 2));
    }

    public function testLte(): void
    {
        self::assertTrue(Num::lte('1.00', '1.00', 2));
        self::assertTrue(Num::lte('0.99', '1.00', 2));
        self::assertFalse(Num::lte('1.01', '1.00', 2));
    }

    public function testToDecimal(): void
    {
        self::assertSame('0.30', Num::toDecimal(0.1 + 0.2, 2));
        self::assertSame('3.14', Num::toDecimal('3.1415', 2));
        self::assertSame('100.00', Num::toDecimal(100, 2));
    }

    public function testPreciseSum(): void
    {
        self::assertSame('0.60', Num::sum(['0.1', '0.2', '0.3'], 2));
        self::assertSame('6.00', Num::sum(['1.5', '2.5', '2.0'], 2));
    }

    public function testPreciseAverage(): void
    {
        self::assertSame('20.00', Num::average(['10', '20', '30'], 2));
        self::assertSame('0.20', Num::average(['0.1', '0.2', '0.3'], 2));
    }

    public function testPreciseAverageThrowsExceptionForEmptyArray(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Num::average([], 2);
    }

    /**
     * Test the classic floating-point precision issue.
     * This is the core use case for bcmath methods.
     */
    public function testFloatingPointPrecisionIssue(): void
    {
        // Regular float arithmetic gives wrong result
        $floatResult = 0.1 + 0.2;
        self::assertNotSame(0.3, $floatResult);

        // bcmath gives correct result
        self::assertSame('0.30', Num::add('0.1', '0.2', 2));
        self::assertTrue(Num::eq(Num::add('0.1', '0.2', 2), '0.3', 2));
    }

    /**
     * Test a realistic financial calculation scenario.
     */
    public function testFinancialCalculationScenario(): void
    {
        // Simulate: 3 items priced at 33.33 each
        $price = '33.33';
        $quantity = '3';
        $total = Num::mul($price, $quantity, 2);
        self::assertSame('99.99', $total);

        // Apply 10% discount (bcmul truncates, so 99.99 * 0.10 = 9.9990 -> 9.99)
        $discount = Num::mul($total, '0.10', 2);
        self::assertSame('9.99', $discount);

        $finalPrice = Num::sub($total, $discount, 2);
        self::assertSame('90.00', $finalPrice);

        // Split between 2 people
        $perPerson = Num::div($finalPrice, '2', 2);
        self::assertSame('45.00', $perPerson);
    }
}
