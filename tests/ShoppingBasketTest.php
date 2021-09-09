<?php declare(strict_types=1);

namespace Tests;

use Basket\ShoppingBasket;
use PHPUnit\Framework\TestCase;

final class ShoppingBasketTest extends TestCase
{
    private $shopping_basket;

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

    
}
