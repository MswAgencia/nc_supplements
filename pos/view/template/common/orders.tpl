<table class="list">
            <thead>
              <tr>
              	<td class="right">Id</td>
                <td class="right">Date</td>
                <td class="left">Payment Method</td>
                <td class="right">Grand Total</td>
                <td class="right">Total Paid</td>
                <td class="left">Balance</td>
                <td class="left">Customer</td>
                <td class="left">Status</td>
                <td class="right">Action</td>
              </tr>
            </thead>
            <tbody>
            <?php if(isset($orders)): ?>
            <?php foreach($orders as $order):?>
            	<tr>
                <td class="right"><?php echo $order['order_id'];?></td>
                <td class="right"><?php echo $order['date_added'];?></td>
                <td class="left"><?php echo $order['payment_method'];?></td>
                <td class="right"><?php echo $order['total'];?></td>
                <td class="right"><?php echo $order['total_paid'];?></td>
                <td class="left"><?php echo $order['balance'];?></td>
                <td class="left"><?php echo $order['customer'];?></td>
                <td class="left"><?php echo $order['status'];?></td>
                <td class="right">
                	<p>
                		<button class="order-view" data-href="<?php echo $order['action']['href']; ?>">View</button>
                		<button  id="refund-btn" order_id="<?php echo $order['order_id'];?>">Edit</button> 
                	</p>
                	
                </td>
              </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
</table>
<div class="pagination"><?php echo $pagination; ?></div>
