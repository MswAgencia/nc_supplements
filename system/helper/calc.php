<?php

function getDiscountPercentage($price, $special, $floor = true)
{
  $percentual = (($price - $special) * 100) / $price;
<<<<<<< HEAD

  if($floor)
    return floor($percentual);

=======
>>>>>>> 92b90a0cbd38dc38cc1cba0f4731a200bdfb26bf
  return number_format($percentual, 2, '.', '');
}
