<?php declare(strict_types=1);
namespace Money;

/**
 * Proposed inteface for handlin currencies
 */
interface CurrencyHandler
{
    public function parseCurrencyForOperations(): int;
    
    public function parseCurrencyForDisplay(): string;

    /**
     * Depending on the implementation could just use
     * standard math library with given rules (e.g. PHP_ROUND_HALF_UP)
     * or use more complicated `bcmath` library for calculations.
     */
    public function manipulateCurrency(): int;
}
