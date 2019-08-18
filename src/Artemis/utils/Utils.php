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

class Utils{

    public static function hexadecimalToRGB(string $hex) : array{
        $parse = intval(str_replace('#', '', $hex), 16);
        return [
            ($parse >> 16) & 0xFF,
            ($parse >> 8) & 0xFF,
            ($parse >> 0) & 0xFF
        ];
    }

}