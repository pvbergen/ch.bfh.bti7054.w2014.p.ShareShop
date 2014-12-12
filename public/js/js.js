$( document ).ready(function() {
    
	$(window).on('scroll', function() {
		if($(window).scrollTop() > 0) {
			$('#header').addClass('fixed');
		} else {
			$('#header').removeClass('fixed');	
		}
	});
	
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


	function debounce(func, wait, immediate) {
		var timeout;
		return function() {
			var context = this, args = arguments;
			var later = function() {
				timeout = null;
				if (!immediate) func.apply(context, args);
			};
			var callNow = immediate && !timeout;
			clearTimeout(timeout);
			timeout = setTimeout(later, wait);
			if (callNow) func.apply(context, args);
		};
	};
	var debouncedResize = debounce(function() {
 		var width = $('.product-list').width();
 		var numbers = width / 200;
 		numbers = Math.floor(numbers);
 		var margins = (numbers - 1) * 20;
 		width = width - margins;
 		var productwidth = width / numbers;
 		$('.product').attr('style','');
 		$('.product').css('width',productwidth-1);
 		$('.product').css('height',productwidth-1);
 		$('.product:nth-child(' + numbers + ')').css('margin-right',0);	
	}, 500);

	//window.addEventListener('resize', debouncedResize);
	//debouncedResize();

	// Ajax inject Productlist
	
	$('.level-1 > li > span').click(function() {
		var id = $(this).data('id');
		$.get("/article/getbycategory/category/" + id).done(function(htmlContent) {
			$('#content').html('');
			$('#content').append(htmlContent);
		});	 
	});

	// ********* Upload **************
	// Ajax inject SubCategories List in Upload
	$('#productCategory').change(function() {
		var arr = [];
		$('#productCategory :selected').each(function(i, selected) {
			arr[i] = selected.value;
		});
		var id = arr.join('-');
		$.get('/article/subcategories/id/' + id).done(function(htmlContent) {
			$('#productSubCategory').html('');
			$('#productSubCategory').append(htmlContent);
		});	 
	});

	// Button Subcategorysubmit
	
	$('#newSubCategorySubmit').click(function() {
		var mainCat = $('#categoryForSub').value();
		var subCat = $('#newSubCategorySubmit').value();
		$.get('/article/subcategories?id=' + mainCat + '&subCategory=' + subCat).done(function(htmlContent) {
			$('#productSubCategory').html('');
			$('#productSubCategory').append(htmlContent);
		});	 
		
	});
	$('#toggleCatSubmitDisplay').click(function() {
		if ($(this).data('visible')) {
			$(this).prop('value', 'Hinzuf&uuml;gen');
			$('#input-Category-Submit').addClass( "invisible" );
			$(this).data('visible',false);
		} else {
			$(this).prop('value', 'Entfernen');
			$('#input-Category-Submit').removeClass( "invisible" );
			$(this).data('visible',true);
		}	
		
	});
	
	
	$('input#adresse').each(function(i, el) {
		var options = {};
		autocomplete = new google.maps.places.Autocomplete(el, options);
	});
		
	/*$('form#registerForm').on('submit', function() {
		service = new google.maps.places.PlacesService(map);
		service.textSearch($('input#adresse').value(), callback);
	});*/
	
});
