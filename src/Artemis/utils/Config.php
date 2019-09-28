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

class Config{

    /** @var int */
    public const INSTAGRAM_PP_SIZES = 320; // 320px x 320px

    /** @var int */
    public const IMAGE_THICKNESS = 12;

    /** @var float */
    public const TEXT_FONT_SIZE = 30.0;
    /** @var int */
    public const TEXT_PADDING = 1;
    /** @var string */
    public const TEXT_FONT = __DIR__ . '/../assets/fonts/ubuntu-bold.ttf';
    /** @var string */
    public const TEXT_COLOR = 'FFFFFF';

    /** @var string */
    public const IMAGE_API_URL = 'https://picsum.photos/id/%d/%d/%d?blur=2';

    /** @var string */
    public const USER_IMAGES_DIR = __DIR__ . '/../assets/images/user-images';
    /** @var string */
    public const CONFIG_JSON_DIR = __DIR__ . '/../../../config.json';

    /** @var string */
    public const DEFAULT_TIMEZONE = 'Europe/Istanbul';

    /**
     * Renk kodları hexadecimal formatında olmak zorundadır.
     *
     * @var array
     */
    public const HEX_COLORS = [
        'A0522D',
        '8470FF',
        '00BFFF',
        '00CED1',
        '00FF00',
        'FFFF00',
        'CD661D',
        'B22222',
        'FF3030',
        'DC143C',
        'FF1493',
        'CD1076',
        'FF34B3',
        'FF00FF',
        'B452CD',
        'C71585',
        '7D26CD'
    ];
}