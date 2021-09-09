<?php declare(strict_types=1);

namespace Basket;

use Exception;

class ShoppingBasket
{
    private static ?ShoppingBasket $instance = null;
    private array $items_in_basket = [];


    /**
     * Avoids multiple Shopping baskets during the lifecycle of the script
     */
    public static function getInstance(): ShoppingBasket
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct(
        protected ?array $product_catalogue = [],
        protected ?object $shipping_rates = null,
        protected ?object $offers = null,
    ) {
    }
  

    public function add(string $product_code): ShoppingBasket
    {
        return $this;
    }
    
    public function getTotal(): int
    {
        // TODO
        return 0;
    }
}
