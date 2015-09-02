<table class="list">
	<thead>
		<tr>
			<td class="right">ID</td>
			<td class="right">Product Name</td>
			<td class="right">Price</td>
			<td class="right">Tax</td>
			<td class="right">Qty</td>
			<td class="left">Total</td>
		</tr>
	</thead>
	<tbody>
		<?php foreach($order_products as $product): ?>
		<tr>
			<td class="right"><?php echo $product['product_id'];?></td>
			<td class="right"><?php echo $product['name'];?></td>
			<td class="right"><?php echo $product['price'];?></td>
			<td class="right"><?php echo $product['tax'];?></td>
			<td class="right"><input type="text" value="<?php echo $product['quantity'];?>" /></td>
			<td class="left"><?php echo $product['total'];?></td>
		</tr>
		<?php endforeach; ?>
		<?php foreach($totals as $total):  ?>
		<tr>
			<td colspan="5" class="right"><?php echo $total['title']; ?></td>
			<td class="left">
			<?php if($total['code'] == 'refund'):?>
			<input type="text" value="<?php echo $total['value']; ?>">
			<?php else: ?>
			<?php echo $total['value']; ?>
			<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
		
	</tbody>
</table>