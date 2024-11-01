<?php
declare(strict_types=1);

namespace hiqdev\php\billing\tests\unit\action;

use DateTimeImmutable;
use hiqdev\php\billing\action\UsageInterval;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UsageIntervalTest extends TestCase
{
    public function testWholeMonthConstructor(): void
    {
        $interval = UsageInterval::wholeMonth(new DateTimeImmutable("2023-02-21 20:10:03"));
        $this->assertEquals("2023-02-01 00:00:00", $interval->start()->format("Y-m-d H:i:s"));
        $this->assertEquals("2023-03-01 00:00:00", $interval->end()->format("Y-m-d H:i:s"));
        $this->assertSame(1.0, $interval->ratioOfMonth());
        $this->assertSame(2_419_200, $interval->seconds());
        $this->assertSame(2_419_200, $interval->secondsInMonth());
    }


    /**
     * @dataProvider provideWithinMonth
     */
    public function testWithinMonth(array $constructor, array $expectations): void
    {
        $month = new DateTimeImmutable($constructor['month']);
        $start = new DateTimeImmutable($constructor['start']);
        $end = $constructor['end'] === null ? null : new DateTimeImmutable($constructor['end']);

        if (isset($expectations['expectedException'])) {
            $this->expectException($expectations['expectedException']);
            $this->expectExceptionMessage($expectations['expectedExceptionMessage']);
        }

        $interval = UsageInterval::withinMonth($month, $start, $end);

        $this->assertEquals($expectations['start'], $interval->start()->format("Y-m-d H:i:s"));
        $this->assertEquals($expectations['end'], $interval->end()->format("Y-m-d H:i:s"));
        $this->assertSame($expectations['ratioOfMonth'], $interval->ratioOfMonth());
        $this->assertSame($expectations['seconds'], $interval->seconds());
        $this->assertSame($expectations['secondsInMonth'], $interval->secondsInMonth());
    }

    public function provideWithinMonth()
    {
        yield 'For a start and end dates outside the month, the interval is the whole month' => [
            ['month' => '2023-02-01 00:00:00', 'start' => '2023-01-01 00:00:00', 'end' => '2023-10-01 00:00:00'],
            [
                'start' => '2023-02-01 00:00:00',
                'end' => '2023-03-01 00:00:00',
                'ratioOfMonth' => 1.0,
                'seconds' => 2_419_200,
                'secondsInMonth' => 2_419_200,
            ]
        ];

        yield 'When start date is greater than a month, the interval is a fraction of month' => [
            ['month' => '2023-02-01 00:00:00', 'start' => '2023-02-15 00:00:00', 'end' => null],
            [
                'start' => '2023-02-15 00:00:00',
                'end' => '2023-03-01 00:00:00',
                'ratioOfMonth' => 0.5,
                'seconds' => 1_209_600,
                'secondsInMonth' => 2_419_200,
            ]
        ];

        yield 'When end date is less than a month, the interval is a fraction of month' => [
            ['month' => '2023-02-01 00:00:00', 'start' => '2021-10-02 19:01:10', 'end' => '2023-02-15 00:00:00'],
            [
                'start' => '2023-02-01 00:00:00',
                'end' => '2023-02-15 00:00:00',
                'ratioOfMonth' => 0.5,
                'seconds' => 1_209_600,
                'secondsInMonth' => 2_419_200,
            ]
        ];

        yield 'When start and end dates are within a month, the interval is a fraction of month' => [
            ['month' => '2023-02-01 00:00:00', 'start' => '2023-02-15 00:00:00', 'end' => '2023-02-20 00:00:00'],
            [
                'start' => '2023-02-15 00:00:00',
                'end' => '2023-02-20 00:00:00',
                'ratioOfMonth' => 0.17857142857142858,
                'seconds' => 432_000,
                'secondsInMonth' => 2_419_200,
            ]
        ];

        yield 'When start date is greater than current month, the interval is zero' => [
            ['month' => '2023-02-01 00:00:00', 'start' => '2023-03-15 00:00:00', 'end' => null],
            [
                'start' => '2023-02-01 00:00:00',
                'end' => '2023-02-01 00:00:00',
                'ratioOfMonth' => 0.0,
                'seconds' => 0,
                'secondsInMonth' => 2_419_200,
            ]
        ];

        yield 'When end date is less than current month, the interval is zero' => [
            ['month' => '2023-02-01 00:00:00', 'start' => '2021-10-02 19:01:10', 'end' => '2023-01-15 00:00:00'],
            [
                'start' => '2023-02-01 00:00:00',
                'end' => '2023-02-01 00:00:00',
                'ratioOfMonth' => 0.0,
                'seconds' => 0,
                'secondsInMonth' => 2_419_200,
            ]
        ];

        yield 'When a start date is greater than the end an exception is thrown' => [
            ['month' => '2023-02-01 00:00:00', 'start' => '2023-03-15 00:00:00', 'end' => '2023-02-15 00:00:00'],
            [
                'expectedException' => InvalidArgumentException::class,
                'expectedExceptionMessage' => 'Start date must be less than end date',
            ]
        ];

        yield 'When start date is greater than start of month' => [
            ['month' => '2024-02-01 00:00:00', 'start' => '2024-02-01 11:50:00', 'end' => '2024-02-29 18:15:00'],
            [
                'start' => '2024-02-01 11:50:00',
                'end' => '2024-02-29 18:15:00',
                'ratioOfMonth' => 0.9747365900383141,
                'seconds' => 2_442_300,
                'secondsInMonth' => 2_505_600,
            ]
        ];
    }

    /**
     * @dataProvider provideWithMonthAndFraction
     */
    public function testWithMonthAndFraction(array $constructor, array $expectations): void
    {
        $month = new DateTimeImmutable($constructor['month']);
        $start = new DateTimeImmutable($constructor['start']);

        if (isset($expectations['expectedException'])) {
            $this->expectException($expectations['expectedException']);
            $this->expectExceptionMessage($expectations['expectedExceptionMessage']);
        }

        $interval = UsageInterval::withMonthAndFraction($month, $start, $constructor['fraction']);

        $this->assertEquals($expectations['start'], $interval->start()->format("Y-m-d H:i:s"));
        $this->assertEquals($expectations['end'], $interval->end()->format("Y-m-d H:i:s"));
        $this->assertSame($expectations['ratioOfMonth'], $interval->ratioOfMonth());
        $this->assertSame($expectations['seconds'], $interval->seconds());
        $this->assertSame($expectations['secondsInMonth'], $interval->secondsInMonth());
    }

    public function provideWithMonthAndFraction()
    {
        yield 'For a start and end dates outside the month, the interval is the whole month' => [
            ['month' => '2023-02-01 00:00:00', 'start' => '2023-01-01 00:00:00', 'fraction' => 1],
            [
                'start' => '2023-02-01 00:00:00',
                'end' => '2023-03-01 00:00:00',
                'ratioOfMonth' => 1.0,
                'seconds' => 2_419_200,
                'secondsInMonth' => 2_419_200,
            ]
        ];

        yield 'When start date is greater than a month, the interval is a fraction of month' => [
            ['month' => '2023-02-01 00:00:00', 'start' => '2023-02-15 00:00:00',  'fraction' => 0.5],
            [
                'start' => '2023-02-15 00:00:00',
                'end' => '2023-03-01 00:00:00',
                'ratioOfMonth' => 0.5,
                'seconds' => 1_209_600,
                'secondsInMonth' => 2_419_200,
            ]
        ];

        yield 'When end date is less than a month, the interval is a fraction of month' => [
            ['month' => '2023-02-01 00:00:00', 'start' => '2021-10-02 19:01:10',  'fraction' => 0.5],
            [
                'start' => '2023-02-01 00:00:00',
                'end' => '2023-02-15 00:00:00',
                'ratioOfMonth' => 0.5,
                'seconds' => 1_209_600,
                'secondsInMonth' => 2_419_200,
            ]
        ];

        yield 'When start and end dates are within a month, the interval is a fraction of month' => [
            ['month' => '2023-02-01 00:00:00', 'start' => '2023-02-15 00:00:00', 'fraction' => 0.17857142857142858],
            [
                'start' => '2023-02-15 00:00:00',
                'end' => '2023-02-20 00:00:00',
                'ratioOfMonth' => 0.17857142857142858,
                'seconds' => 432_000,
                'secondsInMonth' => 2_419_200,
            ]
        ];

        yield 'When start date is greater than current month, the interval is zero' => [
            ['month' => '2023-02-01 00:00:00', 'start' => '2023-03-15 00:00:00', 'fraction' => 0],
            [
                'start' => '2023-02-01 00:00:00',
                'end' => '2023-02-01 00:00:00',
                'ratioOfMonth' => 0.0,
                'seconds' => 0,
                'secondsInMonth' => 2_419_200,
            ]
        ];

        yield 'When end date is less than current month, the interval is zero' => [
            ['month' => '2023-02-01 00:00:00', 'start' => '2021-10-02 19:01:10', 'fraction' => 0],
            [
                'start' => '2023-02-01 00:00:00',
                'end' => '2023-02-01 00:00:00',
                'ratioOfMonth' => 0.0,
                'seconds' => 0,
                'secondsInMonth' => 2_419_200,
            ]
        ];

        yield 'When start date is greater than start of month' => [
            ['month' => '2024-02-01 00:00:00', 'start' => '2024-02-01 11:50:00', 'fraction' => 0.9747365900383141],
            [
                'start' => '2024-02-01 11:50:00',
                'end' => '2024-02-29 18:15:00',
                'ratioOfMonth' => 0.9747365900383141,
                'seconds' => 2_442_300,
                'secondsInMonth' => 2_505_600,
            ]
        ];
    }

    /**
     * @dataProvider provideInvalidFractionOfMonthValues
     */
    public function testWithMonthAndFractionInvalidValues(float $fractionOfMonth): void
    {
        $month = new DateTimeImmutable('2023-01-01');
        $start = new DateTimeImmutable('2023-01-15');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Fraction of month must be between 0 and 1');

        UsageInterval::withMonthAndFraction($month, $start, $fractionOfMonth);
    }

    public function provideInvalidFractionOfMonthValues(): array
    {
        return [
            [-0.1],
            [1.1],
            [2.0],
        ];
    }
}
