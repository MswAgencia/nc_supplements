function ProductCarousel(data)
{
	var jcarousel = $('#ajaxproducts').jcarousel();
	
	
	
	$('#pro-control-prev')
    .on('jcarouselcontrol:active', function() {
        $(this).removeClass('inactive');
    })
    .on('jcarouselcontrol:inactive', function() {
        $(this).addClass('inactive');
    })
    .jcarouselControl({
        target: '-=1',
        carousel: jcarousel
    });

	$('#pro-control-next')
	    .on('jcarouselcontrol:active', function() {
	        $(this).removeClass('inactive');
	    })
	    .on('jcarouselcontrol:inactive', function() {
	        $(this).addClass('inactive');
	    })
	    .jcarouselControl({
	        target: '+=1',
	        carousel: jcarousel
	    });
}

