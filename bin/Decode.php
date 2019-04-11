<?php
namespace daddyy\Endecode {

    use LaLit\XML2Array;

    /**
     * A helper for decoding string and parsing it to either array or string
     * Can parse Base64, json, csv, xml, serialized php strings and querystrings
     */
    class Decode
    {
        /**
         * Result of decoding the $this->source
         * @var array|\stdClass
         */
        private $result;
        /**
         * Source string that will be decoded.
         * @var string
         */
        private $source;
        /**
         * Array of options to be applied while decoding a string
         * @var array
         */
        private $config;

        /**
         * Joint setter for $this->result, $this->source and $this->config
         * @param    mixed              $mixed     Source that was decoded
         * @param    array              $config    Array of options to be applied while decoding a string
         * @param    array|\stlClass    $result    Result of decode
         */
        private function setMe($mixed, array $config, $result): self
        {
            $this->source = $mixed;
            $this->config = $config;
            $this->result = $result;
            return $this;
        }

        /**
         * @todo
         */
        public function initBase64($mixed, array $config = []): self
        {
            $strict = isset($config['strict']) ? $config['strict'] : false;
            $result = base64_decode($mixed, $strict);
            $this->setMe($mixed, $config, $result);
            return $this;
        }

        /**
         * Decodes a string from json to array|\stlClass
         * @param     string    $mixed     Input to be decoded
         * @param     array     $config    Array of options to be applied while decoding a string
         * @return    self                 Instance of self
         */
        public function initJson(string $mixed, array $config = []): self
        {
            if (Util::isJson($mixed) == false) {
                throw new \Exception(json_last_error());
            }
            $result = json_decode($mixed, true);
            $this->setMe($mixed, $config, $result);
            return $this;
        }

        /**
         * Unserialize a string
         * @param     string    $mixed     Input to be decoded
         * @param     array     $config    Array of options to be applied while decoding a string
         * @return    self                 Instance of self
         */
        public function initSerialize(string $mixed, array $config = []): self
        {
            $result = unserialize($mixed);
            $this->setMe($mixed, $config, $result);
            return $this;
        }

        /**
         * Decodes a string from queryString to array
         * @param     string    $mixed     Input to be decoded
         * @param     array     $config    Array of options to be applied while decoding a string
         * @return    self                 Instance of self
         */
        public function initQuery(string $mixed, array $config = []): self
        {
            parse_str($mixed, $result);
            $this->setMe($mixed, $config, $result);
            return $this;
        }

        /**
         * Decodes a string from csv to array
         * @param     string    $mixed     Input to be decoded
         * @param     array     $config    Array of options to be applied while decoding a string
         * @return    self                 Instance of self
         */
        public function initCsv(string $mixed, array $config = []): self
        {
            $result          = array();
            $primary         = isset($config['primary']) ? $config['primary'] : false;
            $delimiter       = isset($config['delimiter']) ? $config['delimiter'] : ";";
            $enclosure       = isset($config['enclosure']) ? $config['enclosure'] : "'";
            $ending_line     = isset($config['ending_line']) ? $config['ending_line'] : $delimiter . PHP_EOL;
            $first_line      = isset($config['first_line']) ? $config['first_line'] : true;
            $head_line       = isset($config['head_line']) ? $config['head_line'] : array();
            $column_join     = isset($config['column_join']) ? $config['column_join'] : '.';
            $column_key_join = isset($config['column_key_join']) ? $config['column_key_join'] : ':';

            if (is_array($head_line) == false) {
                throw new \Exception("Head line has init be array");
            }
            if (is_string($delimiter) == false) {
                throw new \Exception("Delimiter has init be string");
            }
            if (is_string($enclosure) == false) {
                throw new \Exception("Enclosure has init be string");
            }

            $lines = explode($ending_line, $mixed);
            $head  = false;
            if ($first_line && empty($head_line)) {
                $head = str_getcsv(reset($lines), $delimiter, $enclosure);
                $key  = key($lines);
                unset($lines[$key]);
            }
            foreach ($lines as $key => $line) {
                $line = str_getcsv($line, $delimiter, $enclosure);
                if ($primary) {
                    $key = $line[$primary];
                }
                foreach ($head as $key2 => $value) {
                    $name       = $value ? $value : $key2;
                    $cell       = $this->parseCsvCell($line[$key2], $column_join, $column_key_join);
                    $row[$name] = $cell;
                }
                $result[$key] = $row;
            }
            $this->setMe($mixed, $config, $result);
            return $this;
        }

        /**
         * Parse csv cell if it contains notation
         * @todo parse with dotted anotation in recursion
         * @return string|array
         */
        private function parseCsvCell($cell, $column_join, $column_key_join)
        {
            return $cell;
        }

        /**
         * Decodes a string from xml to array
         * @param     string    $mixed     Input to be decoded
         * @param     array     $config    Array of options to be applied while decoding a string
         * @return    self                 Instance of self
         */
        public function initXml(string $mixed, array $config = []): self
        {
            $node_name     = isset($config['node_name']) ? $config['node_name'] : 'node_name';
            $init          = isset($config['init']) ? $config['init'] : array();
            $version       = isset($init['version']) ? $init['version'] : '1.0';
            $encoding      = isset($init['encoding']) ? $init['encoding'] : 'utf-8';
            $standalone    = isset($init['standalone']) ? $init['standalone'] : false;
            $format_output = isset($init['format_output']) ? $init['format_output'] : true;
            XML2Array::init($version, $encoding, $standalone, $format_output);
            try {
                $result = XML2Array::createArray($mixed);
            } catch (\Exception $e) {
                throw $e;
            }
            $this->setMe($mixed, $config, $result);
            return $this;
        }

        /**
         * @return string
         */
        public function getType(): string
        {
            return $this->type;
        }

        /**
         * @return array|\stdClass
         */
        public function getResult()
        {
            return $this->result;
        }

        /**
         * @return string
         */
        public function getSource(): string
        {
            return $this->source;
        }
    }

}
