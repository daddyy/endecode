<?php
namespace daddyy\Endecode;
use LaLit\Array2XML;
use LaLit\XML2Array;

class Tests {
	private static $default = array(
		'base64' => 'This is an encoded string',
		'english' => 'hello world',
		'czech' => 'ahoj světe',
		'next_more' => array(
			'latin' => 'salve mundi',
			'spanish' => 'hola mundo',
		),
	);
	private static $tests = array(
		'xml' => null,
		'csv' => null,
		'json' => null,
		'serialize' => null,
		'query' => null,
		'base64' => null,
	);

    public static function run($inputs = array()) {
    	$result = array();
    	foreach ($inputs as $key => $value) {
    		if ($this::getTest($key)) {
				self::$tests[$key] = $value;
    		}
    	}
    	$tests = self::$tests;
    	foreach ($tests as $test => $source) {
    		$source = self::getData($source, $test);
	    	$testResult['decode'] = Endecode::decode($source, $test);
	    	$testResult['encode'] = Endecode::encode($testResult['decode'], $test);
    		$testResult = array(
				'source' => htmlentities($source),
				'result' => $testResult,
    		);
			echo "<pre>\n============================= start " . $test . " =============================";
    		if (function_exists('dump')) {
    			dump($testResult);
    		} else {
    			print_r($testResult);
    		}
			echo "============================= end " . $test . " =============================\n</pre>";
    	}
    }
    
    private static function getData($mixed, $type) {
    	$result = false;
		if ($mixed == false) {
			if ($type == 'base64') {
				$result = base64_encode(self::$default['base64']);
			} else {
    			$result = Endecode::encode(self::$default, $type);
			}
    	} elseif (filter_var($mixed, FILTER_VALIDATE_URL) !== false) {
    		$result = file_get_contents($mixed);
    	} elseif (is_string($mixed)) {
    		$result = $mixed;
    	}
    	return $result;
    }

	private static function getTest($test) {
		if (isset(self::$tests[$test])) {
			$result = self::$tests[$test];
		} else {
			throw new Exception("Test with name " . $test . " not exists");
		}
		return $result;
	}
}