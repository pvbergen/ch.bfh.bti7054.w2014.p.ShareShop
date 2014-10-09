$( document ).ready(function() {
    
	/*
		Navigation Toggle
	*/
	$('#subnavigation .level-1 > li > span').click(function() {
		var el = $(this).parent().find('.level-2')
		if (el.attr('toggled') == 'true') {
			el.attr('toggled','false');
			el.css('display','none');
		} else {
			el.attr('toggled','true');
			el.css('display','block');
		}
	});

	/*
		Content Elements resize
	*/

 	window.resize = function() {
 		var width = $('.product-list').width();
 		var numbers = width / 12;
 		numbers = Math.floor(numbers);
 		var productwidth = width / numbers;
 		$('.product').css('width',numbers + 'em');
 	}
	
 	window.onresize = resize;

});
