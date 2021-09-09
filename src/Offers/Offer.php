<?php declare(strict_types=1);

namespace Basket\Offers;

interface Offer
{
    public function applyOffer(array $basket): array;
}
