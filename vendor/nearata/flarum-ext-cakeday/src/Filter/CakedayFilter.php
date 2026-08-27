<?php

namespace Nearata\CakeDay\Filter;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Flarum\Filter\FilterInterface;
use Flarum\Filter\FilterState;

class CakedayFilter implements FilterInterface
{
    public function getFilterKey(): string
    {
        return 'cakeday';
    }

    public function filter(FilterState $filterState, string $filterValue, bool $negate): void
    {
        if (! $filterValue) {
            return;
        }

        $today = Carbon::now();
        $tomorrow = $today->copy()->add(1, 'day');
        $upcomingFrom = $today->copy()->add(2, 'day');
        $upcomingTo = $upcomingFrom->copy()->add(1, 'week');

        switch ($filterValue) {
            case 'today':
                $this->getTodayTomorrow($filterState, $today);
                break;
            case 'tomorrow':
                $this->getTodayTomorrow($filterState, $tomorrow);
                break;
            case 'upcoming':
                $this->getUpcoming($filterState, $upcomingFrom, $upcomingTo);
                break;
            case 'all':
                $this->getAll($filterState);
                break;
            default:
                break;
        }
    }

    private function getTodayTomorrow(FilterState $filterState, Carbon $date): void
    {
        $filterState->getQuery()
            ->whereMonth('joined_at', $date->month)
            ->whereDay('joined_at', $date->day);
    }

    private function getUpcoming(FilterState $filterState, Carbon $date1, Carbon $date2): void
    {
        $range = [];

        foreach (CarbonPeriod::create($date1, '1 day', $date2) as $day) {
            $range[$day->format('md')] = $day->format('md');
        }

        $bindings = implode(',', array_fill(0, count($range), '?'));

        $filterState->getQuery()
            ->whereRaw("DATE_FORMAT(joined_at, '%m%d') IN ({$bindings})", $range);
    }

    private function getAll(FilterState $filterState): void
    {
        $filterState->getQuery()
            ->orderByRaw('MONTH(joined_at), DAY(joined_at)');
    }
}
