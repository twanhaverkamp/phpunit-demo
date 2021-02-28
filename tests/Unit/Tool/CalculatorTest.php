<?php

namespace Tests\Unit\Tool;

use App\Tool\Calculator;
use PHPUnit\Framework\TestCase;

/**
 * @author Twan Haverkamp <twan@mailcampaigns.nl>
 * @covers Calculator
 */
class CalculatorTest extends TestCase
{
    private Calculator $calculator;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->calculator = new Calculator();
    }

    /**
     * This is a bad example, because you are only testing one "best case" scenario
     * and it is not clear which scenario you are testing.
     *
     * @covers Calculator::multiply
     */
    public function testMultiply(): void
    {
        $this->assertEquals(1, $this->calculator->multiply(1, 1));
    }

    /**
     * As you can see this method name tells you exactly what you are testing: test{METHOD}With{ARGUMENTS}Will{EXPECTED_RESULT}
     * and by using a data provider you do not limit yourself to just one correct calculation.
     *
     * Note: If you give your test methods valuable names like this and you pass the '--testdox' option on your command line
     * when running your tests, the output will look like this: "Multiply with integer arguments will return expected calculation".
     *
     * @covers       Calculator::multiply
     * @dataProvider getIntegerDataForMultiplier
     */
    public function testMultiplyWithIntegerArgumentsWillReturnExpectedCalculation(
        int $var1,
        int $var2,
        int $expectedReturnValue
    ): void {
        $this->assertEquals($expectedReturnValue, $this->calculator->multiply($var1, $var2));
    }

    /**
     * @covers       Calculator::multiply
     * @dataProvider getFloatDataForMultiplier
     */
    public function testMultiplyWithFloatArgumentsWillReturnExpectedCalculation(
        float $var1,
        float $var2,
        float $expectedReturnValue
    ): void {
        $this->assertEquals($expectedReturnValue, $this->calculator->multiply($var1, $var2));
    }

    /**
     * Each sub-array contains a specific test scenario, where the values are passed as method arguments.
     * @return array<string, array<string, int>>
     */
    public function getIntegerDataForMultiplier(): iterable
    {
        return [
            '1 * 1 = 1' => ['var1' => 1, 'var2' => 1, 'expectedReturnValue' => 1],
            '2 * 2 = 4' => ['var1' => 2, 'var2' => 2, 'expectedReturnValue' => 4],
            '3 * 3 = 9' => ['var1' => 3, 'var2' => 3, 'expectedReturnValue' => 9],
            '4 * 4 = 16' => ['var1' => 4, 'var2' => 4, 'expectedReturnValue' => 16],
            '5 * 5 = 25' => ['var1' => 5, 'var2' => 5, 'expectedReturnValue' => 25],
            '6 * 6 = 36' => ['var1' => 6, 'var2' => 6, 'expectedReturnValue' => 36],
            '7 * 7 = 49' => ['var1' => 7, 'var2' => 7, 'expectedReturnValue' => 49],
            '8 * 8 = 64' => ['var1' => 8, 'var2' => 8, 'expectedReturnValue' => 64],
            '9 * 9 = 81' => ['var1' => 9, 'var2' => 9, 'expectedReturnValue' => 81],
        ];
    }

    /**
     * @return array<string, array<string, float>>
     */
    public function getFloatDataForMultiplier(): iterable
    {
        return [
            '0.1 * 0.1 = 0.01' => ['var1' => 0.1, 'var2' => 0.1, 'expectedReturnValue' => 0.01],
            '0.2 * 0.2 = 0.04' => ['var1' => 0.2, 'var2' => 0.2, 'expectedReturnValue' => 0.04],
            '0.3 * 0.3 = 0.09' => ['var1' => 0.3, 'var2' => 0.3, 'expectedReturnValue' => 0.09],
            '0.4 * 0.4 = 0.16' => ['var1' => 0.4, 'var2' => 0.4, 'expectedReturnValue' => 0.16],
            '0.5 * 0.5 = 0.25' => ['var1' => 0.5, 'var2' => 0.5, 'expectedReturnValue' => 0.25],
            '0.6 * 0.6 = 0.36' => ['var1' => 0.6, 'var2' => 0.6, 'expectedReturnValue' => 0.36],
            '0.7 * 0.7 = 0.49' => ['var1' => 0.7, 'var2' => 0.7, 'expectedReturnValue' => 0.49],
            '0.8 * 0.8 = 0.64' => ['var1' => 0.8, 'var2' => 0.8, 'expectedReturnValue' => 0.64],
            '0.9 * 0.9 = 0.81' => ['var1' => 0.9, 'var2' => 0.9, 'expectedReturnValue' => 0.81],
        ];
    }
}
