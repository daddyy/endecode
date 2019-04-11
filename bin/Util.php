<?php
namespace daddyy\Endecode {

    class Util
    {
        public static function explode2array($strings = array(), $delimiter = '.')
        {
            $result = array();
            foreach ($strings as $key => $value) {
                $temp = &$result;
                $path = explode($delimiter, $key);
                foreach ($path as $i => $v) {
                    $temp = &$temp[$v];
                }

                $temp = $value;
            }
            return $result;
        }

        public static function join2array($mixed, $joiner)
        {
            foreach ($mixed as $key => $value) {
                if (is_object($value) || is_array($value)) {
                    $aTemp = self::join2array($value, $joiner);
                    foreach ($aTemp as $key2 => $temp) {
                        $tempResult[$key . $joiner . $key2] = $temp;
                    }
                    $flatArray = array_merge($flatArray, $tempResult);
                } else {
                    $flatArray[$key] = $value;
                }
            }
            return $flatArray;
        }

        public static function toArray($mixed)
        {
            if (is_object($mixed)) {
                $mixed = (array) $mixed;
            }
            if (is_array($mixed)) {
                foreach ($mixed as $key => $value) {
                    unset($mixed[$key]);
                    $key         = str_ireplace(array('*', "\x00"), '', $key);
                    $mixed[$key] = self::toArray($value);
                }
            }
            return $mixed;
        }

        public static function isJson($string)
        {
            $result = false;
            if (is_string($string)) {
                json_decode($string);
                $result = (json_last_error() == JSON_ERROR_NONE);
            }
            return $result;
        }

        public static function charset($mixed, $config)
        {
            $result = $mixed;
            if (is_string($mixed)) {
                if (isset($config['from_charset'])) {
                    $result = self::fromCharset($mixed, $config['from_charset']);
                } elseif (isset($config['to_charset'])) {
                    $result = self::toCharset($mixed, $config['to_charset']);
                }
            }
            return $result;
        }

        public static function toCharset($string, $charset)
        {
            return $string;
        }

        public static function fromCharset($string, $charset)
        {
            return $string;
        }
    }
}
