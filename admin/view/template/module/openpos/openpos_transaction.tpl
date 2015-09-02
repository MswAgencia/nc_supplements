<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
	    <div class="container-fluid">
	      <div class="pull-right">

	      	<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="Back" class="btn btn-default"><i class="fa fa-reply"></i></a>
	      </div>
	      <h1><?php echo $heading_title; ?></h1>
	      <ul class="breadcrumb">
	        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
	        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
	        <?php } ?>
	      </ul>
	    </div>
	</div>
	<div class="container-fluid">
		<?php if (isset($error_warning)) { ?>
		<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<?php if (isset($success)) { ?>
		<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>
		<div class="panel panel-default">
			<div class="panel-heading">
		      <h3><?php echo $heading_title; ?></h3>
		    </div>
			<div class="panel-body">
				<table class="table table-bordered table-hover" id="transactions-list">
				            <thead>
				              <tr>
				              	<td class="right">Store Name</td>
				              	<td class="right">Cashier</td>
				                <td class="right">Date</td>
				                <td class="right">Type</td>
				                <td class="left">Money IN</td>
				                <td class="left">Money Out</td>
				                <td class="left">Comment</td>
				              </tr>
				            </thead>
				            <tbody>
							<tr class="filter">
								<td class="right">
									<select name="store_id">
										<option value="all">All</option>
										<?php echo $store_id; ?>
										<?php foreach($stores as $store):?>
										<option value="<?php echo $store['store_id']; ?>" <?php echo ($store_id == $store['store_id'] and $store_id != 'all') ? 'selected="selected"':''; ?>><?php echo $store['name']; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td class="right">
									<select name="user_id">
										<option value="all">All</option>
										<?php foreach($users as $user):?>
										<option value="<?php echo $user['user_id']; ?>" <?php echo ($user_id == $user['user_id'] and $user_id != 'all')? 'selected="selected"':''; ?>><?php echo $user['username']; ?></option>
										<?php endforeach; ?>
									</select>
								</td>
								<td class="right"></td>
								<td class="right"></td>
								<td class="left"></td>
								<td class="left"></td>
								<td class="left">Current Balance: <?php echo $total; ?> </td>
							</tr>
				            <?php foreach($transactions as $trans):?>
				            	<tr>
				            	<td class="right"><?php echo ($trans['storename'] !='')?$trans['storename']:'Default'; ?></td>
				              	<td class="right"><?php echo ($trans['username'] !='')?$trans['username']:'Unknow'; ?></td>
				                <td class="right"><?php echo $trans['date_created'];?></td>
				                <td class="right"><?php echo $trans['type'];?></td>
				                <td class="left"><?php echo $trans['money_in'];?></td>
				                <td class="left"><?php echo$trans['money_out'];?></td>
				                <td class="left"><?php echo $trans['comment'];?></td>
				              </tr>
				            <?php endforeach; ?>
				            </tbody>
				</table>
				<div class="pagination"><?php echo $pagination; ?></div>
			</div>
		</div>
	</div>
</div>
<script>
	$(function() {
		url = 'index.php?route=module/openpos/transaction&token=<?php echo $token; ?>';
		var filter_store_id = $('select[name=\'store_id\']').val();
		var filter_user_id = $('select[name=\'user_id\']').val();

		$('select[name="store_id"]').change(function() {
			filter_store_id = $(this).val();
			url += '&store_id=' + encodeURIComponent(filter_store_id);
			url += '&user_id=' + encodeURIComponent(filter_user_id);
			location = url;
		});
		$('select[name="user_id"]').change(function() {
			filter_user_id = $(this).val();
			url += '&store_id=' + encodeURIComponent(filter_store_id);
			url += '&user_id=' + encodeURIComponent(filter_user_id);
			location = url;
		});

	});
</script>
<?php echo $footer; ?>