<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CaptchaController extends Controller
{
    public function generate()
    {
        $code = strtoupper(Str::random(5));
        session(['captcha_code' => $code]);

        $image = imagecreatetruecolor(120, 40);
        $bg = imagecolorallocate($image, 240, 240, 240); // Light gray
        $text_color = imagecolorallocate($image, 24, 30, 75); // Hostoo Dark Blue
        $line_color = imagecolorallocate($image, 223, 105, 81); // Hostoo Orange

        imagefilledrectangle($image, 0, 0, 120, 40, $bg);

        // Add some noise lines
        for ($i = 0; $i < 5; $i++) {
            imageline($image, 0, rand() % 40, 120, rand() % 40, $line_color);
        }

        // Add dots
        for ($i = 0; $i < 50; $i++) {
            imagesetpixel($image, rand() % 120, rand() % 40, $text_color);
        }

        // Write text (using built-in font for simplicity, or we can load a ttf if needed)
        // Using imagestring (built-in font 5 is the largest)
        $x = 35;
        for($i = 0; $i < 5; $i++) {
            imagestring($image, 5, $x, rand(10, 15), $code[$i], $text_color);
            $x += 12;
        }

        header('Content-type: image/png');
        imagepng($image);
        imagedestroy($image);
    }
}
