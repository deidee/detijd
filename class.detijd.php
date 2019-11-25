<?php

class Detijd
{
    private $format = 'H:i:s';

    public function __construct($settings = [])
    {
        // Set default timezone. See https://www.php.net/manual/en/timezones.europe.php.
        date_default_timezone_set('Europe/Amsterdam');
    }

    public function __toString()
    {
        return date($this->format);
    }
}