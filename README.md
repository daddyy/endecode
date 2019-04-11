# endecode
- is a simple decoder and encoder

## encodes
- from string to json, serialize, query string, XML (LaLit\XML2Array)

## decodes
- from json, serialize, query string, XML to string (LaLit\Array2XML)

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