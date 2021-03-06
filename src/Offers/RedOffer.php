<?php declare(strict_types=1);

namespace Basket\Offers;

class RedOffer implements Offer
{
    
    /**
     * Provides logic for 'Buy one Red get second half price' offer.
     *
     * @param array $basket Basket with items to analyse and test for offer.
     *
     * @return array Basket with prices adjusted accordingto the offer rules.
     */
    private function applyRules(array $basket): array
    {
        $state = 0;
        $basket_with_offer = [];
        
        array_walk_recursive($basket, function ($value, $code) use (&$state, &$basket_with_offer) {
            if ($code == 'R01') {
                $state += 1;
            }
            if ($state == 2) {
                // round() by default uses 'rounds halves up'
                $value = intval(round($value / 2));
            }
            $basket_with_offer[] = [$code => $value];
        });
        
        return $basket_with_offer;
    }

    public function applyOffer(array $basket): array
    {
         return $this->applyRules($basket);
    }
}
