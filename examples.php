<?php declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Basket\Shipping\StandardShipping;
use Basket\Offers\RedOffer;

/**
 * Example Array with products. 
 * 'produc' and 'code' are of type `string`
 * 'price' is type 'integer' (in cents)
 */
$products = [
    [
        'product' =>'Red Widget',
        'code' =>'R01',
        'price' => 3295,
    ],
    [
        'product' =>'Blue Widget',
        'code' =>'B01',
        'price' => 795,
    ],
    [
        'product' =>'Green Widget',
        'code' =>'G01',
        'price' => 2495,
    ],
];



/**
 * Examples of usage
 */

/**
 * Instantiate Basket, add items, but do not initialise, ask for basket contents
 */
try {
    echo "------- Example 1 ------- \n";
    $shopping_basket = new Basket\ShoppingBasket();
    $return_value = $shopping_basket->add("R01")->add("B01")->getBasket();
    print_r($return_value);
} catch (\Exception $e) {
    echo($e->getMessage());
} finally {
    $shopping_basket = null;
}

/**
 * Instantiate Basket, fully initialise, add items, and get totals
 */
try {
    echo "------- Example 2 ------- \n";
    $shopping_basket = new Basket\ShoppingBasket();
    $shopping_basket->init(product_catalogue: $products, shipping_rates: new StandardShipping(), offers: new RedOffer());
    $return_value = $shopping_basket->add("R01")->add("R01")->getTotal();
    //reformat value for display
    echo '$'.fdiv($return_value, 100) . "\n";
} catch (\Exception $e) {
    echo($e->getMessage());
} finally {
    $shopping_basket = null;
}



/**
 * Instantiate Basket, DO NOT initialise, add items, get totals, and see it complain.
 */
try {
    echo "------- Example 3 ------- \n";
    $shopping_basket = new Basket\ShoppingBasket();
    $return_value = $shopping_basket->add("R01")->add("R01")->getTotal();
} catch (\Exception $e) {
    echo($e->getMessage());
} finally {
    $shopping_basket = null;
}
