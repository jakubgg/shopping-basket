<?php declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';


//Instantiate Shopping Basket
$testBasket = Basket\ShoppingBasket::getInstance();

try {
    // Add something to the basket and ask for total
    $testBasket->add("R01")->getTotal();
} catch (Exception $e) {
    echo($e->getMessage());
}
