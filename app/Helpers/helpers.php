<?php

if (!function_exists('formatCurrencyIndian')) {
    /**
     * Format number to Indian National Format (Lakhs/Crores)
     * Example: 1480300 -> 14,80,300
     * 
     * @param float|int $number
     * @param int $decimal
     * @return string
     */
    function formatCurrencyIndian($number, $decimal = 0)
    {
        if (is_null($number)) return '0';
        
        $number = (float)$number;
        
        // Handle decimals
        $parts = explode('.', (string)round($number, $decimal));
        $integer = $parts[0];
        $fraction = isset($parts[1]) ? $parts[1] : '';
        
        // Handle negatives
        $negative = false;
        if (str_starts_with($integer, '-')) {
            $negative = true;
            $integer = substr($integer, 1);
        }
        
        // Indian formatting logic: last 3 digits, then groups of 2
        $len = strlen($integer);
        if ($len <= 3) {
            $formatted = $integer;
        } else {
            $lastThree = substr($integer, -3);
            $restUnits = substr($integer, 0, -3);
            
            // Group the rest by 2
            $restUnits = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits);
            $formatted = $restUnits . "," . $lastThree;
        }
        
        if ($decimal > 0) {
            $fraction = str_pad($fraction, $decimal, '0', STR_PAD_RIGHT);
            $formatted .= '.' . $fraction;
        }
        
        return $negative ? '-' . $formatted : $formatted;
    }
}
