<?php


function getBadgeHtml($price, $special, $minPriceForFreeShipping)
{
  if($price >= $minPriceForFreeShipping and empty($special)) {
    return '<div class="promo-badge frete-gratis">Frete Grátis</div>';
  }
  elseif($price >= $minPriceForFreeShipping and !empty($special)) {
    $discount = getDiscountPercentage($price, $special);
    return "<div class=\"two promo-badge frete-gratis\">Frete Grátis</div>
            <div class=\"two promo-badge desconto\">{$discount}% de Desconto</div>";
  }
  elseif(!empty($product['special'])) {
    $discount = getDiscountPercentage($price, $special);
    return "<div class=\"promo-badge desconto\">{$discount}% de Desconto</div>";
  }
}

function getBadgeHtmlForProductPage($price, $special, $minPriceForFreeShipping)
{
  if($price >= $minPriceForFreeShipping and empty($special)) {
    return '<li><div class="promo-badge product-page frete-gratis">Frete Grátis</div></li>';
  }
  elseif($price >= $minPriceForFreeShipping and !empty($special)) {
    $discount = getDiscountPercentage($price, $special);
    return "<div class=\"two promo-badge  product-page frete-gratis\">Frete Grátis</div>
            <div class=\"two promo-badge  product-page desconto\">{$discount}% de Desconto</div>";
  }
  elseif(!empty($product['special'])) {
    $discount = getDiscountPercentage($price, $special);
    return "<div class=\"promo-badge product-page desconto\">{$discount}% de Desconto</div>";
  }
}

function getMinimumPriceForFreeShipping()
{
  return 149.9;
}
