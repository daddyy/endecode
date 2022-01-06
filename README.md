# endecode
- is a simple decoder, encoder, conversion class

## encodes + decodes + conversion
- from <> to json, uu, bin2hex, hex2bin, utf8, base64, serialize, query string, XML (LaLit\XML2Array), units to units

## usage
```
#json
$result = Endecode::encode($string, 'json');
```
```
#mixed to serialize
$result = Endecode::decode($mixed, 'serialize');
```
```
#mixed
$mixed = '12,8cm';
$config = []; //['convert' => 'inch'];
$result = Endecode::conversion($mixed, $config);
# will return 0.128 m
```
```
#encode object
$encodeObject = Endecode::convert($mixed, 'serialize', 'encode');
$result = $encodeObject->getResult();
```