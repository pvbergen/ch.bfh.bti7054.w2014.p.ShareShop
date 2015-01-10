$(document).ready(
		function() {

			$(window).on('scroll', function() {
				if ($(window).scrollTop() > 0) {
					$('#header').addClass('fixed');
				} else {
					$('#header').removeClass('fixed');
				}
			});

			/*
			 * Navigation Toggle
			 */
			$('#subnavigation .level-1 > li > span').click(function() {
				var el = $(this).parent().find('.level-2')
				if (el.attr('toggled') == 'true') {
					el.attr('toggled', 'false');
					el.css('display', 'none');
				} else {
					el.attr('toggled', 'true');
					el.css('display', 'block');
				}
			});

			/*
			 * Content Elements resize
			 */

			function debounce(func, wait, immediate) {
				var timeout;
				return function() {
					var context = this, args = arguments;
					var later = function() {
						timeout = null;
						if (!immediate)
							func.apply(context, args);
					};
					var callNow = immediate && !timeout;
					clearTimeout(timeout);
					timeout = setTimeout(later, wait);
					if (callNow)
						func.apply(context, args);
				};
			}
			;
			var debouncedResize = debounce(
					function() {
						var width = $('.product-list').width();
						var numbers = width / 200;
						numbers = Math.floor(numbers);
						var margins = (numbers - 1) * 20;
						width = width - margins;
						var productwidth = width / numbers;
						$('.product').attr('style', '');
						$('.product').css('width', productwidth - 1);
						$('.product').css('height', productwidth - 1);
						$('.product:nth-child(' + numbers + ')').css(
								'margin-right', 0);
					}, 500);

			// window.addEventListener('resize', debouncedResize);
			// debouncedResize();

			// At Least make those elements sqare

			var makeThemSquare = debounce(function() {
				var width = $('.product').width();
				$('.product img').css('height', width);
			}, 500);

			window.addEventListener('resize', makeThemSquare);
			makeThemSquare();

			// Ajax inject Productlist

			$('.level-2 li > span').click(
					function() {
						var id = $(this).data('id');
						$.get("/article/getbycategory/category/" + id).done(
								function(htmlContent) {
									$('#content').html('');
									$('#content').append(htmlContent);
									window.history.pushState({}, "",
											"/article/search/?category=" + id
													+ "&categorySearch=true");
									defineClickOnProduct();
									makeThemSquare();
								});
					});

			// ********* Show Product **************
			var defineClickOnProduct = function() {
				$('.product').click(
						function() {
							var id = $(this).data('id');
							$.get("/article/show/item/" + id).done(
									function(htmlContent) {
										$('#content').html('');
										$('#content').append(htmlContent);
										window.history.pushState({}, "",
												"/article/show/item/" + id);
									});
						});
			}

			defineClickOnProduct();
			// ********* Upload **************
			// Ajax inject SubCategories List in Upload
			$('#productCategory').change(
					function() {
						var arr = [];
						$('#productCategory :selected').each(
								function(i, selected) {
									arr[i] = selected.value;
								});
						var id = arr.join('-');
						$.get('/article/subcategories/id/' + id).done(
								function(htmlContent) {
									$('#productSubCategory').html('');
									$('#productSubCategory')
											.append(htmlContent);
								});
					});

			// Button Subcategorysubmit

			$('#newSubCategorySubmit').click(
					function() {
						var mainCat = $('#categoryForSub').val();
						var subCat = $('#newSubCategory').val();
						$.get(
								'/article/submitcategory?id=' + mainCat
										+ '&subCategory=' + subCat).done(
								function(htmlContent) {
									$('#productSubCategory').html('');
									$('#productSubCategory')
											.append(htmlContent);
								});

					});
			$('#toggleCatSubmitDisplay').click(function() {
				if ($(this).data('visible')) {
					$(this).attr('value', 'Erstellen');
					$('#input-Category-Submit').addClass('invisible');
					$(this).data('visible', false);
				} else {
					$(this).prop('value', 'Ausblenden');
					$('#input-Category-Submit').removeClass('invisible');
					$(this).data('visible', true);
				}

			});
			
			
			// ********* Search Type Toggle *******
			
			$('.searchTypeToggle').click(function() {
				var type = $(this).data('type');
				
				switch (type) {
				case 1: 
					$(this).data('type', 2);
					$(this).attr('value', 'Umkreissuche');
					prepareNearBySearch();
					$('#searchForm').attr('action', '/Article/nearbysearch/');
					break;
				case 2: 
					removeNearBySearch();
					$(this).data('type', 3);
					$(this).attr('value', 'Postleitzahl');
					$('#searchForm').attr('action', '/Article/plzsearch/');
					break;					
				case 3: 
					$(this).data('type', 1);
					$(this).attr('value', 'Produktsuche');
					$('#searchForm').attr('action', '/Article/search/');
					break;	
				}
			});
			var prepareNearBySearch = function() {
				$('input.searchfield').remove();
				$('#searchForm .search').prepend('<input class="googleSearch searchfield" type="text" name="search" placeholder="Adresse">');
				$('#searchForm').attr('onsubmit','return searchBeforeSubmit();');
				$('input.googleSearch').each(function(i, el) {
					var options = {};
					autocomplete = new google.maps.places.Autocomplete(el, options);	
				});				
			};  
			
			window.searchBeforeSubmit = function() {
				var lat;
				var lng;
				$.get('http://maps.googleapis.com/maps/api/geocode/json?address='+ $('input.searchfield').value).done(function(json) {
					lat  = json.results[0].geometry.location.lat;
					lng = json.results[0].geometry.location.lng;
					$('#searchForm .search').prepend('<input type="hidden" name="lat" value="'+ lat + '">');
					$('#searchForm .search').prepend('<input type="hidden" name="lng" value="'+ lng	 + '">');
					$('#searchForm').removeAttr('onsubmit');
					document.getElementById('searchForm').submit();
				});
				return false;
			}
			
			var removeNearBySearch = function() {
				$('#searchForm').removeAttr('onsubmit');
				$('input.googleSearch').remove();
				$('#searchForm .search').prepend('<input class="searchfield" type="text" name="search" placeholder="Produktsuche">');
			}
			

			

			// ********* Google Maps **************
			
			$('input#adresse').click(
					function() {
						codeAddress();
					});
			
			$('input#adresse').keyup(
					function() {
						codeAddress();
					});
			
			$('input#adresse').focusout(
					function() {
						codeAddress();
					});
			
			$('input#adresse').change(
					function() {
						codeAddress();
					});
			
			var geocoder;
			var map;

			function initialize() {
				geocoder = new google.maps.Geocoder();
				var latlng = new google.maps.LatLng(-34.397, 150.644);
				var mapOptions = {
					zoom : 16,
					center : latlng
				}
				map = new google.maps.Map(
						document.getElementById('map-canvas'), mapOptions);
			}

			function codeAddress() {
				codeAddress.counter = codeAddress.counter || 1;
				var address = document.getElementById('adresse').value;
				
				if (address.length >= 10 && codeAddress.counter >= 5) {
				initialize();
				geocoder.geocode({
					'address' : address
				}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						map.setCenter(results[0].geometry.location);
						var marker = new google.maps.Marker({
							map : map,
							position : results[0].geometry.location
						});
						document.getElementById('adresse_lat').value = results[0].geometry.location.lat();
						document.getElementById('adresse_lng').value = results[0].geometry.location.lng();
					}
				});
				codeAddress.counter = 0;
				} else {
					codeAddress.counter++;
				}
			}
			
			google.maps.event.addDomListener(window, 'load', initialize);
		});





