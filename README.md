# endecode
- is a simple decoder and encoder

## usage
```
#json
$result = Endecode::encode($string, 'json');
#mixed to serialize
$string = Endecode::decode($mixed, 'serialize');
#encode object
$encodeObject = Endecode::convert($mixed, 'serialize', 'encode');
```