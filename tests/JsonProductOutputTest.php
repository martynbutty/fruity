<?php

use fruity\Product;

class JsonProductOutputTest extends PHPUnit_Framework_TestCase
{

    public function testRender()
    {
        $product = new Product();
        $product->setTitle('test');
        $product->setDesc('test');
        $product->setPageSize(10);
        $product->setUnitPrice(2.55);

        $testData = array($product);

        $item['title'] = $product->getTitle();
        $item['size'] = $product->getBytesReadInkb();
        $item['unit_price'] = $product->getUnitPrice();
        $item['description'] = $product->getDesc();

        $expectedArray = array('results' => array($item), 'total' => "2.55");
        $expectedJSON = json_encode($expectedArray);

        $jsonOut = new \fruity\JsonProductOutput();
        $out = $jsonOut->render($testData);

        $this->assertSame($expectedJSON, $out);
    }
}
