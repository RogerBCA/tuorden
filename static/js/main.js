$(function(){
	$(".iTooltip").tooltip({ 
		effect: 'slide', 
		offset:[200, 0], 
		relative:false
	});

	$('#banners').cycle({ pager:  '#button-slider' });
	$('#gallery .gallery').cycle({ 
		// fx:     'scrollHorz',
		speed:   500, 
		timeout: 5000,
		pause:   0,
		next: '#next', 
		prev: '#prev'							
	});
	
	$('#gallery .gallery_title').cycle({ 
		// fx:     'scrollHorz',
		speed:   500, 
		timeout: 5000,
		pause:   0,
		next: '#next', 
		prev: '#prev'							
	});
	
});