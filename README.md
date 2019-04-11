# endecode
- is a simple decoder and encoder

## encodes + decodes
- from <> to json, uu, bin2hex, hex2bin, utf8, base64, serialize, query string, XML (LaLit\XML2Array)

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
#encode object
$encodeObject = Endecode::convert($mixed, 'serialize', 'encode');
$result = $encodeObject->getResult();
```