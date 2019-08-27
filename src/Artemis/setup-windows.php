<?php

namespace Artemis{

    function error(string $error) : void{
        echo "[HATA] $error";
    }

    if(!stristr(PHP_OS, 'WIN')){
        error('Bu setup sadece Windows içindir.');
        exit(1);
    }

    define('Artemis\PATH', dirname(__FILE__, 3) . DIRECTORY_SEPARATOR);
    define('Artemis\SETUP_CACHE_PATH', PATH . 'setup.txt');

    $setupStatus = @file_get_contents(SETUP_CACHE_PATH);
    if(is_string($setupStatus)){
        echo 'Kurulumu zaten yapmışsınız. Tekrar yapmak ister misiniz? [Y/N]: ';
        $reply = trim(fgets(STDIN));
        if($reply == 'Y'){
            echo 'Yeniden kurulum başlatılıyor.' . PHP_EOL;

            unlink(SETUP_CACHE_PATH);
            exec('start setup.cmd');
        }else{
            echo 'Yeniden kurulum iptal edildi.' . PHP_EOL;
        }
        exit(1);
    }

    $parse = parse_ini_file(__DIR__ . '/../../setup.ini');

    if(exec('git') == '')
        error('Kurulum için git yazılımı gerekmektedir. Bu adresten git yazılını indirebilirsiniz; https://git-scm.com/downloads');

    define('Artemis\DEFAULT_COMPOSER_PATH', $parse['composer.path'] ?? '');

    $pathEnv = explode(';', getenv('path'));
    $searchResult = false;
    foreach($pathEnv as $str){
        $exp = explode('\\', $str);
        if(strtolower(end($exp)) == 'composer')
            $searchResult = $str;
    }

    if(!$searchResult){
        if(
            is_dir(DEFAULT_COMPOSER_PATH) and
            (is_file(DEFAULT_COMPOSER_PATH . DIRECTORY_SEPARATOR . 'composer.bat') or is_file(DEFAULT_COMPOSER_PATH . DIRECTORY_SEPARATOR . 'composer.cmd')) and
            is_file(DEFAULT_INCLUDE_PATH . DIRECTORY_SEPARATOR . 'composer.phar')
        ){
            exec("cd C:\\Windows\\System32");
            exec("setx path \"%path%;" . DEFAULT_COMPOSER_PATH . "\"");
        }else{
            error('Bilgisayarınızda composer yazılımı bulunamadı. Bu adresten composer yazılımını indirebilirsiniz; https://getcomposer.org/download/');
            exit(1);
        }
    }

    exec("composer install -d " . PATH);

    $pemPath = null;
    if(file_exists(($binPath = PATH . 'bin/php'))){
        $open = opendir($binPath);
        while(($read = readdir($open))){
            if(is_file(($filePath = $binPath . DIRECTORY_SEPARATOR . $read))){
                if(basename($filePath) == 'cacert.pem')
                    $pemPath = $filePath;
            }
        }
    }else{
        if(is_file(($manuelPemPath = $parse['curl.cainfo.path'])))
            $pemPath = $manuelPemPath;
    }

    if($pemPath != null){
        ini_set('curl.cainfo', $pemPath);
    }else{
        error('CURL sertifikasyon dosyası bulunamadı. Güvenli şekilde veri transferi yapabilmek için bu sertifikayı indirin ve setup.ini dosyasına koyun. İndirmek için; https://curl.haxx.se/docs/caextract.html');
        exit(1);
    }

    static $extensions = [
        'gd',
        'json',
        'curl',
        'mbstring',
        'exif',
        'zlib'
    ];
    foreach($extensions as $ext){
        if(!@extension_loaded($ext)){
            error('Yazılımın çalışması için gereken eklentiler bulunamadı. Bulunamayan eklenti; ' . $ext);
            exit(1);
        }
    }

    echo PHP_EOL . 'Yazılım kurulumu başarıyla gerçekleşti! Sistem başlatılıyor...' . PHP_EOL;
    @file_put_contents(SETUP_CACHE_PATH, 'true');
    exec('start ' . PATH . 'start.cmd');
    exit(1);

}