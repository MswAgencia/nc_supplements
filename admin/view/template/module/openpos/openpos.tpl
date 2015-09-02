<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a class="btn btn-default" href="<?php echo $order; ?>" class="button">POS Orders</a>
      	<a class="btn btn-default" href="<?php echo $transaction; ?>" class="button">Money Transactions</a>
      	<a class="btn btn-default" href="<?php echo $barcode; ?>" class="button">Products</a>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
  <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i><?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
  <?php } ?>
  <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i><?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
  <?php } ?>
  <div class="panel panel-default">
    
    <div class="panel-body">
      
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td class="text-left"><?php echo $column_name; ?></td>
                  <td class="text-left"><?php echo $column_url; ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($stores) { ?>
	            <?php foreach ($stores as $store) { ?>
	            <tr>
	              
	              <td class="text-left"><?php echo $store['name']; ?></td>
	              <td class="text-left"><?php echo $store['url']; ?></td>
	              <td class="text-right"><?php foreach ($store['action'] as $action) { ?>
	                <a href="<?php echo $action['href']; ?>"  class="btn btn-primary"><i class="fa fa-pencil"></i></a>
	                <?php } ?></td>
	            </tr>
	            <?php } ?>
	            <?php } else { ?>
	            <tr>
	              <td class="center" colspan="4"><?php echo $text_no_results; ?></td>
	            </tr>
	            <?php } ?>
              </tbody>
            </table>
          </div>
        
      </form>  
    </div>
  </div>
</div>
<?php echo $footer; ?>