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
    private $c = [];

    public function __construct($settings = [])
    {
        // Set default timezone. See https://www.php.net/manual/en/timezones.europe.php.
        date_default_timezone_set('Europe/Amsterdam');

        $this->c[0x30] = array(1,1,1,1,0,1,1,0,1,1,0,1,1,0,1,1,0,1,1,1,1,0,0,0,0,0,0);//0
        $this->c[0x31] = array(0,1,0,1,1,0,0,1,0,0,1,0,0,1,0,0,1,0,1,1,1,0,0,0,0,0,0);//1
        $this->c[0x32] = array(1,1,1,0,0,1,0,0,1,1,1,1,1,0,0,1,0,0,1,1,1,0,0,0,0,0,0);//2
        $this->c[0x33] = array(1,1,1,0,0,1,0,0,1,1,1,1,0,0,1,0,0,1,1,1,1,0,0,0,0,0,0);//3
        $this->c[0x34] = array(1,0,1,1,0,1,1,0,1,1,1,1,0,0,1,0,0,1,0,0,1,0,0,0,0,0,0);//4
        $this->c[0x35] = array(1,1,1,1,0,0,1,0,0,1,1,1,0,0,1,0,0,1,1,1,1,0,0,0,0,0,0);//5
        $this->c[0x36] = array(1,0,0,1,0,0,1,0,0,1,1,1,1,0,1,1,0,1,1,1,1,0,0,0,0,0,0);//6
        $this->c[0x37] = array(1,1,1,0,0,1,0,0,1,0,0,1,0,0,1,0,0,1,0,0,1,0,0,0,0,0,0);//7
        $this->c[0x38] = array(1,1,1,1,0,1,1,0,1,1,1,1,1,0,1,1,0,1,1,1,1,0,0,0,0,0,0);//8
        $this->c[0x39] = array(1,1,1,1,0,1,1,0,1,1,1,1,0,0,1,0,0,1,0,0,1,0,0,0,0,0,0);//9

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