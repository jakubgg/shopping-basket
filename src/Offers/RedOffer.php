<?php declare(strict_types=1);

namespace Basket\Offers;

class RedOffer implements Offer
{
    
    public function applyOffer(array $basket): array
    {
         return $this->applyRules($basket);
    }

    private function applyRules($basket): array
    {
        $state = 0;
        $basket_with_offer = [];
        
        array_walk_recursive($basket, function ($value, $code) use (&$state, &$basket_with_offer) {
            if ($code == 'R01') {
                $state += 1;
            }
            if ($state == 2) {
                $value = intval(round($value / 2));
            }
            $basket_with_offer[] = [$code => $value];
        });
        
        return $basket_with_offer;
    }
}
