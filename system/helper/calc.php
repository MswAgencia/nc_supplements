<?php

function getDiscountPercentage($price, $special)
{
  return (($price - $special) * 100) / $price;
}

/**
$price = 100
$minus = $y

$price . $y = 100.$minus
$y = 

**/
