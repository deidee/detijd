<?php

class Detijd
{
    const HEIGHT_MULTIPLIER = 9;
    const ROWS = 9;
    const WHITE = '#ffffff';

    private $format = 'H:i:s';
    private $height = 300;
    private $width = 300;
    private $type = 'gif';
    private $x = 0;
    private $y = 0;
    private $size = 24;
    private $c = [];
    private $text = '12:34';
    private $ones = 0;
    private $zeros = 0;

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

        $this->c[0x3A] = array(1,0,0,0,0,0,1,0,0);//:

        $this->x = $this->y = $this->size;
        $this->height = $this->size * self::HEIGHT_MULTIPLIER;
        $this->width = $this->size * 3;

        // Start a basic drawing context, even though we don't know the actual width yet.
        $draw = new ImagickDraw();
        $draw->setViewbox(0, 0, $this->width, $this->height);
        $draw->setStrokeWidth(0);

        for($i = 0; $i < 5; ++$i)
        {
            // Isolate a character from the text string.
            $char = mb_substr($this->text, $i, 1);

            // If we know this character, draw it.
            if(isset($this->c[mb_ord($char)])) {
                $pixels = $this->c[mb_ord($char)];
                // We know the rows and the pixels, so we can calculate the columns.
                $columns = count($pixels) / self::ROWS;
                $column = 0;
                // Adjust the width of the image with accordance to the width of the character.
                $this->width += $columns * $this->size + $this->size;

                // Loop through the pixels of the character.
                foreach($pixels as $pixel) {
                    // If we reached the last column of the character, go to a new line.
                    if ($column === $columns) {
                        $column = 0;
                        $this->x -= $this->size * $columns;
                        $this->y += $this->size;
                    }

                    // If the pixel is "true", paint it.
                    if($pixel === 1) {
                        // Brand it.
                        $draw->setFillColor(new ImagickPixel($this->deJade()));
                        $x2 = $this->x + $this->size + mt_rand(-1, 2);
                        $y2 = $this->y + $this->size + mt_rand(-1, 2);
                        $draw->rectangle($this->x, $this->y, $x2, $y2);
                        // Counted.
                        $this->ones++;
                    } elseif($pixel === 0) {
                        // Also count zeros.
                        $this->zeros++;
                    }

                    ++$column;
                    // For every pixel, move one column to the right.
                    $this->x += $this->size;
                }

                // Once a character is done painting, also move one column to the right. This creates letter spacing.
                $this->x += $this->size;
                // Reset the top position for a character that might follow.
                $this->y -= $this->size * 8;
            }
        }

        $this->im = new Imagick;
        $this->im->newImage($this->width, $this->height, new ImagickPixel(self::WHITE));
        $this->im->setImageFormat($this->type);

        $draw->setFillColor(new ImagickPixel('#000000'));
        $x2 = $this->x + $this->size + mt_rand(-1, 2);
        $y2 = $this->y + $this->size + mt_rand(-1, 2);
        $draw->rectangle($this->x, $this->y, $x2, $y2);

        $this->im->drawImage($draw);

        $seconds = 60;

        for($i = 0; $i < $seconds; $i++) {
            $frame = new Imagick;
            $frame->newImage($this->width, $this->height, new ImagickPixel(self::WHITE));
            $frame->setImageDelay(100);
            $frame->setImageFormat($this->type);

            $color = 'rgb(' . mt_rand(0, 255) . ', ' . mt_rand(0, 255) . ', ' . mt_rand(0, 255) . ')';

            $draw->setFillColor(new ImagickPixel($color));
            $x2 = $this->x + $this->size + mt_rand(-1, 2);
            $y2 = $this->y + $this->size + mt_rand(-1, 2);
            $draw->rectangle($this->x, $this->y, $x2, $y2);

            $frame->drawImage($draw);

            $this->im->addImage($frame);

            $frame->destroy();

            $this->x = $this->size * 15;
            $this->y = $this->size;
        }
    }

    public function __toString()
    {
        return date($this->format);
    }

    public function deJade()
    {
        $r = mt_rand(0, 127);
        $g = mt_rand(127, 255);
        $b = mt_rand(0, 191);

        $color = "rgba($r, $g, $b, .5)";

        return $color;
    }

    public function display()
    {
        header('Content-Type: ' . $this->im->getImageMimeType());

        echo $this->im->getImagesBlob();
    }
}