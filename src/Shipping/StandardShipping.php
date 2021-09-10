<?php declare(strict_types=1);

namespace Basket\Shipping;

use Exception;

class StandardShipping implements Shipping
{

    /**
     * Provides logic for Standard Shipping rules.
     *
     * @param int $price Price value in cents.
     *
     * @return int | Exception Returns value of shipping as integer in cents,
     * or throws an Exception if cannot match the price.
     */
    private function shippingRules(int $price): int | Exception
    {
        if ($price === 0) {
            return 0;
        } elseif ($price < 5000) {
            return 495;
        } elseif ($price < 9000) {
            return 295;
        } elseif ($price >= 9000) {
            return 0;
        } else {
            throw new Exception('Price is in incorrect format.');
        }
    }

    public function getShipping(int $price): int
    {
        return $this->shippingRules($price);
    }
}
