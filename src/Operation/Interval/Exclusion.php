<?php
declare(strict_types=1);

namespace Interval\Operation\Interval;

use Interval\Interval;
use Interval\Intervals;
use RangeException;
use UnexpectedValueException;

/**
 * Class Exclusion
 * @package Interval\Operation\Interval
 */
class Exclusion
{
    /**
     * PHP magic function
     * @param Interval $first
     * @param Interval $second
     * @return Intervals
     * @throws \InvalidArgumentException
     * @throws UnexpectedValueException
     * @throws RangeException
     */
    public function __invoke(Interval $first, Interval $second)
    {
        return $this->compute($first, $second);
    }

    /**
     * Excludes an interval from another one. Exp
     *
     *      |_________________|
     *
     *             -
     *                  |_________________|
     *
     *          =
     *      |___________|
     *
     * @param Interval $first
     * @param Interval $second
     * @return Intervals
     * @throws \InvalidArgumentException
     * @throws UnexpectedValueException
     * @throws RangeException
     */
    public function compute(Interval $first, Interval $second): Intervals
    {
        if (!$first->overlaps($second)) {
            return new Intervals([$first]);
        }

        if ($second->includes($first)) {
            return new Intervals([]);
        }

        if ($second->contains($first->getStart())) {
            return new Intervals([
                new Interval($second->getEnd()->flip(), $first->getEnd()),
            ]);
        }

        if ($second->contains($first->getEnd())) {
            return new Intervals([
                new Interval($first->getStart(), $second->getStart()->flip()),
            ]);
        }

        if ($first->includes($second)) {
            return new Intervals([
                new Interval($first->getStart(), $second->getStart()->flip()),
                new Interval($second->getEnd()->flip(), $first->getEnd()),
            ]);
        }

        throw new \RangeException('Unexpected calculation case');
    }
}
