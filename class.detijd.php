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
    private $size = 12;
    private $c = [];
    private $text = '12:34';
    private $ones = 0;
    private $zeros = 0;
    private $second = 0;
    private $time = 0;

    public function __construct($settings = [])
    {
        // Set default timezone. See https://www.php.net/manual/en/timezones.europe.php.
        date_default_timezone_set('Europe/Amsterdam');

        $this->time = time();
        $this->text = date('H:i:s', $this->time);
        $this->second = idate('s', $this->time);

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
        $this->width = $this->size;

        // Start a basic drawing context, even though we don't know the actual width yet.
        $draw = new ImagickDraw();
        $draw->setViewbox(0, 0, $this->width, $this->height);
        $draw->setStrokeWidth(0);

        $length = mb_strlen($this->text);

        for($i = 0; $i < $length; ++$i)
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
        $this->im->setImageDelay(100);

        $this->im->drawImage($draw);

        $seconds = 60;
        $next_second = $this->second === 59 ? 0 : $this->second + 1;
        $seconds_left = $seconds - $next_second;

        for($i = $this->second; $i < $seconds; $i++) {
            $this->x = $this->size * 21;
            $this->y = $this->size;

            $second = str_pad($i, 2, '0', STR_PAD_LEFT);

            $frame = new Imagick;
            $frame->newImage($this->width, $this->height, new ImagickPixel(self::WHITE));
            $frame->setImageDelay(100);
            $frame->setImageFormat($this->type);

            $draw->setFillColor(new ImagickPixel('#ffffff'));
            $x2 = $this->x + $this->size * 8;
            $y2 = $this->y + $this->size * 8;
            $draw->rectangle($this->x, $this->y, $x2, $y2);

            // Loop through the two digits of the seconds.
            for($j = 0; $j < 2; ++$j)
            {
                // Isolate a character from the text string.
                $char = mb_substr($second, $j, 1);

                // If we know this character, draw it.
                if(isset($this->c[mb_ord($char)])) {
                    $pixels = $this->c[mb_ord($char)];
                    // We know the rows and the pixels, so we can calculate the columns.
                    $columns = count($pixels) / self::ROWS;
                    $column = 0;

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

            $frame->drawImage($draw);

            $this->im->addImage($frame);

            $frame->clear();
        }

        if($seconds_left > 0) {
            for($i = 0; $i < $seconds_left; $i++) {
                $this->x = $this->size * 21;
                $this->y = $this->size;

                $second = str_pad($i, 2, '0', STR_PAD_LEFT);

                $frame = new Imagick;
                $frame->newImage($this->width, $this->height, new ImagickPixel(self::WHITE));
                $frame->setImageDelay(100);
                $frame->setImageFormat($this->type);

                $draw->setFillColor(new ImagickPixel('#ffffff'));
                $x2 = $this->x + $this->size * 8;
                $y2 = $this->y + $this->size * 8;
                $draw->rectangle($this->x, $this->y, $x2, $y2);

                // Loop through the two digits of the seconds.
                for($j = 0; $j < 2; ++$j)
                {
                    // Isolate a character from the text string.
                    $char = mb_substr($second, $j, 1);

                    // If we know this character, draw it.
                    if(isset($this->c[mb_ord($char)])) {
                        $pixels = $this->c[mb_ord($char)];
                        // We know the rows and the pixels, so we can calculate the columns.
                        $columns = count($pixels) / self::ROWS;
                        $column = 0;

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

                $frame->drawImage($draw);

                $this->im->addImage($frame);

                $frame->clear();
            }
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