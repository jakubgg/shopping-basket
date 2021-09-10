<?php declare(strict_types=1);
namespace Tests;

use Basket\Shipping\StandardShipping;
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
    
    private $test_product = [
        [
            'product' =>'Red Widget',
            'code' =>'R01',
            'price' => 2500,
        ],
    ];

    protected function setUp(): void
    {
        $this->shopping_basket = new ShoppingBasket();
    }

    protected function tearDown(): void
    {
    }

    public function testAddMethodReturnsInstanceOfShoppingBasket(): void
    {
        $this->shopping_basket = new ShoppingBasket();
        $this->assertInstanceOf(ShoppingBasket::class, $this->shopping_basket->add($this->valid_basket_value));
    }

    public function testAddMethodAddsToItemsInBasket(): void
    {
        $this->shopping_basket->add($this->valid_basket_value);
        $this->assertContains($this->valid_basket_value, $this->shopping_basket->getBasket());
    }

    public function testBasketDetectsIllegalItem(): void
    {
        $this->expectExceptionMessage('Product code not found in the products_catalogue.');
        $this->shopping_basket->add($this->invalid_basket_value)->getBasketValue();
    }

    public function testBasketValueReturnsCorrectValueWithChaining(): void
    {
        $shipping_stub = $this->createStub(\Basket\Shipping\Shipping::class);
        $this->shopping_basket->init(product_catalogue: $this->products, shipping_rates: $shipping_stub, offers: null);
        $this->assertEquals(0, $this->shopping_basket->getBasketValue());
        $this->assertEquals(4090, $this->shopping_basket->add('R01')->add('B01')->getBasketValue());
    }

    public function testBasketValueReturnsCorrectValue(): void
    {
        $shipping_stub = $this->createStub(\Basket\Shipping\Shipping::class);
        $this->shopping_basket->init(product_catalogue: $this->products, shipping_rates: $shipping_stub, offers: null);
        $this->assertEquals(0, $this->shopping_basket->getBasketValue());
        $this->assertEquals(3295, $this->shopping_basket->add('R01')->getBasketValue());
        $this->assertEquals(4090, $this->shopping_basket->add('R01')->add('B01')->getBasketValue());
        $this->assertEquals(6585, $this->shopping_basket->add('G01')->getBasketValue());
    }

    public function testInitFailedToSet(): void
    {
        $this->expectExceptionMessage('Basket not properly initialised. Missing Products/Shipping rates');
        $this->shopping_basket->init(product_catalogue: null, shipping_rates: null, offers: null);
    }

    public function testInitSetSuccesfully(): void
    {
        $shipping_stub = $this->createStub(\Basket\Shipping\Shipping::class);
        $offer_stub = $this->createStub(\Basket\Offers\Offer::class);

        $this->assertInstanceOf(
            ShoppingBasket::class,
            $this->shopping_basket->init(
                product_catalogue: $this->products,
                shipping_rates: $shipping_stub,
                offers: $offer_stub
            )
        );
    }

    public function testItemsBasketReturnedCorrectly(): void
    {
        $this->shopping_basket->add('R01');
        $this->shopping_basket->add('B01');
        $this->shopping_basket->add('G01');
        $this->assertEquals(['R01','B01','G01'], $this->shopping_basket->getBasket());
    }

    public function testShippingRatesBelow50(): void
    {
        $this->shopping_basket->init(product_catalogue: $this->test_product, shipping_rates: new StandardShipping, offers: null);
        $this->shopping_basket->add('R01');
        $this->assertEquals(495, $this->shopping_basket->getDelivery());
    }
    // getDelivery
    // getTotal
    // getTotalWithOffer
}
