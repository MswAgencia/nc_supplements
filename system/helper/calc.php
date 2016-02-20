<?php

function getDiscountPercentage($price, $special, $floor = true)
{
  $percentual = (($price - $special) * 100) / $price;

  if($floor)
    return floor($percentual);

  return number_format($percentual, 2, '.', '');
}

function getDiscountMessageBasedOnPriceValue($actualProductPrice, $discountPercentage, $numberOfParcels, $minimumPriceToDisplayMessage = null)
{
  if(empty($minimumPriceToDisplayMessage))
    $minimumPriceToDisplayMessage = getMinimumPriceToDisplayMessage();
    
  if($actualProductPrice < $minimumPriceToDisplayMessage)
    return '';

  $parceledPrice = $actualProductPrice / $numberOfParcels;
  $priceWithDiscount = calcPriceWithDiscount($actualProductPrice, $discountPercentage);

  $formattedParceledPrice = number_format($parceledPrice, 2, ',', '');
  $formattedPriceWithDiscount = number_format($priceWithDiscount, 2, ',', '');

  return "<span class=\"pagseguroPrice\">em até {$numberOfParcels}x sem juros de R$ {$formattedParceledPrice} ou {$formattedPriceWithDiscount} à vista!</span>";
}

function calcPriceWithDiscount($price, $discountPercentage)
{
  return $price - ($price * ($discountPercentage / 100));
}

function getMinimumPriceToDisplayMessage()
{
  return 150.0;
}
