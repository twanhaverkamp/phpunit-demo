<?php


namespace App\Tool;

/**
 * @author Twan Haverkamp <twan@mailcampaigns.nl>
 */
class Calculator
{
    public function multiply(float $var1, float $var2): float
    {
        return $var1 * $var2;
    }

    public function divide(float $var1, float $var2): float
    {
        return $var1 / $var2;
    }
}
