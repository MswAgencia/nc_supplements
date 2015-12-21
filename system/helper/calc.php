<?php

function getDiscountPercentage($price, $special, $floor = true)
{
  $percentual = (($price - $special) * 100) / $price;

  if($floor)
    return floor($percentual);

  return number_format($percentual, 2, '.', '');
}
