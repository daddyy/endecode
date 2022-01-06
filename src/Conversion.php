<?php
namespace daddyy\Endecode {

    /**
     * Static class that delegates instructions to a 
     * @author fiala.pvl@gmail.com
     */
    class Conversion
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
         * explode input and return array of measure and number
         * @param  string $mixes [description]
         * @return  array   $result     Array with value and measure
         */
        public function getUnitAndVolume(string $mixed): array {
            $result = array('value' => (float)$mixed);
            preg_match("/[[:alpha:]]+$/", $mixed, $m);
            $result['measure'] = reset($m);
            return $result;
        }

        /**
         * Converts array|\stlClass to json string
         * @param     array|\stlClass     $mixed     Input to be encoded
         * @param     array               $config    Array of options to be applied while encoding
         * @return    self                           Instance of self
         */
        public function init(string $mixed, array $config): self
        {
            $result = false;
            $unitVolume = $this->getUnitAndVolume($mixed);
            if ($unitVolume['value']) {
                $measure = new Conversion\Measure;
                $measure->init($unitVolume['value'], $unitVolume['measure']);
                $result = $measure->getResult($config);
            }
            $this->setMe($mixed, $config, $result);
            return $this;
        }

        /**
         * @return array|\stdClass
         */
        public function getResult()
        {
            return $this->result;
        }

        /**
         * @param array|\stdClass $result
         *
         * @return self
         */
        public function setResult($result)
        {
            $this->result = $result;

            return $this;
        }

        /**
         * @return string
         */
        public function getSource()
        {
            return $this->source;
        }

        /**
         * @param string $source
         *
         * @return self
         */
        public function setSource($source)
        {
            $this->source = $source;

            return $this;
        }

        /**
         * @return array
         */
        public function getConfig()
        {
            return $this->config;
        }

        /**
         * @param array $config
         *
         * @return self
         */
        public function setConfig(array $config)
        {
            $this->config = $config;

            return $this;
        }
    }

}
