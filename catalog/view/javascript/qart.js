function  qurl(s){
	var request = [];
	var query = String(s.href).split('?');
	if (query[1]) {

	var pairs = query[1].split('&');
	for (i = 0; i < pairs.length; i++) {
		var data = pairs[i].split('=');
		if (data[0] && data[1] ) {
			if(data[0] == 'route') data[1] = 'module/'+s.route+'/info/';
			request.push(encodeURIComponent(data[0]) + '=' + encodeURIComponent(data[1]));
		}
	}
	s.url = query[0]+'?'+request.join('&');
	}
}

function binder(s){
	
$(s.selector+', '+s.selector_modal+', '+s.selector_result).on("contentChange", function() {
	
if(s['popup']['status'] == 1){
	$(s.sel +' i.popy').each(function () {
	    $(this).popover({
			placement : s['popup']['placement'],
			trigger: s['popup']['trigger'], 
			
		});
    });
}
		
	$(s.sel).on("click", '#list-view'+s.route+s.mod, function() {
		$(s.sel+' .product-layout > .clearfix').remove();
		$(s.sel+' .row > .product-layout').attr('class', 'product-layout product-list col-xs-12');
		
		localStorage.setItem('idisplay', 'list');
	});
	$(s.sel).on("click", '#grid-view'+s.route+s.mod, function() {
		$(s.sel+' .product-layout > .clearfix').remove();
		cols = $('#column-right, #column-left').length;

		if (cols == 2) {
			$(s.sel+' .product-layout').attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-12');
		} else if (cols == 1) {
			$(s.sel+' .product-layout').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-12');
		} else {
			$(s.sel+' .product-layout').attr('class', 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-12');
		}
		localStorage.setItem('idisplay', 'grid');
	});
	
	if (localStorage.getItem('idisplay') == 'list' || s.iview == 'list') {
		$('#list-view'+s.route+s.mod).trigger('click');
	} else {
		$('#grid-view'+s.route+s.mod).trigger('click');
	}
	
});
}

function getHtml(json,s){
var html = '<div class="" id="">';
if(typeof json['breadcrumbs'] !== 'undefined' && s.breadcrumbs == 1 ){
	if(json['breadcrumbs'].length){
		html += '<ul class="breadcrumb">';
			for(var i = 0; i < json['breadcrumbs'].length; i++) {
				if(s.html == 'categories'){
					html += '<li><a data-path="'+json['breadcrumbs'][i].id +'" href="'+ json['breadcrumbs'][i].href +'">' + json['breadcrumbs'][i].text +'</a></li>';
				}else{
					html += '<li>' + json['breadcrumbs'][i].text +'</li>';
				}
			}
		html += '</ul>';
	}
}
if(typeof json['sorts'] !== 'undefined' && typeof json['limits'] !== 'undefined'){
	html += '<div class="row">';
	html += '<div class="col-md-5"><div class="btn-group hidden-xs">';
	html += '<button type="button" id="list-view'+s.route+s.mod+'" class="btn btn-default" data-toggle="tooltip" title="'+json.button_list+'"><i class="fa fa-th-list"></i></button>';
	html += '<button type="button" id="grid-view'+s.route+s.mod+'" class="btn btn-default" data-toggle="tooltip" title="'+json.button_grid+'"><i class="fa fa-th"></i></button>';
	html += '</div></div>';

	html += '<div class="col-md-3 text-right">';
	html += '<div class="input-group">';
	html += '<span class="input-group-addon" id="addon-sort"><i class="fa fa-sort" data-toggle="tooltip" title="'+json.text_sort+ '"></i> </span>';
	html += '<select id="input-sort" class="form-control" name="sort">';
	for(var i = 0; i < json['sorts'].length; i++) {
		var z = (json['sorts'][i].value == json['sort']+'-'+json['order']) ? ' selected="selected"' : '';
		html += '<option value="'+ json['sorts'][i].href + '"' +z+'>'+json['sorts'][i].text+'</option>';
	}
	html += '</select>';
	html += '</div></div>';
	
	html += '<div class="col-md-3 text-right">';
	html += '<div class="input-group">';
	html += '<span class="input-group-addon" id="addon-limit"><i class="fa fa-sort-numeric-asc" data-toggle="tooltip" title="'+json.text_limit+ '"></i> </span>';
	html += '<select id="input-limit" class="form-control" name="limit">';
	for(var i = 0; i < json['limits'].length; i++) {
		var z = (json['limits'][i].value == json['limit']) ? ' selected="selected"' : '';
		html += '<option value="'+ json['limits'][i].href + '"' +z+'>'+json['limits'][i].text+'</option>';
	}
	html += '</select>';
	html += '</div></div>';
	
	html += '</div><br>';
}
if(json[s.html].length){
	html += '<div class="row">';
	for(var i = 0; i < json[s.html].length; i++) {
		var obj = json[s.html][i];
		html += '<div class="product-layout col-lg-3 col-md-3 col-sm-6 col-xs-12">';
		html  += '<div class="product-thumb transition">';
		html  += '<div class="image">';
		html  += '<a href="' + obj.href + '">';
		html  += '<img src="' + obj.image + '" alt="' + obj.name + '" title="' + obj.name + '" class="img-responsive" /></a></div>';
		html  += '<div class="caption">';
		html  += '<h5><a style="text-decoration: none" href="' + obj.href +'"> '+obj.name+'</a></h5>';
		if(s['popup']['status'] == 1){
			html += '<i class="'+s.popup.class+' popy" tabindex="-1" title="'+obj.name+'" data-content="'+obj.description+'"></i> ';
		}
		html += '&nbsp;<a href="' + obj.href + '" target="_blank" class="external"><i class="fa fa-external-link"></i></a>';
		if(s['popup']['status'] == 0){
			html  += '<p> '+obj.description+'</p>';
		}

		if(obj['rating']){
			html  += '<div class="rating">';
			for ($i = 1; $i <= 5; $i++) {
				if (obj['rating'] < $i) {
					html  += '<span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>';
				}else{
					html  += '<span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i></span>';
				}
			}
			html  += '</div>';
		}
		if(obj['price']){
			html  += '<p class="price">';
			if (!obj['special']) { 
				html  += obj['price'];
			}else{
				html  += '<span class="price-new">'+obj['special']+'</span> <span class="price-old">'+obj['price']+'</span>';
			}
			if(obj['tax']){
				html  += '<span class="price-tax">'+json.text_tax+' '+obj.tax+'</span>';
			}
			html  += '</p>';
		}
		
		html  += '</div> </div></div>';
	}
	html  += '</div>';
if(typeof json['pagination'] != 'undefined' && typeof json['results'] != 'undefined'){
	html += '<div class="">';
	html += '<div class="">'+ json['pagination'] +'</div>';
	html += '<p>'+ json['results'] +'</p>';
	html += '</div>';
}
}else{
	html += '<p>'+json.text_empty+'</p>';
}
html += '</div>';
return html;
}

function ajaxy(s){
	
	$.ajax({
		url: s.url,
		dataType: 'json',
		beforeSend: function() {
			$("body").css('cursor','wait');
		},
		complete: function() {
			$("html, body").css('cursor','auto');
		},
		success: function(json) {
			var html ='';
			html += getHtml(json,s);
			if(s.view == 'self'){
				$(s.sel).html(html).trigger('contentChange');
			}
			if(s.view == 'modal'){
				var $modal = $(s.selector_modal);
				$modal.find('.modal-body').html(html);
				$modal.modal().trigger('contentChange');
				$modal.animate({ scrollTop: 0 }, 'slow');
				$modal.on('shown.bs.modal', function (e) {
					//$(this).modal('handleUpdate');
					if(s.opacity == 1){
						var lastScrollTop = 0;
						$(this).scroll(function(e) {
							var lst = $(this).scrollTop();
							if (lst > lastScrollTop){ 
								$(this).find('.modal-backdrop').css( "opacity", 0 );
							} else {
								$(this).find('.modal-backdrop').css( "opacity", 0.1 );
							}
							lastScrollTop = lst;
						});
					}
				});
			}

		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError );
		}
	});
	
}
