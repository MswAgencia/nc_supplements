<?php

function getDiscountPercentage($price, $special)
{
  return (($price - $special) * 100) / $price;
}
