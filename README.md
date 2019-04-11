# endecode
- is a simple decoder and encoder

## encodes
- from string to json, base64, serialize, query string, XML (LaLit\XML2Array)

## decodes
- from json, serialize, base64, query string, XML to string (LaLit\Array2XML)

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