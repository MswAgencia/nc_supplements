<?php

function getDiscountPercentage($price, $special)
{
  $percentual = (($price - $special) * 100) / $price;
  return number_format($percentual, 2, '.', '');
}
