<?php
/**
 * Небольшой класс обёртка над XMLReader для удобной работы с потоковым чтением xml документа.
 * Преобразовывает каждый узел (имя которого передаём вторым аргументом) в ассоциативный массив
 */

class XMLWrapper {
    private static function xml2assoc($xml) {
        $tree = null;
        while($xml->read()) {
            switch ($xml->nodeType) {
                case XMLReader::END_ELEMENT: return $tree;
                case XMLReader::ELEMENT:
                    $node = array('tag' => $xml->name, 'value' => $xml->isEmptyElement ? '' : self::xml2assoc($xml));
                    if($xml->hasAttributes)
                        while($xml->moveToNextAttribute())
                            $node['attributes'][$xml->name] = $xml->value;
                    $tree[] = $node;
                    break;
                case XMLReader::TEXT:
                case XMLReader::CDATA:
                    $tree .= $xml->value;
            }
        }
        return $tree;
    }
    public static function parseXML($filePath, $containerName) {
        $reader = new XMLReader();
        $reader->open($filePath);
        while($reader->read()) {
            $container = [];
            if($reader->nodeType === XMLReader::ELEMENT && $reader->localName === $containerName) {
                if($reader->hasAttributes) {
                    while($reader->moveToNextAttribute()) {
                        $container['attributes'][$reader->name] = $reader->value;
                    }
                }
                $container['childs'] = self::xml2assoc($reader);
                var_dump($container); // на этом этапе сохраняем в базу или выводим очередной массив-контейнер куда-либо
            }
        }
    }
}


// XMLWrapper::parseXML('A.xml', "Address");