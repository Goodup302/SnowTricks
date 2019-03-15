<?php

namespace App\Service;

class Date
{
    const MOMENTJS_DATE_FORMAT = 'YYYY-MM-DD';
    const MOMENTJS_TIME_FORMAT = 'HH:mm';

    const DATE_FORMAT = 'Y-m-d';
    const DATETIME_FORMAT = 'Y-m-d H:i:s';
    const TIME_FORMAT = 'H:i:00';

    public function __construct($timezone)
    {
        date_default_timezone_set($timezone);
    }

    public function currentDateTime(): \DateTime
    {
        $date = new \DateTime();
        $date->format(self::DATETIME_FORMAT);
        return $date;
    }

    public function currentDateTimeISO(): \DateTime
    {
        $date = new \DateTime();
        $date->format('c');
        return $date;
    }

    public function timeSlot($hour, $minute = 0): \DateTime
    {
        $time = new \DateTime();
        $time->setTime($hour, $minute)->format(self::TIME_FORMAT);
        return $time;
    }

    public function stringToDateTime($string): \DateTime
    {
        $date = \DateTime::createFromFormat(self::DATE_FORMAT, $string);
        $date->setTime(0, 0);
        return $date;
    }

    public function stringToTime($string): \DateTime
    {
        $date = \DateTime::createFromFormat(self::TIME_FORMAT, $string.':00');
        $date->setDate(1, 1, 1);
        return $date;
    }
}