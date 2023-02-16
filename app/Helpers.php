<?php

//standard ingredient unit to usage unit conversion
function convertIngredientUnitToUsageUnit($quantity, $unit): array
{
    return match ($unit) {
        'kg' => ["quantity" => $quantity * 1000, "unit" => "g"],
        'g' => ["quantity" => "$quantity", "unit" => "g"],
        default => ["quantity" => $quantity, "unit" => $unit],
    };
}

/*Usage unit to standard ingredient unit conversion
 *this returns
*/
function convertUsageUnitToIngredientUnit($quantity, $unit): array
{
    return match ($unit) {
        'g' => ["quantity" => $quantity / 1000, "unit" => "kg"],
        'kg' => ["quantity" => "$quantity", "unit" => "kg"],
        default => ["quantity" => $quantity, "unit" => $unit],
    };
}

function formatNumber($number){
    return number_format($number,2);
}
