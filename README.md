# endecode
- is a simple decoder and encoder

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