<?php declare(strict_types=1);

namespace Basket;

use Exception;
use Basket\Offers\Offer;
use Basket\Shipping\Shipping;

class ShoppingBasket
{
    /**
     * Holds codes of items added,
     * these are used to count value of basket.
     */
    private array $items_in_basket = [];

    /**
     * Temporary basket for counting prices,
     * shipping, applying offers.
     */
    private array $transient_basket = [];

    public function __construct(
        protected ?array $product_catalogue = [],
        protected ?Shipping $shipping_rates = null,
        protected ?Offer $offers = null,
    ) {
    }

    /**
     * Allows Shopping Basket initialisation at a later time,
     * looks better than passing all arguments to the constructor and it's easier for testing.
     * Can be chained.
     *
     * @param ?array $product_catalogue An array of products with name, product code and price.
     * @param ?Shipping $shipping_rates An object of type 'Shipping', provides rules for shipping charges.
     * @param ?Offer $offers An object of type 'Offer', provides rules for applying Offers.
     *
     * @return ShoppingBasket
     */
    public function init($product_catalogue, $shipping_rates, $offers): ShoppingBasket
    {
        $this->product_catalogue = $product_catalogue ?? [];
        $this->shipping_rates = $shipping_rates ?? null;
        $this->offers = $offers ?? null;
        
        $this->checkSetupValidity();

        return $this;
    }

    /**
     * Create temporary basket with product codes and prices
     *
     * @return void
     */
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

    /**
     * Calculates total value of items in transient basket
     *
     * @return int Value of all items in transient basket in cents.
     */
    protected function getTransientBasketValue(): int
    {
        $basket_value = 0;

        array_walk_recursive($this->transient_basket, function ($value, $code) use (&$basket_value) {
            $basket_value += $value;
        });

        return $basket_value;
    }

    /**
     * Searches through 'products array' by 'product code'
     * and checks if the item (item code) in basket has a match there so later on we can obtain its price.
     *
     * @return int|Exception Returns integer pointing to array index of the item in product catalogue on success,
     * or raises Exception if the item does not exist in the catalogue.
     */
    protected function searchItemInBasket($item_code): int | Exception
    {
        $index = array_search($item_code, array_column($this->product_catalogue, 'code'));
        if ($index === false) {
            throw new Exception('Product code not found in the products_catalogue.');
        }

        return $index;
    }
    
    /**
     * Checks if the Basket class has been initialised with all arguments necessary for calculating totals.
     *
     * @return bool | Exception Returns `true` on success,
     * or raises Exception if there is something missing.
     */
    protected function checkSetupValidity() : bool | Exception
    {
        if (empty($this->product_catalogue) || is_null($this->shipping_rates)) {
            throw new Exception('Basket not properly initialised. Missing Products/Shipping rates');
        }

        return true;
    }

    /**
     * Adds item to the items_in_basket.
     * Can be chained.
     *
     * @return ShoppingBasket
     */
    public function add(string $product_code): ShoppingBasket
    {
        array_push($this->items_in_basket, $product_code);
        return $this;
    }
    
    /**
     * Gets the total value of items_in_basket and
     * hands over to 'Shipping' object to calculate shipping price.
     * Must be last in the chain of commands.
     *
     * @return int Value of shopping for items in cents.
     */
    public function getDelivery(): int
    {
        if ($this->transient_basket===[]) {
            $this->fillTransientBasket();
        }
        $shipping = $this->shipping_rates->getShipping($this->getTransientBasketValue());
        return $shipping;
    }

    /**
     * Recalculates transient basket and
     * obtains total value of items in it.
     * Must be last in the chain of commands.
     *
     * @return int Value of all items in transient basket in cents.
     */
    public function getBasketValue(): int
    {
        $this->fillTransientBasket();
        return $this->getTransientBasketValue();
    }

    /**
     * Returns items_in_basket
     * Must be last in the chain of commands.
     *
     * @return array
     */
    public function getBasket(): array
    {
        return $this->items_in_basket;
    }

    /**
     * Gathers all parts, smashes them together and returns grand total with offers and shipping applied.
     * Allows to inject `Offer` at the last moment, if the offer was ommited from init().
     * Must be last in the chain of commands.
     *
     * @return int Value of all items with Shipping and Offer applied, in cents.
     */
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
