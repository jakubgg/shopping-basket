<?php declare(strict_types=1);

namespace Basket\Shipping;

interface Shipping
{
    public function getShipping(int $price): int;
}
