<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-setting" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
	<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
	  <button type="button" class="close" data-dismiss="alert">&times;</button>
	</div>
	<?php } ?>
	<?php if ($success) { ?>
	<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
	  <button type="button" class="close" data-dismiss="alert">&times;</button>
	</div>
	<?php } ?>
  
  <div class="panel panel-default">
  	<div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo 'Edit';//$text_edit; ?></h3>
    </div>
     <div class="panel-body">
      
      <form action="<?php echo $action; ?>" id="form-setting" method="post" enctype="multipart/form-data" id="form" class="form-horizontal">
       	<input type="hidden" name="store_id" value="<?php echo $store_id; ?>">
        <ul class="nav nav-tabs">
	      <li class="active"><a href="#tab-general"  data-toggle="tab"><?php echo $tab_general; ?></a></li>
	      <li><a href="#tab-payment"  data-toggle="tab">Payment Method</a></li>
      	</ul>
      	<div class="tab-content">
	        <div id="tab-general" class="tab-pane active">
	        	<div class="form-group">
	                <label class="col-sm-2 control-label" for="input-geocode"><span data-toggle="tooltip" data-container="#tab-general">Choose POS Category Show</span></label>
	                <div class="col-sm-10">
	                	<div class="well well-sm" style="height: 150px; overflow: auto;">
		                  	<?php 
		              			
		              			$class = 'even'; 
		              			if(!isset($openpos['openpos_categories']))
		              			{
		              				$openpos['openpos_categories'] = array();
		              			}
		              		?>
		                 
			                  <?php foreach ($categories as $category) { ?>
			                  <?php $class = ($class == 'even' ? 'odd' : 'even'); ?>
			                  <div class="checkbox">
			                  	<label>
			                    <?php if ( in_array($category['category_id'],$openpos['openpos_categories'])) { ?>
			                    <input type="checkbox" name="openpos_categories[]" value="<?php echo $category['category_id']; ?>" checked="checked" />
			                    <?php echo $category['name']; ?>
			                    <?php } else { ?>
			                    <input type="checkbox" name="openpos_categories[]" value="<?php echo $category['category_id']; ?>" />
			                    <?php echo $category['name']; ?>
			                    <?php } ?>
			                    </label>
			                  </div>
			                 <?php } ?>
		                 </div>
	                </div>
	             </div>
	             <div class="form-group">
	                <label class="col-sm-2 control-label"><span data-toggle="tooltip">Default customer Email</span></label>
	                <div class="col-sm-4">
	                  <?php
	              			if(!isset($openpos['openpos_default_customer']['email']))
	              			{
	              				$openpos['openpos_default_customer']['email'] = '';
	              			}
	              			
	              			if(!isset($openpos['openpos_default_customer']['id']))
	              			{
	              				$openpos['openpos_default_customer']['id'] = '';
	              			}
	              		?>
	              		<input type="text" class="form-control" id="default_customer_email" value="<?php echo $openpos['openpos_default_customer']['email'];?>"  name="openpos_default_customer[email]" size="40">              
	                    <input type="hidden" id="default_customer_id" value="<?php echo $openpos['openpos_default_customer']['id'];?>" name="openpos_default_customer[id]" size="40">              
	                </div>
	             </div>
	             <div class="form-group">
	                <label class="col-sm-2 control-label"><span data-toggle="tooltip" >Default Order Status</span></label>
	                <div class="col-sm-2">
	                  	<select name="openpos_config_pos_complete_status_id" class="form-control" >
	                  	<?php foreach($order_statuses as $s): ?>
	              		  <?php if($s['order_status_id'] == $openpos['openpos_config_pos_complete_status_id']):?>
	              		  <option value="<?php echo $s['order_status_id']; ?>" selected="selected"><?php echo $s['name']; ?></option>
	              		  <?php else: ?>
	              		  <option value="<?php echo $s['order_status_id']; ?>"><?php echo $s['name']; ?></option>
	              		  <?php endif; ?>
	                      
	                      <?php endforeach; ?>
	                	</select>
	               </div>
	             </div>
				<div class="form-group">
					<label class="col-sm-2 control-label"><span data-toggle="tooltip" >Cancel Order Status</span></label>
					<div class="col-sm-2">
						<select name="openpos_config_pos_cancel_status_id" class="form-control" >
							<?php foreach($order_statuses as $s): ?>
							<?php if($s['order_status_id'] == $openpos['openpos_config_pos_cancel_status_id']):?>
							<option value="<?php echo $s['order_status_id']; ?>" selected="selected"><?php echo $s['name']; ?></option>
							<?php else: ?>
							<option value="<?php echo $s['order_status_id']; ?>"><?php echo $s['name']; ?></option>
							<?php endif; ?>

							<?php endforeach; ?>
						</select>
					</div>
				</div>
	             <div class="form-group">
	                <label class="col-sm-2 control-label"><span data-toggle="tooltip" >User Group</span></label>
	                <div class="col-sm-2">
	                  	<select name="openpos_config_pos_user_group_id" class="form-control">
	              		  <?php foreach($user_groups as $g): ?>
	              		  <?php if($g['user_group_id'] == $openpos['openpos_config_pos_user_group_id']):?>
	              		  <option value="<?php echo $g['user_group_id']; ?>" selected="selected"><?php echo $g['name']; ?></option>
	              		  <?php else: ?>
	              		  <option value="<?php echo $g['user_group_id']; ?>"><?php echo $g['name']; ?></option>
	              		  <?php endif; ?>
	                      
	                      <?php endforeach; ?>
	                    </select>
	               </div>
	             </div>
	             <div class="form-group">
	                <label class="col-sm-2 control-label"><span data-toggle="tooltip" >Tax</span></label>
	                <div class="col-sm-2">
	                  	<select name="openpos_config_pos_tax" class="form-control">
	              		  <option value="0" <?php if(isset($openpos['openpos_config_pos_tax']) and $openpos['openpos_config_pos_tax'] == 0):?>selected="selected"<?php endif;?> >No Tax</option>
	                      <option value="-1"<?php if(isset($openpos['openpos_config_pos_tax']) and $openpos['openpos_config_pos_tax'] == -1):?>selected="selected"<?php endif;?> >Use Product Tax</option>
	                      <?php foreach($tax_classes as $s): ?>
	              		  <?php if($s['tax_class_id'] == $openpos['openpos_config_pos_tax']):?>
	              		  <option value="<?php echo $s['tax_class_id']; ?>" selected="selected"><?php echo $s['title']; ?></option>
	              		  <?php else: ?>
	              		  <option value="<?php echo $s['tax_class_id']; ?>"><?php echo $s['title']; ?></option>
	              		  <?php endif; ?>
	                      
	                      <?php endforeach; ?>
	                    </select>
	               </div>
	             </div>
	             <div class="form-group">
	                <label class="col-sm-2 control-label"><span data-toggle="tooltip" >Subtract stock</span></label>
	                <div class="col-sm-2">
	                  	<select name="openpos_config_pos_subtract_stock" class="form-control">
	              		  <option value="0" <?php if(isset($openpos['openpos_config_pos_subtract_stock']) and $openpos['openpos_config_pos_subtract_stock'] == 0):?>selected="selected"<?php endif;?> >No</option>
	                      <option value="1"<?php if(isset($openpos['openpos_config_pos_subtract_stock']) and $openpos['openpos_config_pos_subtract_stock'] == 1):?>selected="selected"<?php endif;?> >Yes</option>
	                    </select>
	               </div>
	             </div>
	             <div class="form-group">
	                <label class="col-sm-2 control-label"><span data-toggle="tooltip" >Receipt Template</span></label>
	                <div class="col-sm-10">
	                  	<textarea name="openpos_receipt_header" id="receipt_header" class="form-control" style="min-height: 200px;"><?php
	            			if(isset($openpos['openpos_receipt_header']))
	            			{
	            				echo $openpos['openpos_receipt_header'];
	            			}
	            		?></textarea>
						<br/>
						<span class="help">Samples: <a href="javascript:void(0);" onClick="showSample()" id="click-to-view">Click to view</a> </span>
	               </div>

	             </div>

	          
	        </div>
	        <div id="tab-payment" class="tab-pane">
	        	<div class="table-responsive">
	        	 <table class="table table-striped table-bordered table-hover">
	        	 	<thead>
	        	 	<tr>
	        	 		<td class="right"> Method Name</td>
	        	 		<td class="left"> Is Cash method</td>
	        	 		<td>&nbsp; </td>
	        	 	</tr>
	        	 	</thead>
	        	 	<?php $i = 0; foreach($payment as $p):?>
	        	 	<tr>
		        	 	<td class="right"><input class="form-control" type="text" name="payment_name[<?php echo $i; ?>]" value="<?php echo $p['payment_name']; ?>"></td>
		        	 	<td class="left"> <input type="checkbox" <?php echo ($p['is_cash'] == 1) ? 'checked="checked"':''; ?> name="is_cash[<?php echo $i; ?>]" value="1"></td>
		        	 	<td><a onclick="removePayment($(this));" title="Remove" class="btn btn-danger"><i class="fa fa-minus-circle"></i></a> </td>
	        	 	</tr>
	        	 	<?php $i ++; endforeach; ?>
	        	 	<tr class="add-payment">
	        	 		<td colspan="2">&nbsp; </td>
	        	 		<td><a onclick="addPayment();"  data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="Add New Payment" class="button btn btn-primary" href="javascript:void(0);"><i class="fa fa-plus-circle"></i></a></td>
	        	 	</tr>
	        	 </table>
	        	</div>
	        </div>
        </div>
        </form>
     </div>   
  </div>
</div>

<script type="text/javascript"><!--
var start = <?php echo $i; ?>;
	function showSample()
	{
		var html = '<div style="width:500px;height: 400px;"><textarea style="width: 100%;height: 400px;"> ';
		html += '<table>\n';
		html += '<tr>\n';
		html += '\t<td>Order Date:</td>\n';
		html += '\t<td>{{date}}</td>\n';
		html += '</tr> \n';
		html += '<tr>\n';
		html += '\t<td>Cashier:</td>\n';
		html += '\t<td>{{cashier}}</td>\n';
		html += '</tr> \n';
		html += '<tr>\n';
		html += '\t<td>Customer Name:</td>\n';
		html += '\t<td>{{customer.firstname}} {{customer.lastname}}</td>\n';
		html += '</tr> \n';
		html += '<tr>\n';
		html += '\t<td>Customer Phone:</td>\n';
		html += '\t<td>{{customer.telephone}}</td>\n';
		html += '</tr>\n';
		html += '<tr>\n';
		html += '\t<td>Customer Email:</td>\n';
		html += '\t<td>{{customer.email}}</td>\n';
		html += '</tr>\n';
		html += '</table>\n';
		html += '<table>\n';
		html += '<thead>\n';
		html += '<tr>\n';
		html += '\t<th>Name</th>\n';
		html += '\t<th>QTY</th>\n';
		html += '\t<th>Price</th>\n';
		html += '\t<th>SubTotal</th>\n';
		html += '</tr>\n';
		html += '</thead>\n';
		html += '<tbody>\n';
		html += '{{#products}}\n';
		html += '\t<tr>\n';
		html += '\t\t<td>{{name}}\n';
		html += '\t\t\t<ul>\n';
		html += '\t\t\t\t{{#options}}\n';
		html += '\t\t\t\t<li>{{name}} : {{value}}</li>\n';
		html += '\t\t\t\t{{/options}}\n';
		html += '\t\t\t</ul>\n';
		html += '\t\t</td>\n';
		html += '\t\t<td>{{qty}}</td>\n';
		html += '\t\t<td>{{price_formated}}</td>\n';
		html += '\t\t<td>{{subtotal_formated}}</td>\n';
		html += '\t</tr>\n';
		html += '{{/products}}\n';
		html += '</tbody>\n';
		html += '<tfooter>\n';
		html += '<tr>\n';
		html += '\t<td colspan="3">Sub-Total</td>\n';
		html += '\t<td>{{subtotal_formated}}</td>\n';
		html += '</tr>\n';
		html += '<tr>\n';
		html += '\t<td colspan="3">Tax</td>\n';
		html += '\t<td>{{tax_formated}}</td>\n';
		html += '</tr>\n';
		html += '<tr>\n';
		html += '\t<td colspan="3">Discount</td>\n';
		html += '\t<td>{{discount_formated}}</td>\n';
		html += '</tr>\n';
		html += '<tr>\n';
		html += '\t<td colspan="3">Grand-Total</td>\n';
		html += '\t<td>{{grandtotal_formated}}</td>\n';
		html += '</tr>\n';
		html += '<tr>\n';
		html += '\t<td colspan="3">Total Paid</td>\n';
		html += '\t<td>{{total_paid_formated}}</td>\n';
		html += '</tr>\n';
		html += '<tr>\n';
		html += '\t<td colspan="3">Balance</td>\n';
		html += '\t<td>{{balance_formated}}</td>\n';
		html += '</tr>\n';
		html += '</tfooter>\n';
		html += '</table>';
		html += '</textarea></div>';
		$(html).alert();
		$('#click-to-view').popover({content:html,html:true});

	}
function addPayment()
{
	var html = '<tr><td class="right"><input type="text" class="form-control" name="payment_name['+start+']"></td><td class="left"> <input type="checkbox" name="is_cash['+start+']" value="1"></td><td><a onclick="removePayment($(this));" title="Remove" class="btn btn-danger"><i class="fa fa-minus-circle"></i></a> </td></tr>';
	$( ".add-payment" ).before( html );
	start ++;
}

function removePayment(ob)
{
	ob.closest('tr').remove();
}

// Category
$('input#default_customer_email').autocomplete({
	delay: 500,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=module/openpos/customerautocomplete&token=<?php echo $token; ?>&filter_email=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {		
				response($.map(json, function(item) {
					return {
						label: item.email,
						value: item.customer_id
					}
				}));
			}
		});
	}, 
	select: function(item) {
		
		$('#default_customer_email').val(item.label);
		$('#default_customer_id').val(item.value);
		return false;
	},
	focus: function(event, ui) {
      return false;
   }
});
//--></script> 

<script type="text/javascript"><!--

//--></script> 
<?php echo $footer; ?>