<?php

namespace fruity;

use DOMDocument;
use DOMXPath;

class Parser
{
    /**
     * @var string
     */
    private $html = '';

    /**
     * @var array of Product objects
     */
    private $products = array();

    public function __construct($html)
    {
        // production code would perform some validation on input, possibly by calling a setter
        $this->html = $html;
    }

    /**
     * Parse the HTML string given when constructed and look for products. Any found products will be added to an
     * internal array and can later retrieved by calling getProducts()
     *
     * @return $this
     */
    public function lookForProducts()
    {
        // load the HTML into a DOMDocument
        $dom = $this->loadTheHTML();

        // look for all elements where we expect to find the products
        $finder = new DOMXPath($dom);
        $prods = $finder->query('//div[@class="product "]');

        foreach ($prods as $prod) {
            $title = '';
            $link = '';
            $price = 0.0;

            /* @var \DOMElement $prod */
            $aEl = $prod->getElementsByTagName('a');
            if ($aEl->length == 0) {
                continue;
            }

            $nameEl = $aEl->item(0);
            $title = trim($nameEl->nodeValue);
            $link = $nameEl->getAttribute('href');

            $price = $this->lookForThePrice($prod);

            if ($title !== '' && $link !== '' && $price > 0) {
                $this->addProduct($title, $link, $price);
            }
        }

        return $this;
    }

    public function lookForDescription()
    {
        $desc = '<DECSRIPTION NOT FOUND>';

        // load the HTML into a DOMDocument
        $dom = $this->loadTheHTML();

        // look for all elements where we expect to find the products
        $finder = new DOMXPath($dom);
        $elements = $finder->query('//h3[@class="productDataItemHeader"]');
        foreach ($elements as $element) {
            /* @var \DOMElement $element */
            if ($element->nodeValue === 'Description') {
                $div = $element->nextSibling;
                $div = $div->nextSibling;
                $aEl = $div->getElementsByTagName('p');
                if ($aEl->length == 0) {
                    continue;
                }

                $desc = trim($aEl->item(0)->nodeValue);
            }
        }

        return $desc;
    }

    /**
     * Return array of \Product objects, should only call this after a lookForProducts() call
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    private function loadTheHTML()
    {
        $this->products = array();
        $dom = new DOMDocument();
        $dom->validateOnParse = false;
        libxml_use_internal_errors(true); // suppress PHP warnings from parse of html
        $dom->loadHTML($this->html);
        libxml_clear_errors();  // not interested in errors & warnings so clear to save memory
        return $dom;
    }

    private function lookForThePrice(\DOMElement $prod)
    {
        $price = 0.0;
        $els = $prod->getElementsByTagName('p');

        foreach($els as $p) {
            /* @var \DOMElement $p */
            $class = $p->getAttribute('class');
            if ($class === 'pricePerUnit') {
                $raw = $p->nodeValue;

                /*
                 * Use regex to get the decimal price from the string. Note that live site uses different prefix to the
                 * price than the version for this tech test, so not including "£" or "$pound" in regex so can work
                 * against either source.
                 */
                $priceFound = preg_match('/([0-9]+[\.]*[0-9]*)/', $raw, $matches);
                if ($priceFound) {
                    $price = $matches[1];
                    if ($price > 0) {
                        return $price;
                    }
                }
            }
        }

        return $price; // Will return zero price here, calling code to handle if required.
    }

    /**
     * @return Product
     */
    protected function createProduct()
    {
        return new Product();
    }

    private function addProduct($title, $link, $price)
    {
        $product = $this->createProduct();
        $product->setTitle($title);
        $product->setLink($link);
        $product->setUnitPrice($price);

        $product->findDescriptionText();

        $this->products[] = $product;
    }
}