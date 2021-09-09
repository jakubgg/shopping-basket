<?php declare(strict_types=1);
namespace Tests;

use Basket\ShoppingBasket;
use Exception;
use PHPUnit\Framework\TestCase;

final class ShoppingBasketTest extends TestCase
{
    private $shopping_basket;
    private $invalid_basket_value = 'TEST';
    private $valid_basket_value = 'R01';
    private $products = [
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

    protected function setUp(): void
    {

        $this->shopping_basket = ShoppingBasket::getInstance();
    }

    public function testShoppingBasketUniqueness()
    {
        $firstCall = ShoppingBasket::getInstance();
        $secondCall = ShoppingBasket::getInstance();

        $this->assertInstanceOf(ShoppingBasket::class, $firstCall);
        $this->assertSame($firstCall, $secondCall);
    }

    public function testAddMethodReturnsInstanceOfShoppingBasket(): void
    {
        $this->assertInstanceOf(ShoppingBasket::class, $this->shopping_basket->add($this->valid_basket_value));
    }

    public function testAddMethodAddsToItemsInBasket(): void
    {
        $this->assertContains($this->valid_basket_value, $this->shopping_basket->getBasket());
    }

    public function testInitFailedToSet(): void
    {
        $this->expectException(Exception::class);
        $this->shopping_basket->init(product_catalogue: null, shipping_rates: null, offers: null);
    }

    public function testInitProperlySet(): void
    {
        $shipping_stub = $this->createStub(\Basket\Shipping\Shipping::class);
        $offer_stub = $this->createStub(\Basket\Offers\Offer::class);

        $this->assertInstanceOf(ShoppingBasket::class, $this->shopping_basket->init(product_catalogue: $this->products, shipping_rates: $shipping_stub, offers: $offer_stub));
    }


    // getDelivery
    // getBasketValue
    // getBasket
    // getTotal
    // getTotalWithOffer
}
