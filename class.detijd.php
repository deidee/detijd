<?php

class Detijd
{
    const WHITE = '#ffffff';

    private $format = 'H:i:s';
    private $height = 300;
    private $width = 300;
    private $type = 'png';
    private $x = 0;
    private $y = 0;
    private $size = 24;

    public function __construct($settings = [])
    {
        // Set default timezone. See https://www.php.net/manual/en/timezones.europe.php.
        date_default_timezone_set('Europe/Amsterdam');

        $this->im = new Imagick();
        $this->im->newImage($this->width, $this->height, new ImagickPixel(self::WHITE));
        $this->im->setImageFormat($this->type);

        $draw = new ImagickDraw();
        $draw->setViewbox(0, 0, $this->width, $this->height);
        $draw->setStrokeWidth(0);

        $draw->setFillColor(new ImagickPixel('#000000'));
        $x2 = $this->x + $this->size + mt_rand(-1, 2);
        $y2 = $this->y + $this->size + mt_rand(-1, 2);
        $draw->rectangle($this->x, $this->y, $x2, $y2);

        $this->im->drawImage($draw);
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