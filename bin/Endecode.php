<?php
namespace daddyy\Endecode {

    /**
     * Static class that delegates instructions to a \daddyy\Endecode\(Encode|Decode)
     */
    class Endecode
    {
        /**
         * Constructs decoder/encoder object and calls the right method on it and returns the instance
         * @param  array|object|string                                      $input        input to be processed
         * @param  string                                                   $type         [description]
         * @param  string                                                   $direction    [description]
         * @param  array                                                    $config       array of options for the decoder
         * @return \daddyy\Endecode\Encode|daddyy\Endecode\Decode                  instance of encoder/decoder
         */
        public static function convert($input, string $type, string $direction, array $config = [])
        {
            $class    = '\daddyy\Endecode\\' . ucfirst($direction);
            $instance = new $class;
            $call     = 'init' . ucfirst($type);
            $instance->{$call}($input, $config);
            return $instance;
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
            $result = Util::toCharset($result, $config);

            return $result;
        }

        /**
         * Shortcut for decoding a string to an associative array
         * @param     string    $input     string to be decoded
         * @param     string    $type      input format
         * @param     array     $config    array of options for the decoder
         * @return    array                An associative array | object decoded from input string
         */
        public static function decode( ? string $input, string $type, array $config = []) //mixed

        {
            $input    = is_null($input) ? 0 : $input;
            $input    = Util::fromCharset($input, $config);
            $instance = self::convert($input, $type, 'decode', $config);
            try {
                $result = $instance->getResult();
            } catch (Exception $e) {
                print_r($e->getMessage());
            }

            return $result;
        }

        ///**
        // * Base function with common instruction for $this::encode/decode
        // * @param     array|object                                             $mixed
        // * @param     string                                                   $type
        // * @param     string                                                   $direction
        // * @return    daddyy\Endecode\Encode | daddyy\Endecode\Decode    depends on direction
        // */
        //public static function convert($input, string $type, string $direction, array $config = array())
        //{
        //    $instance = self::init($input, $type, $direction, $config);
        //    $result   = $instance;
        //    return $result;
        //}

    }

}
