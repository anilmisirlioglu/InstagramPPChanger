<?php

namespace Artemis\utils;

class Timezone{

    public function get(){
        return ini_get('date.timezone');
    }

    public function __construct(string $timezone){
        if($timezone != "auto"){
            foreach(timezone_abbreviations_list() as $zones){
                foreach($zones as $zone){
                    if($zone['timezone_id'] == $timezone and
                        date_default_timezone_set($timezone)
                    ){
                        ini_set('date.timezone', $timezone);
                        return;
                    }
                }
            }
        }

        self::detectAutoTimezone();
    }

    private function detectAutoTimezone() : void{
        if($response = file_get_contents("http://ip-api.com/json") and
            $geolocation_data = json_decode($response, true) and
            $geolocation_data['status'] != 'fail' and
            date_default_timezone_set(($zone = $geolocation_data['timezone']))
        ){
            ini_set('date.timezone', $zone);
            return;
        }

        if(($zone = self::detectSystemTimezone() and
            date_default_timezone_set($zone))
        ){
            ini_set('date.timezone', $zone);
            return;
        }
    }

    /**
     * @return bool|string
     */
    public static function detectSystemTimezone(){
        switch(PHP_OS_FAMILY){
            case 'Windows':
                $regex = '/(UTC)(\+*\-*\d*\d*\:*\d*\d*)/';
                exec("wmic timezone get Caption", $output);
                $string = trim(implode("\n", $output));
                //Detect the Time Zone string
                preg_match($regex, $string, $matches);
                if(!isset($matches[2])){
                    return false;
                }
                $offset = $matches[2];
                if($offset == ""){
                    return "UTC";
                }
                return self::parseOffset($offset);
            case 'Linux':
                // Ubuntu / Debian.
                if(file_exists('/etc/timezone')){
                    $data = file_get_contents('/etc/timezone');
                    if($data){
                        return trim($data);
                    }
                }
                // RHEL / CentOS
                if(file_exists('/etc/sysconfig/clock')){
                    $data = parse_ini_file('/etc/sysconfig/clock');
                    if(!empty($data['ZONE'])){
                        return trim($data['ZONE']);
                    }
                }
                // Portable method for incompatible linux distributions.
                $offset = trim(exec('date +%:z'));
                if($offset == "+00:00"){
                    return "UTC";
                }
                return self::parseOffset($offset);
            case 'mac':
                if(is_link('/etc/localtime')){
                    $filename = readlink('/etc/localtime');
                    if(strpos($filename, '/usr/share/zoneinfo/') === 0){
                        $timezone = substr($filename, 20);
                        return trim($timezone);
                    }
                }
                return false;
            default:
                return false;
        }
    }

    private static function parseOffset($offset){
        if(strpos($offset, '-') !== false){
            $negative_offset = true;
            $offset = str_replace('-', '', $offset);
        }else{
            if(strpos($offset, '+') !== false){
                $negative_offset = false;
                $offset = str_replace('+', '', $offset);
            }else{
                return false;
            }
        }
        $parsed = date_parse($offset);
        $offset = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];

        if($negative_offset == true){
            $offset = -abs($offset);
        }

        foreach(timezone_abbreviations_list() as $zones){
            foreach($zones as $timezone){
                if($timezone['offset'] == $offset){
                    return $timezone['timezone_id'];
                }
            }
        }
        return false;
    }


}