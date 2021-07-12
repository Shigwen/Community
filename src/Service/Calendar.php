<?php

namespace App\Service;

use \DateTime;

class Calendar
{
    // Widgets = Months
    static public function GetDefaultWidgets()
    {
        $date = new DateTime();
        $widgets = [];

        $date->modify('first day of this month');
        $widgets[] = self::Process($date->format('Y-m-d'));

        $date->modify('next month');
        $widgets[] = self::Process($date->format('Y-m-d'));

        return $widgets;
    }

    static public function Process($date)
    {
        $from = new DateTime($date);
        $from->modify('midnight');
        $from->modify('previous month');
        $from->modify('last day of this month');

        $to = new DateTime($date);
        $to->modify('midnight');
        $to->modify('next month');
        $to->modify('first day of this month');

        $delta = $from->diff($to);
        $full_duration = $delta->days;
        $available_days = [];

        $date = new DateTime();
        $date->setTimestamp($from->getTimestamp());

        $threshold = new Datetime('today');

        // Initialization
        for ($i = 0; $i <= $full_duration; ++$i) {
            $key = $date->format('Y-m-d');

            // @TODO : initialiser hasEvents

            $available_days[$key] = [
                'date' => $key,
                'number' => $date->format('j'),
                'past' => $date < $threshold,
                'hasEvents' => false
            ];

            $date->modify('+1 day');
        }

        $available_days = array_slice($available_days, 1, -1);

        // Shift to First day of the month
        $from->modify('+1 day');

        $year = $from->format('Y');
        $month = $from->format('F');
        $empty_days_padding = (int) $from->format('N') - 1;

        $data = [
            'title' => $month . ' ' . $year,
            'empty_days_padding' => $empty_days_padding,
            'days' => $available_days
        ];

        return $data;
    }
}
