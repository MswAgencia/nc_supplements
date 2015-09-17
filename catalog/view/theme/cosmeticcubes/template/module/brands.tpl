<?php if ($type == 'div') { ?>
<?php if ($brands) { ?>
	<div id="brands<?php echo $mod; ?>">
		<?php if(!empty($title)){ ?>
			<h3><?php if(!empty($class)){ ?> <i class="<?php echo $class; ?>"></i> &nbsp;<?php } ?>
			<?php echo $title; ?></h3>
		<?php } ?>
	<?php if($view == 'basic') { ?>
		<p><strong><?php echo $text_index; ?></strong>
			<?php foreach ($brands as $brand) { ?>
				&nbsp;&nbsp;&nbsp;<a href="index.php?route=product/manufacturer#<?php echo $brand['name']; ?>"><?php echo $brand['name']; ?></a>
			<?php } ?>
		</p>
	<?php } ?>
		<?php foreach ($brands as $brand) { ?>
			<h2 id="<?php echo $brand['name']; ?>"><?php echo $brand['name']; ?></h2>
				<?php if ($brand['manufacturer']) { ?>
					<div class="list-group">
						<?php foreach ($brand['manufacturer'] as $manufacturer) { ?>
					<div>
						<a class="list-group-item" href="<?php echo $manufacturer['href']; ?>"><?php echo $manufacturer['name']; ?></a>
		</div>
				<?php } ?>
					</div>
			<?php } ?>
		<?php } ?>
	</div>
<?php } ?>
<?php } ?>

<?php if ($type == 'link') { ?>
<div id="#brands<?php echo $mod; ?>" class="row navbar-text">
<a href="brands-modal<?php echo $mod; ?>" id="brands-modalb<?php echo $mod; ?>" class="navbar-link" data-toggle="modal" data-target="#brands-modal<?php echo $mod; ?>" >
<?php if(!empty($class)){ ?> <i class="<?php echo $class; ?>"></i> <?php } ?>
<?php echo $title; ?>
</a>
</div>
<?php } ?>

<?php if($view == 'self') { ?>
      <div class="" id="brands-result<?php echo $mod; ?>" style="padding:5px;"> </div>
<?php } ?>

<?php if($view == 'modal') { ?>

<div class="modal fade" id="brands-modal<?php echo $mod; ?>" tabindex="-1" role="dialog" aria-labelledby="brandModalLabel<?php echo $mod; ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="brandModalLabel<?php echo $mod; ?>"> </h4>
      </div>
      <div class="modal-body">
<?php if ($brands) { ?>
	<div id="brands<?php echo $mod; ?>">
		<?php if(!empty($title)){ ?>
			<h3><?php if(!empty($class)){ ?> <i class="<?php echo $class; ?>"></i> <?php } ?>
			<?php echo $title; ?></h3>
		<?php } ?>
		<?php if($view == 'basic') { ?>
			<p><strong><?php echo $text_index; ?></strong>
			<?php foreach ($brands as $brand) { ?>
				&nbsp;&nbsp;&nbsp;<a href="index.php?route=product/manufacturer#<?php echo $brand['name']; ?>"><?php echo $brand['name']; ?></a>
			<?php } ?>
			</p>
		<?php } ?>
			<?php foreach ($brands as $brand) { ?>
				<h2 id="<?php echo $brand['name']; ?>"><?php echo $brand['name']; ?></h2>
					<?php if ($brand['manufacturer']) { ?>
						<div class="list-group">
							<?php foreach ($brand['manufacturer'] as $manufacturer) { ?>
								<div>
									<a data-path="<?php echo $manufacturer['id']; ?>" class="list-group-item" href="<?php echo $manufacturer['href']; ?>"><?php echo $manufacturer['name']; ?></a>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
			<?php } ?>
	</div>
<?php } ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        
      </div>
    </div>
  </div>
</div>
<?php } ?>

<?php if($view != 'basic') { ?>
<script type="text/javascript"><!--

$(document).ready(function(){

var s = JSON.parse('<?php echo $set; ?>');
binder(s);
$(s.selector+', '+ s.selector_modal).on('click', 'a[href]:not(.external, [href="brands-modal<?php echo $mod; ?>"])', function(e){
	e.preventDefault();
	s.href = this.href;
	qurl(s);
	ajaxy(s);
	
});
$(s.sel).on('change', 'select', function(e){
	s.href = $(this).val();
	qurl(s);
	ajaxy(s);
	
});

});

//--></script>
<?php } ?>
