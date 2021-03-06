<?php echo $header; 
$theme_options = $this->registry->get('theme_options');
$config = $this->registry->get('config'); 
include('catalog/view/theme/' . $config->get('config_template') . '/template/new_elements/wrapper_top.tpl'); ?>

<h2><?php echo $text_address_book; ?></h2>
<?php if ($addresses) { ?>
<table class="table table-bordered table-hover">
  <?php foreach ($addresses as $result) { ?>
  <tr>
    <td class="text-left"><?php echo $result['address']; ?></td>
    <td class="text-right"><a href="<?php echo $result['update']; ?>" class="btn btn-info"><?php echo $button_edit; ?></a> &nbsp; <a href="<?php echo $result['delete']; ?>" class="btn btn-danger"><?php echo $button_delete; ?></a></td>
  </tr>
  <?php } ?>
</table>
<?php } else { ?>
<p><?php echo $text_empty; ?></p>
<?php } ?>
<div class="buttons clearfix">
  <div class="pull-left"><a href="<?php echo $back; ?>" class="btn btn-default"><?php echo $button_back; ?></a></div>
  <div class="pull-right"><a href="<?php echo $insert; ?>" class="btn btn-primary"><?php echo $button_new_address; ?></a></div>
</div>
  
<?php include('catalog/view/theme/' . $config->get('config_template') . '/template/new_elements/wrapper_bottom.tpl'); ?>
<?php echo $footer; ?>