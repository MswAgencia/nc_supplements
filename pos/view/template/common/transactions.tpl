<div id="transfer">
	<form onSubmit="return false;" id="frm-transfer">
		<div id="transfer-header">Manual Transfer</div>
		<div id="money-in">
			<span>Money In:</span>
			<input type="text" name="money_in" placeholder="0.00" value="0">
		</div>
		<div id="money-out">
			<span>Money Out:</span>
			<input type="text" name="money_out" placeholder="0.00" value="0">
		</div>
		<div id="money-comment">
			<textarea name="comment">Manual transfer</textarea>
		</div>
		<div id="money-transfer">
			<button id="transfer-btn">Transfer</button>
		</div>
	</form>
</div>
<div id="balance" class="transaction-total">
	<span><?php echo $total; ?></span>
</div>
<table class="list" id="transactions-list">
            <thead>
              <tr>
              	<td class="right">Cashier</td>
                <td class="right">Date</td>
                <td class="right">Type</td>
                <td class="left">Money IN</td>
                <td class="left">Money Out</td>
                <td class="left">Comment</td>
              </tr>
            </thead>
            <tbody>
            <?php foreach($transactions as $trans):?>
            	<tr>
            	<td class="right"><?php echo $trans['username'];?></td>
                <td class="right"><?php echo $trans['date_created'];?></td>
                <td class="right"><?php echo $trans['type'];?></td>
                <td class="left"><?php echo $trans['money_in'];?></td>
                <td class="left"><?php echo $trans['money_out'];?></td>
                <td class="left"><?php echo $trans['comment'];?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
</table>
<?php echo $pagination; ?>