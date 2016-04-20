<?php
use fruity\Parser;
use fruity\Reader;

/*
 * Include the composer autoloader.
 * Note that in the composer.json file, we have defined autoloading for our apps namespace too
 */
require __DIR__ . '/vendor/autoload.php';

try {
    $htmlReader = new Reader();
    $htmlReader->readFile();

    $parser = new Parser($htmlReader->getContent());
    $products = $parser->lookForProducts()->getProducts();

    if (count($products) > 0 ){
        $output = new \fruity\JsonProductOutput();
        echo "\n\n";
        echo $output->render($products);
        echo "\n\n";
    } else {
        echo "\n\n No products found.";
    }

} catch (Exception $e) {
    echo "\n\nSomething went wrong processing your request. Details follow (if available).\n";
    echo "\nError message: \n==============\n" . $e->getMessage();
    echo "\n\nError code: \n===========\n" . $e->getCode();
}

echo "\n\n - Program execution finished -\n\n";
