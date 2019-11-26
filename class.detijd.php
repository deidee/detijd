<?php

class Detijd
{
    const WHITE = '#ffffff';

    private $format = 'H:i:s';
    private $height = 300;
    private $width = 300;
    private $type = 'png';

    public function __construct($settings = [])
    {
        // Set default timezone. See https://www.php.net/manual/en/timezones.europe.php.
        date_default_timezone_set('Europe/Amsterdam');

        $this->im = new Imagick();
        $this->im->newImage($this->width, $this->height, new ImagickPixel(self::WHITE));
        $this->im->setImageFormat($this->type);
    }

    public function __toString()
    {
        return date($this->format);
    }

    public function display()
    {
        header('Content-Type: ' . $this->im->getImageMimeType());

        echo $this->im->getImageBlob();
    }
}