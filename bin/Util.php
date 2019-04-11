<?php
namespace daddyy\Endecode {

    class Util
    {
        /**
         * @param  array  $strings
         * @param  string $delimiter
         * @return array
         */
        public static function explode2array(array $strings, string $delimiter = '.'): array
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

        /**
         * @param  array  $mixed
         * @param  string $joiner
         * @return array
         */
        public static function join2array(array $mixed, string $joiner): array
        {
            $flatArray = array();
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

        /**
         * @param  mixed $mixed
         * @return array
         */
        public static function toArray($mixed): array
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

        /**
         * @param  string  $string
         * @return boolean
         */
        public static function isJson(string $string): bool
        {
            $result = false;
            json_decode($string);
            $result = (json_last_error() == JSON_ERROR_NONE);
            return $result;
        }

        /**
         * @param  string $string
         * @param  array  $config
         * @return string
         */
        public static function charset(string $string, array $config = array()): string
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

        /**
         * @param  string $string
         * @param  string $charset
         * @return string
         */
        public static function toCharset(string $string, string $charset): string
        {
            return $string;
        }
        /**
         * @param  string $string
         * @param  string $charset
         * @return string
         */
        public static function fromCharset(string $string, string $charset): string
        {
            return $string;
        }
    }
}
