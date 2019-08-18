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

trait Internet{

    public function downloadRandomImage() : void{
        file_put_contents(
            __DIR__ . '/../assets/images/image.jpg',
            ($content = file_get_contents(sprintf(
                Config::IMAGE_API_URL,
                mt_rand(0, 1085) /* Total image count*/,
                Config::INSTAGRAM_PP_SIZES,
                Config::INSTAGRAM_PP_SIZES
            )))
        );

        !$content ? $this->downloadRandomImage() : Terminal::log(Terminal::AQUA . 'API \'den yeni resim başarıyla indirildi.', IMAGE_API);
    }

}