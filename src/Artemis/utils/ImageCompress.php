<?php

/*
 *  Copyright (C) 2019 Anıl Mısırlıoğlu
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace Artemis\utils;

class ImageCompress{

    public function resizeImage(string $file, int $h = Config::INSTAGRAM_PP_SIZES, int $w = Config::INSTAGRAM_PP_SIZES, $crop = false) : ImageCompress{
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if($crop){
            if($width > $height){
                $width = ceil($width - ($width * abs($r - $w / $h)));
            }else{
                $height = ceil($height - ($height * abs($r - $w / $h)));
            }
            $newWidth = $w;
            $newHeight = $h;
        }else{
            if($w / $h > $r){
                $newWidth = $h * $r;
                $newHeight = $h;
            }else{
                $newHeight = $w / $r;
                $newWidth = $w;
            }
        }

        $src = imagecreatefrompng($file);
        $destination = imagecreatetruecolor($newWidth, $newWidth);

        imagecopyresampled($destination, $src, 0, 0 ,0 ,0, $newWidth, $newHeight, $width, $height);
        imagepng($destination, dirname($file, 1) . DIRECTORY_SEPARATOR . 'image.png');
        imagedestroy($destination);
        imagedestroy($src);

        return $this;
    }

    public function writeOnImage(
        string $file,
        string $text,
        string $font = Config::TEXT_FONT,
        float $fontSize = Config::TEXT_FONT_SIZE,
        int $padding = Config::TEXT_PADDING,
        string $color = Config::TEXT_COLOR
    ) : ImageCompress{
        list($width, $height) = getimagesize($file);

        $image = imagecreatefrompng($file);

        $textRows = $this->getTextRowsFromText($fontSize, $font, $text, $width - ($padding * 2));

        for($i = 0; $i < count($textRows); $i++){
            imagettfbbox($fontSize, 0, $font, $textRows[$i]);
            $text_width = $this->getTextWidth($fontSize, $font, $textRows[$i]);
            $text_height = $this->getMaxTextHeight($fontSize, $font, $textRows) * 3;

            $position_center = ceil(($width - $text_width) / 2);

            $test = (count($textRows) - $i) - ceil(count($textRows) / 2);
            $position_middle = ceil(($height - ($text_height * $test)) / 2);

            $textColor = imagecolorallocate($image, ...Utils::hexadecimalToRGB($color));
            $strokeColor = imagecolorallocate($image, 220, 20, 60);
            $this->imageTtfStrokeText(
                $image,
                $fontSize,
                0,
                $position_center,
                $position_middle,
                $textColor,
                $strokeColor, // Gray
                $font,
                $textRows[$i],
                2
            );
        }

        imagepng($image, dirname($file, 1) . DIRECTORY_SEPARATOR . 'image.png');
        imagedestroy($image);

        return $this;
    }

    public function jpgConvertToPng(string $file, string $fileName = null) : ImageCompress{
        imagepng(imagecreatefromstring(file_get_contents($file)), dirname($file, 1) . DIRECTORY_SEPARATOR . ($fileName == null ? 'image.png' : $fileName . '.png'));

        return $this;
    }

    public function drawCircleOnImage(string $file, int $thickness = Config::IMAGE_THICKNESS) : ImageCompress{
        list($width, $height) = getimagesize($file);

        $image = imagecreatefrompng($file);
        $ellipseColor = imagecolorallocate($image, ...Utils::hexadecimalToRGB(Config::HEX_COLORS[mt_rand(0, count(Config::HEX_COLORS) - 1)]));

        imagesetthickness($image, 2);

        for($i = $thickness; $i > 0; $i--)
            imagearc($image, $width / 2, $height / 2, $width - $i, $height - $i,  0, 360, $ellipseColor);

        imagepng($image, dirname($file, 1) . DIRECTORY_SEPARATOR . 'image.png');
        imagedestroy($image);

        return $this;
    }

    private function imageTtfStrokeText(
        &$image,
        int $size,
        int $angle,
        int $x,
        int $y,
        &$textColor,
        &$strokeColor,
        string $fontFile,
        string $text,
        $px
    ) : array{
        for($c1 = ($x - abs($px)); $c1 <= ($x + abs($px)); $c1++)
            for($c2 = ($y - abs($px)); $c2 <= ($y + abs($px)); $c2++)
                imagettftext($image, $size, $angle, $c1, $c2, $strokeColor, $fontFile, $text);

        return imagettftext($image, $size, $angle, $x, $y, $textColor, $fontFile, $text);
    }

    private function getTextWidth(float $fontSize, string $font, string $text) : float{
        $line_box = imagettfbbox($fontSize, 0, $font, $text);
        return ceil($line_box[0] + $line_box[2]);
    }

    private function getTextHeight(float $fontSize, string $font, string $text) : float{
        $line_box = imagettfbbox($fontSize, 0, $font, $text);
        return ceil($line_box[1] - $line_box[7]);
    }

    private function getMaxTextHeight(float $fontSize, string $font, array $textArray) : float{
        $maxHeight = 0;
        for($i = 0; $i < count($textArray); $i++){
            $height = $this->getTextHeight($fontSize, $font, $textArray[$i]);
            if($height > $maxHeight)
                $maxHeight = $height;
        }

        return $maxHeight;
    }

    private function getTextRowsFromText(float $fontSize, string $font, string $text, float $maxWidth) : array{
        $text = str_replace("\n", "\n ", $text);
        $text = str_replace("\\n", "\n ", $text);
        $words = explode(" ", $text);

        $rows = [];
        $tmpRow = "";
        for($i = 0; $i < count($words); $i++){
            if($i == count($words) -1){
                $rows[] = $tmpRow.$words[$i];
                break;
            }

            if($this->getTextWidth($fontSize, $font, $tmpRow.$words[$i]) > $maxWidth){
                $rows[] = $tmpRow;
                $tmpRow = "";
            }elseif($this->stringEndsWith($tmpRow, "\n ")){
                $tmpRow = str_replace("\n ", "", $tmpRow);
                $rows[] = $tmpRow;
                $tmpRow = "";
            }

            $tmpRow .= $words[$i] . " ";

        }

        return $rows;
    }

    private function stringEndsWith(string $haystack, string $needle) : bool{
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

}