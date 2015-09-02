</div>
<script>
	$('body').on('click','ul.pagination a',function(){
		$(".ui-dialog-content").dialog("close");
		var tag_refund = $("<div id='refund-list'></div>");
		$.ajax({
		    url: $(this).attr('href'),
		    success: function(data) {
		    	tag_refund.html(data).dialog({modal:true,minWidth: 800,title: "Orders",close: function() {$('#refund-list').remove();}}).dialog('open');
		    }
		  });
		return false;
	});
	
	$('body').on('click','button.order-view',function(){
		var url = $(this).attr('data-href');
		$(".ui-dialog-content").dialog("close");
		var tag_refund = $("<div id='refund-list'></div>");
		$.ajax({
		    url: url,
		    success: function(data) {
		    	tag_refund.html(data).dialog({modal:true,minWidth: 800,title: "Orders",close: function() {$('#refund-list').remove();}}).dialog('open');
		    }
		  });
	});
</script>
</body></html>