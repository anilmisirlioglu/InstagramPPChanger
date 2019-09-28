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

define('IMAGE_PROCESSOR', Terminal::GOLD . 'Görüntü İşleyici' . Terminal::GRAY . ' > ');
define('IMAGE_API', Terminal::YELLOW . 'Image' . Terminal::WHITE . 'API' . Terminal::GRAY . ' > ');
define('SYSTEM', Terminal::AQUA . 'Sistem' . Terminal::GRAY . ' > ');
define('API', Terminal::LIGHT_PURPLE . 'Instagram' . Terminal::WHITE . 'API' . Terminal::GRAY . ' > ');

class Terminal{

    public const ESCAPE = "\xc2\xa7"; //§

    public const BLACK = self::ESCAPE . "0";
    public const DARK_BLUE = self::ESCAPE . "1";
    public const DARK_GREEN = self::ESCAPE . "2";
    public const DARK_AQUA = self::ESCAPE . "3";
    public const DARK_RED = self::ESCAPE . "4";
    public const DARK_PURPLE = self::ESCAPE . "5";
    public const GOLD = self::ESCAPE . "6";
    public const GRAY = self::ESCAPE . "7";
    public const DARK_GRAY = self::ESCAPE . "8";
    public const BLUE = self::ESCAPE . "9";
    public const GREEN = self::ESCAPE . "a";
    public const AQUA = self::ESCAPE . "b";
    public const RED = self::ESCAPE . "c";
    public const LIGHT_PURPLE = self::ESCAPE . "d";
    public const YELLOW = self::ESCAPE . "e";
    public const WHITE = self::ESCAPE . "f";

    public const OBFUSCATED = self::ESCAPE . "k";
    public const BOLD = self::ESCAPE . "l";
    public const STRIKETHROUGH = self::ESCAPE . "m";
    public const UNDERLINE = self::ESCAPE . "n";
    public const ITALIC = self::ESCAPE . "o";
    public const RESET = self::ESCAPE . "r";

    public static $FORMAT_BOLD = "";
    public static $FORMAT_OBFUSCATED = "";
    public static $FORMAT_ITALIC = "";
    public static $FORMAT_UNDERLINE = "";
    public static $FORMAT_STRIKETHROUGH = "";
    public static $FORMAT_RESET = "";
    public static $COLOR_BLACK = "";
    public static $COLOR_DARK_BLUE = "";
    public static $COLOR_DARK_GREEN = "";
    public static $COLOR_DARK_AQUA = "";
    public static $COLOR_DARK_RED = "";
    public static $COLOR_PURPLE = "";
    public static $COLOR_GOLD = "";
    public static $COLOR_GRAY = "";
    public static $COLOR_DARK_GRAY = "";
    public static $COLOR_BLUE = "";
    public static $COLOR_GREEN = "";
    public static $COLOR_AQUA = "";
    public static $COLOR_RED = "";
    public static $COLOR_LIGHT_PURPLE = "";
    public static $COLOR_YELLOW = "";
    public static $COLOR_WHITE = "";

    private static $formattingCodes = null;

    public static function log(string $text, string $prefix = SYSTEM) : void{
        $text = Terminal::DARK_GRAY . '[' . Terminal::YELLOW . date('H:i:s') . Terminal::DARK_GRAY . '] ' . $prefix . $text . Terminal::RESET . PHP_EOL;
        echo ((self::hasFormattingCodes() ? self::toANSI($text) : self::clean($text)));
    }

    public static function hasFormattingCodes(){
        if(self::$formattingCodes === null){
            $opts = getopt("", ["enable-ansi", "disable-ansi"]);
            if(isset($opts["disable-ansi"])){
                self::$formattingCodes = false;
            }else{
                $stdout = fopen("php://stdout", "w");
                self::$formattingCodes = (isset($opts["enable-ansi"]) or ( //user explicitly told us to enable ANSI
                        stream_isatty($stdout) and //STDOUT isn't being piped
                        (
                            getenv('TERM') !== false or //Console says it supports colours
                            (function_exists('sapi_windows_vt100_support') and sapi_windows_vt100_support($stdout)) //we're on windows and have vt100 support
                        )
                    ));
                fclose($stdout);
            }
            self::init();
        }
        return self::$formattingCodes;
    }

    public static function init() : void{
        if(self::hasFormattingCodes()){
            switch(PHP_OS_FAMILY){
                case "Linux":
                case "Mac":
                case "BSD":
                    self::getEscapeCodes();
                    return;
                case "Windows":
                case "Android":
                    self::getFallbackEscapeCodes();
                    return;
            }
        }
    }

    protected static function getEscapeCodes(){
        self::$FORMAT_BOLD = `tput bold`;
        self::$FORMAT_OBFUSCATED = `tput smacs`;
        self::$FORMAT_ITALIC = `tput sitm`;
        self::$FORMAT_UNDERLINE = `tput smul`;
        self::$FORMAT_STRIKETHROUGH = "\x1b[9m"; //`tput `;
        self::$FORMAT_RESET = `tput sgr0`;
        $colors = (int) `tput colors`;
        if($colors > 8){
            self::$COLOR_BLACK = $colors >= 256 ? `tput setaf 16` : `tput setaf 0`;
            self::$COLOR_DARK_BLUE = $colors >= 256 ? `tput setaf 19` : `tput setaf 4`;
            self::$COLOR_DARK_GREEN = $colors >= 256 ? `tput setaf 34` : `tput setaf 2`;
            self::$COLOR_DARK_AQUA = $colors >= 256 ? `tput setaf 37` : `tput setaf 6`;
            self::$COLOR_DARK_RED = $colors >= 256 ? `tput setaf 124` : `tput setaf 1`;
            self::$COLOR_PURPLE = $colors >= 256 ? `tput setaf 127` : `tput setaf 5`;
            self::$COLOR_GOLD = $colors >= 256 ? `tput setaf 214` : `tput setaf 3`;
            self::$COLOR_GRAY = $colors >= 256 ? `tput setaf 145` : `tput setaf 7`;
            self::$COLOR_DARK_GRAY = $colors >= 256 ? `tput setaf 59` : `tput setaf 8`;
            self::$COLOR_BLUE = $colors >= 256 ? `tput setaf 63` : `tput setaf 12`;
            self::$COLOR_GREEN = $colors >= 256 ? `tput setaf 83` : `tput setaf 10`;
            self::$COLOR_AQUA = $colors >= 256 ? `tput setaf 87` : `tput setaf 14`;
            self::$COLOR_RED = $colors >= 256 ? `tput setaf 203` : `tput setaf 9`;
            self::$COLOR_LIGHT_PURPLE = $colors >= 256 ? `tput setaf 207` : `tput setaf 13`;
            self::$COLOR_YELLOW = $colors >= 256 ? `tput setaf 227` : `tput setaf 11`;
            self::$COLOR_WHITE = $colors >= 256 ? `tput setaf 231` : `tput setaf 15`;
        }else{
            self::$COLOR_BLACK = self::$COLOR_DARK_GRAY = `tput setaf 0`;
            self::$COLOR_RED = self::$COLOR_DARK_RED = `tput setaf 1`;
            self::$COLOR_GREEN = self::$COLOR_DARK_GREEN = `tput setaf 2`;
            self::$COLOR_YELLOW = self::$COLOR_GOLD = `tput setaf 3`;
            self::$COLOR_BLUE = self::$COLOR_DARK_BLUE = `tput setaf 4`;
            self::$COLOR_LIGHT_PURPLE = self::$COLOR_PURPLE = `tput setaf 5`;
            self::$COLOR_AQUA = self::$COLOR_DARK_AQUA = `tput setaf 6`;
            self::$COLOR_GRAY = self::$COLOR_WHITE = `tput setaf 7`;
        }
    }

    protected static function getFallbackEscapeCodes(){
        self::$FORMAT_BOLD = "\x1b[1m";
        self::$FORMAT_OBFUSCATED = "";
        self::$FORMAT_ITALIC = "\x1b[3m";
        self::$FORMAT_UNDERLINE = "\x1b[4m";
        self::$FORMAT_STRIKETHROUGH = "\x1b[9m";
        self::$FORMAT_RESET = "\x1b[m";
        self::$COLOR_BLACK = "\x1b[0;30m";
        self::$COLOR_DARK_BLUE = "\x1b[0;34m";
        self::$COLOR_DARK_GREEN = "\x1b[0;32m";
        self::$COLOR_DARK_AQUA = "\x1b[0;36m";
        self::$COLOR_DARK_RED = "\x1b[0;30m";
        self::$COLOR_PURPLE = "\x1b[0;35m";
        self::$COLOR_GOLD = "\x1b[38;5;214m";
        self::$COLOR_GRAY = "\x1b[0;37m";
        self::$COLOR_DARK_GRAY = "\x1b[1;30m";
        self::$COLOR_BLUE = "\x1b[1;34m";
        self::$COLOR_GREEN = "\x1b[1;32m";
        self::$COLOR_AQUA = "\x1b[1;36m";
        self::$COLOR_RED = "\x1b[1;31m";
        self::$COLOR_LIGHT_PURPLE = "\x1b[1;35m";
        self::$COLOR_YELLOW = "\x1b[1;33m";
        self::$COLOR_WHITE = "\x1b[1;37m";
    }

    public static function toANSI($string) : string{
        if(!is_array($string))
            $string = self::tokenize($string);

        $newString = "";
        foreach($string as $token){
            switch($token){
                case self::BOLD:
                    $newString .= self::$FORMAT_BOLD;
                    break;
                case self::OBFUSCATED:
                    $newString .= self::$FORMAT_OBFUSCATED;
                    break;
                case self::ITALIC:
                    $newString .= self::$FORMAT_ITALIC;
                    break;
                case self::UNDERLINE:
                    $newString .= self::$FORMAT_UNDERLINE;
                    break;
                case self::STRIKETHROUGH:
                    $newString .= self::$FORMAT_STRIKETHROUGH;
                    break;
                case self::RESET:
                    $newString .= self::$FORMAT_RESET;
                    break;
                //Colors
                case self::BLACK:
                    $newString .= self::$COLOR_BLACK;
                    break;
                case self::DARK_BLUE:
                    $newString .= self::$COLOR_DARK_BLUE;
                    break;
                case self::DARK_GREEN:
                    $newString .= self::$COLOR_DARK_GREEN;
                    break;
                case self::DARK_AQUA:
                    $newString .= self::$COLOR_DARK_AQUA;
                    break;
                case self::DARK_RED:
                    $newString .= self::$COLOR_DARK_RED;
                    break;
                case self::DARK_PURPLE:
                    $newString .= self::$COLOR_PURPLE;
                    break;
                case self::GOLD:
                    $newString .= self::$COLOR_GOLD;
                    break;
                case self::GRAY:
                    $newString .= self::$COLOR_GRAY;
                    break;
                case self::DARK_GRAY:
                    $newString .= self::$COLOR_DARK_GRAY;
                    break;
                case self::BLUE:
                    $newString .= self::$COLOR_BLUE;
                    break;
                case self::GREEN:
                    $newString .= self::$COLOR_GREEN;
                    break;
                case self::AQUA:
                    $newString .= self::$COLOR_AQUA;
                    break;
                case self::RED:
                    $newString .= self::$COLOR_RED;
                    break;
                case self::LIGHT_PURPLE:
                    $newString .= self::$COLOR_LIGHT_PURPLE;
                    break;
                case self::YELLOW:
                    $newString .= self::$COLOR_YELLOW;
                    break;
                case self::WHITE:
                    $newString .= self::$COLOR_WHITE;
                    break;
                default:
                    $newString .= $token;
                    break;
            }
        }
        return $newString;
    }

    public static function tokenize(string $string) : array{
        return preg_split("/(" . self::ESCAPE . "[0-9a-fk-or])/", $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
    }

    public static function clean(string $string, bool $removeFormat = true) : string{
        if($removeFormat){
            return str_replace(self::ESCAPE, "", preg_replace(["/" . self::ESCAPE . "[0-9a-fk-or]/", "/\x1b[\\(\\][[0-9;\\[\\(]+[Bm]/"], "", $string));
        }
        return str_replace("\x1b", "", preg_replace("/\x1b[\\(\\][[0-9;\\[\\(]+[Bm]/", "", $string));
    }
}