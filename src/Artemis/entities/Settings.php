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

namespace Artemis\entities;

use Artemis\utils\Config;
use Artemis\utils\Terminal;
use Exception;

class Settings{

    /** @var User */
    private $user;
    /** @var string */
    private $timezone;

    public function __construct(){
        try{
            Terminal::log(Terminal::GREEN . 'Ayar dosyası okunuyor.', SYSTEM);

            $json = json_decode(file_get_contents(Config::CONFIG_JSON_DIR));
            $this->user = new User($json->username, $json->password);
            $this->timezone = $json->timezone;

            Terminal::log(Terminal::GREEN . 'Ayar dosyası başarıyla okundu.', SYSTEM);
        }catch(Exception $exception){
            die('Olmaması gereken bir hata ile karşılaştık. Hata: ' . $exception->getMessage());
        }
    }

    public function getUser() : User{
        return $this->user;
    }

    public function getTimezone() : string{
        return $this->timezone;
    }

}