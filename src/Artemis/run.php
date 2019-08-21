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

declare(strict_types=1);

namespace Artemis{

    function critical_error($message){
        echo '[ERROR] ' . $message . PHP_EOL;
    }

    define('Artemis\NAME', 'Instagram Profil Photo Updater');
    define('Artemis\MIN_PHP_VERSION', '7.2.0');

    if(PHP_INT_SIZE < 8)
        critical_error('Running ' . NAME . ' with 32-bit systems/PHP is no longer supported. Please upgrade to a 64-bit system, or use a 64-bit PHP binary if this is a 64-bit system.');

    if(php_sapi_name() !== 'cli')
        critical_error('You must run ' . NAME . ' using the CLI.');

    if(version_compare(MIN_PHP_VERSION, PHP_VERSION) > 0)
        critical_error(NAME . ' requires PHP >= ' . MIN_PHP_VERSION . ', but you have PHP ' . PHP_VERSION . '.');

    ini_set('memory_limit', '-1');

    define('Artemis\PATH', dirname(__FILE__, 3) . DIRECTORY_SEPARATOR);
    define('Artemis\COMPOSER_AUTOLOAD_PATH', PATH . 'vendor/autoload.php');

    ini_set('memory_limit', '-1');

    if(COMPOSER_AUTOLOAD_PATH !== false and is_file(COMPOSER_AUTOLOAD_PATH)){
        require_once(COMPOSER_AUTOLOAD_PATH);
    }else{
        critical_error('Composer autoloader not found at ' . COMPOSER_AUTOLOAD_PATH);
        critical_error('Please install/update Composer dependencies or use provided builds.');
        exit(1);
    }

    new Main();
}
