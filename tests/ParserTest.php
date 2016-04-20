<?php

class ParserTest extends PHPUnit_Framework_TestCase
{

    public static $html;

    public static function setUpBeforeClass()
    {
        /*
         * Originally provided with a link to live Sainsbury's web. This stopped returning <body> content because
         * of JavaScript on the page, so downloaded the source from a browser and stored locally for unit tests.
         * Was later supplied a different link to the product page (http://hiring-tests.s3-website....), which works
         * without JS. Keeping live site HTML src here for tests too just for extra coverage.
         */
        //static::$html = file_get_contents('mainSearchPage.html');
        static::$html = file_get_contents('newPageSource.html');
    }

    public function testlookForProducts()
    {
        $mockProd = $this->getMockBuilder('\fruity\Product')
            ->setMethods(array('findDescriptionText'))
            ->getMock();

        // Create a mock product object as we don't need to test getting descriptions here
        $mockProd->method('findDescriptionText')->willReturn('test');

        // only need to mock createProduct so that we can use a mock product object
        $parser = $this->getMockBuilder('\fruity\Parser')
            ->setConstructorArgs(array(static::$html))
            ->setMethods(array('createProduct'))
            ->getMock();

        // Get Parser class to use our mock product object
        $parser->method('createProduct')->willReturn($mockProd);

        $parser->lookForProducts();
        $actual = $parser->getProducts();
        $this->assertSame(7, count($actual));
    }

    public function testPArseDescription()
    {
        $testFile = file_get_contents('productPage.html');
        $parser = new \fruity\Parser($testFile);
        $expectedDesc = 'Kiwi';
        $actualDesc = $parser->lookForDescription();
        $this->assertSame($expectedDesc, $actualDesc);
    }

}
