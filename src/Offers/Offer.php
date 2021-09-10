<?php declare(strict_types=1);

namespace Basket\Offers;

interface Offer
{
    /**
     * Public interface for applying offer rules on the basket with items.
     *
     * @param array $basket Basket with items to analyse and test for offer.
     * 
     * @return array Basket with prices adjusted by the offer rules.
     */
    public function applyOffer(array $basket): array;
}
