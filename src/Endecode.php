<?php
namespace daddyy\Endecode {

    /**
     * Static class that delegates instructions to a \daddyy\Endecode\(Encode|Decode)
     * @author fiala.pvl@gmail.com
     */
    class Endecode
    {
        /**
         * Constructs decoder/encoder object and calls the right method on it and returns the instance
         * @param  array|object|string   $input        input to be processed
         * @param  string                $type         json|xml|base64|query|csv|serialize
         * @param  string                $direction    encode|decode
         * @param  array                 $config       array of options for the decoder|encoder
         * @return \daddyy\Endecode\Encode|daddyy\Endecode\Decode instance of encoder/decoder
         */
        public static function convert($input, string $type, string $direction, array $config = [])
        {
            $class    = '\daddyy\Endecode\\' . ucfirst($direction);
            $instance = new $class;
            $call     = 'init' . ucfirst($type);
            if (method_exists($instance, $call) == false) {
                $call     = 'init';
            }
            $instance->{$call}($input, $config);
            return $instance;
        }


        /**
         * method for conversion fom metric unit to metric unit
         * @param     string|number   $input    input to be converted
         * @param     array           $config   array of options for the decoder
         * @return    string                    string converted to desired output format from input
         */
        public static function conversion($input, array $config = []): string
        {
            $config = is_array($config) ? $config : array($config);
            $config = is_numeric(key($config)) && count($config) == 1 ? array('unit' => reset($config)) : $config;
            $instance = self::endecode($input, '', 'conversion', $config);
            try {
                $result = $instance->getResult();
            } catch (\Exception $e) {
                print_r($e->getMessage());
            }
            $result = Util::toCharset($result, $config);

            return $result;
        }


        /**
         * Shortcut for decoding an array or object to requested string format
         * @param     array|object    $input    input to be encoded
         * @param     string          $type     output format
         * @param     array           $config   array of options for the decoder
         * @return    string                    string converted to desired output format from input
         */
        public static function encode($input, string $type, array $config = []): string
        {
            $instance = self::convert($input, $type, 'encode', $config);
            try {
                $result = $instance->getResult();
            } catch (Exception $e) {
                print_r($e->getMessage());
            }
            $charset  = isset($config['toCharset']) ? $config['toCharset'] : false;
            if ($charset) {
                $result = Util::toCharset($result, $config);
            }

            return $result;
        }

        /**
         * Shortcut for decoding a string to an associative array
         * @param     string    $input     string to be decoded
         * @param     string    $type      input format
         * @param     array     $config    array of options for the decoder
         * @return    mixed                An associative array | object decoded from input string
         */
        public static function decode( ? string $input, string $type, array $config = [])
        {
            $input    = is_null($input) ? 0 : $input;
            $charset  = isset($config['fromCharset']) ? $config['fromCharset'] : false;
            if ($charset) {
                $input    = Util::fromCharset($input, $charset);
            }
            $instance = self::convert($input, $type, 'decode', $config);
            try {
                $result = $instance->getResult();
            } catch (Exception $e) {
                print_r($e->getMessage());
            }

            return $result;
        }
    }
}
