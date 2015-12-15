<h3><?php echo $heading_title;setlocale(LC_MONETARY, 'pt_BR');  ?></h3>
<div class="row product-layout">
  <?php foreach ($products as $product) { ?>
  <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="product-thumb transition">
      <div class="image"><a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /></a></div>
      <div class="caption">
        <h4><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
        <p><?php echo $product['description']; ?></p>
        <?php if ($product['rating']) { ?>
        <div class="rating">
          <?php for ($i = 1; $i <= 5; $i++) { ?>
          <?php if ($product['rating'] < $i) { ?>
          <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
          <?php } else { ?>
          <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i></span>
          <?php } ?>
          <?php } ?>
        </div>
        <?php } ?>
        <?php
        $num_parcelas=5;
        if ($product['price']) { ?>
        <p class="price">
          <?php if (!$product['special']) { ?>
          <?php echo $product['price']; ?>
          <?php
           $precoreal = $product['price'];
           $source = array('.', ',','R$');
           $replace = array('', '.','');
           $precoreal = str_replace($source, $replace, $precoreal);
           if($precoreal > 50){?><br/>
              <span class="pagseguroPrice">em at&eacute; <?=$num_parcelas?>x s/ juros de <?php echo(money_format('%n',$precoreal/$num_parcelas));?></span>
              <span class="promo_5_pagseguro">+ 5% de desconto à vista (PagSeguro)</span>
            <?php
          }
          ?>
          <?php } else { ?>
          <span class="price-new"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span>
          <?php
           $precoreal = $product['special'];
           $source = array('.', ',','R$');
           $replace = array('', '.','');
           $precoreal = str_replace($source, $replace, $precoreal);
           if($precoreal > 50){?><br/>
              <span class="pagseguroPrice">em at&eacute; <?=$num_parcelas?>x s/ juros de <?php echo(money_format('%n',$precoreal/$num_parcelas));?></span>
              <span class="promo_5_pagseguro">+ 5% de desconto à vista (PagSeguro)</span>
            <?php
          }
          ?>
          <?php } ?>
          <?php if ($product['tax']) { ?>
          <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
          <?php } ?>
        </p>
        <?php } ?>
      </div>
      <div class="button-group">
        <button type="button" onclick="cart.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-shopping-cart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $button_cart; ?></span></button>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart"></i></button>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i></button>
      </div>
    </div>
  </div>
  <?php } ?>
</div>
