<!DOCTYPE html>
<html dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>">
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
<link rel="stylesheet" type="text/css" href="view/stylesheet/stylesheet.css" />
<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>
<script type="text/javascript" src="view/javascript/jquery/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
<link type="text/css" href="view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/jquery/tabs.js"></script>
<script type="text/javascript" src="view/javascript/jquery/superfish/js/superfish.js"></script>
<script type="text/javascript" src="view/javascript/common.js"></script>
<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>

</head>
<body>
<div id="calculator" style="display:none;"></div>
<div id="container">
    <div id="header">
  
  <?php if ($logged) { ?>
  <div id="menu">
    <ul class="left" style="display: none;">
      <li id="network-status"></li>
      <li id="li-refresh">
        <a class="menu-refresh">
          <img id="refresh" style="margin-top: 5px;" src="view/image/pos/gtk-refresh.png">
          <img id="loading" style="margin-top: 5px;display:none;" src="view/image/pos/loading.gif">
        </a>
      </li>
      <li class="order-status">
        <a id="status" class="ok"></a>
      </li>
        <li class="keyboard-status">
            <a id="keyboard" class="add" href="javascript:void(0);"></a>
        </li>
      
    </ul>
    <ul class="right" style="display: none;">
      <li id="pos"><a href="<?php echo $pos; ?>" class="top">
          <script>
            document.write(Mustache.render("{{menu_pos}}", lang));
          </script>
        </a></li>
      <li id="refund"><a href="javascript:void(0);" class="top">
          <script>
            document.write(Mustache.render("{{menu_order}}", lang));
          </script>
        </a></li>
      <li id="transaction"><a href="javascript:void(0);" class="top">
          <script>
            document.write(Mustache.render("{{menu_transaction}}", lang));
          </script>
        </a></li>
      <li id="setting-panel"><a href="javascript:void(0);" class="top">
          <script>
            document.write(Mustache.render("{{menu_setting}}", lang));
          </script>
        </a>
      </li>
      <li id="calculator-panel"><a href="javascript:void(0);" class="top">
          <script>
            document.write(Mustache.render("{{menu_calculator}}", lang));
          </script>
        </a></li>

      <li id="help"><a href="javascript:void(0);" class="top"><script>
            document.write(Mustache.render("{{menu_help}}", lang));
          </script></a>

      </li>
      <li><a class="top" href="<?php echo $logout; ?>"><script>
            document.write(Mustache.render("{{menu_logout}}", lang));
          </script></a></li>
    </ul>
  </div>
  <?php } ?>
</div>
