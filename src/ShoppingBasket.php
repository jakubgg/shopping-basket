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
  
        /**
     * Initialising the basket via getInstance looks ugly,
     * so instead we have a method to initialse the basket.
     * This also allow us to init basket later in the process if necessary
     */
    public function init($product_catalogue, $shipping_rates, $offers): ShoppingBasket
    {
        $this->product_catalogue = $product_catalogue ?? [];
        $this->shipping_rates = $shipping_rates ?? null;
        $this->offers = $offers ?? null;
        
        $this->checkBasketValidity();

        return $this;
    }

    protected function checkBasketValidity() : bool | Exception
    {
        if (empty($this->product_catalogue) || is_null($this->shipping_rates)) {
            throw new Exception('Basket not properly initialised. Missing Products/Shipping rates');
        }

        return true;
    }

    public function add(string $product_code): ShoppingBasket
    {
        array_push($this->items_in_basket, $product_code);
        return $this;
    }
    
    public function getBasket(): array
    {
        return $this->items_in_basket;
    }
    
    public function getTotal(): int
    {
        // TODO
        return 0;
    }
}
