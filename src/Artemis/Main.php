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

namespace Artemis;

use Artemis\entities\Image;
use Artemis\entities\Settings;
use Artemis\utils\Config;
use Artemis\utils\ImageCompress;
use Artemis\utils\Internet;
use Artemis\utils\Terminal;
use Artemis\utils\Timezone;
use Exception;
use InstagramAPI\Instagram;

class Main{

    use Internet;

    /** @var Instagram */
    private $instagram;
    /** @var Settings */
    private $settings;
    /** @var Timezone */
    private $timezone;
    /** @var ImageCompress */
    private $imageCompress;
    /** @var Image[] */
    private $images = [];

    public function __construct(){
        logSys(Terminal::GOLD . 'Uygulama başlatılıyor...', SYSTEM);

        $this->settings = new Settings;
        $this->imageCompress = new ImageCompress;
        $this->timezone = new Timezone($this->settings->getTimezone());
        $this->startInstagram();

        $this->startApp();
    }

    private function startInstagram() : void{
        logSys(Terminal::GREEN . 'Instagram başlatılıyor...', SYSTEM);

        Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
        $this->instagram = new Instagram;

        logSys(Terminal::GREEN . 'Instagram başarıyla başlatıldı.', SYSTEM);

        $this->loginInstagramAccount();
    }

    private function loginInstagramAccount() : void{
        $user = $this->settings->getUser();

        try{
            logSys(Terminal::YELLOW . 'Instagram hesabınıza giriş yapılıyor.', SYSTEM);

            $response = $this->instagram->login(
                ($username = $user->getUsername()),
                ($password = $user->getPassword())
            );

            if($response !== null and $response->isTwoFactorRequired()){
                logSys(Terminal::YELLOW . 'Hesabınızın iki adımlı doğrulaması açık. Lütfen size gelen doğrulama kodunu giriniz...', API);
                $twoFactorIdentifier = $response->getTwoFactorInfo()->getTwoFactorIdentifier();
                $verificationCode = trim(fgets(STDIN));
                $this->instagram->finishTwoFactorLogin($username, $password, $twoFactorIdentifier, $verificationCode);
            }

            logSys(Terminal::GOLD . $username . Terminal::GREEN . ' hesabına başarıyla giriş yapıldı.', API);
        }catch(Exception $exception){
            logSys(Terminal::RED . 'Bir sebepten dolayı hesabınıza giriş yapamadık. Hata: ' . Terminal::WHITE . $exception->getMessage(), API);
            exit(1);
        }

    }

    private function setupImageSys(){
        if(!$this->settings->getRandomImageOpt()){
            logSys(Terminal::GREEN . 'Görüntüler kullanıcı tarafından belirlenenler arasından çekilicek.');

            if(file_exists(Config::USER_IMAGES_DIR)){
                $open = opendir(Config::USER_IMAGES_DIR);
                while(($read = readdir($open))){
                    if(is_file(($dir = Config::USER_IMAGES_DIR . DIRECTORY_SEPARATOR . $read))){
                        $ext = pathinfo($dir)['extension'];
                        if($ext == 'jpg' or $ext = 'png')
                            $this->images[] = new Image($dir);
                    }
                }
            }

            if(count($this->images) == 0){
                logSys(Terminal::RED . 'Sistem png formatında resim bulamadı.', IMAGE_PROCESSOR);
                exit(1);
            }
        }else logSys(Terminal::GREEN . 'Görüntüler internetten rastgele çekilicek.');
    }

    private function startApp() : void{
        logSys(Terminal::DARK_PURPLE . 'Sistemin zaman dilimi ' . Terminal::WHITE . $this->timezone->get() . Terminal::DARK_PURPLE . ' olarak ayarlandı.', SYSTEM);

        $this->setupImageSys();

        sleep(60 - (time() % 60));

        $i = 0;
        static $imageFile = __DIR__ . '/assets/images/image.%s';
        while(true){
            logSys(Terminal::LIGHT_PURPLE . 'Sistem tekrardan aktif.', SYSTEM);

            $png = null;
            if(!$this->settings->getRandomImageOpt()){
                $image = $this->images[$i] ?? (function(int &$i) : Image{
                    $i = 0;
                    return $this->images[$i];
                })($i);
                $path = $image->getPath();
                if($image->getExtension() == 'jpg'){
                    logSys(Terminal::GREEN . 'Görüntü jpg formatından png formatına çevriliyor...', IMAGE_PROCESSOR);

                    $this->imageCompress->jpgConvertToPng($path, $image->getFilename());

                    logSys(Terminal::GREEN . 'Görüntü jpg formatından png formatına başarıyla çevrildi.', IMAGE_PROCESSOR);

                    unlink($path);

                    $image->setPath(str_replace('jpg', 'png', $path));

                    logSys(Terminal::RED . 'Eski görüntü verisi silindi.', SYSTEM);
                }
                file_put_contents(
                    ($png = sprintf($imageFile, 'png')),
                    file_get_contents($image->getPath())
                );
            }else{
                $this->downloadRandomImage();

                $jpg = sprintf($imageFile, 'jpg');

                logSys(Terminal::GREEN . 'Görüntü jpg formatından png formatına çevriliyor...', IMAGE_PROCESSOR);

                $this->imageCompress->jpgConvertToPng($jpg);

                logSys(Terminal::GREEN . 'Görüntü jpg formatından png formatına başarıyla çevrildi.', IMAGE_PROCESSOR);
                unlink($jpg);

                logSys(Terminal::RED . 'Eski görüntü verisi silindi.', SYSTEM);

                $png = sprintf($imageFile, 'png');
            }

            if($png != null){
                logSys(Terminal::GREEN . 'Görüntü yeniden boyutlandırıp, yeniden düzenleniyor...', IMAGE_PROCESSOR);

                $this->imageCompress
                    ->resizeImage($png)
                    ->drawCircleOnImage($png)
                    ->writeOnImage($png, date('H:i'))
                    ->writeOnImage($png, PHP_EOL . date('d.m.Y'));

                logSys(Terminal::GREEN . 'Görüntü yeniden boyutlandırıldı ve düzenlendi. Görüntü verisi kaydedildi.', IMAGE_PROCESSOR);

                try{
                    $this->instagram->account->changeProfilePicture($png);

                    logSys(Terminal::GREEN . 'Profil fotoğrafınız güncellendi.', API);
                }catch(Exception $exception){
                    logSys(Terminal::RED . 'Profil fotoğrafı bir sebepten dolayı değiştirilemedi. Program kapatılıyor. Hata: ' . Terminal::WHITE . $exception->getMessage(), API);
                }

                $sleep = 60 - (time() % 60);
                logSys(Terminal::GOLD . 'Bir dahaki güncelleme için ' . Terminal::AQUA . $sleep . Terminal::GOLD . ' saniye bekleniyor...');

                $i++;

                usleep(($sleep * 1000000) - 500000);
            }else{
                logSys(Terminal::RED . 'Sistem belirlenemeyen bir hata sebebi ile çöktü.', SYSTEM);
                exit(1);
            }
        }

    }

}