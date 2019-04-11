<?php
namespace daddyy\Endecode {

    use LaLit\Array2XML;

    /**
     * A helper for encoding entities to strings
     * Can convert into Base64, json, csv, xml, serialized php strings and querystrings
     */
    class Encode
    {
        /**
         * Result of encoding the $this->source
         * @var array|\stdClass
         */
        private $result;
        /**
         * Source string that will be encoded.
         * @var string
         */
        private $source;
        /**
         * Array of options to be applied while encoding a string
         * @var array
         */
        private $config;

        /**
         * Joint setter for $this->result, $this->source and $this->config
         * @param    mixed              $mixed     Source that was encoded
         * @param    array              $config    Array of options to be applied while encoding
         * @param    array|\stlClass    $result    Result of encode
         */
        private function setMe($mixed, $config, $result): self
        {
            $this->source = $mixed;
            $this->config = $config;
            $this->result = $result;
            return $this;
        }

        /**
         * Converts array|\stlClass to json string
         * @param     array|\stlClass     $mixed     Input to be encoded
         * @param     array               $config    Array of options to be applied while encoding
         * @return    self                           Instance of self
         */
        public function initJson($mixed, array $config): self
        {
            $result = json_encode($mixed);
            $this->setMe($mixed, $config, $result);
            return $this;
        }

        /**
         * Converts input to json serialized php string
         * @param     mixed               $mixed     Input to be encoded
         * @param     array               $config    Array of options to be applied while encoding
         * @return    self                           Instance of self
         */
        public function initSerialize($mixed, array $config): self
        {
            $result = serialize($mixed);
            $this->setMe($mixed, $config, $result);
            return $this;
        }

        /**
         * Converts array|\stlClass to queryString
         * @param     array|\stlClass     $mixed     Input to be encoded
         * @param     array               $config    Array of options to be applied while encoding
         * @return    self                           Instance of self
         */
        public function initQuery(array $mixed, array $config): self
        {
            $mixed  = http_build_query($mixed);
            $result = urldecode($mixed);
            $this->setMe($mixed, $config, $result);
            return $this;
        }

        /**
         * @param  string $mixed
         * @param  array  $config
         * @return self   Instance of self
         */
        public function initUu(string $mixed, array $config): self 
        {
            $result = convert_uuencode($mixed);
            $this->setMe($mixed, $config, $result);
            return $this;
        }

        /**
         * @param  string $mixed
         * @param  array  $config
         * @return self
         */
        public function initUf8(string $mixed, array $config): self
        {
            $result = utf8_encode($mixed);
            $this->setMe($mixed, $config, $result);
            return $this;
        }

        /**
         * Converts string to Base64 binary
         * @param     string    $mixed     Input to be encoded
         * @param     array     $config    Array of options to be applied while encoding
         * @return    self                 Instance of self
         */
        public function initBase64(string $mixed, array $config): self
        {
            $result = base64_encode($mixed);
            $this->setMe($mixed, $config, $result);
            return $this;
        }

        /**
         * Converts array|object to csv string
         * @todo      find a better way to encode multidimensional array|object
         * @param     array|object    $mixed     Input to be encoded
         * @param     array           $config    Array of options to be applied while encoding
         * @return    self                       Instance of self
         */
        public function initCsv($mixed, array $config): self
        {
            $primary     = isset($config['primary']) ? $config['primary'] : true;
            $delimiter   = isset($config['delimiter']) ? $config['delimiter'] : ";";
            $enclosure   = isset($config['enclosure']) ? $config['enclosure'] : "'";
            $ending_line = isset($config['ending_line']) ? $config['ending_line'] : $delimiter . PHP_EOL;
            $first_line  = isset($config['first_line']) ? $config['first_line'] : true;
            $head_line   = isset($config['head_line']) ? $config['head_line'] : array();
            $column_join = isset($config['column_join']) ? $config['column_join'] : '.';

            if (is_array($head_line) == false) {
                throw new \Exception("Head line has init be array");
            }
            if (is_string($delimiter) == false) {
                throw new \Exception("Delimiter has init be string");
            }
            if (is_string($enclosure) == false) {
                throw new \Exception("Enclosure has init be string");
            }
            $sizeRow = 1;
            foreach ($mixed as $key => $row) {
                $row = is_array($row) ? $row : array($row);
                ksort($row);
                if ($primary) {
                    $row = array_merge(array('primary' => $key), $row);
                }
                $row    = $this->csvRow($row, $enclosure, $column_join);
                $actual = is_array($row) ? count($row) : $sizeRow;
                if ($actual > $sizeRow) {
                    $sizeRow = $actual;
                    if ($first_line && $head_line == false) {
                        $head_line = array_keys($row);
                    }
                }
                $mixed[$key] = $row;
            }
            if ($first_line && $head_line) {
                $head_line = $this->csvRow($head_line, $enclosure, $column_join);
                $mixed     = array_merge(array($head_line), $mixed);
            }
            foreach ($mixed as $key => $values) {
                $checkCount = $sizeRow - count($values);
                if ($checkCount > 0) {
                    for ($i = 0; $i < $checkCount; $i++) {
                        $values[] = $enclosure . $enclosure;
                    }
                }
                $rows[] = join($delimiter, $values);
            }
            $result = join($ending_line, $rows) . $delimiter;
            $this->setMe($mixed, $config, $result);
            return $this;
        }

        /**
         * Created a string csv row from array
         * @todo params docs
         */
        private function csvRow($row, $enclosure, $column_join)
        {
            $result = array();
            foreach ($row as $key => $value) {
                $value = is_object($value) ? Util::toArray($value) : $value;
                if (is_array($value)) {
                    $values = Util::join2array($value, $column_join);
                    foreach ($values as $key2 => $value) {
                        $result[$key2] = $enclosure . $value . $enclosure;
                    }
                } else {
                    $value        = $value;
                    $result[$key] = $enclosure . $value . $enclosure;
                }
            }

            return $result;
        }

        /**
         * Converts array|object to xml
         * @param     array|object    $mixed     Input to be encoded
         * @param     array           $config    Array of options to be applied while encoding
         * @return    self                       Instance of self
         */
        public function initXml(array $mixed, array $config): self
        {
            $init          = isset($config['init']) ? $config['init'] : array();
            $version       = isset($init['version']) ? $init['version'] : '1.0';
            $encoding      = isset($init['encoding']) ? $init['encoding'] : 'utf-8';
            $standalone    = isset($init['standalone']) ? $init['standalone'] : false;
            $format_output = isset($init['format_output']) ? $init['format_output'] : true;
            Array2XML::init($version, $encoding, $standalone, $format_output);
            $config    = isset($config['convert']) ? $config['convert'] : array();
            $xml_root  = isset($config['xml_root']) ? $config['xml_root'] : 'xml_root';
            $node_name = isset($config['node_name']) ? $config['node_name'] : 'node_name';
            $doc_type  = isset($config['doc_type']) ? $config['doc_type'] : null;
            $mixed     = count($mixed) > 1 ? array($node_name => $mixed) : $mixed;
            try {
                $xml    = Array2XML::createXML($xml_root, $mixed, $doc_type);
                $result = $xml->saveXML();
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
         * @return mixed
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
