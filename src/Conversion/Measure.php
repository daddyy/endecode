<?php
namespace daddyy\Endecode\Conversion {

    /**
     * Static class that delegates instructions
     * @author fiala.pvl@gmail.com
     */
    class Measure
    {
        private $selected;
        public $baseMeasures = array(
            'length' => array(
                'unit'     => array(
                    'p'       => -12,
                    'n'       => -9,
                    'µ'       => -6,
                    'm'       => -3,
                    'c'       => -2,
                    'd'       => -1,
                    'default' => 1,
                    'h'       => 2,
                    'k'       => 3,
                    'M'       => 6,
                ),
                'unit_aliases' => [
                    'piko'       => 'p',
                    'nano'       => 'n',
                    'micro'      => 'µ',
                    'mili'       => 'm',
                    'centi'      => 'c',
                    'deci'       => 'd',
                    'hekto'      => 'h',
                    'kilo'       => 'k',
                    'mega'       => 'm',
                ],
                'measures' => array(
                    'metric' => array(
                        'base'     => 'meter',
                        'shortcut' => 'm',
                        'aliases'  => array(
                            'meters',
                            'metre',
                            'metr',
                            'metres',
                        ),
                    ),
                    'weight' => array(
                        'base'     => 'gram',
                        'shortcut' => 'g',
                        'aliases'  => array(
                            'gram',
                            'grams',
                        ),
                    ),
                    'inch'   => array(
                        'base'     => 'inch',
                        'shortcut' => '\'',
                        'aliases'  => array(
                            'inches',
                            'coul',
                        ),
                    ),
                ),
            ),
        );

        public $convertTable = array(
            'metric' => array(
                'inch' => 0.0254,
            ),
        );
        public function init($inputValue, $inputMeasure)
        {
            $result = null;
            $result = $this->getMeasure($inputMeasure, $inputValue);
            if (is_null($result)) {
                throw new \Exception("Unit (" . $inputMeasure . ") in our measure table was not found");
            }
            $this->selected = $result;
        }

        public function getMeasure($inputMeasure, $inputValue = 1)
        {
            $result = null;
            $tries = [];
            foreach ($this->baseMeasures as $baseMeasure => $base) {
                foreach ($base['measures'] as $measure => $value) {
                    foreach ($base['unit'] as $unit => $exponent) {
                        $measureTest = $inputMeasure;
                        $tries[] = $value['base'];
                        $aaa = false;
                        $tries[] = $value['shortcut'];
                        if ($inputMeasure == false) {
                            $measureTest = $value['shortcut'];
                        }

                        $tries = array_merge($tries, $value['aliases']);
                        $tries = array_unique($tries);

                        $testUnit = substr($inputMeasure, strlen($inputMeasure) - strlen($value['base']));
                        if (strtolower($testUnit) == strtolower($value['base'])) {
                            $testMeasure = substr($inputMeasure, 0, strlen($inputMeasure) - strlen($value['base']));
                            $testMeasure = isset($this->baseMeasures['length']['unit_aliases'][$testMeasure]) ? $this->baseMeasures['length']['unit_aliases'][$testMeasure] : false;
                            if ($testMeasure) {
                                $measureTest = $testMeasure . $value['shortcut'];
                            }
                        }
                        foreach ($tries as $try) {
                            $test = $unit . $try;
                            if (($test == $measureTest && $unit && $try)) {
                                $result['base_value'] = $inputValue * ($exponent != 1 ? pow(10, $exponent) : 1);
                                $result['unit']       = $base['unit'];
                                $result['measure']    = $value;
                                $result['type']       = $measure;
                                break 4;
                            }
                        }
                    }
                }
            }

            return $result;
        }

        public function getConvertValue($type, $measure)
        {
            $result = empty($this->convertTable[$type][$measure]) == false ? $this->convertTable[$type][$measure] : false;
            return $result;
        }

        public function getResult(array $config = array())
        {
            if ($config) {
                $value    = $this->selected['base_value'];
                $exponent = 1;
                if (isset($config['unit'])) {
                    $unit       = rtrim($config['unit'], $this->selected['measure']['shortcut']);
                    $toExponent = isset($this->selected['unit'][$unit]) ? $this->selected['unit'][$unit] : null;
                    if ($toExponent) {
                        $exponent = $toExponent;
                    } else {
                        throw new \Exception("Exponent in our measure table was not found");
                    }
                    if (is_null($toExponent) == false) {
                        $value   = $value * pow(10, (-1 * ($exponent)));
                        $measure = $exponent == 1 ? $this->selected['base_value'] : ($unit . $this->selected['measure']['shortcut']);
                    }
                } elseif (isset($config['convert'])) {
                    $convertTo = $this->getMeasure($config['convert']);
                    $convertValue = $this->getConvertValue($this->selected['type'], $convertTo['measure']['base']);
                    $value = $this->selected['base_value'] / $convertValue;
                    $measure = $convertTo['measure']['shortcut'];
                }
            } else {
                $value   = $this->selected['base_value'];
                $measure = ($this->selected['measure']['shortcut']);
            }
            $result = $value . (isset($config['measure']) && $config['measure'] ?  $measure : null);
            return $result;
        }
    }
}
