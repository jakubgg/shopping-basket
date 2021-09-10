<?php declare(strict_types=1);

namespace Basket;

use Exception;
use Basket\Offers\Offer;
use Basket\Shipping\Shipping;

class ShoppingBasket
{
    private array $items_in_basket = [];
    private array $transient_basket = [];

    private ?int $shipping_total = null; //? not necessary


    public function __construct(
        protected ?array $product_catalogue = [],
        protected ?Shipping $shipping_rates = null,
        protected ?Offer $offers = null,
    ) {
    }

    /**
     * Allows Shopping Basket initialisation at a later time, and it's easier for testing.
     */
    public function init($product_catalogue, $shipping_rates, $offers): ShoppingBasket
    {
        $this->product_catalogue = $product_catalogue ?? [];
        $this->shipping_rates = $shipping_rates ?? null;
        $this->offers = $offers ?? null;
        
        $this->checkSetupValidity();

        return $this;
    }

    protected function fillTransientBasket(): void
    {
        $this->transient_basket = [];
        try {
            array_map(function ($item_code) {
                $index = $this->searchItemInBasket($item_code);
                $this->transient_basket[] = [
                    $this->product_catalogue[$index]['code'] => $this->product_catalogue[$index]['price']
                ];
            }, $this->items_in_basket);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function getTransientBasketValue(): int
    {
        $basket_value = 0;

        array_walk_recursive($this->transient_basket, function ($value, $code) use (&$basket_value) {
            $basket_value += $value;
        });

        return $basket_value;
    }

    protected function searchItemInBasket($item_code): int | Exception
    {
        $index = array_search($item_code, array_column($this->product_catalogue, 'code'));
        if ($index === false) {
            throw new Exception('Product code not found in the products_catalogue.');
        }

        return $index;
    }
    
    protected function checkSetupValidity() : bool | Exception
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
    
    public function getDelivery(): int
    {
        $this->fillTransientBasket();
        $shipping = $this->shipping_rates->getShipping($this->getTransientBasketValue());
        return $shipping;
    }

    public function getBasketValue(): int
    {
        $this->fillTransientBasket();
        return $this->getTransientBasketValue();
    }

    public function getBasket(): array
    {
        return $this->items_in_basket;
    }

    public function getTotal(?Offer $offer = null): int | Exception
    {
        if (is_null($this->offers) && is_null($offer)) {
            throw new Exception('No offers provided');
        }

        $this->checkSetupValidity();
        $this->fillTransientBasket();
        $this->transient_basket = $this->offers->applyOffer($this->transient_basket);
        $basket_value = $this->getTransientBasketValue();
        $delivery = $this->getDelivery();
        
        return $basket_value + $delivery;
    }
}
