<?php

namespace Elcodi\Store\CoreBundle\Services;

use Symfony\Component\Security\Core\Exception\RuntimeException;
use Symfony\Component\Templating\EngineInterface;

class DateTimeManager
{
    protected $holidayDays = ['*-12-25', '*-12-26', '*-01-01', '2013-12-23']; # variable and fixed holidays
    protected $workingDays = [1, 2, 3, 4, 5]; # date format = N (1 = Monday, ...)

    public function numberOfWorkingDays($from, $to)
    {
        // $from->modify('+1 day');
        // $to->modify('+1 day');
        $interval = new \DateInterval('P1D');
        $periods = new \DatePeriod($from, $interval, $to);

        $days = 0;
        foreach ($periods as $period) {
            if ($this->isWorkingDay($period)) {
                $days++;
            }
        }
        return $days;
    }

    public function addWorkingDaysToDate($from, $workingDays)
    {
        $to = clone $from;
        for ($i = 0; $i < 10000; $i++) {
            $to->modify('+1 day');
            if ($this->isWorkingDay($to)) {
                $workingDays--;
            }

            if ($workingDays <= 0) {
                break;
            }
        }
        return $to;
    }

    public function isWorkingDay(\DateTime $dateTime)
    {
        if (!in_array($dateTime->format('N'), $this->workingDays)) {
            return false;
        }

        if (in_array($dateTime->format('Y-m-d'), $this->holidayDays)) {
            return false;
        }

        if (in_array($dateTime->format('*-m-d'), $this->holidayDays)) {
            return false;
        }
        return true;
    }

}
