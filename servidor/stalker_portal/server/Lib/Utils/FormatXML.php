<?php

namespace Ministra\Lib\Utils;

class FormatXML extends \Ministra\Lib\Utils\Format
{
    private $xml;
    public function __construct($array)
    {
        $this->xml = new \SimpleXMLElement('<response/>');
        \array_walk_recursive($array, [$this, 'addNode']);
        $this->formatted = $this->xml->asXML();
    }
    private function addNode($value, $name)
    {
        $this->xml->addChild($name, $value);
    }
}
