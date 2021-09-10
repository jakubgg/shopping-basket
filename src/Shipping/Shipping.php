<?php declare(strict_types=1);

namespace Basket\Shipping;

interface Shipping
{
    /**
     * Calculate shipping on given price
     *
     * @param int $price Price value in cents.
     */
    public function getShipping(int $price): int;
}
