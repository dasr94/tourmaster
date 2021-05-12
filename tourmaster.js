(function($){
	"use strict";
	$(document).ready(function(){

		console.log("pagina cargada - torumaster.js")

		
		
		

		$("#add-trip").click(function(){
			$("#insert-trip").show();
		})

		$(".mytours-delete").click(function(){
			var id = $(this).attr('id');
			$.ajax({
				url:'../wp-content/plugins/tourmaster/custom.php',
				type: 'POST',
				data: {id: id},
				sucess : function (response){
					console.log(response);
				}
			})
		})

		$('.tourmaster-page-option-content').each(function(){
			
			var data_input = $(this).siblings('.tourmaster-page-option-value');
			var html_option = new tourmasterHtmlOption($(this));
				
			$('#post-preview, #publish, #save-post').click(function(){
				data_input.val(JSON.stringify(html_option.get_val()));
			});

			// action for tab
			var tab_head = $(this).find('#tourmaster-page-option-tab-head');
			var tab_content = $(this).find('#tourmaster-page-option-tab-content');
			tab_head.children('.tourmaster-page-option-tab-head-item').click(function(){
				if( $(this).hasClass('tourmaster-active') ){ return; }
				
				var active_tab = $(this).attr('data-tab-slug');
				$(this).addClass('tourmaster-active').siblings().removeClass('tourmaster-active');
				tab_content.find('[data-tab-slug="' + active_tab + '"]').fadeIn(200).siblings().css('display', 'none');
			});

			// ajax save
			var security = $(this).siblings('[name="plugin_page_option_security"]').val();
			$(this).siblings('.tourmaster-page-option-head').on('click', '.tourmaster-page-option-head-save', function(){

				if( $(this).hasClass('tourmaster-now-loading') ) return; 

				var nav_update_button = $(this).addClass('tourmaster-now-loading');
				var post_id = $(this).attr('data-post-id');

				data_input.val(JSON.stringify(html_option.get_val()));
				
				// nonce
				$.ajax({
					type: 'POST',
					url: $(this).attr('data-ajax-url'),
					data: { 'security': security, 'action': 'tourmaster_save_page_option_data', 'post_id': post_id, 'name': data_input.attr('name'), 'value': data_input.val() },
					dataType: 'json',
					error: function(jqXHR, textStatus, errorThrown){
						nav_update_button.removeClass('tourmaster-now-loading');
						gdlr_core_alert_box({ status: 'failed', head: nav_update_button.attr('data-failed-head'), message: nav_update_button.attr('data-failed-message') });
						
						// for displaying the debug text
						console.log(jqXHR, textStatus, errorThrown);
					},
					success: function(data){

						nav_update_button.removeClass('tourmaster-now-loading');
						gdlr_core_alert_box({status: data.status, head: data.head, message: data.message});	
					}
				});	
			});
		});
	});

	// Detect Mobile Device
	var tourmaster_mobile = false;
	if( navigator.userAgent.match(/Android/i) || navigator.userAgent.match(/webOS/i) || navigator.userAgent.match(/BlackBerry/i) ||
		navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i) || navigator.userAgent.match(/iPod/i) || navigator.userAgent.match(/Windows Phone/i) ){ 
		tourmaster_mobile = true; 
	}else{ 
		tourmaster_mobile = false; 
	}

	// Detect Screen
	var tourmaster_display = 'desktop';
	if( typeof(window.matchMedia) == 'function' ){
		$(window).on('resize tourmaster-set-display', function(){
			if( window.matchMedia('(max-width: 419px)').matches ){
				tourmaster_display = 'mobile-portrait';
			}else if( window.matchMedia('(max-width: 767px)').matches ){
				tourmaster_display = 'mobile-landscape'
			}else if( window.matchMedia('(max-width: 999px)').matches ){
				tourmaster_display = 'tablet'
			}else{
				tourmaster_display = 'desktop';
			}
		});
		$(window).trigger('tourmaster-set-display');
	}else{
		$(window).on('resize tourmaster-set-display', function(){
			if( $(window).innerWidth() <= 419 ){
				tourmaster_display = 'mobile-portrait';
			}else if( $(window).innerWidth() <= 767 ){
				tourmaster_display = 'mobile-landscape'
			}else if( $(window).innerWidth() <= 999 ){
				tourmaster_display = 'tablet'
			}else{
				tourmaster_display = 'desktop';
			}
		});
		$(window).trigger('tourmaster-set-display');
	}	

	// ref : http://unscriptable.com/2009/03/20/debouncing-javascript-methods/
	// ensure 1 is fired
	var tourmaster_debounce = function(func, threshold, execAsap){
		
		var timeout;

		return function debounced(){
			
			var obj = this, args = arguments;
			
			function delayed(){
				if( !execAsap ){
					func.apply(obj, args);
				}
				timeout = null;
			};

			if( timeout ){
				clearTimeout(timeout);
			}else if( execAsap ){
				func.apply(obj, args);
			}
			timeout = setTimeout(delayed, threshold);
		};
	}	
	
	// reduce the event occurance
	var tourmaster_throttling = function(func, threshold){
		
		var timeout;

		return function throttled(){
			var obj = this, args = arguments;
			
			function delayed(){
				func.apply(obj, args);
				timeout = null;
			};

			if( !timeout ){
				timeout = setTimeout(delayed, threshold);
			}
		};
	}	

	// create the conformation message
	window.tourmaster_front_confirm_box = function(options){

        var settings = $.extend({
			head: '',
			text: '',
			sub: '',
			yes: '',
			no: '',
			success:  function(){}
        }, options);
		
		var confirm_overlay = $('<div class="tourmaster-conform-box-overlay"></div>').appendTo($('body'));
		var confirm_button = $('<span class="tourmaster-confirm-box-button tourmaster-yes">' + settings.yes + '</span>');
		var decline_button = $('<span class="tourmaster-confirm-box-button tourmaster-no">' + settings.no + '</span>');
		
		var confirm_box = $('<div class="tourmaster-confirm-box-wrapper">\
				<div class="tourmaster-confirm-box-head">' + settings.head + '</div>\
				<div class="tourmaster-confirm-box-content-wrapper" >\
					<div class="tourmaster-confirm-box-text">' + settings.text + '</div>\
					<div class="tourmaster-confirm-box-sub">' + settings.sub + '</div>\
				</div>\
			</div>').insertAfter(confirm_overlay);
	
	
		$('<div class="tourmaster-confirm-box-button-wrapper"></div>')
			.append(decline_button).append(confirm_button)
			.appendTo(confirm_box);
		
		// center the alert box position
		confirm_box.css({
			'margin-left': -(confirm_box.outerWidth() / 2),
			'margin-top': -(confirm_box.outerHeight() / 2)
		});
				
		// animate the alert box
		confirm_overlay.css({opacity: 0}).animate({opacity:0.6}, 200);
		confirm_box.css({opacity: 0}).animate({opacity:1}, 200);
		
		confirm_button.click(function(){
			if(typeof(settings.success) == 'function'){ 
				settings.success();
			}
			confirm_overlay.fadeOut(200, function(){
				$(this).remove();
			});
			confirm_box.fadeOut(200, function(){
				$(this).remove();
			});
		});
		decline_button.click(function(){
			confirm_overlay.fadeOut(200, function(){
				$(this).remove();
			});
			confirm_box.fadeOut(200, function(){
				$(this).remove();
			});
		});
		
	} // tourmaster_front_confirm_box

	// set cookie
	function tourmaster_set_cookie( cname, cvalue, expires ){
		if( typeof(expires) != 'undefined' ){
			if( expires == 0 ){
				expires = 86400;
			}

			var now = new Date();
			var new_time  = now.getTime() + (parseInt(expires) * 1000);
			now.setTime(new_time);

			expires = now.toGMTString();
		}

	    document.cookie = cname + "=" + encodeURIComponent(cvalue) + "; expires=" + expires + "; path=/";
	}

	// tourmaster lightbox
	function tourmaster_lightbox( content ){

		var lightbox_wrap = $('<div class="tourmaster-lightbox-wrapper" ></div>').hide();
		var lightbox_content_wrap = $('<div class="tourmaster-lightbox-content-cell" ></div>');
		lightbox_wrap.append(lightbox_content_wrap);
		lightbox_content_wrap.wrap($('<div class="tourmaster-lightbox-content-row" ></div>'));

		lightbox_content_wrap.append(content);

		var scrollPos = $(window).scrollTop();
		$('html').addClass('tourmaster-lightbox-on');
		$('body').append(lightbox_wrap);
		lightbox_wrap.fadeIn(300);

		// bind lightbox form script
		tourmaster_form_script(lightbox_wrap);

		// rating action
		tourmaster_rating(lightbox_wrap);

		// do a lightbox action
		lightbox_wrap.on('click', '.tourmaster-lightbox-close', function(){
			$('html').removeClass('tourmaster-lightbox-on');
			$(window).scrollTop(scrollPos);
			lightbox_wrap.fadeOut(300, function(){
				$(this).remove();
			});
		});

		// verify 
		lightbox_content_wrap.find('form').not('.tourmaster-register-form').each(function(){

			// required field
			$(this).submit(function(){
				var validate = true;
				var error_box = $(this).find('.tourmaster-lb-submit-error');
				error_box.slideUp(200);

				$(this).find('input[data-required], select[data-required], textarea[data-required]').each(function(){
					if( !$(this).val() ){
						validate = false;
					}
				});

				if( !validate ){
					error_box.slideDown(200);
				}

				return validate;
			});

		});

	} // tourmaster_lightbox

	// rating
	function tourmaster_rating( container ){

		container.find('.tourmaster-review-form-rating, .tourmaster-tour-search-field-rating').each(function(){

			$(this).children('.tourmaster-rating-select').click(function(){
				$(this).siblings('input').val($(this).attr("data-rating-score"));

				if($(this).is('i')){ $(this).removeClass().addClass('tourmaster-rating-select fa fa-star-half-empty'); }
				$(this).prevAll('i').removeClass().addClass('tourmaster-rating-select fa fa-star');
				$(this).nextAll('i').removeClass().addClass('tourmaster-rating-select fa fa-star-o');
			});

		});

	}

	// form script
	function tourmaster_form_script( container ){
		
		if( typeof(container) == 'undefined' ){
			var date_select = $('.tourmaster-date-select');
			var input_file = $('.tourmaster-file-label');
		}else{
			var date_select = container.find('.tourmaster-date-select');
			var input_file = container.find('.tourmaster-file-label');
		}

		// fill the date option
		date_select.on('change', 'select', function(){
			var parent = $(this).closest('.tourmaster-date-select');
			var date = 0;
			var month = 0;
			var year = 0;

			parent.find('select[data-type]').each(function(){
				if( $(this).attr('data-type') == 'date' ){
					date = parseInt($(this).val());
				}else if( $(this).attr('data-type') == 'month' ){
					month = parseInt($(this).val());
				}else if( $(this).attr('data-type') == 'year' ){
					year = parseInt($(this).val());
				}
			});

			if( date > 0 && month > 0 && year > 0 ){
				parent.siblings('input[name]').val(year + '-' + month + '-' + date);
			}

		});

		// input file 
		input_file.on('change', 'input[type="file"]', function(){
			var label_text = $(this).siblings('.tourmaster-file-label-text');

			if( $(this).val() ){
				label_text.html($(this).val().split('\\').pop());
			}else{
				label_text.html(label_text.attr('data-default'));
			}
		});

	}

	// single review
	$.fn.tourmaster_single_review = function(){
		var review_filter = $(this).find('#tourmaster-single-review-filter');
		var review_content = $(this).find('#tourmaster-single-review-content');

		// bind the filter
		var sort_by = review_filter.find('[data-sort-by]');
		var filter_by = review_filter.find('#tourmaster-filter-by');

		sort_by.click(function(){
			if( $(this).hasClass('tourmaster-active') ) return false;

			$(this).addClass('tourmaster-active').siblings('[data-sort-by]').removeClass('tourmaster-active');
			tourmaster_get_review_ajax({
				'action': 'get_single_tour_review',
				'tour_id': review_content.attr('data-tour-id'),
				'sort_by': $(this).attr('data-sort-by'),
				'filter_by': filter_by.val()
			}, review_content);
		});
		filter_by.change(function(){
			tourmaster_get_review_ajax({
				'action': 'get_single_tour_review',
				'tour_id': review_content.attr('data-tour-id'),
				'sort_by': sort_by.filter('.tourmaster-active').attr('data-sort-by'),
				'filter_by': $(this).val()
			}, review_content);
		});

		review_content.on('click', '[data-paged]', function(){
			tourmaster_get_review_ajax({
				'action': 'get_single_tour_review',
				'tour_id': review_content.attr('data-tour-id'),
				'paged': $(this).attr('data-paged'),
				'sort_by': sort_by.filter('.tourmaster-active').attr('data-sort-by'),
				'filter_by': filter_by.val()
			}, review_content);
		});

	}
	function tourmaster_get_review_ajax( ajax_data, content_section ){
		content_section.animate({opacity: 0.2}, 200);

		$.ajax({
			type: 'POST',
			url: content_section.attr('data-ajax-url'),
			data: ajax_data,
			dataType: 'json',
			error: function( jqXHR, textStatus, errorThrown ){

				content_section.animate({opacity: 1}, 200);

				// print error message for debug purpose
				console.log(jqXHR, textStatus, errorThrown);
			},
			success: function( data ){

				if( typeof(data.content) != 'undefined' ){
					var old_height = content_section.outerHeight();
					content_section.html(data.content);

					var new_height = content_section.outerHeight();
					content_section.css({'height': old_height});
					content_section.animate({height: new_height}, 200 , function(){
						content_section.css({height: 'auto'});
					});
				}	

				content_section.animate({opacity: 1}, 200);

			}
		});
	}

	function tourmaster_date_diff(date1, date2){
		var dt1 = new Date(date1);
		var dt2 = new Date(date2);
		return Math.floor((Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) - Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate()) ) /(1000 * 60 * 60 * 24));
	}

	// datepicker
	$.fn.tourmaster_datepicker = function(){

		$(this).each(function(){
			var alternate_field = $(this).siblings('.tourmaster-datepicker-alt');
			var date_format = $(this).attr('data-date-format');

				

			if( !$(this).attr('data-tour-date') ){
				$(this).datepicker({
					dateFormat: date_format,
					altFormat: 'yy-mm-dd',
					altField: alternate_field,
					changeMonth: true,
					changeYear: true
				});
			}else{
				var date_range = $(this).attr('data-tour-range');
				var available_date = JSON.parse($(this).attr('data-tour-date'));	
				var current_date = $(this).val();
				var selected_date = current_date;

				$(this).datepicker({
					dateFormat: date_format,
					altFormat: 'yy-mm-dd',
					altField: alternate_field,
					changeMonth: true,
					changeYear: true,
					minDate: new Date(available_date[0]),
					maxDate: new Date(available_date[ available_date.length - 1 ]),

					// determine selectable date
					beforeShowDay: function( date ){
						current_date  = date.getFullYear() + '-';
						current_date += ('0' +(date.getMonth() + 1)).slice(-2) + '-';
						current_date += ('0' + date.getDate()).slice(-2);

						var extra_class = '';
						var date_diff = tourmaster_date_diff(selected_date, current_date);
						if( date_diff >= 0 && date_diff < date_range ){
							extra_class = 'tourmaster-highlight';
						}

						if( available_date.indexOf(current_date) >= 0 ){
							return [true, extra_class, ''];
						}else{
							return [false, extra_class, ''];
						}
					},

					// for date range
					onSelect: function( dateText, inst ){
						selected_date  = inst.selectedYear + '-';
						selected_date += ('0' +(inst.selectedMonth + 1)).slice(-2) + '-';
						selected_date += ('0' + inst.selectedDay).slice(-2);

						alternate_field.trigger('change');
					},

					// datepicker position right
				 	beforeShow: function(input, inst){
				        var widget = $(inst).datepicker('widget');
				        if( $("body").hasClass("rtl") ){
							widget.css('margin-left', widget.outerWidth() - $(input).outerWidth() );
						}else{
							widget.css('margin-left', $(input).outerWidth() - widget.outerWidth());
						}
				        widget.css('margin-top', -2);
				    },

					// for localization
					closeText: TMi18n.closeText,
					currentText: TMi18n.currentText,
					monthNames: TMi18n.monthNames,
					monthNamesShort: TMi18n.monthNamesShort,
					dayNames: TMi18n.dayNames,
					dayNamesShort: TMi18n.dayNamesShort,
					dayNamesMin: TMi18n.dayNamesMin,
					firstDay: TMi18n.firstDay
				});
				
				var initial_date = new Date(current_date + 'T00:00:00+00:00');
				initial_date = new Date(initial_date.getTime() + (initial_date.getTimezoneOffset() * 60000));
				$(this).datepicker('setDate', initial_date);
			}
		});

	} // tourmaster_datepicker

	// tour booking bar
	function tourmaster_tour_booking_ajax( ajax_url, ajax_settings, ajax_data ){

		var ajax_settings = $.extend({
			beforeSend: function( jqXHR, settings ){},
			error: function( jqXHR, textStatus, errorThrown ){

				// print error message for debug purpose
				console.log(jqXHR, textStatus, errorThrown);
			},
			success: function( data ){ 
				// console.log('success', data); 
			}
		}, ajax_settings);

		var ajax_data = $.extend({
			action: 'tourmaster_tour_booking',
		}, ajax_data);

		$.ajax({
			type: 'POST',
			url: ajax_url,
			data: ajax_data,
			dataType: 'json',
			beforeSend: ajax_settings.beforeSend,
			error: ajax_settings.error,
			success: ajax_settings.success
		});
	}
	function tourmaster_tour_input( form ){
		var ret = {};

		form.find('input[name], select[name], textarea[name]').each(function(){
			var key = $(this).attr('name');
			if( (key.lastIndexOf('[]') == (key.length - 2)) ){
				key = key.substr(0, key.length - 2);
				if( typeof(ret[key]) != 'object' ){
					ret[key] = []
				}

				ret[key].push($(this).val());
			}else{	
				ret[key] = $(this).val();
			}
		});

		return ret;
	}
	function tourmaster_get_booking_detail( form ){
		var booking_detail = {};

		form.find('input[name], select[name], textarea[name]').each(function(){
			var key = $(this).attr('name');
			var value;

			if( $(this).is('[type="checkbox"]') ){
				var value = $(this).is(':checked')? $(this).val(): 0;
			}else if( $(this).is('[type="radio"]') ){
				if( $(this).is(':checked') ){
					var value = $(this).val();
				}else{
					return;
				}
			}else{
				var value = $(this).val();
			}

			if( (key.lastIndexOf('[]') == (key.length - 2)) ){
				key = key.substr(0, key.length - 2);
				if( typeof(booking_detail[key]) != 'object' ){
					booking_detail[key] = []
				}

				booking_detail[key].push(value);
			}else{	
				booking_detail[$(this).attr('name')] = value;
			}
		});	

		// console.log(booking_detail);

		return booking_detail;
	}
	$.fn.tourmaster_tour_booking = function(){

		var form = $(this);
		var ajax_url = $(this).attr('data-ajax-url');

		// step 1
		$(this).on('change', 'input[name="tour-date"], select[name="tour-date"]', function(){

			var sent_data = tourmaster_tour_input(form);
			sent_data['step'] = 1;

			// remove unrelated input
			form.find('[data-step]').each(function(){
				if( $(this).attr('data-step') > 1 ){
					$(this).slideUp(200, function(){ $(this).remove(); });
				}
			});

			// get new input
			tourmaster_tour_booking_ajax(ajax_url, {
				success: function( data ){
					if( typeof(data.content) != 'undefined' ){

						// remove unrelated input once again
						form.find('[data-step]').each(function(){
							if( $(this).attr('data-step') > 1 ){
								$(this).slideUp(200, function(){ $(this).remove(); });
							}
						});

						var content = $(data.content).hide();
						form.append(content);
						content.find('.tourmaster-datepicker').tourmaster_datepicker();
						content.slideDown(200);
					}
				}
			}, {
				data: sent_data
			});
		});

		// step 2
		$(this).on('change', 'input[name="package"]', function(){

			var sent_data = tourmaster_tour_input(form);
			sent_data['step'] = 2;

			// remove unrelated input
			form.find('[data-step]').each(function(){
				if( $(this).attr('data-step') > 2 ){
					$(this).slideUp(200, function(){ $(this).remove(); });
				}
			});

			// get new input
			tourmaster_tour_booking_ajax(ajax_url, {
				success: function( data ){
					if( typeof(data.content) != 'undefined' ){
						var content = $(data.content).hide();
						form.append(content);
						content.find('.tourmaster-datepicker').tourmaster_datepicker();
						content.slideDown(200);
					}
				}
			}, {
				data: sent_data
			});
		});

		// step 3
		$(this).on('change', 'select[name="tour-room"]', function(){
			var wrap = $(this).closest('.tourmaster-tour-booking-room');
			var template = wrap.siblings('.tourmaster-tour-booking-room-template').children();
			var container = wrap.siblings('.tourmaster-tour-booking-people-container');
			var container_animate = false;

			if( $(this).val() && container.length == 0 ){
				var container = $('<div class="tourmaster-tour-booking-people-container" data-step="999" ></div>').hide();
				container.insertAfter(wrap);
				container_animate = true;
			}

			if( $(this).val() ){
				var count = parseInt($(this).val()) - container.children().length;

				// add template fields
				if( count > 0 ){
					for( var i = 0; i < count; i++ ){
						var clone = template.clone();
						clone.attr('data-step', 4);
						clone.find('.tourmaster-tour-booking-room-text > span').html((container.children().length + 1));

						container.append(clone);
						if( !container_animate ){
							clone.hide();
							clone.slideDown(200);
						}
					} 

				// remove excess fields
				}else if( count < 0 ){
					container.children('div').slice(count).slideUp(200, function(){ $(this).remove(); });	
				}

				if( container_animate ){
					container.slideDown(200);
				}
			
			}else{
				// remove container out
				if( container.length > 0 ){
					container.slideUp(200, function(){ $(this).remove(); });
				}
			}
		});
		// $(this).find('select[name="tour-room"]').trigger('change');

		// for updating the head price
		if( $(this).hasClass('tourmaster-update-header-price') ){
			$(this).on('change', 'input, select', function(){

				var booking_data = tourmaster_get_booking_detail(form);
				
				// if( $(this).is('[name="package"]') ){
				// 	booking_data['tour-people'] = 0;
				// 	booking_data['tour-adult'] = 0;
				// 	booking_data['tour-male'] = 0;
				// 	booking_data['tour-female'] = 0;
				// 	booking_data['tour-children'] = 0;
				// 	booking_data['tour-student'] = 0;
				// 	booking_data['tour-infant'] = 0;
				// }
				
				tourmaster_tour_booking_ajax(ajax_url, {
					success: function( data ){
						if( typeof(data.price) != 'undefined' ){
							var header_price = $('.tourmaster-header-price');
							header_price.find('.tourmaster-tour-discount-price').remove();
							header_price.find('.tourmaster-tour-price-wrap').removeClass('tourmaster-discount');
							header_price.addClass('tourmaster-price-updated').find('.tourmaster-tour-price .tourmaster-tail').html(data.price);
							$(window).trigger('resize');
						}
					}
				}, {
					action: 'tourmaster_update_head_price',
					data: booking_data
				});
			});		
		}

		// validate input before submitting
		$(this).on('click', 'input[type="submit"]', function(){

			var submit_button = $(this);
			var error_message = $(this).siblings('.tourmaster-tour-booking-submit-error');

			// validate extra fields
			var validate = true;
			form.find('input[data-required], select[data-required], textarea[data-required]').each(function(){
				if( !$(this).val() ){
					validate = false;
				}
			});
			if( !validate ){
				error_message.slideDown(200);
				return false;
			}
			
			var extra_booking_info = {};
			form.find('.tourmaster-extra-booking-field').find('input, select, textarea').each(function(){
				extra_booking_info[$(this).attr('name')] = $(this).val();
			});

			// validate booking fields
			var submit = true;
			var tour_package = '';
			var traveller_amount = 0;
			var adult_amount = 0;
			var male_amount = 0;
			var female_amount = 0;
			var max_traveller_per_room = 0;
			error_message.filter('.tourmaster-temp').slideUp(200, function(){ $(this).remove() });

			form.find('[data-step]').each(function(){
				var step = $(this).attr('data-step');
				if( step == 1 || step == 2 || step == 3 ){
					$(this).find('input[name], select[name]').each(function(){
						if( $(this).val() == "" ){
							submit = false;
						}else if( $(this).attr('name') == 'package' ){
							tour_package = $(this).val();
						}
					});
				}else if( step == 4 ){
					var num_people = 0;
					var room_people = 0;
					$(this).find('select[name], input[name]').each(function(){
						if( $(this).attr('name') == 'group' ){
							traveller_amount = 'group';
							adult_amount = 'group';
						}else if( $(this).val() != "" ){
							room_people += parseInt($(this).val());

							if( $(this).is('[name^="tour-adult"], [name^="tour-people"]') ){
								adult_amount += room_people;
							}else if( $(this).is('[name^="tour-male"]') ){
								male_amount += room_people;
								adult_amount += room_people;
							}else if( $(this).is('[name^="tour-female"]') ){
								female_amount += room_people;
								adult_amount += room_people;
							}
						}
					});

					num_people += room_people;
					if( room_people > max_traveller_per_room ){
						max_traveller_per_room = room_people;
					}

					if( traveller_amount != 'group' ){
						if( num_people <= 0 ){
							submit = false;
						}else{
							traveller_amount += num_people;
						}
					}
				}
			});

			if( !submit ){
				error_message.slideDown(200);
			}else{
				error_message.slideUp(200);

				submit_button.animate({ opacity: 0.5 });
				tourmaster_tour_booking_ajax(ajax_url, {
					success: function( data ){
						
						if( data.status == 'success' ){
							if( submit && submit_button.attr('data-ask-login') ){;
								
								// get lightbox content
								var content = submit_button.siblings('[data-tmlb-id="' + submit_button.attr('data-ask-login') + '"]');
								if( content.length == 0 ){
									content = form.closest('form').siblings('[data-tmlb-id="' + submit_button.attr('data-ask-login') + '"]');
								}
								var lb_content = content.clone();
								
								// check for social login plugin
								if( lb_content.find('.nsl-container-block').length > 0 ){
									lb_content.find('.nsl-container-block').replaceWith(content.find('.nsl-container-block').clone(true));
								}
								
								tourmaster_lightbox(lb_content);

								var booking_detail = tourmaster_get_booking_detail(form);
								tourmaster_set_cookie('tourmaster-booking-detail', JSON.stringify(booking_detail), 0);
							}else{
								var booking_detail = tourmaster_get_booking_detail(form);
								tourmaster_set_cookie('tourmaster-booking-detail', JSON.stringify(booking_detail), 0);
								
								form.submit();
							}
						}else if( typeof(data.message) != 'undefined' ){
							var temp_error = $('<div class="tourmaster-tour-booking-submit-error tourmaster-temp" ></div>').html(data.message);
							temp_error.insertAfter(submit_button);
							temp_error.slideDown(200);
						}
						submit_button.animate({ opacity: 1 });
					}
				}, {
					action: 'tourmaster_tour_booking_amount_check',
					tid: form.find('[name="tid"]').val(),
					tour_id: form.find('[name="tour-id"]').val(),
					tour_date: form.find('[name="tour-date"]').val(),
					traveller: traveller_amount,
					'adult_amount': adult_amount,
					'male_amount': male_amount,
					'female_amount': female_amount,
					'package': tour_package,
					'max_traveller_per_room': max_traveller_per_room,
					'extra_booking_info': extra_booking_info
				});
			}

			return false;
		});
		
	}

	$.fn.tourmaster_tour_booking_sticky = function(){

		// animate sidebar
		$(this).each(function(){
			var page_wrap = $(this).closest('.tourmaster-page-wrapper');
			var template_wrap = page_wrap.children('.tourmaster-template-wrapper');
			var booking_bar_wrap = $(this);
			var booking_bar_anchor = $(this).siblings('.tourmaster-tour-booking-bar-anchor');
			var top_offset = parseInt($('html').css('margin-top'));
			var left_offset = parseInt(booking_bar_anchor.css('margin-left'));
			var right_offset = parseInt(booking_bar_anchor.css('margin-right'));

			// hide header price and replace with header price in the booking bar
			if( page_wrap.hasClass('tourmaster-tour-style-1') ){ 
				$(this).addClass('tourmaster-start-script');
				page_wrap.siblings('.tourmaster-single-header').addClass('tourmaster-start-script');
				
				var header_price = $(this).children('.tourmaster-tour-booking-bar-outer').children('.tourmaster-header-price');
				booking_bar_wrap.css('margin-top', -header_price.outerHeight());
				booking_bar_anchor.css('margin-top', -header_price.outerHeight());
				page_wrap.css('min-height', booking_bar_wrap.height() - header_price.outerHeight());
				$(window).resize(function(){
					booking_bar_wrap.css('margin-top', -header_price.outerHeight());
					booking_bar_anchor.css('margin-top', -header_price.outerHeight());
					page_wrap.css('min-height', booking_bar_wrap.height() - header_price.outerHeight())

					if( $("body").hasClass("rtl") ){
						booking_bar_wrap.css({ 
							'position': '', 
							'top': '', 
							'right': '',
							'margin-top': booking_bar_anchor.css('margin-top')
						});
					}else {
						booking_bar_wrap.css({ 
							'position': '', 
							'top': '', 
							'left': '',
							'margin-top': booking_bar_anchor.css('margin-top')
						});
					}
					booking_bar_wrap.removeClass('tourmaster-fixed tourmaster-top tourmaster-bottom tourmaster-lock');
				}); 
			}

			// scroll action
			var top_padding = 0;
			var prev_scroll = 0;
			$(window).on('scroll resize', function(){

				var animate_on_scroll = true;
				if( tourmaster_display == 'mobile-landscape' || tourmaster_display == 'mobile-portrait' || tourmaster_display == 'tablet' ){
					animate_on_scroll = false;
				}

				var scroll_direction = (prev_scroll > $(window).scrollTop())? 'up': 'down';
				prev_scroll = $(window).scrollTop();

				// fixed nav bar
				if( animate_on_scroll && $(window).scrollTop() + top_offset + top_padding > booking_bar_anchor.offset().top ){

					// bar smaller than screensize
					if( $(window).height() > booking_bar_wrap.outerHeight() + top_padding ){
						
						if( $(window).scrollTop() + booking_bar_wrap.outerHeight() + top_offset + (top_padding * 2) > page_wrap.offset().top + page_wrap.outerHeight() ){
								
								if( !booking_bar_wrap.hasClass('tourmaster-fixed-lock') ){
									if( $("body").hasClass("rtl") ){
										booking_bar_wrap.css({
											'position': 'absolute',
											'top': template_wrap.outerHeight() - booking_bar_wrap.outerHeight() - top_padding,
											'right': 'auto',
											'margin-top': 0
										});
									}else{
										booking_bar_wrap.css({
											'position': 'absolute',
											'top': template_wrap.outerHeight() - booking_bar_wrap.outerHeight() - top_padding,
											'left': 'auto',
											'margin-top': 0
										});
									}

									booking_bar_wrap.removeClass('tourmaster-fixed');
									booking_bar_wrap.addClass('tourmaster-fixed-lock');
								}
							
						}else if( !booking_bar_wrap.hasClass('tourmaster-fixed') ){
							if( $("body").hasClass("rtl") ){
								booking_bar_wrap.css({ 
									'position': 'fixed', 
									'top': top_padding + top_offset, 
									'right': $(window).width() - (booking_bar_anchor.offset().left + booking_bar_anchor.outerWidth() ) - right_offset, 
									'margin-top': 0 
								});		
							}else {
								booking_bar_wrap.css({ 
									'position': 'fixed', 
									'top': top_padding + top_offset, 
									'left': booking_bar_anchor.offset().left - left_offset, 
									'margin-top': 0 
								});
							}

							booking_bar_wrap.removeClass('tourmaster-fixed-lock');
							booking_bar_wrap.addClass('tourmaster-fixed');
						}else{
							if( booking_bar_wrap.hasClass('tourmaster-fixed') ){
								if( $("body").hasClass("rtl") ){
									booking_bar_wrap.css({ 
										'right': $(window).width() - (booking_bar_anchor.offset().left + booking_bar_anchor.outerWidth() ) - right_offset, 
									});		
								}else {
									booking_bar_wrap.css({ 
										'left': booking_bar_anchor.offset().left - left_offset, 
									});
								}
							}	
						}

					// bar larger than screensize
					}else{

						// scroll down
						if( scroll_direction == 'down' ){
							
							if( booking_bar_wrap.hasClass('tourmaster-top') ){
								if( $("body").hasClass("rtl") ){
									booking_bar_wrap.css({
										'position': 'absolute',
										'top': $(window).scrollTop() + top_padding + top_offset - booking_bar_wrap.parent().offset().top,
										'right': 'auto',
										'margin-top': 0
									});	
								}else {
									booking_bar_wrap.css({
										'position': 'absolute',
										'top': $(window).scrollTop() + top_padding + top_offset - booking_bar_wrap.parent().offset().top,
										'left': 'auto',
										'margin-top': 0
									});
								}

								booking_bar_wrap.removeClass('tourmaster-top');
								booking_bar_wrap.addClass('tourmaster-lock');
							
							}else if( $(window).scrollTop() + $(window).height() > page_wrap.offset().top + page_wrap.outerHeight() ){

								if( !booking_bar_wrap.hasClass('tourmaster-lock') ){
									if( $("body").hasClass("rtl") ){ 
										booking_bar_wrap.css({
											'position': 'absolute',
											'top': template_wrap.outerHeight() - booking_bar_wrap.outerHeight(),
											'right': 'auto',
											'margin-top': 0
										});
									}else {
										booking_bar_wrap.css({
											'position': 'absolute',
											'top': template_wrap.outerHeight() - booking_bar_wrap.outerHeight(),
											'left': 'auto',
											'margin-top': 0
										});
									}

									booking_bar_wrap.removeClass('tourmaster-bottom');
									booking_bar_wrap.addClass('tourmaster-lock');
								}
							
							}else if( $(window).scrollTop() + $(window).height() > booking_bar_wrap.offset().top + booking_bar_wrap.outerHeight() ){	
								if( !booking_bar_wrap.hasClass('tourmaster-bottom') ){
									if( $("body").hasClass("rtl") ){
										booking_bar_wrap.css({ 
											'position': 'fixed', 
											'top': $(window).height() - booking_bar_wrap.outerHeight(),
											'right': $(window).width() - (booking_bar_anchor.offset().left + booking_bar_anchor.outerWidth() ) - right_offset, 
											'margin-top': 0 
										});
									}else {
										booking_bar_wrap.css({ 
											'position': 'fixed', 
											'top': $(window).height() - booking_bar_wrap.outerHeight(),
											'left': booking_bar_anchor.offset().left - left_offset, 
											'margin-top': 0 
										});
									}

									booking_bar_wrap.removeClass('tourmaster-top tourmaster-lock');
									booking_bar_wrap.addClass('tourmaster-bottom');
								}
							}else{
								if( booking_bar_wrap.hasClass('tourmaster-bottom') ){
									if( $("body").hasClass("rtl") ){
										booking_bar_wrap.css({ 
											'right': $(window).width() - (booking_bar_anchor.offset().left + booking_bar_anchor.outerWidth() ) - right_offset, 
										});
									}else{
										booking_bar_wrap.css({ 
											'left': booking_bar_anchor.offset().left - left_offset
										});
									}
								}
							}

						// scroll up
						}else{
							if( booking_bar_wrap.hasClass('tourmaster-bottom') ){
								if( $("body").hasClass("rtl") ){
									booking_bar_wrap.css({
										'position': 'absolute',
										'top': $(window).scrollTop() + $(window).height() - booking_bar_wrap.outerHeight() - booking_bar_wrap.parent().offset().top,
										'right': 'auto',
										'margin-top': 0
									});
								}else {
									booking_bar_wrap.css({
										'position': 'absolute',
										'top': $(window).scrollTop() + $(window).height() - booking_bar_wrap.outerHeight() - booking_bar_wrap.parent().offset().top,
										'left': 'auto',
										'margin-top': 0
									});
								}

								booking_bar_wrap.removeClass('tourmaster-bottom');
								booking_bar_wrap.addClass('tourmaster-lock');
							}else if( booking_bar_wrap.hasClass('tourmaster-lock') && $(window).scrollTop() + top_offset + top_padding < booking_bar_wrap.offset().top ){
								if( $("body").hasClass("rtl") ){ 
									booking_bar_wrap.css({ 
										'position': 'fixed', 
										'top': top_padding + top_offset,
										'right': $(window).width() - (booking_bar_anchor.offset().left + booking_bar_anchor.outerWidth() ) - right_offset, 
										'margin-top': 0 
									});
								}else {
									booking_bar_wrap.css({ 
										'position': 'fixed', 
										'top': top_padding + top_offset,
										'left': booking_bar_anchor.offset().left - left_offset, 
										'margin-top': 0 
									});
								}

								booking_bar_wrap.removeClass('tourmaster-bottom tourmaster-lock');
								booking_bar_wrap.addClass('tourmaster-top');
							}
						}
					}

				// retun nav bar to original position
				}else{

					if( booking_bar_wrap.hasClass('tourmaster-fixed') || booking_bar_wrap.hasClass('tourmaster-top') ||
						booking_bar_wrap.hasClass('tourmaster-bottom') ||booking_bar_wrap.hasClass('tourmaster-lock') ){

						if( $("body").hasClass("rtl") ){
							booking_bar_wrap.css({ 
								'position': '', 
								'top': '', 
								'right': '',
								'margin-top': booking_bar_anchor.css('margin-top')
							});
						}else {
							booking_bar_wrap.css({ 
								'position': '', 
								'top': '', 
								'left': '',
								'margin-top': booking_bar_anchor.css('margin-top')
							});
						}
						booking_bar_wrap.removeClass('tourmaster-fixed tourmaster-top tourmaster-bottom tourmaster-lock');
					}
				}

			});
		});

	} // tourmaster_tour_booking_sticky

	var tourmaster_payment_template = function(){

		var t = this;
		t.form = $('#tourmaster-payment-template-wrapper');
		t.sidebar = t.form.find('#tourmaster-tour-booking-bar-inner');
		t.content = t.form.find('#tourmaster-tour-payment-content');

		t.payment_step = $('#tourmaster-payment-step-wrap');
		t.payment_template = $('#tourmaster-page-wrapper');
		t.init();
	}
	tourmaster_payment_template.prototype = {

		init: function(){
			
			var t = this;

			t.bind_script();
			t.bind_script_recurring();

			// bind the next state button
			t.form.on('click', '.tourmaster-payment-step', function(){
				
				var booking_detail_data = t.get_booking_detail();
				if( $(this).attr('data-name') ){
					booking_detail_data[$(this).attr('data-name')] = $(this).attr('data-value');
				}
				if( $(this).attr('data-step') ){
					booking_detail_data['step'] = $(this).attr('data-step');
				}

				if( t.check_required_field(booking_detail_data['step']) ){
					t.change_step({
						booking_detail: booking_detail_data
					});
				}
				
			});

			// bind the change state button
			t.payment_step.on('click', '.tourmaster-payment-step-item', function(){
				if( $(this).hasClass('tourmaster-enable') ){
					var booking_detail_data = t.get_booking_detail();
					if( $(this).attr('data-step') ){
						booking_detail_data['step'] = $(this).attr('data-step');
					}

					if( t.check_required_field(booking_detail_data['step']) ){
						t.change_step({
							booking_detail: booking_detail_data
						});
					}
				}
			});

			// additional service ajax
			t.form.on('change input', '.tourmaster-payment-service-form-wrap input', tourmaster_debounce(function(e){
				if( e.type == 'change' && $(e.target).is('input[type="text"]') ) return;

				var booking_detail_data = t.get_booking_detail();

				if( $(this).attr('data-step') ){
					booking_detail_data['step'] = 3;
				}

				t.change_step({
					booking_detail: booking_detail_data,
					sub_action: 'update_sidebar'
				});

			}, 1000));

			// bind the deposit button
			t.form.on('change', 'input[name="payment-type"]', function(){

				var total_price_wrap = $(this).closest('.tourmaster-tour-booking-bar-total-price-wrap');
				var deposit_price_wrap = total_price_wrap.siblings('.tourmaster-tour-booking-bar-deposit-text');

				if( $(this).is(':checked') ){
					if( $(this).val() == 'full' ){
						total_price_wrap.removeClass('tourmaster-deposit');
						deposit_price_wrap.slideUp(200);
					}else if( $(this).val() == 'partial' ){
						total_price_wrap.addClass('tourmaster-deposit');
						deposit_price_wrap.slideDown(200);
					}
				}
			});

		},

		animate_content: function(element, content){

			var orig_height = element.outerHeight();
			element.html(content);
			var new_height = element.outerHeight();

			// animate
			element.css({height: orig_height});
			element.animate({height: new_height}, function(){
				element.css({height: 'auto'}, 1000)
			});
		},

		// bind general script
		bind_script_recurring: function(){

			var t = this;

			// or divider
			t.content.find('#tourmaster-payment-method-or').each(function(){
				var divider_width = ($(this).width() - $(this).children('.tourmaster-middle').width() - 40) / 2;
				$(this).children('.tourmaster-left, .tourmaster-right').css('width', divider_width);
			});
			$(window).resize(function(){
				t.content.find('#tourmaster-payment-method-or').each(function(){
					var divider_width = ($(this).width() - $(this).children('.tourmaster-middle').width() - 40) / 2;
					$(this).children('.tourmaster-left, .tourmaster-right').css('width', divider_width);
				});
			});

		}, 

		bind_script: function(){

			var t = this;

			// max unit
			t.form.on('change input', '[data-max-unit]', function(){
				if( parseInt($(this).val()) > parseInt($(this).attr('data-max-unit')) ){
					$(this).val($(this).attr('data-max-unit'));
				}

			});

			// view price breakdown
			t.sidebar.on('click', '#tourmaster-tour-booking-bar-price-breakdown-link', function(){
				$(this).siblings('.tourmaster-price-breakdown').slideToggle(200);
			});

			// edit date
			t.sidebar.on('click', '.tourmaster-tour-booking-bar-date-edit', function(){
				var temp_form = $(this).siblings('form');
				var booking_detail = t.get_booking_detail();
				temp_form.append($('<input name="tour_temp" />').val(JSON.stringify(booking_detail)));
				temp_form.submit();
			});

			// coupon
			t.sidebar.on('click', '.tourmaster-tour-booking-bar-coupon-validate', function(){

				var coupon_code = $(this).siblings('[name="coupon-code"]');
				var coupon_message = $(this).siblings('.tourmaster-tour-booking-coupon-message');

				$.ajax({
					type: 'POST',
					url: $(this).attr('data-ajax-url'),
					data: { 'coupon_code': coupon_code.val(), 'tour_id': $(this).attr('data-tour-id'), 'action': 'tourmaster_validate_coupon_code' },
					dataType: 'json',
					beforeSend: function(){
						coupon_code.animate({opacity: 0.3});
						coupon_message.slideUp(150);
						coupon_message.removeClass('tourmaster-success tourmaster-failed');
					},
					error: function( jqXHR, textStatus, errorThrown ){

						// print error message for debug purpose
						console.log(jqXHR, textStatus, errorThrown);
					},
					success: function( data ){
						coupon_code.animate({opacity: 1});

						if( data.status == 'success' ){
							var booking_detail_data = t.get_booking_detail();
							t.change_step({ booking_detail: booking_detail_data, sub_action: 'update_sidebar' });
						}else{
							coupon_message.addClass('tourmaster-' + data.status);
							coupon_message.html(data.message);
							coupon_message.slideDown(150);
						}
					}
				});
				

			});

			// payment billing
			t.content.on('click', '#tourmaster-payment-billing-copy', function(){
				if( $(this).is(':checked') ){
					var billing_info = $(this).closest('.tourmaster-payment-billing-wrap');
					var contact_info = billing_info.siblings('.tourmaster-payment-contact-wrap');

					billing_info.find('[data-contact-detail]').each(function(){
						var contact_field = contact_info.find('[name="' + $(this).attr('data-contact-detail') + '"]');
						$(this).val(contact_field.val());
					});
				}
			});

			// lightbox popup
			t.content.on('click', '[data-tmlb]', function(){
				var lb_content = $(this).siblings('[data-tmlb-id="' + $(this).attr('data-tmlb') + '"]');
				tourmaster_lightbox(lb_content.clone());
			});

			// payment button
			t.content.on('click', '[data-method]', function(){
				if( t.check_required_field(4) ){
					var action = $(this).attr('data-action');
					var type = $(this).attr('data-action-type');

					if( $(this).attr('data-method') == 'ajax' ){
						var booking_detail_data = t.get_booking_detail();
						t.change_step({
							'action': action, 
							'type': type, 
							'booking_detail': booking_detail_data
						});
					}
				}
			});
			t.content.on('click', '.goodlayers-payment-plugin-complete', function(){
				t.change_step({
					'action': 'tourmaster_payment_plugin_complete',
					'step': 4
				});
			});
		},

		// check required input field
		check_required_field: function(step){

			var t = this;
			var error = false;

			var error_box = t.form.find('.tourmaster-tour-booking-required-error');
			if( error_box.length ){
				error_box.slideUp(200);

				if( step == 3 ){
					t.form.find('input[data-required], select[data-required], textarea[data-required]').each(function(){
						if( !$(this).val() ){ 	
							$(this).addClass('tourmaster-validate-error');
							error = 'default';
						}else if( $(this).is('[type="email"]') ){
							var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    						if( !re.test($(this).val().toLowerCase()) ){
    							$(this).addClass('tourmaster-validate-error');
    							error = 'email';
    						}
						}else if( $(this).is('[name="phone"], [name="billing_phone"]') ){
							var re = /^[\d\+\-\s\(\)\.]*$/;
    						if( !re.test($(this).val().toLowerCase()) ){
    							$(this).addClass('tourmaster-validate-error');
    							error = 'phone';
    						}
						}

						if( !error ){
							$(this).removeClass('tourmaster-validate-error');
						}
					});
					if( error ){
						error_box.html(error_box.data(error));
						error_box.slideDown(200);

						var scrollPos = error_box.offset().top - $(window).height() + 200;
						if( scrollPos > 0 ){
							$('html, body').animate({scrollTop: scrollPos}, 600, 'easeOutQuad');
						}
					}
				}

				if( step == 4 ){
					t.form.find('[name="term-and-service"]').each(function(){
						if( !$(this).prop('checked') ){
							error = 'default'; 
							error_box.html(error_box.data(error));
							error_box.slideDown(200);
						}
					});
				}
			}
			
			return (error === false);
		},

		// get the input field
		get_booking_detail: function(){
			var t = this;
			var booking_detail = {};
			if( t.form.attr('data-booking-detail') ){
				booking_detail = JSON.parse(t.form.attr('data-booking-detail'));
			}

			var booking_detail_new = tourmaster_get_booking_detail(t.form);

			// assign value back
			for( var slug in booking_detail_new ){ booking_detail[slug] = booking_detail_new[slug]; }
			t.form.attr('data-booking-detail', JSON.stringify(booking_detail));

			return booking_detail;
		},

		change_step: function(ajax_data, ajax_settings){

			var t = this;

			var ajax_data = $.extend({
				action: 'tourmaster_payment_template',
			}, ajax_data);

			var ajax_settings = $.extend({
				beforeSend: function(){

					// loading animation
					if( typeof(ajax_data.sub_action) == 'undefined' || ajax_data.sub_action != 'update_sidebar' ){
						t.content.animate({opacity: 0.1});

						// animate to the top
						$('html, body').animate({scrollTop: t.payment_template.offset().top}, 600, 'easeOutQuad');
					}

					t.sidebar.animate({opacity: 0.1});

				},
				error: function( jqXHR, textStatus, errorThrown ){

					// print error message for debug purpose
					console.log(jqXHR, textStatus, errorThrown);
				},
				success: function( data ){

					// assign content
					if( typeof(data.content) != 'undefined' ){
						t.animate_content(t.content, data.content);
					}

					// assign sidebar
					if( typeof(data.sidebar) != 'undefined' ){
						t.animate_content(t.sidebar, data.sidebar);
					}

					// update cookie
					if( typeof(data.cookie) != 'undefined' ){
						var new_booking_detail = JSON.stringify(data.cookie);
						tourmaster_set_cookie('tourmaster-booking-detail', new_booking_detail, 0);
						t.form.attr('data-booking-detail', new_booking_detail);
					}

					// set the step bar
					if( typeof(ajax_data.booking_detail) != 'undefined' || typeof(ajax_data.step) != 'undefined' ){
						if( typeof(ajax_data.step) != 'undefined' ){
							var booking_step = ajax_data.step;
						}else{
							var booking_step = ajax_data.booking_detail.step;
						}

						t.payment_step.find('.tourmaster-payment-step-item').each(function(){
							if( booking_step == 4 ){
								$(this).addClass('tourmaster-checked').removeClass('tourmaster-current tourmaster-enable')
							}else{
								if( $(this).attr('data-step') == 1 ){
									$(this).addClass('tourmaster-checked').removeClass('tourmaster-current tourmaster-enable');
								}else if( $(this).attr('data-step') == booking_step ){
									$(this).addClass('tourmaster-current').removeClass('tourmaster-checked tourmaster-enable');
								}else if( $(this).attr('data-step') < booking_step ){
									$(this).addClass('tourmaster-enable').removeClass('tourmaster-checked tourmaster-current');
								}else{
									$(this).removeClass('tourmaster-checked tourmaster-current tourmaster-enable');
								}
							}
						});
					}

					t.content.animate({opacity: 1});
					t.sidebar.animate({opacity: 1});

					t.bind_script_recurring();
				}
			}, ajax_settings);

			$.ajax({
				type: 'POST',
				url: t.form.attr('data-ajax-url'),
				data: ajax_data,
				dataType: 'json',
				beforeSend: ajax_settings.beforeSend,
				error: ajax_settings.error,
				success: ajax_settings.success
			});
		},
	};

	$.fn.tourmaster_video_background = function(){

		if( tourmaster_mobile ){
			$(this).children('[data-background-type="video"]').remove();
			
			if( $(this).attr('data-video-fallback') ){
				$(this).css('background-image', 'url(' + $(this).attr('data-video-fallback') + ')');
			}
		}else{

			var video_wrapper = $(this);
			$(this).children('[data-background-type="video"]').each(function(){

				$(this).tourmaster_set_video_background_position();
					$(window).on('load resize', function(){ 
					$(this).tourmaster_set_video_background_position();
				});

				// script for muting the vimeo/youtube player
				$(this).find('iframe').each(function(){
					if( $(this).attr('data-player-type') == 'vimeo' ){
						var player = $f($(this)[0]);
						
						player.addEvent('ready', function() {
							player.api('setVolume', 0);
						});
					}else if( $(this).attr('data-player-type') == 'youtube' ){

						// assign the script
						if( $('body').children('#tourmaster-youtube-api').length == 0 ){
							$('body').append('<script type="text/javascript" src="https://www.youtube.com/iframe_api" id="tourmaster-youtube-api" ></script>');
						}
						
						// store to global variable
						if( typeof(window.tourmaster_ytb) == 'undefined' ){
							window.tourmaster_ytb = [$(this)[0]];
						}else{
							window.tourmaster_ytb.push($(this)[0]);
						}
						
						// script loading action
						window.onYouTubeIframeAPIReady = function(){
							for( var key in window.tourmaster_ytb ){
								new YT.Player(tourmaster_ytb[key],{
									events: { 
										'onReady': function(e){
											e.target.mute();
										}
									}						
								});
							}
						}
					}
				});
			});
		}

	} // tourmaster_video_background 
	$.fn.tourmaster_set_video_background_position = function(){
			
		var wrapper_bg = $(this).parent();

		// set video height
		var ratio = 640 / 360;
		$(this).each(function(){
			if( (wrapper_bg.width() / wrapper_bg.height()) > ratio ){
				var v_height = wrapper_bg.width() / ratio;
				var v_margin = (wrapper_bg.height() - v_height) / 2;
				$(this).css({width: wrapper_bg.width(), height: v_height, 'margin-left': 0, 'margin-top': v_margin});
			}else{
				var v_width = wrapper_bg.height() * ratio;
				var v_margin = (wrapper_bg.width() - v_width) / 2;
				$(this).css({width: v_width, height: wrapper_bg.height(), 'margin-left': v_margin, 'margin-top': 0});
			}
		});

	} // tourmaster_set_video_background_position

	///////////////////////////////////////////////////
	// goodlayers core function
	///////////////////////////////////////////////////

	$.fn.tourmaster_set_flexslider = function( filter_elem ){

		if( typeof(filter_elem) == 'undefined' ){
			var elem = $(this).find('.tourmaster-flexslider');
		}else{
			var elem = filter_elem.filter('.tourmaster-flexslider');
		}	
	
		elem.each(function(){

			var flex_attr = {
				namespace: 'tourmaster-flex-',
				useCSS: false,
				animation: 'fade',
				animationLoop: true,
				prevText: '<i class="arrow_carrot-left"></i>',
				nextText: '<i class="arrow_carrot-right"></i>'
			};

			if( $(this).find('.tourmaster-flexslider').length > 0 ){ 
				$(this).children('ul.slides').addClass('parent-slides');
				flex_attr.selector = '.parent-slides > li';
			}

			// variable settings
			if( $(this).attr('data-disable-autoslide') ){
				flex_attr.slideshow = false;
			}
			if( $(this).attr('data-pausetime') ){
				flex_attr.slideshowSpeed = parseInt($(this).attr('data-pausetime'));
			}
			if( $(this).attr('data-slidespeed') ){
				flex_attr.animationSpeed = parseInt($(this).attr('data-slidespeed'));
			}else{
				flex_attr.animationSpeed = 500;
			}

			// for carousel
			if( $(this).attr('data-type') == 'carousel' ){
				flex_attr.move = 1;
				flex_attr.animation = 'slide';

				// determine the spaces
				var column_num = parseInt($(this).attr('data-column'));
				flex_attr.itemMargin = 2 * parseInt($(this).children('ul.slides').children('li:first-child').css('margin-right'));
				flex_attr.itemWidth = (($(this).width() + flex_attr.itemMargin) / column_num) - (flex_attr.itemMargin);

				flex_attr.minItems = column_num;
				flex_attr.maxItems = column_num;
				
				var t = $(this);
				$(window).resize(function(){
					if( t.data('tourmaster_flexslider') ){
						var newWidth = ((t.width() + flex_attr.itemMargin) / column_num) - (flex_attr.itemMargin);
						t.data('tourmaster_flexslider').editItemWidth(newWidth);
					}
				});

			}else if( $(this).attr('data-effect') ){
				if( $(this).attr('data-effect') == 'kenburn' ){
					flex_attr.animation = 'fade';
				}else{
					flex_attr.animation = $(this).attr('data-effect');
				}
			}

			// for navigation
			if( !$(this).attr('data-nav') || $(this).attr('data-nav') == 'both' || $(this).attr('data-nav') == 'navigation' || $(this).attr('data-nav') == 'navigation-outer' ){
				if( $(this).attr('data-nav-parent') ){

					if( $(this).attr('data-nav-type') == 'custom' ){
						flex_attr.customDirectionNav = $(this).closest('.' + $(this).attr('data-nav-parent')).find('.flex-prev, .flex-next');
					}else{
						$(this).closest('.' + $(this).attr('data-nav-parent')).each(function(){
							var flex_nav = $('<ul class="tourmaster-flex-direction-nav">' + 
											'<li class="tourmaster-flex-nav-prev"><a class="tourmaster-flex-prev" href="#"><i class="arrow_carrot-left"></i></a></li>' +
											'<li class="tourmaster-flex-nav-next"><a class="tourmaster-flex-next" href="#"><i class="arrow_carrot-right"></i></a></li>' +
										'</ul>');

							var flex_nav_position = $(this).find('.tourmaster-flexslider-nav');
							if( flex_nav_position.length ){
								flex_nav_position.append(flex_nav);
								flex_attr.customDirectionNav = flex_nav.find('.tourmaster-flex-prev, .tourmaster-flex-next');
							}
						});
					}
				}
			}else{
				flex_attr.directionNav = false;
			}
			if( $(this).attr('data-nav') == 'both' || $(this).attr('data-nav') == 'bullet' ){
				flex_attr.controlNav = true;
			}else{
				flex_attr.controlNav = false;
			}

			// for thumbnail 
			if( $(this).attr('data-thumbnail') ){
				var thumbnail_slide = $(this).siblings('.gdlr-core-sly-slider');

				flex_attr.manualControls = thumbnail_slide.find('ul.slides li')
				flex_attr.controlNav = true;
			}

			// center the navigation
			// add active class for kenburn effects
			if( $(this).attr('data-vcenter-nav') ){
				flex_attr.start = function(slider){
					if( slider.directionNav ){
						$(window).resize(function(){
							slider.directionNav.each(function(){
								var margin = -(slider.height() + $(this).outerHeight()) / 2;
								$(this).css('margin-top', margin);
							});
						});
					}
					if( typeof(slider.slides) != 'undefined' ){
						$(window).trigger('resize');
						slider.slides.filter('.tourmaster-flex-active-slide').addClass('tourmaster-active').siblings().removeClass('tourmaster-active');
					}
				};
			}else{
				flex_attr.start = function(slider){
					if( typeof(slider.slides) != 'undefined' ){
						$(window).trigger('resize');
						slider.slides.filter('.tourmaster-flex-active-slide').addClass('tourmaster-active').siblings().removeClass('tourmaster-active');
					}
				}
			}

			// add the action for class
			flex_attr.after = function(slider){
				slider.slides.filter('.tourmaster-flex-active-slide').addClass('tourmaster-active').siblings().removeClass('tourmaster-active');
			}

			// add outer frame class
			if( $(this).find('.tourmaster-outer-frame-element').length > 0 ){
				$(this).addClass('tourmaster-with-outer-frame-element');
			}

			$(this).tourmaster_flexslider(flex_attr);
		});

		return $(this);

	} // tourmaster-set-flexslier

	$.fn.tourmaster_set_image_height = function(){

		var all_image = $(this).find('img');

		all_image.each(function(){
			var img_width = $(this).attr('width');
			var img_height = $(this).attr('height');

			if( img_width && img_height ){
				var parent_item = $(this).parent('.tourmaster-temp-image-wrap');

				if( parent_item.length ){
					parent_item.height((img_height * $(this).width()) / img_width);
				}else{
					parent_item = $('<div class="tourmaster-temp-image-wrap" ></div>');
					parent_item.css('height', ((img_height * $(this).width()) / img_width));
					$(this).wrap(parent_item);
				}
			}else{
				return;
			}
		});
		$(window).resize(function(e){
			all_image.each(function(){
				var parent_item = $(this).parent('.tourmaster-temp-image-wrap');

				if( parent_item.length ){
					$(this).unwrap();
				}
			});
			
			$(window).unbind('resize', e.handleObj.handler, e);
		});

		return $(this);
	} // tourmaster_set_image_height

	// ajax action
	function tourmaster_ajax_action(ajax_section, name, value){

		if( ajax_section.attr('data-target-action') == 'replace' ){
			ajax_section.siblings('.' + ajax_section.attr('data-target')).each(function(){
				var scroll_pos = $(this).offset().top - 100;
				if( typeof(window.traveltour_anchor_offset) != 'undefined' ){
					scroll_pos = scroll_pos - window.traveltour_anchor_offset; 
				}
				if( $(window).scrollTop() > scroll_pos ){
					$('html, body').animate({scrollTop: scroll_pos}, 600, 'easeOutQuad');
				}
			});
		}

		$.ajax({
			type: 'POST',
			url: ajax_section.attr('data-ajax-url'),
			data: { 
				'action': ajax_section.attr('data-tm-ajax'), 
				'settings': ajax_section.data('settings'), 
				'option': { 'name':name, 'value':value } 
			},
			dataType: 'json',
			beforeSend: function(jqXHR, settings){
				// before send action
				if( ajax_section.attr('data-target-action') == 'replace' ){
					ajax_section.siblings('.' + ajax_section.attr('data-target')).animate({opacity: 0}, 150);
				}
			},
			error: function(jqXHR, textStatus, errorThrown){
				console.log(jqXHR, textStatus, errorThrown);
			},
			success: function(data){
				
				if( data.status == 'success' ){
					if( data.content && ajax_section.attr('data-target') ){
						if( ajax_section.attr('data-target-action') == 'append' ){
							var content = $(data.content);
							ajax_section.siblings('.' + ajax_section.attr('data-target')).each(function(){

								if( typeof($.fn.gdlr_core_animate_list_item) == 'function' ){
									if( $(this).attr('data-layout') != 'masonry' || typeof($.fn.isotope) != 'function' ){
										content.addClass('gdlr-core-animate-init');
									}
								}

								$(this).append(content);
								content.tourmaster_flexslider().tourmaster_set_image_height();

								if( $(this).attr('data-layout') == 'masonry' && typeof($.fn.isotope) == 'function' ){
									var addItems = $(this).isotope('addItems', content);
									$(this).isotope('layoutItems', addItems, true);
								}
								
								if( typeof($.fn.gdlr_core_animate_list_item) == 'function' ){
									content.gdlr_core_animate_list_item();
								}
							});

							if( data.load_more ){
								if( data.load_more != 'none' ){
									var load_more = $(data.load_more);
									ajax_section.parent().append(load_more);
									load_more.tourmaster_ajax(load_more);
									load_more.css('display', 'none').slideDown(100);

									ajax_section.remove();
								}else{
									ajax_section.slideUp(100, function(){ $(this).remove(); });
								}
							}
							
						}else if( ajax_section.attr('data-target-action') == 'replace' ){
							var content = $(data.content);
							
							ajax_section.siblings('.' + ajax_section.attr('data-target')).each(function(){
								var fix_height = false;
								var current_height = $(this).height();
								$(this).empty().append(content);
								content.tourmaster_flexslider().tourmaster_set_image_height();

								if( typeof($.fn.gdlr_core_animate_list_item) == 'function' ){
									content.gdlr_core_animate_list_item();
								}
								
								var new_height = $(this).height();
								$(this).css({height:current_height, opacity:1}).animate({'height':new_height}, {'duration':400, 'easing':'easeOutExpo', 'complete': function(){
									if( !fix_height ){ $(this).css('height',''); }
								}});
							});

							// pagination
							if( data.pagination ){
								if( ajax_section.is('.tourmaster-pagination, .gdlr-core-pagination') ){
									ajax_section.slideUp(100, function(){ $(this).remove(); });
								}else{
									ajax_section.siblings('.tourmaster-pagination, .gdlr-core-pagination').slideUp(100, function(){ $(this).remove(); });
								}
								
								if( data.pagination != 'none' ){
									var pagination = $(data.pagination);
									ajax_section.parent().append(pagination);
									pagination.tourmaster_ajax(pagination);
									pagination.css('display', 'none').slideDown(100);
								}
							}

							// load more button
							if( data.load_more ){
								ajax_section.siblings('.tourmaster-load-more-wrap, .gdlr-core-load-more-wrap').slideUp(100, function(){ $(this).remove(); });
								
								if( data.load_more != 'none' ){
									var load_more = $(data.load_more);
									ajax_section.parent().append(load_more);
									load_more.tourmaster_ajax(load_more);
									load_more.css('display', 'none').slideDown(100);
								}
							}

						}
					}

					if( typeof(data.settings) != 'undefined' ){
						ajax_section.data('settings', data.settings);
					}
				}else{
					console.log(data);
				}
			}
		});	

	} // tourmaster_ajax_action


	$.fn.tourmaster_lightgallery = function(){
		
		// ilightbox
		var lightgallery = $(this);
		var lightbox_groups = [];

		lightgallery.each(function(){
			if( $(this).attr('data-lightbox-group') ){
				if( lightbox_groups.indexOf($(this).attr('data-lightbox-group')) == -1 ){
					lightbox_groups.push($(this).attr('data-lightbox-group'));
				}
			}else{
				$(this).lightGallery({ selector: 'this' });
			}
		});

		for( var key in lightbox_groups ){
			var group_selector = '.tourmaster-lightgallery[data-lightbox-group="' + lightbox_groups[key] + '"]';
			
			lightgallery.filter(group_selector).first().lightGallery({ 
				selector: group_selector, 
				selectWithin: 'body',
				thumbnail: false
			});
		}

		// lightbox gallery
		if( typeof(filter_elem) == 'undefined' ){
			var gallery_lb = $(this).find('[data-gallery-lb]');
		}else{
			var gallery_lb = filter_elem.filter('[data-gallery-lb]');
		}

		gallery_lb.click(function(){
			$(this).lightGallery({ 
				dynamic: true,
				dynamicEl: $(this).data('gallery-lb'),
				thumbnail: false
			});

			return false;
		});

		return $(this);
	}

	$.fn.tourmaster_ajax = function( filter_elem ){

		if( typeof(filter_elem) == 'undefined' ){
			var elem = $(this).find('[data-tm-ajax]');
		}else{
			var elem = filter_elem.filter('[data-tm-ajax]');
		}
		
		elem.each(function(){

			var ajax_section = $(this);

			// button click
			$(this).on('click', 'a', function(){
				if( $(this).hasClass('tourmaster-active') ){
					return false;
				}

				$(this).addClass('tourmaster-active').siblings().removeClass('tourmaster-active');

				var name = $(this).attr('data-ajax-name');
				var value = $(this).attr('data-ajax-value');

				tourmaster_ajax_action(ajax_section, name, value);

				return false;
			});

			// filter changed
			$(this).on('change', 'select', function(){
				var name = $(this).attr('data-ajax-name');
				var value = $(this).val();

				tourmaster_ajax_action(ajax_section, name, value);
			});

		});	

	} // tourmaster_ajax

	// on document ready
	$(document).ready(function(){

		var body = $('body');

		// ajax action
		body.tourmaster_ajax();

		// video bg
		$('.tourmaster-background-video-wrap').tourmaster_video_background();

		// confirm button
		$('[data-confirm]').click(function(){
			var confirm_button = $(this);

			tourmaster_front_confirm_box({
				head: confirm_button.attr('data-confirm'),
				text: confirm_button.attr('data-confirm-text'),
				sub: confirm_button.attr('data-confirm-sub'),
				yes: confirm_button.attr('data-confirm-yes'),
				no: confirm_button.attr('data-confirm-no'), 
				success: function(){
					window.location.href = confirm_button.attr('href');
				}
			});

			return false;
		})
		
		// sync grid content height
		$('.tourmaster-tour-item-style-grid').each(function(){
			var max_height = 0;
			var default_padding = 8;
			var sync_item = $(this).find('.tourmaster-tour-grid.tourmaster-tour-frame .tourmaster-tour-content-wrap');
			
			sync_item.each(function(){
				if( $(this).outerHeight() > max_height ){	
					max_height = $(this).outerHeight();
				}
			});
			sync_item.each(function(){
				var bottom_padding = max_height - $(this).outerHeight() + parseInt($(this).css('padding-bottom'));
				$(this).css('padding-bottom', bottom_padding);
			});


			$(window).resize(function(){
				max_height = 0;
				sync_item.css('padding-bottom', default_padding);
				sync_item.each(function(){
					if( $(this).outerHeight() > max_height ){	
						max_height = $(this).outerHeight();
					}
				});
				sync_item.each(function(){
					var bottom_padding = max_height - $(this).outerHeight() + parseInt($(this).css('padding-bottom'));
					$(this).css('padding-bottom', bottom_padding);
				});
			});
		});

		// center right content
		$('.tourmaster-center-tour-content').each(function(){
			var left_height = $(this).siblings('.tourmaster-content-left').outerHeight();
			$(this).css({'padding-top': 0, 'padding-bottom': 0});
			var padding = (left_height - $(this).outerHeight()) / 2;
			padding = (padding > 0)? padding: 0;
			$(this).css({'padding-top': padding, 'padding-bottom': padding });

			$(window).resize(function(){
				var left_height = $(this).siblings('.tourmaster-content-left').outerHeight();
				$(this).css({'padding-top': 0, 'padding-bottom': 0});
				var padding = (left_height - $(this).outerHeight()) / 2;
				padding = (padding > 0)? padding: 0;
				$(this).css({'padding-top': padding, 'padding-bottom': padding });
			});
		});

		// tipsy
		if( $("body").hasClass("rtl") ){
			$('[data-rel=tipsy]').tipsy({fade: true, gravity: 'sw'});
		}else {
			$('[data-rel=tipsy]').tipsy({fade: true, gravity: 'se'});
		}

		// lightbox popup
		$('[data-tmlb]').on('click', function(){
			var content = $(this).siblings('[data-tmlb-id="' + $(this).attr('data-tmlb') + '"]');

			// check for social login plugin
			if( content.find('.nsl-container-block').length > 0 ){
				var lb_content = content.clone();
				lb_content.find('.nsl-container-block').replaceWith(content.find('.nsl-container-block').clone(true));
			}else if( $(this).attr('data-tmlb') == 'signup' ){
				var lb_content = content.clone(true);
			}else{
				var lb_content = content.clone();
			}
			
			tourmaster_lightbox(lb_content);
		});

		// register form
		$('.tourmaster-register-form').submit(function(){
			var condition_accepted_input = $(this).find('[name="tourmaster-require-acceptance"]');

			if( !condition_accepted_input.is(':checked') ){
				condition_accepted_input.siblings('.tourmaster-notification-box').slideDown(150);
				return false;
			}else{
				condition_accepted_input.siblings('.tourmaster-notification-box').slideUp(150);
			}
		});

		// search rating
		var search_item = $('.tourmaster-tour-search-item');
		if( search_item.length ){
			tourmaster_rating(search_item);

			search_item.find('.tourmaster-type-filter-title i').click(function(){
				var filter_content = $(this).parent().siblings('.tourmaster-type-filter-item-wrap');

				if( $(this).hasClass('tourmaster-active') ){
					$(this).removeClass('tourmaster-active');
					filter_content.slideUp(200);
				}else{
					$(this).addClass('tourmaster-active');
					filter_content.slideDown(200);
				}
			});
		}

		// top bar script
		$('.tourmaster-user-top-bar').each(function(){	
			
			// if login 
			if( $(this).hasClass('tourmaster-user') ){
				var top_bar_nav = $(this).children('.tourmaster-user-top-bar-nav').children('.tourmaster-user-top-bar-nav-inner');

				$(this).hover(function(){
					top_bar_nav.fadeIn(200);
				}, function(){
					top_bar_nav.fadeOut(200);
				})
			}
		});

		// trigger the datepicker
		$('.tourmaster-datepicker').tourmaster_datepicker();

		// on user template
		if( body.hasClass('single-tour')){

			// read more content button
			$('.tourmaster-single-tour-read-more-wrap .tourmaster-button').on('click', function(){
				var scrollPos = $(window).scrollTop();

				$(this).hide();
				$(this).parent().parent().siblings('.tourmaster-single-tour-read-more-gradient').hide();
				$(this).closest('.tourmaster-single-tour-content-wrap').css({'max-height': 'none', 'margin-bottom': 0});

				$('html, body').scrollTop(scrollPos);

				return false;
			});

			// tour booking bar
			$('#tourmaster-single-tour-booking-fields').tourmaster_tour_booking();
			$('#tourmaster-tour-booking-bar-wrap').tourmaster_tour_booking_sticky();

			// submit booking form
			$('#tourmaster-enquiry-form').find('input[type="submit"]').click(function(){
				if( $(this).hasClass('tourmaster-now-loading') ){ return false; }

				var form = $(this).closest('form');
				var form_button = $(this);
				var message_box = form.find('.tourmaster-enquiry-form-message').not('.tourmaster-enquiry-term-message');

				var condition_accepted_input = form.find('[name="tourmaster-require-acceptance"]');
                if( condition_accepted_input.length && !condition_accepted_input.is(':checked') ){
                    condition_accepted_input.siblings('.tourmaster-enquiry-form-message').slideDown(150);
                    return false;
                }else{
                    condition_accepted_input.siblings('.tourmaster-enquiry-form-message').slideUp(150);
                }

				var validate = true;
				form.find('input[data-required], select[data-required], textarea[data-required]').each(function(){
					if( !$(this).val() ){
						validate = false;
					}
				});

				if( !validate ){
					if( form.attr('data-validate-error') ){
						message_box.removeClass('tourmaster-success').addClass('tourmaster-failed');
						message_box.html(form.attr('data-validate-error'));
						message_box.slideDown(300);
					}
				}else{

					message_box.slideUp(300);
					form_button.addClass('tourmaster-now-loading');

					$.ajax({
						type: 'POST',
						url: form.attr('data-ajax-url'),
						data: { action: form.attr('data-action'), data: tourmaster_get_booking_detail(form) },
						dataType: 'json',
						error: function( jqXHR, textStatus, errorThrown ){
							// print error message for debug purpose
							console.log(jqXHR, textStatus, errorThrown);
						},
						success: function( data ){
							form_button.removeClass('tourmaster-now-loading');

							if( typeof(data.message) != 'undefined' ){
								if( data.status == 'success' ){
									form.find('input[name], textarea[name], select[name]').not('[name="tour-id"]').val('');
									message_box.removeClass('tourmaster-failed').addClass('tourmaster-success');
								}else{
									message_box.removeClass('tourmaster-success').addClass('tourmaster-failed');
								}

								message_box.html(data.message);
								message_box.slideDown(300);
							}
							
						}
					});
				}

				return false;
			});

			// save wishlist
			$('#tourmaster-save-wish-list').click(function(){
				if( $(this).hasClass('tourmaster-active') ) return;
				$(this).addClass('tourmaster-active');
				
				$.ajax({
					type: 'POST',
					url: $(this).attr('data-ajax-url'),
					data: { action: 'tourmaster_add_wish_list', 'tour-id': $(this).attr('data-tour-id') },
					dataType: 'json'
				});		
			});

			// single review
			$('#tourmaster-single-review').tourmaster_single_review();

			// urgency message
			$('#tourmaster-urgency-message').click(function(){
				var expire_time = $(this).attr('data-expire');
				if( !expire_time ){ expire_time = 3600; }
				tourmaster_set_cookie('tourmaster-urgency-message', '1', expire_time);

				$(this).fadeOut(200, function(){ $(this).remove(); });
			});

			// combobox list
			$('.tourmaster-single-tour-booking-fields').on('click', '.tourmaster-combobox-list-display', function(){
				$(this).siblings('ul').fadeToggle(200);
			});
			$('.tourmaster-single-tour-booking-fields').on('click', '.tourmaster-combobox-list-wrap ul li', function(){
				var value = $(this).attr('data-value');

				$(this).closest('ul').fadeOut(200);
				$(this).closest('ul').siblings('input').val(value).trigger('change');
				$(this).closest('ul').siblings('.tourmaster-combobox-list-display').children('span').html(value);
			});
			$(document).mouseup(function(e){
			    var container = $('.tourmaster-combobox-list-wrap');

			    // if the target of the click isn't the container nor a descendant of the container
			    if( container.length && !container.is(e.target) && container.has(e.target).length === 0 ) {
			        container.find('ul').fadeOut(200);
			    }
			});

			// booking form tab
			$('#tourmaster-booking-tab-title').children().click(function(){
				if( $(this).hasClass('tourmaster-active') ){
					return false;
				}else{
					$(this).addClass('tourmaster-active').siblings().removeClass('tourmaster-active');
				}

				var selected_tab = $(this).attr('data-tourmaster-tab');
				$(this).parent().siblings('.tourmaster-booking-tab-content').each(function(){
					if( $(this).is('[data-tourmaster-tab="' + selected_tab + '"]') ){
						$(this).fadeIn(200, function(){ $(this).addClass('tourmaster-active'); });
					}else{
						$(this).removeClass('tourmaster-active').hide();
					}
				});
			});

		}else if( body.hasClass('tourmaster-template-register') ){

			// age combobox set
			tourmaster_form_script();
			
		}else if( body.hasClass('tourmaster-template-user') ){

			// age combobox set
			tourmaster_form_script();

			// print html script
			$('.tourmaster-print').click(function(){
				var printed_id = $(this).attr('data-id');
				if( printed_id ){
					var printed_content = $($('#' + printed_id).html());
					$('body').children().css('display', 'none');
					$('body').append(printed_content);
					window.print();
					printed_content.remove();	
					$('body').children().css('display', '');	
				}
			});

			// upload preview
			$('input[name="profile-image"]').on('change', function(e){
				var temp_image = $(this).closest('label').siblings('img');

				if( e.target.files && e.target.files[0] ){
					var reader = new FileReader();
					reader.onload = function(e_reader){
						temp_image.attr('src', e_reader.target.result);
						temp_image.attr('srcset', '');
					}
					reader.readAsDataURL(e.target.files[0]);
				}
			});

			// deposit item expand
			$('.tourmaster-deposit-item-head').on('click', function(){
				var item = $(this).parent();

				if( item.hasClass('tourmaster-active') ){
					$(this).siblings('.tourmaster-deposit-item-content').css({'display': 'block'}).slideUp(150);
					item.removeClass('tourmaster-active');
				}else{
					$(this).siblings('.tourmaster-deposit-item-content').slideDown(150);
					item.addClass('tourmaster-active');
				}
			});
		
		// on payment template
		}else if( body.hasClass('tourmaster-template-payment') ){

			new tourmaster_payment_template();

		}

	}); // document.ready

	// responsive video
	$.fn.gdlr_core_fluid_video = function( filter_elem ){
		
		if( typeof(filter_elem) == 'undefined' ){
			var elem = $(this).find('iframe[src*="youtube"], iframe[src*="vimeo"]');
		}else{
			var elem = filter_elem.filter('iframe[src*="youtube"], iframe[src*="vimeo"]');
		}
		
		elem.each(function(){

			// ignore if inside slider
			if( $(this).closest('.ls-container, .master-slider').length <= 0 ){ 
				if( ($(this).is('embed') && $(this).parent('object').length) || $(this).parent('.gdlr-core-fluid-video-wrapper').length ){ return; } 
				if( !$(this).attr('id') ){ $(this).attr('id', 'gdlr-video-' + Math.floor(Math.random()*999999)); }			
			
				var ratio = $(this).height() / $(this).width();
				$(this).removeAttr('height').removeAttr('width');
				
				try{
					$(this).wrap('<div class="gdlr-core-fluid-video-wrapper"></div>').parent().css('padding-top', (ratio * 100)+"%");
					$(this).attr('src', $(this).attr('src'));
				}catch(e){}
			}
		});	

		return $(this);
	}
	$(document).ready(function(){
		$('body').gdlr_core_fluid_video();

		$('.tourmaster-lightgallery').tourmaster_lightgallery();
	});

	$(window).load(function(){
		var body = $('body');

		// flexslider
		body.tourmaster_set_flexslider();

		// content navigation
		var content_nav = $('#tourmaster-content-navigation-item-outer');

		if( !body.is('.wp-admin') && content_nav.length ){
			window.traveltour_anchor_offset	= content_nav.height();

			var content_nav_container = content_nav.parent();
			var offset = parseInt($('html').css('margin-top'));

			// slidebar
			var slidebar = content_nav.find('.tourmaster-content-navigation-slider');
			content_nav.find('.tourmaster-active').each(function(){
				slidebar.css({width: $(this).outerWidth(), left: $(this).position().left});
			});
			content_nav.on('tourmaster-change', function(){
				var active_slidebar = $(this).find('.tourmaster-active');
				if( !active_slidebar.hasClass('tourmaster-slidebar-active') ){
					active_slidebar.addClass('tourmaster-slidebar-active');
					slidebar.animate({width: active_slidebar.outerWidth(), left: active_slidebar.position().left}, { queue: false, duration: 200 });
				}
			});
			$(window).resize(function(){ content_nav.trigger('tourmaster-change'); });
			content_nav.each(function(){
				$(this).find('.tourmaster-content-navigation-tab').hover(function(){
					slidebar.animate({ width: $(this).outerWidth(), left: $(this).position().left }, { queue: false, duration: 150 });
				}, function(){
					var active_slidebar = $(this).parent().children('.tourmaster-slidebar-active');
					if( active_slidebar.length ){
						slidebar.animate({ width: active_slidebar.outerWidth(), left: active_slidebar.position().left }, { queue: false, duration: 150 });
					}
				});
			});

			// sticky scroll
			$(window).scroll(function(){
				if( tourmaster_display == 'mobile-landscape' || tourmaster_display == 'mobile-portrait' || tourmaster_display == 'tablet' ) return;

				if( $(this).scrollTop() + offset > content_nav_container.offset().top ){
					if( !content_nav.hasClass('tourmaster-fixed') ){
						content_nav.parent().css('height', content_nav.parent().height());
						content_nav.addClass('tourmaster-fixed');

						window.traveltour_anchor_offset	= content_nav.height();
					}
				}else{
					if( content_nav.hasClass('tourmaster-fixed') ){
						content_nav.parent().css('height', 'auto');
						content_nav.removeClass('tourmaster-fixed');
					}

				}
			});
		}

	});

})(jQuery);
(function($){
	"use strict";
	
	window.tourmasterHtmlOption = function( container ){
		
		this.container = $(container);
		
		this.init();
	}
	
	tourmasterHtmlOption.prototype = {
		
		// bind the action and events
		init: function(){
			
			// bind input format
			this.bind_input_format();

			// bind input time
			this.bind_input_time();

			// bind the image uploader
			this.bind_image_uploader();
			
			// bind conditional for showing item
			this.bind_conditional();

			// bind button
			this.bind_button_action();
			
			// bind the fixed event item
			this.rebind();
		},
		
		// rebind specific element when content in the container changed
		rebind: function(){
			
			var t = this;
			
			t.init_conditional();

			t.bind_import();

			t.bind_export();
			
			t.bind_datepicker();

			t.bind_font_slider();

			// bind custom item
			t.container.find('[data-type="custom"]').each(function(){
				var custom_val = $(this).children('.tourmaster-html-option-custom-value').data('value');
				var custom_options = $(this).children('.tourmaster-html-option-custom-options').data('value');
				var custom_settings = $(this).children('.tourmaster-html-option-custom-settings').data('value');
				
				if( custom_val ){
					$(this).data('value', custom_val);
				}
				if( custom_options ){
					$(this).data('options', custom_options);	
				}
				if( custom_settings ){
					$(this).data('settings', custom_settings);	
				}
				
				if( $(this).is('[data-item-type="tabs"]') ){
					new tourmaster_tabs($(this));
				}else if( $(this).is('[data-item-type="gallery"]') ){
					new tourmaster_gallery($(this));
				}else if( $(this).is('[data-item-type="group-discount"]') ){
					new tourmaster_group_discount($(this));
				}
			});

		},
		
		// retrieve all value within the container area
		get_val: function(){
			var obj = {};
			
			this.container.find('[data-slug]').each(function(){
				var slug = $(this).attr('data-slug');
				var input_type = $(this).attr('data-type');
				
				if( input_type == 'text' || input_type == 'textarea' || input_type == 'combobox' ||  
					input_type == 'upload' || input_type == 'colorpicker' || input_type == 'font' || input_type == 'multi-combobox' ){ 

					obj[slug] = $(this).val();
				}else if( input_type == 'checkbox' ){
					
					obj[slug] = ($(this).is(':checked'))? 'enable': 'disable';
				}else if( input_type == 'radioimage' ){

					if( $(this).is(':checked') ){
						obj[slug] = $(this).val();
					}
				}else if( input_type == 'custom' ){
					obj[slug] = $(this).data('value');
				}
			});
			
			return obj;
			
		}, // get_val
		
		// condition for show/hide item
		init_conditional: function(){	
		
			this.container.find('[data-condition]').each(function(){
				var wrapper = $(this).closest('.tourmaster-condition-wrapper');
				var conditions = JSON.parse($(this).attr('data-condition'));
				var visible = true;
				for( var key in conditions ){
					
					var obj = wrapper.find('[data-slug="' + key + '"], [data-tabs-slug="' + key + '"]');
					var obj_val = '';
					if( obj.is('input[type="checkbox"]') ){
						obj_val = (obj.is(':checked'))? 'enable': 'disable';
					}else if( obj.is('input[type="radio"]') ){
						obj_val = obj.filter(':checked').val();
					}else{
						obj_val = obj.val();
					}

					if( conditions[key] == 'css-condition' ){
						$(this).attr('data-' + key, obj_val);
					}else{
						visible = visible && (obj_val == conditions[key] || (conditions[key].constructor === Array && conditions[key].indexOf(obj_val) != -1));
					}
				}
				
				if( !visible ){
					$(this).hide();
				}else{
					$(this).show();
				}
			});
			
		}, // init_conditional
		bind_conditional: function(){

			this.container.on('change', 'select, input[type="checkbox"], input[type="radio"]', function(){
				var wrapper = $(this).closest('.tourmaster-condition-wrapper');
				wrapper.find('[data-condition]').each(function(){
					var conditions = JSON.parse($(this).attr('data-condition'));
					var visible = true;

					for( var key in conditions ){
						var obj = wrapper.find('[data-slug="' + key + '"], [data-tabs-slug="' + key + '"]');
						var obj_val = '';
						if( obj.is('input[type="checkbox"]') ){
							obj_val = (obj.is(':checked'))? 'enable': 'disable';
						}else if( obj.is('input[type="radio"]') ){
							obj_val = obj.filter(':checked').val();
						}else{
							obj_val = obj.val();
						}

						if( conditions[key] == 'css-condition' ){
							$(this).attr('data-' + key, obj_val);
						}else{
							visible = visible && (obj_val == conditions[key] || (conditions[key].constructor === Array && conditions[key].indexOf(obj_val) != -1));
						}
					}
					
					if( !visible ){
						if( $(this).css('display') == 'none' ){
							$(this).hide();
						}else{
							$(this).slideUp(200);
						}
					}else{
						$(this).slideDown(200);
					}
				});
			});
			
		}, // bind_conditional
		
		////////////////
		// item event
		////////////////
		
		bind_input_format: function(){

			this.container.on('change', 'input[data-input-type]', function(){

				var val = $(this).val();
				var match = val.match(/^-?\d+/g);
				var suffix = '';
				if( $(this).attr('data-input-type') == 'pixel' ){
					suffix = 'px';
				}

				if( typeof(match) != 'undefined' && match != null ){
					$(this).val( parseInt(match[0]) + suffix );
				}

				$(this).trigger('tourmaster_change');

			});
			this.container.on('keydown', 'input[data-input-type]', function(e){

				var code = (e.keyCode ? e.keyCode : e.which);

				if( code == 40 || code == 38 ){
					var val = $(this).val();
					var match = val.match(/^-?\d+/g);
					var suffix = '';
					if( $(this).attr('data-input-type') == 'pixel' ){
						suffix = 'px';
					}

					if( typeof(match) != 'undefined' && match != null ){
						if( code == 40 ){
							$(this).val( (parseInt(match[0]) - 1) + suffix );
						}else if( code == 38 ){
							$(this).val( (parseInt(match[0]) + 1) + suffix );
						}

						$(this).trigger('tourmaster_change');
					}
				}

			});

		},

		bind_input_time: function(){

			this.container.on('change', '.tourmaster-html-option-time', function(){
				var time_input = $(this).siblings('input[type="hidden"]');
				var h_input = time_input.siblings('.tourmaster-input-hh');
				var m_input = time_input.siblings('.tourmaster-input-mm');

				if( h_input.val() || m_input.val() ){
					time_input.val(('00' + h_input.val()).slice(-2) + ':' + ('00' + m_input.val()).slice(-2));
				}else{
					time_input.val('');
				}

				time_input.trigger('change');
			});

		},

		bind_image_uploader: function(){

			this.container.on('click', '.tourmaster-upload-image-button', function(){
				
				var image_wrapper = $(this).closest('.tourmaster-html-option-upload-appearance');
				var image_container = image_wrapper.children('.tourmaster-upload-image-container');
				var image_input = image_wrapper.children('.tourmaster-html-option-upload');
				var image_input_url = image_wrapper.children('.tourmaster-html-option-upload-image');
				
				if( $(this).hasClass('tourmaster-upload-image-add') ){

					var frame = wp.media({
						title: html_option_val.text.upload_media,
						button: { text: html_option_val.text.choose_media },
						multiple: false
					}).on('select', function(){
			  
						// Get media attachment details from the frame state
						var attachment = frame.state().get('selection').first().toJSON();

						image_wrapper.addClass('tourmaster-active');
						image_container.css('background-image', 'url(' + attachment.url + ')');
						image_input.val(attachment.id);
						image_input_url.val(attachment.url);

						image_input.trigger('change');
					}).open();
					
				}else if( $(this).hasClass('tourmaster-upload-image-remove') ){
					image_wrapper.removeClass('tourmaster-active');
					image_container.css('background-image', '');
					image_input.val('');
					image_input_url.val('');

					image_input.trigger('change');
				}
				
			});
			
		}, // bind image uploader
		
		bind_datepicker: function(){		

			this.container.find('.tourmaster-html-option-datepicker').each(function(){
				$(this).datepicker({
					dateFormat : 'yy-mm-dd',
					changeMonth: true,
					changeYear: true 
				});
			});

		},		

		bind_button_action: function(){

			var t = this;

			t.container.on('click', '.tourmaster-html-option-button', function(){

				if( $(this).hasClass('tourmaster-now-loading') ){ return; }
				
				var button = $(this);

				tourmaster_confirm_box({ 
					success: function(){
						if( button.attr('data-type') == 'ajax' ){
							var wrapper = button.closest('.tourmaster-condition-wrapper');
						 	var ajax_url = button.attr('data-ajax-url');
						 	var data = {
						 		action: button.attr('data-action'),
						 		post_id: button.attr('data-post-id')
						 	};

						 	// populate fields
						 	var fields = button.data('fields');
						 	if( typeof(fields) != 'undefined' && fields instanceof Array ){
						 		for( var i in fields ){
						 			var field = wrapper.find('[data-slug="' + fields[i] + '"]');
						 			if( field.is('input[type="checkbox"]') ){
						 				data[fields[i]] = field.is(':checked')? 'enable': 'disable';
						 			}else{
						 				data[fields[i]] = field.val();
						 			}
						 		}
						 	}

						 	// ajax
						 	t.button_ajax_action( button, ajax_url, data );
						}
					}
				}); // tourmaster_confirm_box

			});			

		}, // bind_button_action
		button_ajax_action: function( button, ajax_url, data_sent ){

			// ajax to obtain value
			$.ajax({
				type: 'POST',
				url: ajax_url,
				data: data_sent,
				dataType: 'json',
				beforeSend: function(jqXHR, settings){
					button.addClass('tourmaster-now-loading');
				},
				error: function(jqXHR, textStatus, errorThrown){
					button.removeClass('tourmaster-now-loading');

					// for displaying the debug text
					console.log(jqXHR, textStatus, errorThrown);
				},
				success: function(data){
					button.removeClass('tourmaster-now-loading');

					if( typeof(data.status) != 'undefined' && typeof(data.message) != 'undefined' ){
						tourmaster_alert_box({
							status: data.status,
							message: data.message,
						});
					}
				}
			});

		}, // button_ajax_action

		bind_font_slider: function(){
		
			this.container.find('.tourmaster-html-option-fontslider').each(function(){
				var t = $(this);
				var display = $('<div class="tourmaster-html-option-fontslider-appearance" ></div>');
				var min = ($(this).attr('data-min-value'))? parseInt($(this).attr('data-min-value')): 6;
				var max = ($(this).attr('data-max-value'))? parseInt($(this).attr('data-max-value')): 160;
				var suffix = ($(this).attr('data-suffix'))? $(this).attr('data-suffix'): 'px';

				if( suffix == 'none' ){ suffix = ''; }

				display.insertBefore($(this));
				display.slider({'range': 'min', 'min': min, 'max': max, value: parseInt(t.val()),
					slide: function(event, ui) {
						t.val(ui.value + suffix);
					}
				});
				t.val(parseInt(t.val()) + suffix);

				// update the font slider when input changes
				$(this).on('input change', function(){
					display.slider('value', parseInt(t.val()));
				});
				
			});
		}, // bind font slider		

		bind_import: function(){

			this.container.find('.tourmaster-html-option-import').each(function(){

				var import_form = $(this).find('form');
				var import_file = $(this).find('.tourmaster-html-option-import-file');
				var import_button = $(this).find('.tourmaster-html-option-import-button');

				import_button.click(function(){

					if( import_file.val().length > 0 ){
						tourmaster_confirm_box({ 
							success: function(){
								import_form.submit();
							} 
						});
					}else{
						tourmaster_alert_box({
							status: 'failed',
							head: 'File not found',
							message: 'Please select the file before importing',
						});
					}

					return false;
				});


			});

		}, // bind import

		bind_export: function(){

			this.container.find('.tourmaster-html-option-export').each(function(){

				var export_wrap = $(this);
				var export_button = export_wrap.find('.tourmaster-html-option-export-button');

				export_button.click(function(){

					var data_sent = {
						'action': export_wrap.attr('data-action'),
						'security': html_option_val.text.nonce
					};
					export_wrap.find('.tourmaster-html-option-export-option').each(function(){
						data_sent.options = $(this).val();
					});

					// ajax to obtain value
					$.ajax({
						type: 'POST',
						url: html_option_val.text.ajaxurl,
						data: data_sent,
						dataType: 'json',
						beforeSend: function(jqXHR, settings){
							export_button.addClass('tourmaster-now-loading');
						},
						error: function(jqXHR, textStatus, errorThrown){
							tourmaster_alert_box({ status: 'failed', head: html_option_val.text.error_head, message: html_option_val.text.error_message });
							
							// for displaying the debug text
							console.log(jqXHR, textStatus, errorThrown);
						},
						success: function(data){
							export_button.removeClass('tourmaster-now-loading');

							if( data.status == 'success' ){
								tourmaster_download_file(data.url, data.filename);
							}else if( data.status == 'success-2' ){
								tourmaster_download_content(data.content, data.filename);
							}else if( data.status == 'failed' ){
								tourmaster_alert_box({status: data.status, head: data.head, message: data.message});		
							}
						}
					});

				});


			});

		}, // bind export
		
	}; // tourmasterHtmlOption.prototype

	//////////////////////////
	// tabs settings
	//////////////////////////
	var tourmaster_tabs = function(c){
		
		var t = this;
		
		t.container = c;	
		t.values = c.data('value');

		t.tabs_container = $('<div class="tourmaster-html-option-tabs-container" ></div>');
		t.add_button = $('<div class="tourmaster-html-option-tabs-add"></div>');
		
		t.template = {
			title: '<div class="tourmaster-html-option-tabs-field clearfix" >\
				<span class="tourmaster-html-option-tabs-input-title tourmaster-html-option-title-item" ></span>\
			</div>',
			description: '<div class="tourmaster-html-option-tabs-field tourmaster-html-option-tabs-field-description clearfix" >\
			</div>',
			text: '<div class="tourmaster-html-option-tabs-field clearfix" >\
				<span class="tourmaster-html-option-tabs-input-title" ></span>\
				<input type="text" class="tourmaster-html-option-tabs-input tourmaster-html-option-text" data-tabs-slug="" >\
			</div>',
			time: '<div class="tourmaster-html-option-tabs-field tourmaster-html-option-tabs-field-time clearfix" >\
				<span class="tourmaster-html-option-tabs-input-title" ></span>\
				<input type="text" class="tourmaster-html-option-time tourmaster-input-hh" placeholder="HH" />\
				<span class="tourmaster-html-option-time-sep" >:</span>\
				<input type="text" class="tourmaster-html-option-time tourmaster-input-mm" placeholder="MM" />\
				<input type="hidden" data-tabs-slug="" data-template-type="time" />\
			</div>',
			tabs: '<div class="tourmaster-html-option-tabs-field clearfix" >\
				<span class="tourmaster-html-option-tabs-inner-tab-title" ></span>\
				<div class="tourmaster-html-option-tabs-field-tab" data-tabs-slug="" ></div>\
			</div>',
			datepicker: '<div class="tourmaster-html-option-tabs-field clearfix" >\
				<span class="tourmaster-html-option-tabs-input-title" ></span>\
				<input type="text" class="tourmaster-html-option-tabs-input tourmaster-html-option-text tourmaster-html-option-datepicker" data-tabs-slug="" >\
			</div>',
			textarea: '<div class="tourmaster-html-option-tabs-field clearfix" >\
				<span class="tourmaster-html-option-tabs-input-title" ></span>\
				<textarea class="tourmaster-html-option-tabs-input tourmaster-html-option-textarea" data-tabs-slug="" ></textarea>\
			</div>',
			combobox: '<div class="tourmaster-html-option-tabs-field clearfix" >\
				<span class="tourmaster-html-option-tabs-input-title" ></span>\
				<div class="tourmaster-custom-combobox" >\
					<select class="tourmaster-html-option-combobox" data-type="combobox" data-tabs-slug="" ></select>\
				</div>\
			</div>',
			radioimage: '<div class="tourmaster-html-option-tabs-field clearfix" >\
				<span class="tourmaster-html-option-tabs-input-title" ></span>\
			</div>',
			colorpicker: '<div class="tourmaster-html-option-tabs-field clearfix" >\
				<span class="tourmaster-html-option-tabs-input-title" ></span>\
				<input type="text" class="tourmaster-html-option-tabs-colorpicker tourmaster-html-option-colorpicker" data-tabs-slug="" >\
			</div>',
			checkbox: '<div class="tourmaster-html-option-tabs-field clearfix" >\
				<span class="tourmaster-html-option-tabs-input-title" ></span>\
				<label class="tourmaster-checkbox" >\
					<input type="checkbox" class="tourmaster-html-option-checkbox" data-tabs-slug="" checked="checked" style="display: none;">\
					<div class="tourmaster-html-option-checkbox-appearance tourmaster-noselect">\
						<span class="tourmaster-checkbox-button tourmaster-on">' + html_option_val.tabs.tab_checkbox_on + '</span>\
						<span class="tourmaster-checkbox-separator"></span>\
						<span class="tourmaster-checkbox-button tourmaster-off">' + html_option_val.tabs.tab_checkbox_off + '</span>\
					</div>\
				</label>\
			</div>',
			checkboxes: '<div class="tourmaster-html-option-tabs-field tourmaster-html-option-tabs-checkboxes clearfix" >\
				<span class="tourmaster-html-option-tabs-input-title" ></span>\
				<div class="tourmaster-html-option-tabs-content clearfix"></div>\
			</div>',
			upload: '<div class="tourmaster-html-option-tabs-field clearfix" >\
				<span class="tourmaster-html-option-tabs-input-title" ></span>\
				<div class="tourmaster-html-option-upload-appearance" >\
					<input type="hidden" class="tourmaster-html-option-upload" data-type="upload" data-tabs-slug="" />\
					<input type="hidden" class="tourmaster-html-option-upload-image" data-type="upload-img" data-tabs-slug="" />\
					<div class="tourmaster-upload-image-container" style="" ></div>\
					<div class="tourmaster-upload-image-overlay" >\
						<div class="tourmaster-upload-image-button-hover">\
							<span class="tourmaster-upload-image-button tourmaster-upload-image-add"><i class="icon_plus" ></i></span>\
							<span class="tourmaster-upload-image-button tourmaster-upload-image-remove"><i class="icon_minus-06" ></i></span>\
						</div>\
					</div>\
				</div>\
			</div>',
		}

		var tab_title_text = html_option_val.tabs.title_text;
		var template_settings = c.data('settings');
		if( typeof(template_settings) != 'undefined' && typeof(template_settings['tab-title']) != 'undefined' ){
			tab_title_text = template_settings['tab-title'];
		}
		
		var duplicate_button = '';
		if( typeof(template_settings) != 'undefined' && typeof(template_settings['allow-duplicate']) != 'undefined' ){
			duplicate_button = '<span class="tourmaster-html-option-tabs-duplicate" >' + template_settings['allow-duplicate'] + '</span>';
		}

		t.item_template = $('<div class="tourmaster-html-option-tabs-template tourmaster-condition-wrapper" >\
				<div class="tourmaster-html-option-tabs-template-title" >' + duplicate_button + '\
					<span class="tourmaster-head" >' + tab_title_text + '</span>\
					<div class="tourmaster-html-option-tabs-remove"></div>\
				</div>\
			</div>');

		var template_options = c.data('options');
		var template_content = $('<div class="tourmaster-html-option-tabs-template-content" ></div>');
		for( var slug in template_options ){
			var temp = $(t.template[template_options[slug].type]);

			// set the value
			temp.find('[data-tabs-slug]').each(function(){
				if( typeof(template_options[slug]['data-input-type']) != 'undefined' ){
					$(this).attr('data-input-type', template_options[slug]['data-input-type']);
				}

				if( $(this).attr('data-type') == 'upload-img' ){
					$(this).attr('data-tabs-slug', slug + '-img');
				}else{
					$(this).attr('data-tabs-slug', slug);

					// add the option to select box
					if( $(this).attr('data-type') == 'combobox' && typeof(template_options[slug].options) != 'undefined' ){
						for( var option_slug in template_options[slug].options ){
							var combobox_option = $('<option></option>').attr('value', option_slug).html(template_options[slug].options[option_slug]);
							$(this).append(combobox_option);
						}
					}
				}
			});

			// for radioimage
			if( template_options[slug].type == 'radioimage' && typeof(template_options[slug].options) != 'undefined' ){
				var radioimage_default = '';
				for( var option_slug in template_options[slug].options ){
					var radio_option = $('<label class="tourmaster-html-option-tabs-radioimage" ></label>');
					radio_option.append($('<input class="tourmaster-html-option-radioimage" type="radio" data-type="radioimage" />').attr('name', slug).attr('data-tabs-slug', slug).attr('value', option_slug));
					radio_option.append('<div class="tourmaster-radioimage-checked"></div>');
					radio_option.append($('<img alt="" />').attr('src', template_options[slug].options[option_slug]));

					// set the default value
					if( radioimage_default == '' || radioimage_default == option_slug ){
						radioimage_default = option_slug;
						radio_option.find('input[type="radio"]').prop('checked', true);
					}
					temp.append(radio_option);
				}

			// for checkbox
			}else if( template_options[slug].type == 'checkbox' ){
				if( template_options[slug].default == 'disable' ){
					temp.find('input[type="checkbox"]').prop('checked', false);
				}
			}else if( template_options[slug].type == 'checkboxes' && typeof(template_options[slug].options) != 'undefined' ){
				for( var option_slug in template_options[slug].options ){
					if( option_slug == 'select-all' ){
						var select_all = '<span class="tourmaster-html-option-checkboxes-selected" >(\
							<span class="tourmaster-select-all" >' + template_options[slug].options['select-all'] + '</span>\
							<span class="tourmaster-separator" >/</span>\
							<span class="tourmaster-deselect-all" >' + template_options[slug].options['deselect-all'] + '</span>\
							)</span>';

						temp.children('.tourmaster-html-option-tabs-content').append(select_all);
					}else if( option_slug == 'deselect-all' ){
					}else{
						var checkbox_option = $('<label></label>');
						checkbox_option.append($('<input type="checkbox" class="tourmaster-html-option-checkboxes" >').attr('data-tabs-slug', slug).attr('value', option_slug));
						checkbox_option.append(template_options[slug].options[option_slug]);
						temp.children('.tourmaster-html-option-tabs-content').append(checkbox_option);
					}
				}

			// for tab options
			}else if( template_options[slug].type == 'tabs' ){
				temp.find('.tourmaster-html-option-tabs-field-tab').data('options', template_options[slug].options);

				if( template_options[slug].settings ){
					temp.find('.tourmaster-html-option-tabs-field-tab').data('settings', template_options[slug].settings);
				}
			}

			// assigning title
			temp.find('.tourmaster-html-option-tabs-input-title, .tourmaster-html-option-tabs-inner-tab-title').html(template_options[slug].title);
			if( typeof(template_options[slug].title_color) != 'undefined' ){
				temp.find('.tourmaster-html-option-tabs-input-title').css('color', template_options[slug].title_color);
			}

			// assigning class
			if( typeof(template_options[slug].wrapper_class) != 'undefined' ){
				temp.addClass(template_options[slug].wrapper_class);
			}
			temp.attr('data-wrapper-slug', slug);

			// assigning condition
			if( typeof(template_options[slug].condition) != 'undefined' ){
				temp.attr('data-condition', JSON.stringify(template_options[slug].condition));
			}

			// description
			if( typeof(template_options[slug].description) != 'undefined' ){
				temp.append($('<div class="tourmaster-html-option-tabs-input-description" ></div>').html(template_options[slug].description));
			}

			template_content.append(temp);
		}
		t.item_template.append(template_content);
		
		t.init();
	}
	tourmaster_tabs.prototype = {
		
		init: function(){
			
			var t = this;
			
			t.container.append(t.tabs_container);
			t.container.closest('.tourmaster-html-option-item-input').siblings('.tourmaster-html-option-item-title').append(t.add_button);
			t.container.siblings('.tourmaster-html-option-tabs-inner-tab-title').append(t.add_button);

			// init the content
			if( t.values && t.values.length > 0 ){
				for( var key in t.values ){
					var temp_template = t.get_template(t.values[key]);
					t.tabs_container.append(temp_template);

					// for inner tabs
					var inner_tab = temp_template.find('.tourmaster-html-option-tabs-field-tab');
					if( inner_tab.length ){
						new tourmaster_tabs(inner_tab);
					}
				}

				t.tabs_container.find('.tourmaster-html-option-datepicker').each(function(){
					$(this).datepicker({
						dateFormat : 'yy-mm-dd',
						changeMonth: true,
						changeYear: true 
					});
				});

				// trigger the condition
				t.tabs_container.find('select, input[type="checkbox"], input[type="radio"]').trigger('change');
			}

			// bind sortable
			t.tabs_container.sortable({
				tolerance: 'pointer',
				delay: 150,
				handle: '.tourmaster-html-option-tabs-template-title',
				stop: function( e, ui ){
					t.update_data();
				}
			});
			
			// bind add button
			t.bind_add();
			
			// bind the duplicate button
			t.container.on('click', '.tourmaster-html-option-tabs-duplicate', function(){
				var template = $(this).closest('.tourmaster-html-option-tabs-template');
				var clone = template.clone(true);

				// prevent conflicts
				clone.find('input[type="radio"]').each(function(){
					$(this).attr('name', $(this).attr('name') + '-clone');
				});

				// rebind datepicker
				clone.find('.tourmaster-html-option-datepicker').datepicker('destroy').attr('id', '');
				clone.find('.tourmaster-html-option-datepicker').datepicker({
					dateFormat : 'yy-mm-dd',
					changeMonth: true,
					changeYear: true 
				});

				// clone and update the data
				clone.css('display', 'none');
				clone.insertAfter(template);
				clone.slideDown(200);

				t.update_data();

				return false;
			});

			// bind the remove button
			t.container.on('click', '.tourmaster-html-option-tabs-remove', function(){
				$(this).closest('.tourmaster-html-option-tabs-template').slideUp(200, function(){
					if( t.is_inner_tab_element($(this)) ){ return; }

					$(this).remove();
					
					t.update_data();
				});

				return false;
			});
				
			// bind the toggle button
			t.container.on('click', '.tourmaster-html-option-tabs-template-title', function(){
				if( t.is_inner_tab_element($(this)) ){ return; }

				$(this).siblings('.tourmaster-html-option-tabs-template-content').slideToggle(200);
			});
			
			// bind select/deselect button
			t.container.on('click', '.tourmaster-select-all, .tourmaster-deselect-all', function(){
				if( t.is_inner_tab_element($(this)) ){ return; }

				var parent = $(this).closest('.tourmaster-html-option-tabs-content');

				if( $(this).hasClass('tourmaster-select-all') ){
					parent.find('input[type="checkbox"]').prop('checked', true);
				}else{
					parent.find('input[type="checkbox"]').prop('checked', false);
				}
			});

			// bind the input change
			t.container.on('change tourmaster_change', 'input[type="text"], textarea', function(){
				if( t.is_inner_tab_element($(this)) ){ return; }

				if( $(this).attr('data-tabs-slug') == 'title' ){
					$(this).closest('.tourmaster-html-option-tabs-template-content')
						.siblings('.tourmaster-html-option-tabs-template-title').children('.tourmaster-head').html($(this).val());
				}

				t.update_data();
			});			
			t.container.on('change', 'input[type="checkbox"], input[type="radio"], input[type="hidden"]', function(){
				if( t.is_inner_tab_element($(this)) ){ return; }
				
				t.update_data();
			});		
			t.container.on('tourmaster_change', '.tourmaster-html-option-tabs-field-tab', function(){
				if( t.is_inner_tab_element($(this)) ){ return; }
				
				t.update_data();
			});
			
		}, // init
		
		bind_add: function(){
			
			var t = this;
			
			t.add_button.click(function(){
				var template = t.get_template();
				
				t.tabs_container.append(template);


				template.find('.tourmaster-html-option-datepicker').each(function(){
					$(this).datepicker({
						dateFormat : 'yy-mm-dd',
						changeMonth: true,
						changeYear: true 
					});
				});

				var inner_tab = template.find('.tourmaster-html-option-tabs-field-tab');
				if( inner_tab.length ){
					new tourmaster_tabs(inner_tab);
				}

				// trigger the condition
				template.find('select, input[type="checkbox"], input[type="radio"]').trigger('change');

				template.find('.tourmaster-html-option-tabs-template-content').css({display: 'block'});
				template.css({display: 'none'}).slideDown(200);
				
				t.update_data();
			});
			
		}, // bind_add
		
		get_template: function( values ){
			
			var t = this;
			var template = t.item_template.clone(true);

			// assign value
			for( var key in values ){
				template.find('[data-tabs-slug="' + key + '"]').each(function(){
					if( $(this).is('input[type="checkbox"]') ){
						if( $(this).is('.tourmaster-html-option-checkboxes') ){
							if( values[key].indexOf($(this).val()) >= 0 ){
								$(this).prop('checked', true);
							}
						}else{
							if( values[key] == 'enable' ){
								$(this).prop('checked', true);
							}else{
								$(this).prop('checked', false);
							}
						}
					}else if( $(this).is('input[type="radio"]') ){
						if( values[key] == $(this).val() ){
							$(this).prop('checked', true);
						}else{
							$(this).prop('checked', false);
						}
					}else if( $(this).attr('data-type') == 'upload-img' ){
						$(this).val(values[key]);
						$(this).closest('.tourmaster-html-option-upload-appearance').addClass('tourmaster-active');
						$(this).siblings('.tourmaster-upload-image-container').css('background-image', 'url(' + values[key] + ')');
					}else if( $(this).is('.tourmaster-html-option-tabs-field-tab') ){
						$(this).data('value', values[key]);
					}else{	
						$(this).val(values[key]);

						if( $(this).attr('data-template-type') == 'time' ){
							var time = values[key].split(':');

							if( typeof(time[0]) != 'undefined' && typeof(time[1]) != 'undefined' ){
								$(this).siblings('.tourmaster-input-hh').val(time[0]);
								$(this).siblings('.tourmaster-input-mm').val(time[1]);
							}
						}
					}
				});

				// for inner tabs
				var inner_tab = template.children('.tourmaster-html-option-tabs-field-tab');
				if( inner_tab.length ){
					inner_tab.data('value', values[key]);
				}
				
				if( key == 'title' && values[key] != '' ){
					template.find('.tourmaster-html-option-tabs-template-title').children('.tourmaster-head').html(values[key]);
				}
			}			

			// change the raio name so does not conflict with another tab
			var num = t.tabs_container.children().length;
			template.find('input[type="radio"]').each(function(){
				$(this).attr('name', $(this).attr('name') + '-' + num);
			});
			
			return template;
			
		}, // get_template

		is_inner_tab_element: function( element ){
			var t = this;

			// if container is outer tab && if element is in inner tab
			if( !t.container.is('.tourmaster-html-option-tabs-field-tab') &&
				!element.is('.tourmaster-html-option-tabs-field-tab') &&
				element.closest('.tourmaster-html-option-tabs-field-tab').length > 0 ){

				return true;
			}
			return false;
		},
		
		update_data: function(){
			var t = this;
			t.values = [];
			
			t.tabs_container.children('.tourmaster-html-option-tabs-template').each(function(){
				var tab_item = {};

				$(this).find('[data-tabs-slug]').each(function(){

					// detect the data of inner tab
					if( t.is_inner_tab_element($(this)) ){ return; }


					if( $(this).is('.tourmaster-html-option-tabs-field-tab') ){
						tab_item[$(this).attr('data-tabs-slug')] = $(this).data('value');
					}else if( $(this).is('input[type="checkbox"]') ){
						if( $(this).is('.tourmaster-html-option-checkboxes') ){
							if( typeof(tab_item[$(this).attr('data-tabs-slug')]) != 'object' ){
								tab_item[$(this).attr('data-tabs-slug')] = [];
							}
							if( $(this).is(':checked') ){
								tab_item[$(this).attr('data-tabs-slug')].push($(this).val());
							}

						}else{
							tab_item[$(this).attr('data-tabs-slug')] = ($(this).is(':checked'))? 'enable': 'disable';
						}
					}else if( $(this).is('input[type="radio"]') ){
						if( $(this).is(':checked') ){
							tab_item[$(this).attr('data-tabs-slug')] = $(this).val();
						}
					}else{
						tab_item[$(this).attr('data-tabs-slug')] = $(this).val();
					}

				});
				
				t.values.push(tab_item);
			});

			t.container.data('value', t.values);
			t.container.trigger('tourmaster_change');

		} // update_data
		
	}; // tourmaster_tabs.prototype
	
	//////////////////////////
	// gallery settings
	//////////////////////////	
	var tourmaster_gallery = function(c){
		
		var t = this;
		
		t.container = c;
		t.values = c.data('value');
		t.options = c.data('options');

		t.gallery_container = $('<div class="tourmaster-html-option-gallery-container" ><input type="hidden" value="" class="slider-gallery-trip" name="slider-gallery-trip"/></div>');
		t.add_button = $('<div class="tourmaster-html-option-gallery-add" ><i class="icon_plus" ></i></div>');
		
		t.template = $('<div class="tourmaster-html-option-gallery-template">\
				<img class="tourmaster-html-option-gallery-template-thumbnail" src="" alt="" /> \
				<div class="tourmaster-html-option-gallery-template-remove" ><i class="fa fa-remove" ></i></div>\
			</div>');
		
		t.init();
		
	}
	var valsli = [];
	tourmaster_gallery.prototype = {
		
		init: function(){
			
			var t = this;
			
			// initialize the gallery
			t.container.append(t.add_button).append(t.gallery_container);
			
			for( var key in t.values ){
				t.add_template(t.values[key]);
			}
			
			// bind add image event
			t.bind_add();
			
			// bind option edit
			t.bind_gallery_edit();
			
			// bind remove image event
			t.gallery_container.on('click', '.tourmaster-html-option-gallery-template-remove', function(){
				$(this).closest('.tourmaster-html-option-gallery-template').fadeOut(200, function(){
					$(this).remove();
					t.update_data();
				});

				return false;
			});

			t.gallery_container.sortable({
				tolerance: 'pointer',
				stop: function( e, ui ){
					t.update_data();
				}
			});
			
		}, // init
		
		bind_add: function(){
			
			var t = this;
			
			t.add_button.click(function(){
				
				var frame = wp.media({
					title: html_option_val.text.upload_media,
					button: { text: html_option_val.text.choose_media },
					multiple: 'add'
				}).on('select', function(){
		  
					// Get media attachment details from the frame state
					var attachments = frame.state().get('selection').toJSON();
					for( var key in attachments ){

						var thumbnail_url = attachments[key].sizes.full.url;
						if( typeof(attachments[key].sizes.thumbnail) != 'undefined' ){
							thumbnail_url = attachments[key].sizes.thumbnail.url;
						}
						
						t.add_template({
							id: attachments[key].id,
							thumbnail: thumbnail_url
						});
					}
					t.update_data();
				}).open();
				
			});
			
		}, // bind_add
		
		bind_gallery_edit: function(){
			
			var t = this;

			t.gallery_container.on('click', '.tourmaster-html-option-gallery-template', function(){
				
				var gallery_item = $(this);
				var loading = $('<div class="tourmaster-html-option-gallery-loading" ></div>');
				$.ajax({
					type: 'POST',
					url: html_option_val.text.ajaxurl,
					data: { 'action': 'gdlr_get_gallery_options', 'security': html_option_val.text.nonce,
						'options': t.options, 'value': $(this).data('value') },
					dataType: 'json',
					beforeSend: function(jqXHR, settings){
						loading.css({display: 'none'}).appendTo('body');
						loading.fadeIn(150);
					},
					error: function(jqXHR, textStatus, errorThrown){
						tourmaster_alert_box({ status: 'failed', head: html_option_val.text.error_head, message: html_option_val.text.error_message });
						
						// for displaying the debug text
						console.log(jqXHR, textStatus, errorThrown);
					},
					success: function(data){
						loading.remove();

						if( data.status == 'success' ){
							t.gallery_lb_edit( data.option_content, gallery_item );
						}else if( data.status == 'failed' ){
							tourmaster_alert_box({status: data.status, head: data.head, message: data.message});		
						}
					}
				});
			
			});
			
		}, // bind_gallery_edit
		
		gallery_lb_edit: function( content, current_item ){
			
			var t = this;
			var lb_content = $('<div class="tourmaster-gallery-lightbox-content"></div>');
		
			$('body').append(lb_content);
			lb_content.append(content);
			lb_content.css({opacity: 0}).animate({opacity: 1}, 400);

			// action for html option script
			var html_option = new gdlrCoreHtmlOption(lb_content);
			
			// close lb action
			lb_content.find('#tourmaster-gallery-lb-head-close').click(function(){
				lb_content.fadeOut(200, function(){
					$(this).remove();
				});
			});

			// save button 
			lb_content.find('#tourmaster-gallery-lb-options-save').click(function(){
				var new_value = $.extend(html_option.get_val(), current_item.data('value'));
				current_item.data('value', new_value);
				t.update_data();
				
				lb_content.fadeOut(200, function(){
					$(this).remove();
				});
			});			
			
		}, // gallery_lb_edit
		
		add_template: function( values ){
			
			var template = this.template.clone();
			var gallery_container = this.gallery_container;			
			template.data('value', values);
			template.find('.tourmaster-html-option-gallery-template-thumbnail').attr('src', values.thumbnail);
			this.gallery_container.append(template);

			
			

		}, // get_template
		
		update_data: function(){
			var t = this;
			t.values = [];
			
			t.gallery_container.find('.tourmaster-html-option-gallery-template').each(function(){
				t.values.push($(this).data('value'));
			});
                    
			t.container.data('value', t.values); 


			var dataJson = t.values;

			$('input[name=header-slider]').val(JSON.stringify(dataJson));

			console.log(t.values);
		} // update_data
		
	}; // tourmaster_alert_box_gallery.prototype

	//////////////////////////
	// group discount settings
	//////////////////////////
	var tourmaster_group_discount = function(c){
		
		var t = this;
		
		t.container = c;	
		t.values = c.data('value');

		t.tabs_container = $('<div class="tourmaster-html-option-group-discount-container" ></div>');
		t.add_button = $('<div class="tourmaster-html-option-group-discount-add"></div>');
		
		t.template = {
			text: '<div class="tourmaster-html-option-group-discount-field clearfix" >\
				<span class="tourmaster-html-option-group-discount-input-title" ></span>\
				<input type="text" class="tourmaster-html-option-group-discount-input tourmaster-html-option-text" data-tabs-slug="" >\
			</div>',
			description: '<div class="tourmaster-html-option-group-discount-description" >\
			</div>'
		};

		t.item_template = $('<div class="tourmaster-html-option-group-discount-template" ></div>');

		var template_options = c.data('options');
		var template_content = $('<div class="tourmaster-html-option-group-discount-template-content clearfix" ></div>');
		for( var slug in template_options ){
 			var temp = $(t.template[template_options[slug].type]);
 
 			// assign title
 			if( typeof(template_options[slug].title) != 'undefined' ){
 				temp.find('.tourmaster-html-option-group-discount-input-title').html(template_options[slug].title);
 			}
 
 			// set value
 			temp.find('[data-tabs-slug]').attr('data-tabs-slug', slug);
 
 			// description
 			if( typeof(template_options[slug].description) != 'undefined' ){
 				temp.append($('<div class="tourmaster-html-option-group-discount-description-content" ></div>').html(template_options[slug].description));
 			}
 
 			template_content.append(temp);
		}
		t.item_template.append(template_content);
		t.item_template.append('<div class="tourmaster-html-option-group-discount-remove"></div>');
		
		t.init();
	}
	tourmaster_group_discount.prototype = {
		
		init: function(){
			
			var t = this;
			
			t.container.append(t.tabs_container);
			t.container.closest('.tourmaster-html-option-item-input').siblings('.tourmaster-html-option-item-title').append(t.add_button);
			
			// init the content
			if( t.values && t.values.length > 0 ){
				for( var key in t.values ){
					t.tabs_container.append(t.get_template(t.values[key]));
				}
			}

			// bind sortable
			t.tabs_container.sortable({
				tolerance: 'pointer',
				delay: 150,
				stop: function( e, ui ){
					t.update_data();
				}
			});
			
			// bind add button
			t.bind_add();
			
			// bind the remove button
			t.container.on('click', '.tourmaster-html-option-group-discount-remove', function(){
				$(this).closest('.tourmaster-html-option-group-discount-template').slideUp(200, function(){
					$(this).remove();
					
					t.update_data();
				});
			});

			// bind the input change
			t.container.on('input change', 'input[type="text"]', tourmaster_debounce(function(){
				t.update_data();
			}, 500));
			
		}, // init
		
		bind_add: function(){
			
			var t = this;
			
			t.add_button.click(function(){
				var template = t.get_template();
				
				t.tabs_container.append(template);

				template.find('.tourmaster-html-option-group-discount-template-content').css({display: 'block'});
				template.css({display: 'none'}).slideDown(200);
				
				t.update_data();
			});
			
		}, // bind_add
		
		get_template: function( values ){
			
			var t = this;
			var template = t.item_template.clone();

			// assign value
			for( var key in values ){
				template.find('[data-tabs-slug="' + key + '"]').each(function(){
					$(this).val(values[key]);
				});
			}
			
			return template;
			
		}, // get_template
		
		update_data: function(){
			var t = this;
			t.values = [];
			
			t.tabs_container.find('.tourmaster-html-option-group-discount-template').each(function(){
				var tab_item = {};
				$(this).find('[data-tabs-slug]').each(function(){
					tab_item[$(this).attr('data-tabs-slug')] = $(this).val();
				});
				
				t.values.push(tab_item);
			});

			t.container.data('value', t.values); 

		} // update_data
		
	}; // tourmaster_group_discount.prototype	

})(jQuery);	
(function($){
	"use strict";

	// count recursive array/object
	window.gdlr_core_array_count = function(arr){
        var count = 0;
        for(var k in arr){
            count++;
            if( typeof(arr[k]) == 'object' ){
                count += window.gdlr_core_array_count(arr[k]);
            }
        }
        return count;
    } // gdlr_core_array_count

	// create the alert message
	window.gdlr_core_alert_box = function(options){
	
        var settings = $.extend({
			status: '',
			head: '',
			message: '',
			duration: 1500
        }, options);

		if( settings.status == 'success' ){
			settings.icon = 'fa fa-check';
		}else if( settings.status == 'failed' ){
			settings.icon = 'fa fa-remove';
		}
		
		var alert_box = $('<div class="gdlr-core-alert-box-wrapper">\
				<div class="gdlr-core-alert-box-head">\
					<span class="gdlr-core-alert-box-head">' + settings.head + '</span>\
				</div>' +
				((settings.message.length > 0)? '<div class="gdlr-core-alert-box-text">' + settings.message + '</div>': '') +
			'</div>').appendTo($('body'));
		
		alert_box.css({opacity: 0}).animate({opacity:1}, 150);
		
		// center the alert box position
		alert_box.css({
			'margin-left': -(alert_box.outerWidth() / 2),
			'margin-top': -(alert_box.outerHeight() / 2)
		});
				
		// animate the alert box
		alert_box.animate({opacity:1}, function(){
			$(this).delay(settings.duration).fadeOut(200, function(){
				$(this).remove();
			});
		});
		
	} // gdlr_core_alert_box
	
	// create the conformation message
	window.gdlr_core_confirm_box = function(options){

        var settings = $.extend({
			head: gdlr_utility.confirm_head,
			text: gdlr_utility.confirm_text,
			sub: gdlr_utility.confirm_sub,
			success:  function(){}
        }, options);
		
		var confirm_overlay = $('<div class="gdlr-conform-box-overlay"></div>').appendTo($('body'));
		var confirm_button = $('<span class="gdlr-core-confirm-box-button gdlr-core-yes">' + gdlr_utility.confirm_yes + '</span>');
		var decline_button = $('<span class="gdlr-core-confirm-box-button gdlr-core-no">' + gdlr_utility.confirm_no + '</span>');
		
		var confirm_box = $('<div class="gdlr-core-confirm-box-wrapper">\
				<div class="gdlr-core-confirm-box-head">' + settings.head + '</div>\
				<div class="gdlr-core-confirm-box-content-wrapper" >\
					<div class="gdlr-core-confirm-box-text">' + settings.text + '</div>\
					<div class="gdlr-core-confirm-box-sub">' + settings.sub + '</div>\
				</div>\
			</div>').insertAfter(confirm_overlay);
	
	
		$('<div class="gdlr-core-confirm-box-button-wrapper"></div>')
			.append(decline_button).append(confirm_button)
			.appendTo(confirm_box);
		
		// center the alert box position
		confirm_box.css({
			'margin-left': -(confirm_box.outerWidth() / 2),
			'margin-top': -(confirm_box.outerHeight() / 2)
		});
				
		// animate the alert box
		confirm_overlay.css({opacity: 0}).animate({opacity:0.6}, 200);
		confirm_box.css({opacity: 0}).animate({opacity:1}, 200);
		
		confirm_button.click(function(){
			if(typeof(settings.success) == 'function'){ 
				settings.success();
			}
			confirm_overlay.fadeOut(200, function(){
				$(this).remove();
			});
			confirm_box.fadeOut(200, function(){
				$(this).remove();
			});
		});
		decline_button.click(function(){
			confirm_overlay.fadeOut(200, function(){
				$(this).remove();
			});
			confirm_box.fadeOut(200, function(){
				$(this).remove();
			});
		});
		
	} // gdlr_core_confirm_box
	
	// ref : http://unscriptable.com/2009/03/20/debouncing-javascript-methods/
	// ensure 1 is fired
	window.gdlr_core_debounce = function(func, threshold, execAsap){
		
		var timeout;

		return function debounced(){
			
			var obj = this, args = arguments;
			
			function delayed(){
				if( !execAsap ){
					func.apply(obj, args);
				}
				timeout = null;
			};

			if( timeout ){
				clearTimeout(timeout);
			}else if( execAsap ){
				func.apply(obj, args);
			}
			timeout = setTimeout(delayed, threshold);
		};
	}	
	
	// reduce the event occurance
	window.gdlr_core_throttling = function(func, threshold){
		
		var timeout;

		return function throttled(){
			var obj = this, args = arguments;
			
			function delayed(){
				func.apply(obj, args);
				timeout = null;
			};

			if( !timeout ){
				timeout = setTimeout(delayed, threshold);
			}
		};
	}

	// download file
	window.gdlr_core_download_content = function(data, filename){
		var element = document.createElement('a');
		element.setAttribute('href', 'data:application/json;charset=utf-8,' + encodeURIComponent(data));
		element.setAttribute('download', filename);

		element.style.display = 'none';
		document.body.appendChild(element);

		element.click();

		document.body.removeChild(element);
	}
	window.gdlr_core_download_file = function(url, filename){
		var element = document.createElement('a');
		element.setAttribute('href', url);
		element.setAttribute('download', filename);

		element.style.display = 'none';
		document.body.appendChild(element);

		element.click();

		document.body.removeChild(element);
	}

	// clone without event 
	// also remove jquery ui-sortable data
	$.fn.gdlr_core_clone = function(){
		var clone = $(this).clone(true).off().removeData('uiSortable sortableItem').removeClass('ui-sortable ui-sortable-handle');
		clone.find('*').off().removeData('uiSortable sortableItem').removeClass('ui-sortable ui-sortable-handle');

		return clone;
	}

	// from wp-admin/js/editor.js
	// Replace paragraphs with double line breaks
	window.gdlr_core_removep = function( html ) {
		var blocklist = 'blockquote|ul|ol|li|dl|dt|dd|table|thead|tbody|tfoot|tr|th|td|h[1-6]|fieldset',
			blocklist1 = blocklist + '|div|p',
			blocklist2 = blocklist + '|pre',
			preserve_linebreaks = false,
			preserve_br = false;

		if ( ! html ) {
			return '';
		}

		// Protect pre|script tags
		if ( html.indexOf( '<pre' ) !== -1 || html.indexOf( '<script' ) !== -1 ) {
			preserve_linebreaks = true;
			html = html.replace( /<(pre|script)[^>]*>[\s\S]+?<\/\1>/g, function( a ) {
				a = a.replace( /<br ?\/?>(\r\n|\n)?/g, '<wp-line-break>' );
				a = a.replace( /<\/?p( [^>]*)?>(\r\n|\n)?/g, '<wp-line-break>' );
				return a.replace( /\r?\n/g, '<wp-line-break>' );
			});
		}

		// keep <br> tags inside captions and remove line breaks
		if ( html.indexOf( '[caption' ) !== -1 ) {
			preserve_br = true;
			html = html.replace( /\[caption[\s\S]+?\[\/caption\]/g, function( a ) {
				return a.replace( /<br([^>]*)>/g, '<wp-temp-br$1>' ).replace( /[\r\n\t]+/, '' );
			});
		}

		// Pretty it up for the source editor
		html = html.replace( new RegExp( '\\s*</(' + blocklist1 + ')>\\s*', 'g' ), '</$1>\n' );
		html = html.replace( new RegExp( '\\s*<((?:' + blocklist1 + ')(?: [^>]*)?)>', 'g' ), '\n<$1>' );

		// Mark </p> if it has any attributes.
		html = html.replace( /(<p [^>]+>.*?)<\/p>/g, '$1</p#>' );

		// Separate <div> containing <p>
		html = html.replace( /<div( [^>]*)?>\s*<p>/gi, '<div$1>\n\n' );

		// Remove <p> and <br />
		html = html.replace( /\s*<p>/gi, '' );
		html = html.replace( /\s*<\/p>\s*/gi, '\n\n' );
		html = html.replace( /\n[\s\u00a0]+\n/g, '\n\n' );
		html = html.replace( /\s*<br ?\/?>\s*/gi, '\n' );

		// Fix some block element newline issues
		html = html.replace( /\s*<div/g, '\n<div' );
		html = html.replace( /<\/div>\s*/g, '</div>\n' );
		html = html.replace( /\s*\[caption([^\[]+)\[\/caption\]\s*/gi, '\n\n[caption$1[/caption]\n\n' );
		html = html.replace( /caption\]\n\n+\[caption/g, 'caption]\n\n[caption' );

		html = html.replace( new RegExp('\\s*<((?:' + blocklist2 + ')(?: [^>]*)?)\\s*>', 'g' ), '\n<$1>' );
		html = html.replace( new RegExp('\\s*</(' + blocklist2 + ')>\\s*', 'g' ), '</$1>\n' );
		html = html.replace( /<((li|dt|dd)[^>]*)>/g, ' \t<$1>' );

		if ( html.indexOf( '<option' ) !== -1 ) {
			html = html.replace( /\s*<option/g, '\n<option' );
			html = html.replace( /\s*<\/select>/g, '\n</select>' );
		}

		if ( html.indexOf( '<hr' ) !== -1 ) {
			html = html.replace( /\s*<hr( [^>]*)?>\s*/g, '\n\n<hr$1>\n\n' );
		}

		if ( html.indexOf( '<object' ) !== -1 ) {
			html = html.replace( /<object[\s\S]+?<\/object>/g, function( a ) {
				return a.replace( /[\r\n]+/g, '' );
			});
		}

		// Unmark special paragraph closing tags
		html = html.replace( /<\/p#>/g, '</p>\n' );
		html = html.replace( /\s*(<p [^>]+>[\s\S]*?<\/p>)/g, '\n$1' );

		// Trim whitespace
		html = html.replace( /^\s+/, '' );
		html = html.replace( /[\s\u00a0]+$/, '' );

		// put back the line breaks in pre|script
		if ( preserve_linebreaks ) {
			html = html.replace( /<wp-line-break>/g, '\n' );
		}

		// and the <br> tags in captions
		if ( preserve_br ) {
			html = html.replace( /<wp-temp-br([^>]*)>/g, '<br$1>' );
		}

		return html;
	}

	// Similar to `wpautop()` in formatting.php
	window.gdlr_core_autop = function( text ) {
		var preserve_linebreaks = false,
			preserve_br = false,
			blocklist = 'table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre' +
				'|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|legend|section' +
				'|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary';

		// Normalize line breaks
		text = text.replace( /\r\n|\r/g, '\n' );

		if ( text.indexOf( '\n' ) === -1 ) {
			return text;
		}

		if ( text.indexOf( '<object' ) !== -1 ) {
			text = text.replace( /<object[\s\S]+?<\/object>/g, function( a ) {
				return a.replace( /\n+/g, '' );
			});
		}

		text = text.replace( /<[^<>]+>/g, function( a ) {
			return a.replace( /[\n\t ]+/g, ' ' );
		});

		// Protect pre|script tags
		if ( text.indexOf( '<pre' ) !== -1 || text.indexOf( '<script' ) !== -1 ) {
			preserve_linebreaks = true;
			text = text.replace( /<(pre|script)[^>]*>[\s\S]*?<\/\1>/g, function( a ) {
				return a.replace( /\n/g, '<wp-line-break>' );
			});
		}

		// keep <br> tags inside captions and convert line breaks
		if ( text.indexOf( '[caption' ) !== -1 ) {
			preserve_br = true;
			text = text.replace( /\[caption[\s\S]+?\[\/caption\]/g, function( a ) {
				// keep existing <br>
				a = a.replace( /<br([^>]*)>/g, '<wp-temp-br$1>' );
				// no line breaks inside HTML tags
				a = a.replace( /<[^<>]+>/g, function( b ) {
					return b.replace( /[\n\t ]+/, ' ' );
				});
				// convert remaining line breaks to <br>
				return a.replace( /\s*\n\s*/g, '<wp-temp-br />' );
			});
		}

		text = text + '\n\n';
		text = text.replace( /<br \/>\s*<br \/>/gi, '\n\n' );
		text = text.replace( new RegExp( '(<(?:' + blocklist + ')(?: [^>]*)?>)', 'gi' ), '\n$1' );
		text = text.replace( new RegExp( '(</(?:' + blocklist + ')>)', 'gi' ), '$1\n\n' );
		text = text.replace( /<hr( [^>]*)?>/gi, '<hr$1>\n\n' ); // hr is self closing block element
		text = text.replace( /\s*<option/gi, '<option' ); // No <p> or <br> around <option>
		text = text.replace( /<\/option>\s*/gi, '</option>' );
		text = text.replace( /\n\s*\n+/g, '\n\n' );
		text = text.replace( /([\s\S]+?)\n\n/g, '<p>$1</p>\n' );
		text = text.replace( /<p>\s*?<\/p>/gi, '');
		text = text.replace( new RegExp( '<p>\\s*(</?(?:' + blocklist + ')(?: [^>]*)?>)\\s*</p>', 'gi' ), '$1' );
		text = text.replace( /<p>(<li.+?)<\/p>/gi, '$1');
		text = text.replace( /<p>\s*<blockquote([^>]*)>/gi, '<blockquote$1><p>');
		text = text.replace( /<\/blockquote>\s*<\/p>/gi, '</p></blockquote>');
		text = text.replace( new RegExp( '<p>\\s*(</?(?:' + blocklist + ')(?: [^>]*)?>)', 'gi' ), '$1' );
		text = text.replace( new RegExp( '(</?(?:' + blocklist + ')(?: [^>]*)?>)\\s*</p>', 'gi' ), '$1' );

		// Remove redundant spaces and line breaks after existing <br /> tags
		text = text.replace( /(<br[^>]*>)\s*\n/gi, '$1' );

		// Create <br /> from the remaining line breaks
		text = text.replace( /\s*\n/g, '<br />\n');

		text = text.replace( new RegExp( '(</?(?:' + blocklist + ')[^>]*>)\\s*<br />', 'gi' ), '$1' );
		text = text.replace( /<br \/>(\s*<\/?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)>)/gi, '$1' );
		text = text.replace( /(?:<p>|<br ?\/?>)*\s*\[caption([^\[]+)\[\/caption\]\s*(?:<\/p>|<br ?\/?>)*/gi, '[caption$1[/caption]' );

		text = text.replace( /(<(?:div|th|td|form|fieldset|dd)[^>]*>)(.*?)<\/p>/g, function( a, b, c ) {
			if ( c.match( /<p( [^>]*)?>/ ) ) {
				return a;
			}

			return b + '<p>' + c + '</p>';
		});

		// put back the line breaks in pre|script
		if ( preserve_linebreaks ) {
			text = text.replace( /<wp-line-break>/g, '\n' );
		}

		if ( preserve_br ) {
			text = text.replace( /<wp-temp-br([^>]*)>/g, '<br$1>' );
		}

		return text;
	}

})(jQuery);	

(function($){
	function MapTour() {

		var lat = $("#latitude-tour").attr('value');
		var lon = $("#longitude-tour").attr('value');

		if (lat != null) {
			if(lat.includes(',')){
				var lati = lat.replace(",", ".")
				var latitude = Number(lati);
			} else {
				var latitude = Number(lat);
			}
		}

		if (lon != null) {
			if(lon.includes(',')){
				var long = lon.replace(",", ".")
				var longitude = Number(long);
			} else {
				var longitude = Number(lon);
			}
	
		}
		
		if (document.getElementById("map-tour-det")) {
			var myLatLng = {lat: latitude, lng: longitude};
		
			var map = new google.maps.Map(document.getElementById('map-tour-det'), {
				zoom: 4,
				center: myLatLng
			});
			
			var marker = new google.maps.Marker({
				position: myLatLng,
				map: map,
				title: 'Hello World!'
			});
		}
		
		
	}
	$(document).ready(MapTour);

	$(document).ready(function (){   
		if (document.getElementById('check-fixed')) {
			$('.traveltour-header-background-transparent').css('position','fixed');
		}
		if (document.getElementById('wpadminbar')) {
			$('#map').addClass('map-fixer');
			$('#map').removeClass('map-fixer-2');
		} else {
			$('#map').addClass('map-fixer-2');
			$('#map').removeClass('map-fixer');
		}
		if(document.getElementById('search-form')){
			$('#search-form').empty();
			$('#search-form').removeClass('dlr-core-pbf-column');
			$('#search-form').removeClass('gdlr-core-column-60');
			$('#search-form').removeClass('gdlr-core-column-first');
			$('#search-form').addClass('tourmaster-tour-search-item');
			$('#search-form').addClass('clearfix tourmaster-style-column');
			$('#search-form').addClass('tourmaster-column-count-1');
			$('#search-form').addClass('tourmaster-input-style-no-border');
			$('#search-form').addClass('tourmaster-item-pdlr');
						    
			$('#search-form').append('<div class="tourmaster-tour-search-wrap ">'+
			'<form class="tourmaster-form-field  tourmaster-medium" action="https://theoutdoortrip.com/tours/" method="GET">'+
			// '<input name="tour-search" type="hidden" value="">'+
			'<h2 id="form_text">Early Access Site</h2>'+
			'<div class="tourmaster-tour-search-field tourmaster-tour-search-field-tax" style="padding-right: 10px;margin-bottom: 10px;">'+
			'<div class="form-group>" <label for="trip-location"> Destination, Guide or Charter </label> ' +
			// '<input type="text" placeholder="Destination, Guide or Charter" id="trip-location" name="location" class="pac-target-input" autocomplete="off">'+
			'<input type="text" placeholder="Destination, Guide or Charter" id="trip-location" name="guide_name" class="pac-target-input" autocomplete="off">'+
			// '<input type="hidden" id="trip-data-location" name="data-place" value=""/>'+
			'</div>'+
			'</div>'+

			'<div class="tourmaster-tour-search-field tourmaster-tour-search-field-date" style="padding-right: 10px;margin-bottom: 10px;">'+
			'<div class="tourmaster-datepicker-wrap">'+
			'<div class="form-group>" <label> Trip Date </label>' +
			// '<input readonly class="tourmaster-datepicker custom-datepicker" type="text" value="" placeholder="Start Date" data-date-format="d M yy"><input class="tourmaster-datepicker-alt" name="date-start" type="hidden" value="">'+
			'<input readonly class="tourmaster-datepicker custom-datepicker" type="text" value="" placeholder="Start Date" data-date-format="d M yy"><input class="tourmaster-datepicker-alt" name="start_date_search" type="hidden" value="">'+
			'</div>'+
			'</div>'+
			'</div>'+

			// '<div class="form-group>" <label> Trips </label> <div class="tourmaster-tour-search-field tourmaster-tour-search-field-tour-category"><div class="tourmaster-combobox-wrap"><select name="tax-tour_category"><option value="fishing">Fishing</option><option value="hunting">Hunting</option></select></div></div></div>'+
			'<div class="form-group>" <label> Trips </label> <div class="tourmaster-tour-search-field tourmaster-tour-search-field-tour-category"><div class="tourmaster-combobox-wrap"><select name="category_search"><option value="fishing">Fishing</option><option value="hunting">Hunting</option></select></div></div></div>'+
			// '<div class="form-group>" <label> Species </label> <div class="tourmaster-tour-search-field tourmaster-tour-search-field-tour-category"><div class="tourmaster-combobox-wrap"><select name="tax-tour_species"><option value="">All</option>' +
			'<div class="form-group>" <label> Species </label> <div class="tourmaster-tour-search-field tourmaster-tour-search-field-tour-category"><div class="tourmaster-combobox-wrap"><select name="species_search"><option value="">All</option>' +
			'<option value="86">Catfish</option>'+
			'<option value="278">Crappie</option>'+
			'<option value="266">Flounder</option>'+
			'<option value="279">Grouper</option>'+
			'<option value="280 Bass">Largemouth Bass</option>'+
			'<option value="281">Musky</option>'+
			'<option value="282">Pike</option>'+
			'<option value="283">Redfish</option>'+
			'<option value="284">Sand Bass</option>'+
			'<option value="285">Smallmouth Bass</option>'+
			'<option value="286">Snapper</option>'+
			'<option value="287">Snook</option>'+
			'<option value="288">Speckled Trout</option>'+
			'<option value="289">Spoonbill</option>'+
			'<option value="290">Striper</option>'+
			'<option value="291">Sturgeon</option>'+
			'<option value="292">Trout</option>'+
			'<option value="293">Tuna</option>'+
			'<option value="294">Walleye</option>'+
			'<option value="295">White Bass</option>'+
			'</select></div></div></div>'+
			/* '<div class="tourmaster-tour-search-field tourmaster-tour-search-field-tax" style="padding-right: 10px;margin-bottom: 10px;">'+
			'<input autocomplete="off" type="text" placeholder="Search by guide" max="6" id="trip-guide" name="guide" list="data-guides"/>'+
			'<template id="template-guides"></template>'+
			'<datalist id="data-guides"></datalist>'+
			'</div>'+ */
			
			/* '<div class="tourmaster-tour-search-field tourmaster-tour-search-field-date" style="padding-right: 10px;margin-bottom: 10px;">'+
			'<div class="tourmaster-datepicker-wrap">'+
			'<input readonly class="tourmaster-datepicker custom-datepicker" type="text" value="" placeholder="End Date" data-date-format="d M yy"><input class="tourmaster-datepicker-alt" name="date-end" type="hidden" value="">'+
			'</div>'+
			'</div>'+ */
			'<div id="submit-frm" style="text-align:center"><input class="tourmaster-tour-search-submit" type="submit" value="Search"></div>'+
			'</form></div>');
		}

		var dat;
		$.ajax({
			type: "POST",
			url: 'https://theoutdoortrip.com/tours/',
			data: {op:'1'},
			success: function(data) {
				var append = '';
				dat = JSON.parse(data);
				dat.forEach(element => {
					append += "<option>"+element.name+"</option>";
				});
				$('#template-guides').append(append);
			}
		}); 

		var search = document.querySelector('#trip-guide');
		var results = document.querySelector('#data-guides');
		var templateContent = document.querySelector('#template-guides');
		if (search) {
			search.addEventListener('keyup', function handler(event) {
				while (results.children.length) results.removeChild(results.firstChild);
				var inputVal = search.value.trim();
				var clonedOptions = templateContent.cloneNode(true);
				var set = Array.prototype.reduce.call(clonedOptions.children, function searchFilter(frag, el) {
					if (el.textContent.startsWith(inputVal) && frag.children.length < 5) frag.appendChild(el);
					return frag;
				}, document.createDocumentFragment());
				results.appendChild(set);
			});
		}
		

		var search2 = document.querySelector('#trip-species');
		
		if (search2) {
			var results2 = document.querySelector('#data-species');
		var templateContent2 = document.querySelector('#template-species').content;
			search2.addEventListener('keyup', function handler(event) {
				while (results2.children.length) results2.removeChild(results2.firstChild);
				var inputVal = search2.value.trim();
				var clonedOptions = templateContent2.cloneNode(true);
				var set = Array.prototype.reduce.call(clonedOptions.children, function searchFilter(frag, el) {
					if (el.textContent.startsWith(inputVal) && frag.children.length < 5) frag.appendChild(el);
					return frag;
				}, document.createDocumentFragment());
				results2.appendChild(set);
			});
			
		}
		/*
		if (document.getElementById('trip-location')) {
			const input = document.getElementById('trip-location');
			const searchBox = new google.maps.places.SearchBox(input);

			searchBox.addListener("places_changed", () => {
				if (searchBox.getPlaces() != null) {
					var places = searchBox.getPlaces();
					$('#trip-data-location').attr('value', JSON.stringify(places));
				
					/*if (places.length == 0) {
					return;
					}
					
					return;
				}
			});
		}
*/
		var max_price, min_price;
		if ($("#trip-min-price").val()=='') {
			min_price = 100;
		} else {
			min_price = $("#trip-min-price").val()
		}

		if ($("#trip-max-price").val()=='') {
			max_price = 5000;
		} else {
			max_price = $("#trip-max-price").val()
		}

		$("#slider-range").slider({
			range: true,
			min: 0,
			max: 5000,
			values: [ min_price, max_price ],
			slide: function( event, ui ) {
			  $("#amount").text("$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ]);
			  $("#trip-min-price").val(ui.values[ 0 ]);
			  $("#trip-max-price").val(ui.values[ 1 ]);
			}
		});
		$("#amount").text( "$" + $( "#slider-range" ).slider( "values", 0 ) +
		" - $" + $( "#slider-range" ).slider( "values", 1 ) );
		

		$('.custom-datepicker').each(function(){ 
			var alternate_field = $(this).siblings('.tourmaster-datepicker-alt');
			var date_format = $(this).attr('data-date-format');
			$(this).datepicker({
				dateFormat: date_format,
				altFormat: 'yy-mm-dd',
				altField: alternate_field,
				changeMonth: true,
				changeYear: true
			});
		});
		

	});

	function Mapset(){
		if (document.getElementById("map-settings")) {
			if($('#latitude-map-set').val()!= null || $('#longitude-map-set').val() != null){
				var myLatLng = {lat: Number($('#latitude-map-set').val()), lng: Number($('#longitude-map-set').val())};
			}
			else {
				var myLatLng = {lat: 49, lng: -102};
			}
			geocoder = new google.maps.Geocoder();
			var map = new google.maps.Map(document.getElementById('map-settings'), {
				zoom: 3,
				streetViewControl: false,
				center: myLatLng
			});
			var marker;
			marker = new google.maps.Marker({ //on créé le marqueur
				position: myLatLng, 
				map: map
			});
			google.maps.event.addListener(map, 'click', function(event) {
                placeMarker(event.latLng);
			});
            function placeMarker(location) {
                if(marker){ //on vérifie si le marqueur existe
                    marker.setPosition(location); //on change sa position
                }else{
                    marker = new google.maps.Marker({ //on créé le marqueur
                        position: location, 
                        map: map
                    });
                }
                document.getElementById('latitude-map-set').value=location.lat();
                document.getElementById('longitude-map-set').value=location.lng();
                getAddress(location);
			}
			function getAddress(latLng) {
				geocoder.geocode( {'latLng': latLng},
				  function(results, status) {
					if(status == google.maps.GeocoderStatus.OK) {
					  if(results[0]) {
						var add = results[0].address_components;
						console.log(results[0].address_components);
						for (var item of add) {
							console.log(item.types[0]);
							if (item.types[0]=="locality") {
								document.getElementById("city-map-set").value = item.long_name;
							}
							if (item.types[0]=="administrative_area_level_1") {
								document.getElementById("state-map-set").value = item.long_name;
							}
							if (item.types[0]=="country") {
								document.getElementById("country-map-set").value = item.long_name;
							}
						}
						document.getElementById("address-map-set").value = results[0].formatted_address;
					  }
					  else {
						document.getElementById("address-map-set").value = "No results";
					  }
					}
					else {
					  document.getElementById("address-map-set").value = status;
					}
				  }
				);
				
			}
			
		}
	}
	function Mapset2(){
		if (document.getElementById("map-settings2")) {
			if($('#latitude-map-set').val()!= null || $('#longitude-map-set').val() != null){
				var myLatLng = {lat: Number($('#latitude-map-set').val()), lng: Number($('#longitude-map-set').val())};
			}
			else {
				var myLatLng = {lat: 49, lng: -102};
			}
			geocoder = new google.maps.Geocoder();
			var map = new google.maps.Map(document.getElementById('map-settings2'), {
				zoom: 3,
				streetViewControl: false,
				center: myLatLng
			});
			var marker;
			marker = new google.maps.Marker({ //on créé le marqueur
				position: myLatLng, 
				map: map
			});
			google.maps.event.addListener(map, 'click', function(event) {
				console.log(event);
                placeMarker(event.latLng);
			});
            function placeMarker(location) {
                if(marker){ //on vérifie si le marqueur existe
                    marker.setPosition(location); //on change sa position
                }else{
                    marker = new google.maps.Marker({ //on créé le marqueur
                        position: location, 
                        map: map
                    });
                }
                document.getElementById('latitude-map-set').value=location.lat();
                document.getElementById('longitude-map-set').value=location.lng();
                getAddress(location);
			}
			function getAddress(latLng) {
				geocoder.geocode( {'latLng': latLng},
				  function(results, status) {
					if(status == google.maps.GeocoderStatus.OK) {
					  if(results[0]) {
						var add = results[0].address_components;
						console.log(results[0].address_components);
						for (var item of add) {
							console.log(item.types[0]);
							if (item.types[0]=="locality") {
								document.getElementById("city-map-set").value = item.long_name;
							}
							if (item.types[0]=="administrative_area_level_1") {
								document.getElementById("state-map-set").value = item.long_name;
							}
							if (item.types[0]=="country") {
								document.getElementById("country-map-set").value = item.long_name;
							}
						}
						document.getElementById("address-map-set").value = results[0].formatted_address;
					  }
					  else {
						document.getElementById("address-map-set").value = "No results";
					  }
					}
					else {
					  document.getElementById("address-map-set").value = status;
					}
				  }
				);
				
			}
			
		}
	}
	$(document).ready(Mapset);
	$(document).ready(Mapset2);
})(jQuery);

// https://github.com/jaz303/tipsy
!function(a){function b(a,b){return"function"==typeof a?a.call(b):a}function c(a){for(;a=a.parentNode;)if(a==document)return!0;return!1}function d(b,c){this.$element=a(b),this.options=c,this.enabled=!0,this.fixTitle()}d.prototype={show:function(){var c=this.getTitle();if(c&&this.enabled){var d=this.tip();d.find(".tipsy-inner")[this.options.html?"html":"text"](c),d[0].className="tipsy",d.remove().css({top:0,left:0,visibility:"hidden",display:"block"}).prependTo(document.body);var i,e=a.extend({},this.$element.offset(),{width:this.$element[0].offsetWidth,height:this.$element[0].offsetHeight}),f=d[0].offsetWidth,g=d[0].offsetHeight,h=b(this.options.gravity,this.$element[0]);switch(h.charAt(0)){case"n":i={top:e.top+e.height+this.options.offset,left:e.left+e.width/2-f/2};break;case"s":i={top:e.top-g-this.options.offset,left:e.left+e.width/2-f/2};break;case"e":i={top:e.top+e.height/2-g/2,left:e.left-f-this.options.offset};break;case"w":i={top:e.top+e.height/2-g/2,left:e.left+e.width+this.options.offset}}2==h.length&&("w"==h.charAt(1)?i.left=e.left+e.width/2-15:i.left=e.left+e.width/2-f+15),d.css(i).addClass("tipsy-"+h),d.find(".tipsy-arrow")[0].className="tipsy-arrow tipsy-arrow-"+h.charAt(0),this.options.className&&d.addClass(b(this.options.className,this.$element[0])),this.options.fade?d.stop().css({opacity:0,display:"block",visibility:"visible"}).animate({opacity:this.options.opacity}):d.css({visibility:"visible",opacity:this.options.opacity})}},hide:function(){this.options.fade?this.tip().stop().fadeOut(function(){a(this).remove()}):this.tip().remove()},fixTitle:function(){var a=this.$element;(a.attr("title")||"string"!=typeof a.attr("original-title"))&&a.attr("original-title",a.attr("title")||"").removeAttr("title")},getTitle:function(){var a,b=this.$element,c=this.options;this.fixTitle();var a,c=this.options;return"string"==typeof c.title?a=b.attr("title"==c.title?"original-title":c.title):"function"==typeof c.title&&(a=c.title.call(b[0])),a=(""+a).replace(/(^\s*|\s*$)/,""),a||c.fallback},tip:function(){return this.$tip||(this.$tip=a('<div class="tipsy"></div>').html('<div class="tipsy-arrow"></div><div class="tipsy-inner"></div>'),this.$tip.data("tipsy-pointee",this.$element[0])),this.$tip},validate:function(){this.$element[0].parentNode||(this.hide(),this.$element=null,this.options=null)},enable:function(){this.enabled=!0},disable:function(){this.enabled=!1},toggleEnabled:function(){this.enabled=!this.enabled}},a.fn.tipsy=function(b){function e(c){var e=a.data(c,"tipsy");return e||(e=new d(c,a.fn.tipsy.elementOptions(c,b)),a.data(c,"tipsy",e)),e}function f(){var a=e(this);a.hoverState="in",0==b.delayIn?a.show():(a.fixTitle(),setTimeout(function(){"in"==a.hoverState&&a.show()},b.delayIn))}function g(){var a=e(this);a.hoverState="out",0==b.delayOut?a.hide():setTimeout(function(){"out"==a.hoverState&&a.hide()},b.delayOut)}if(b===!0)return this.data("tipsy");if("string"==typeof b){var c=this.data("tipsy");return c&&c[b](),this}if(b=a.extend({},a.fn.tipsy.defaults,b),b.live||this.each(function(){e(this)}),"manual"!=b.trigger){var h=b.live?"live":"bind",i="hover"==b.trigger?"mouseenter":"focus",j="hover"==b.trigger?"mouseleave":"blur";this[h](i,f)[h](j,g)}return this},a.fn.tipsy.defaults={className:null,delayIn:0,delayOut:0,fade:!1,fallback:"",gravity:"n",html:!1,live:!1,offset:0,opacity:.8,title:"title",trigger:"hover"},a.fn.tipsy.revalidate=function(){a(".tipsy").each(function(){var b=a.data(this,"tipsy-pointee");b&&c(b)||a(this).remove()})},a.fn.tipsy.elementOptions=function(b,c){return a.metadata?a.extend({},c,a(b).metadata()):c},a.fn.tipsy.autoNS=function(){return a(this).offset().top>a(document).scrollTop()+a(window).height()/2?"s":"n"},a.fn.tipsy.autoWE=function(){return a(this).offset().left>a(document).scrollLeft()+a(window).width()/2?"e":"w"},a.fn.tipsy.autoBounds=function(b,c){return function(){var d={ns:c[0],ew:c.length>1&&c[1]},e=a(document).scrollTop()+b,f=a(document).scrollLeft()+b,g=a(this);return g.offset().top<e&&(d.ns="n"),g.offset().left<f&&(d.ew="w"),a(window).width()+a(document).scrollLeft()-g.offset().left<b&&(d.ew="e"),a(window).height()+a(document).scrollTop()-g.offset().top<b&&(d.ns="s"),d.ns+(d.ew?d.ew:"")}}}(jQuery);

/*! Froogaloop for vimeo api
* http://a.vimeocdn.com/js/froogaloop2.min.js */
var Froogaloop=function(){function e(a){return new e.fn.init(a)}function g(a,c,b){if(!b.contentWindow.postMessage)return!1;a=JSON.stringify({method:a,value:c});b.contentWindow.postMessage(a,h)}function l(a){var c,b;try{c=JSON.parse(a.data),b=c.event||c.method}catch(e){}"ready"!=b||k||(k=!0);if(!/^https?:\/\/player.vimeo.com/.test(a.origin))return!1;"*"===h&&(h=a.origin);a=c.value;var m=c.data,f=""===f?null:c.player_id;c=f?d[f][b]:d[b];b=[];if(!c)return!1;void 0!==a&&b.push(a);m&&b.push(m);f&&b.push(f); return 0<b.length?c.apply(null,b):c.call()}function n(a,c,b){b?(d[b]||(d[b]={}),d[b][a]=c):d[a]=c}var d={},k=!1,h="*";e.fn=e.prototype={element:null,init:function(a){"string"===typeof a&&(a=document.getElementById(a));this.element=a;return this},api:function(a,c){if(!this.element||!a)return!1;var b=this.element,d=""!==b.id?b.id:null,e=c&&c.constructor&&c.call&&c.apply?null:c,f=c&&c.constructor&&c.call&&c.apply?c:null;f&&n(a,f,d);g(a,e,b);return this},addEvent:function(a,c){if(!this.element)return!1; var b=this.element,d=""!==b.id?b.id:null;n(a,c,d);"ready"!=a?g("addEventListener",a,b):"ready"==a&&k&&c.call(null,d);return this},removeEvent:function(a){if(!this.element)return!1;var c=this.element,b=""!==c.id?c.id:null;a:{if(b&&d[b]){if(!d[b][a]){b=!1;break a}d[b][a]=null}else{if(!d[a]){b=!1;break a}d[a]=null}b=!0}"ready"!=a&&b&&g("removeEventListener",a,c)}};e.fn.init.prototype=e.fn;window.addEventListener?window.addEventListener("message",l,!1):window.attachEvent("onmessage",l);return window.Froogaloop=window.$f=e}();

// flexslider
!function(e){var t=!0;e.tourmaster_flexslider=function(a,n){var i=e(a);i.vars=e.extend({},e.tourmaster_flexslider.defaults,n);var r,s=i.vars.namespace,o=window.navigator&&window.navigator.msPointerEnabled&&window.MSGesture,l=("ontouchstart"in window||o||window.DocumentTouch&&document instanceof DocumentTouch)&&i.vars.touch,c="click touchend MSPointerUp keyup",d="",u="vertical"===i.vars.direction,v=i.vars.reverse,p=i.vars.itemWidth>0,m="fade"===i.vars.animation,f=""!==i.vars.asNavFor,h={};e.data(a,"tourmaster_flexslider",i),h={init:function(){i.animating=!1,i.currentSlide=parseInt(i.vars.startAt?i.vars.startAt:0,10),isNaN(i.currentSlide)&&(i.currentSlide=0),i.animatingTo=i.currentSlide,i.atEnd=0===i.currentSlide||i.currentSlide===i.last,i.containerSelector=i.vars.selector.substr(0,i.vars.selector.search(" ")),i.slides=e(i.vars.selector,i),i.container=e(i.containerSelector,i),i.count=i.slides.length,i.syncExists=e(i.vars.sync).length>0,"slide"===i.vars.animation&&(i.vars.animation="swing"),i.prop=u?"top":"marginLeft",i.args={},i.manualPause=!1,i.stopped=!1,i.started=!1,i.startTimeout=null,i.transitions=!i.vars.video&&!m&&i.vars.useCSS&&function(){var e=document.createElement("div"),t=["perspectiveProperty","WebkitPerspective","MozPerspective","OPerspective","msPerspective"];for(var a in t)if(void 0!==e.style[t[a]])return i.pfx=t[a].replace("Perspective","").toLowerCase(),i.prop="-"+i.pfx+"-transform",!0;return!1}(),i.ensureAnimationEnd="",""!==i.vars.controlsContainer&&(i.controlsContainer=e(i.vars.controlsContainer).length>0&&e(i.vars.controlsContainer)),""!==i.vars.manualControls&&(i.manualControls=e(i.vars.manualControls).length>0&&e(i.vars.manualControls)),""!==i.vars.customDirectionNav&&(i.customDirectionNav=2===e(i.vars.customDirectionNav).length&&e(i.vars.customDirectionNav)),i.vars.randomize&&(i.slides.sort(function(){return Math.round(Math.random())-.5}),i.container.empty().append(i.slides)),i.doMath(),i.setup("init"),i.vars.controlNav&&h.controlNav.setup(),i.vars.directionNav&&h.directionNav.setup(),i.vars.keyboard&&(1===e(i.containerSelector).length||i.vars.multipleKeyboard)&&e(document).bind("keyup",function(e){var t=e.keyCode;if(!i.animating&&(39===t||37===t)){var a=39===t?i.getTarget("next"):37===t&&i.getTarget("prev");i.flexAnimate(a,i.vars.pauseOnAction)}}),i.vars.mousewheel&&i.bind("mousewheel",function(e,t,a,n){e.preventDefault();var r=t<0?i.getTarget("next"):i.getTarget("prev");i.flexAnimate(r,i.vars.pauseOnAction)}),i.vars.pausePlay&&h.pausePlay.setup(),i.vars.slideshow&&i.vars.pauseInvisible&&h.pauseInvisible.init(),i.vars.slideshow&&(i.vars.pauseOnHover&&i.hover(function(){i.manualPlay||i.manualPause||i.pause()},function(){i.manualPause||i.manualPlay||i.stopped||i.play()}),i.vars.pauseInvisible&&h.pauseInvisible.isHidden()||(i.vars.initDelay>0?i.startTimeout=setTimeout(i.play,i.vars.initDelay):i.play())),f&&h.asNav.setup(),l&&i.vars.touch&&h.touch(),(!m||m&&i.vars.smoothHeight)&&e(window).bind("resize orientationchange focus",h.resize),i.find("img").attr("draggable","false"),setTimeout(function(){i.vars.start(i)},200)},asNav:{setup:function(){i.asNav=!0,i.animatingTo=Math.floor(i.currentSlide/i.move),i.currentItem=i.currentSlide,i.slides.removeClass(s+"active-slide").eq(i.currentItem).addClass(s+"active-slide"),o?(a._slider=i,i.slides.each(function(){var t=this;t._gesture=new MSGesture,t._gesture.target=t,t.addEventListener("MSPointerDown",function(e){e.preventDefault(),e.currentTarget._gesture&&e.currentTarget._gesture.addPointer(e.pointerId)},!1),t.addEventListener("MSGestureTap",function(t){t.preventDefault();var a=e(this),n=a.index();e(i.vars.asNavFor).data("tourmaster_flexslider").animating||a.hasClass("active")||(i.direction=i.currentItem<n?"next":"prev",i.flexAnimate(n,i.vars.pauseOnAction,!1,!0,!0))})})):i.slides.on(c,function(t){t.preventDefault();var a=e(this),n=a.index();a.offset().left-e(i).scrollLeft()<=0&&a.hasClass(s+"active-slide")?i.flexAnimate(i.getTarget("prev"),!0):e(i.vars.asNavFor).data("tourmaster_flexslider").animating||a.hasClass(s+"active-slide")||(i.direction=i.currentItem<n?"next":"prev",i.flexAnimate(n,i.vars.pauseOnAction,!1,!0,!0))})}},controlNav:{setup:function(){i.manualControls?h.controlNav.setupManual():h.controlNav.setupPaging()},setupPaging:function(){var t,a,n="thumbnails"===i.vars.controlNav?"control-thumbs":"control-paging",r=1;if(i.controlNavScaffold=e('<ol class="'+s+"control-nav "+s+n+'"></ol>'),i.pagingCount>1)for(var o=0;o<i.pagingCount;o++){if(void 0===(a=i.slides.eq(o)).attr("data-thumb-alt")&&a.attr("data-thumb-alt",""),altText=""!==a.attr("data-thumb-alt")?altText=' alt="'+a.attr("data-thumb-alt")+'"':"",t="thumbnails"===i.vars.controlNav?'<img src="'+a.attr("data-thumb")+'"'+altText+"/>":'<a href="#">'+r+"</a>","thumbnails"===i.vars.controlNav&&!0===i.vars.thumbCaptions){var l=a.attr("data-thumbcaption");""!==l&&void 0!==l&&(t+='<span class="'+s+'caption">'+l+"</span>")}i.controlNavScaffold.append("<li>"+t+"</li>"),r++}i.controlsContainer?e(i.controlsContainer).append(i.controlNavScaffold):i.append(i.controlNavScaffold),h.controlNav.set(),h.controlNav.active(),i.controlNavScaffold.delegate("a, img",c,function(t){if(t.preventDefault(),""===d||d===t.type){var a=e(this),n=i.controlNav.index(a);a.hasClass(s+"active")||(i.direction=n>i.currentSlide?"next":"prev",i.flexAnimate(n,i.vars.pauseOnAction))}""===d&&(d=t.type),h.setToClearWatchedEvent()})},setupManual:function(){i.controlNav=i.manualControls,h.controlNav.active(),i.controlNav.bind(c,function(t){if(t.preventDefault(),""===d||d===t.type){var a=e(this),n=i.controlNav.index(a);a.hasClass(s+"active")||(n>i.currentSlide?i.direction="next":i.direction="prev",i.flexAnimate(n,i.vars.pauseOnAction))}""===d&&(d=t.type),h.setToClearWatchedEvent()})},set:function(){var t="thumbnails"===i.vars.controlNav?"img":"a";i.controlNav=e("."+s+"control-nav li "+t,i.controlsContainer?i.controlsContainer:i)},active:function(){i.controlNav.removeClass(s+"active").eq(i.animatingTo).addClass(s+"active")},update:function(t,a){i.pagingCount>1&&"add"===t?i.controlNavScaffold.append(e('<li><a href="#">'+i.count+"</a></li>")):1===i.pagingCount?i.controlNavScaffold.find("li").remove():i.controlNav.eq(a).closest("li").remove(),h.controlNav.set(),i.pagingCount>1&&i.pagingCount!==i.controlNav.length?i.update(a,t):h.controlNav.active()}},directionNav:{setup:function(){var t=e('<ul class="'+s+'direction-nav"><li class="'+s+'nav-prev"><a class="'+s+'prev" href="#">'+i.vars.prevText+'</a></li><li class="'+s+'nav-next"><a class="'+s+'next" href="#">'+i.vars.nextText+"</a></li></ul>");i.customDirectionNav?i.directionNav=i.customDirectionNav:i.controlsContainer?(e(i.controlsContainer).append(t),i.directionNav=e("."+s+"direction-nav li a",i.controlsContainer)):(i.append(t),i.directionNav=e("."+s+"direction-nav li a",i)),h.directionNav.update(),i.directionNav.bind(c,function(t){t.preventDefault();var a;""!==d&&d!==t.type||(a=e(this).hasClass(s+"next")?i.getTarget("next"):i.getTarget("prev"),i.flexAnimate(a,i.vars.pauseOnAction)),""===d&&(d=t.type),h.setToClearWatchedEvent()})},update:function(){var e=s+"disabled";1===i.pagingCount?i.directionNav.addClass(e).attr("tabindex","-1"):i.vars.animationLoop?i.directionNav.removeClass(e).removeAttr("tabindex"):0===i.animatingTo?i.directionNav.removeClass(e).filter("."+s+"prev").addClass(e).attr("tabindex","-1"):i.animatingTo===i.last?i.directionNav.removeClass(e).filter("."+s+"next").addClass(e).attr("tabindex","-1"):i.directionNav.removeClass(e).removeAttr("tabindex")}},pausePlay:{setup:function(){var t=e('<div class="'+s+'pauseplay"><a href="#"></a></div>');i.controlsContainer?(i.controlsContainer.append(t),i.pausePlay=e("."+s+"pauseplay a",i.controlsContainer)):(i.append(t),i.pausePlay=e("."+s+"pauseplay a",i)),h.pausePlay.update(i.vars.slideshow?s+"pause":s+"play"),i.pausePlay.bind(c,function(t){t.preventDefault(),""!==d&&d!==t.type||(e(this).hasClass(s+"pause")?(i.manualPause=!0,i.manualPlay=!1,i.pause()):(i.manualPause=!1,i.manualPlay=!0,i.play())),""===d&&(d=t.type),h.setToClearWatchedEvent()})},update:function(e){"play"===e?i.pausePlay.removeClass(s+"pause").addClass(s+"play").html(i.vars.playText):i.pausePlay.removeClass(s+"play").addClass(s+"pause").html(i.vars.pauseText)}},touch:function(){var e,t,n,r,s,l,c,d,f,h=!1,g=0,S=0,x=0;o?(a.style.msTouchAction="none",a._gesture=new MSGesture,a._gesture.target=a,a.addEventListener("MSPointerDown",function(e){e.stopPropagation(),i.animating?e.preventDefault():(i.pause(),a._gesture.addPointer(e.pointerId),x=0,r=u?i.h:i.w,l=Number(new Date),n=p&&v&&i.animatingTo===i.last?0:p&&v?i.limit-(i.itemW+i.vars.itemMargin)*i.move*i.animatingTo:p&&i.currentSlide===i.last?i.limit:p?(i.itemW+i.vars.itemMargin)*i.move*i.currentSlide:v?(i.last-i.currentSlide+i.cloneOffset)*r:(i.currentSlide+i.cloneOffset)*r)},!1),a._slider=i,a.addEventListener("MSGestureChange",function(e){e.stopPropagation();var t=e.target._slider;if(t){var i=-e.translationX,o=-e.translationY;s=x+=u?o:i,h=u?Math.abs(x)<Math.abs(-i):Math.abs(x)<Math.abs(-o),e.detail!==e.MSGESTURE_FLAG_INERTIA?(!h||Number(new Date)-l>500)&&(e.preventDefault(),!m&&t.transitions&&(t.vars.animationLoop||(s=x/(0===t.currentSlide&&x<0||t.currentSlide===t.last&&x>0?Math.abs(x)/r+2:1)),t.setProps(n+s,"setTouch"))):setImmediate(function(){a._gesture.stop()})}},!1),a.addEventListener("MSGestureEnd",function(a){a.stopPropagation();var i=a.target._slider;if(i){if(i.animatingTo===i.currentSlide&&!h&&null!==s){var o=v?-s:s,c=o>0?i.getTarget("next"):i.getTarget("prev");i.canAdvance(c)&&(Number(new Date)-l<550&&Math.abs(o)>50||Math.abs(o)>r/2)?i.flexAnimate(c,i.vars.pauseOnAction):m||i.flexAnimate(i.currentSlide,i.vars.pauseOnAction,!0)}e=null,t=null,s=null,n=null,x=0}},!1)):(c=function(s){i.animating?s.preventDefault():(window.navigator.msPointerEnabled||1===s.touches.length)&&(i.pause(),r=u?i.h:i.w,l=Number(new Date),g=s.touches[0].pageX,S=s.touches[0].pageY,n=p&&v&&i.animatingTo===i.last?0:p&&v?i.limit-(i.itemW+i.vars.itemMargin)*i.move*i.animatingTo:p&&i.currentSlide===i.last?i.limit:p?(i.itemW+i.vars.itemMargin)*i.move*i.currentSlide:v?(i.last-i.currentSlide+i.cloneOffset)*r:(i.currentSlide+i.cloneOffset)*r,e=u?S:g,t=u?g:S,a.addEventListener("touchmove",d,!1),a.addEventListener("touchend",f,!1))},d=function(a){g=a.touches[0].pageX,S=a.touches[0].pageY,s=u?e-S:e-g;(!(h=u?Math.abs(s)<Math.abs(g-t):Math.abs(s)<Math.abs(S-t))||Number(new Date)-l>500)&&(a.preventDefault(),!m&&i.transitions&&(i.vars.animationLoop||(s/=0===i.currentSlide&&s<0||i.currentSlide===i.last&&s>0?Math.abs(s)/r+2:1),i.setProps(n+s,"setTouch")))},f=function(o){if(a.removeEventListener("touchmove",d,!1),i.animatingTo===i.currentSlide&&!h&&null!==s){var c=v?-s:s,u=c>0?i.getTarget("next"):i.getTarget("prev");i.canAdvance(u)&&(Number(new Date)-l<550&&Math.abs(c)>50||Math.abs(c)>r/2)?i.flexAnimate(u,i.vars.pauseOnAction):m||i.flexAnimate(i.currentSlide,i.vars.pauseOnAction,!0)}a.removeEventListener("touchend",f,!1),e=null,t=null,s=null,n=null},a.addEventListener("touchstart",c,!1))},resize:function(){!i.animating&&i.is(":visible")&&(p||i.doMath(),m?h.smoothHeight():p?(i.slides.width(i.computedW),i.update(i.pagingCount),i.setProps()):u?(i.viewport.height(i.h),i.setProps(i.h,"setTotal")):(i.vars.smoothHeight&&h.smoothHeight(),i.newSlides.width(i.computedW),i.setProps(i.computedW,"setTotal")))},smoothHeight:function(e){if(!u||m){var t=m?i:i.viewport;e?t.animate({height:i.slides.eq(i.animatingTo).height()},e):t.height(i.slides.eq(i.animatingTo).height())}},sync:function(t){var a=e(i.vars.sync).data("tourmaster_flexslider"),n=i.animatingTo;switch(t){case"animate":a.flexAnimate(n,i.vars.pauseOnAction,!1,!0);break;case"play":a.playing||a.asNav||a.play();break;case"pause":a.pause()}},uniqueID:function(t){return t.filter("[id]").add(t.find("[id]")).each(function(){var t=e(this);t.attr("id",t.attr("id")+"_clone")}),t},pauseInvisible:{visProp:null,init:function(){var e=h.pauseInvisible.getHiddenProp();if(e){var t=e.replace(/[H|h]idden/,"")+"visibilitychange";document.addEventListener(t,function(){h.pauseInvisible.isHidden()?i.startTimeout?clearTimeout(i.startTimeout):i.pause():i.started?i.play():i.vars.initDelay>0?setTimeout(i.play,i.vars.initDelay):i.play()})}},isHidden:function(){var e=h.pauseInvisible.getHiddenProp();return!!e&&document[e]},getHiddenProp:function(){var e=["webkit","moz","ms","o"];if("hidden"in document)return"hidden";for(var t=0;t<e.length;t++)if(e[t]+"Hidden"in document)return e[t]+"Hidden";return null}},setToClearWatchedEvent:function(){clearTimeout(r),r=setTimeout(function(){d=""},3e3)}},i.flexAnimate=function(t,a,n,r,o){if(i.vars.animationLoop||t===i.currentSlide||(i.direction=t>i.currentSlide?"next":"prev"),f&&1===i.pagingCount&&(i.direction=i.currentItem<t?"next":"prev"),!i.animating&&(i.canAdvance(t,o)||n)&&i.is(":visible")){if(f&&r){var c=e(i.vars.asNavFor).data("tourmaster_flexslider");if(i.atEnd=0===t||t===i.count-1,c.flexAnimate(t,!0,!1,!0,o),i.direction=i.currentItem<t?"next":"prev",c.direction=i.direction,Math.ceil((t+1)/i.visible)-1===i.currentSlide||0===t)return i.currentItem=t,i.slides.removeClass(s+"active-slide").eq(t).addClass(s+"active-slide"),!1;i.currentItem=t,i.slides.removeClass(s+"active-slide").eq(t).addClass(s+"active-slide"),t=Math.floor(t/i.visible)}if(i.animating=!0,i.animatingTo=t,a&&i.pause(),i.vars.before(i),i.syncExists&&!o&&h.sync("animate"),i.vars.controlNav&&h.controlNav.active(),p||i.slides.removeClass(s+"active-slide").eq(t).addClass(s+"active-slide"),i.atEnd=0===t||t===i.last,i.vars.directionNav&&h.directionNav.update(),t===i.last&&(i.vars.end(i),i.vars.animationLoop||i.pause()),m)l?(i.slides.eq(i.currentSlide).css({opacity:0,zIndex:1}),i.slides.eq(t).css({opacity:1,zIndex:2}),i.wrapup(x)):(i.slides.eq(i.currentSlide).css({zIndex:1}).animate({opacity:0},i.vars.animationSpeed,i.vars.easing),i.slides.eq(t).css({zIndex:2}).animate({opacity:1},i.vars.animationSpeed,i.vars.easing,i.wrapup));else{var d,g,S,x=u?i.slides.filter(":first").height():i.computedW;p?(d=i.vars.itemMargin,g=(S=(i.itemW+d)*i.move*i.animatingTo)>i.limit&&1!==i.visible?i.limit:S):g=0===i.currentSlide&&t===i.count-1&&i.vars.animationLoop&&"next"!==i.direction?v?(i.count+i.cloneOffset)*x:0:i.currentSlide===i.last&&0===t&&i.vars.animationLoop&&"prev"!==i.direction?v?0:(i.count+1)*x:v?(i.count-1-t+i.cloneOffset)*x:(t+i.cloneOffset)*x,i.setProps(g,"",i.vars.animationSpeed),i.transitions?(i.vars.animationLoop&&i.atEnd||(i.animating=!1,i.currentSlide=i.animatingTo),i.container.unbind("webkitTransitionEnd transitionend"),i.container.bind("webkitTransitionEnd transitionend",function(){clearTimeout(i.ensureAnimationEnd),i.wrapup(x)}),clearTimeout(i.ensureAnimationEnd),i.ensureAnimationEnd=setTimeout(function(){i.wrapup(x)},i.vars.animationSpeed+100)):i.container.animate(i.args,i.vars.animationSpeed,i.vars.easing,function(){i.wrapup(x)})}i.vars.smoothHeight&&h.smoothHeight(i.vars.animationSpeed)}},i.wrapup=function(e){m||p||(0===i.currentSlide&&i.animatingTo===i.last&&i.vars.animationLoop?i.setProps(e,"jumpEnd"):i.currentSlide===i.last&&0===i.animatingTo&&i.vars.animationLoop&&i.setProps(e,"jumpStart")),i.animating=!1,i.currentSlide=i.animatingTo,i.vars.after(i)},i.animateSlides=function(){!i.animating&&t&&i.flexAnimate(i.getTarget("next"))},i.pause=function(){clearInterval(i.animatedSlides),i.animatedSlides=null,i.playing=!1,i.vars.pausePlay&&h.pausePlay.update("play"),i.syncExists&&h.sync("pause")},i.play=function(){i.playing&&clearInterval(i.animatedSlides),i.animatedSlides=i.animatedSlides||setInterval(i.animateSlides,i.vars.slideshowSpeed),i.started=i.playing=!0,i.vars.pausePlay&&h.pausePlay.update("pause"),i.syncExists&&h.sync("play")},i.stop=function(){i.pause(),i.stopped=!0},i.canAdvance=function(e,t){var a=f?i.pagingCount-1:i.last;return!!t||(!(!f||i.currentItem!==i.count-1||0!==e||"prev"!==i.direction)||(!f||0!==i.currentItem||e!==i.pagingCount-1||"next"===i.direction)&&(!(e===i.currentSlide&&!f)&&(!!i.vars.animationLoop||(!i.atEnd||0!==i.currentSlide||e!==a||"next"===i.direction)&&(!i.atEnd||i.currentSlide!==a||0!==e||"next"!==i.direction))))},i.getTarget=function(e){return i.direction=e,"next"===e?i.currentSlide===i.last?0:i.currentSlide+1:0===i.currentSlide?i.last:i.currentSlide-1},i.setProps=function(e,t,a){var n=function(){var a=e||(i.itemW+i.vars.itemMargin)*i.move*i.animatingTo;return-1*function(){if(p)return"setTouch"===t?e:v&&i.animatingTo===i.last?0:v?i.limit-(i.itemW+i.vars.itemMargin)*i.move*i.animatingTo:i.animatingTo===i.last?i.limit:a;switch(t){case"setTotal":return v?(i.count-1-i.currentSlide+i.cloneOffset)*e:(i.currentSlide+i.cloneOffset)*e;case"setTouch":return e;case"jumpEnd":return v?e:i.count*e;case"jumpStart":return v?i.count*e:e;default:return e}}()+"px"}();i.transitions&&(n=u?"translate3d(0,"+n+",0)":"translate3d("+n+",0,0)",a=void 0!==a?a/1e3+"s":"0s",i.container.css("-"+i.pfx+"-transition-duration",a),i.container.css("transition-duration",a)),i.args[i.prop]=n,(i.transitions||void 0===a)&&i.container.css(i.args),i.container.css("transform",n)},i.setup=function(t){if(m)i.slides.css({width:"100%",float:"left",marginRight:"-100%",position:"relative"}),"init"===t&&(l?i.slides.css({opacity:0,display:"block",webkitTransition:"opacity "+i.vars.animationSpeed/1e3+"s ease",zIndex:1}).eq(i.currentSlide).css({opacity:1,zIndex:2}):0==i.vars.fadeFirstSlide?i.slides.css({opacity:0,display:"block",zIndex:1}).eq(i.currentSlide).css({zIndex:2}).css({opacity:1}):i.slides.css({opacity:0,display:"block",zIndex:1}).eq(i.currentSlide).css({zIndex:2}).animate({opacity:1},i.vars.animationSpeed,i.vars.easing)),i.vars.smoothHeight&&h.smoothHeight();else{var a,n;"init"===t&&(i.viewport=e('<div class="'+s+'viewport"></div>').css({overflow:"hidden",position:"relative"}).appendTo(i).append(i.container),i.cloneCount=0,i.cloneOffset=0,v&&(n=e.makeArray(i.slides).reverse(),i.slides=e(n),i.container.empty().append(i.slides))),i.vars.animationLoop&&!p&&(i.cloneCount=2,i.cloneOffset=1,"init"!==t&&i.container.find(".clone").remove(),i.container.append(h.uniqueID(i.slides.first().clone().addClass("clone")).attr("aria-hidden","true")).prepend(h.uniqueID(i.slides.last().clone().addClass("clone")).attr("aria-hidden","true"))),i.newSlides=e(i.vars.selector,i),a=v?i.count-1-i.currentSlide+i.cloneOffset:i.currentSlide+i.cloneOffset,u&&!p?(i.container.height(200*(i.count+i.cloneCount)+"%").css("position","absolute").width("100%"),setTimeout(function(){i.newSlides.css({display:"block"}),i.doMath(),i.viewport.height(i.h),i.setProps(a*i.h,"init")},"init"===t?100:0)):(i.container.width(200*(i.count+i.cloneCount)+"%"),i.setProps(a*i.computedW,"init"),setTimeout(function(){i.doMath(),i.newSlides.css({width:i.computedW,marginRight:i.computedM,float:"left",display:"block"}),i.vars.smoothHeight&&h.smoothHeight()},"init"===t?100:0))}p||i.slides.removeClass(s+"active-slide").eq(i.currentSlide).addClass(s+"active-slide"),i.vars.init(i)},i.doMath=function(){var t=i.slides.first(),a=i.vars.itemMargin,n=i.vars.minItems,r=i.vars.maxItems;"function"==typeof window.matchMedia?(window.matchMedia("(max-width: 767px)").matches&&(n=1,r=1),window.matchMedia("(max-width: 419px)").matches&&(n=1,r=1)):(e(window).innerWidth()<767&&(n=1,r=1),e(window).innerWidth()<419&&(n=1,r=1)),i.w=void 0===i.viewport?i.width():i.viewport.width(),i.h=t.height(),i.boxPadding=t.outerWidth()-t.width(),p?(i.itemT=i.vars.itemWidth+a,i.itemM=a,i.minW=n?n*i.itemT:i.w,i.maxW=r?r*i.itemT-a:i.w,i.itemW=i.minW>i.w?(i.w-a*(n-1))/n:i.maxW<i.w?(i.w-a*(r-1))/r:i.vars.itemWidth>i.w?i.w:i.vars.itemWidth,i.visible=Math.floor((i.w+i.itemM)/(i.itemW+i.itemM)),i.move=i.vars.move>0&&i.vars.move<i.visible?i.vars.move:i.visible,i.pagingCount=Math.ceil((i.count-i.visible)/i.move+1),i.last=i.pagingCount-1,i.limit=1===i.pagingCount?0:i.vars.itemWidth>i.w?i.itemW*(i.count-1)+a*(i.count-1):(i.itemW+a)*i.count-i.w-a):(i.itemW=i.w,i.itemM=a,i.pagingCount=i.count,i.last=i.count-1),i.computedW=i.itemW-i.boxPadding,i.computedM=i.itemM},i.update=function(e,t){i.doMath(),p||(e<i.currentSlide?i.currentSlide+=1:e<=i.currentSlide&&0!==e&&(i.currentSlide-=1),i.animatingTo=i.currentSlide),i.vars.controlNav&&!i.manualControls&&("add"===t&&!p||i.pagingCount>i.controlNav.length?h.controlNav.update("add"):("remove"===t&&!p||i.pagingCount<i.controlNav.length)&&(p&&i.currentSlide>i.last&&(i.currentSlide-=1,i.animatingTo-=1),h.controlNav.update("remove",i.last))),i.vars.directionNav&&h.directionNav.update()},i.addSlide=function(t,a){var n=e(t);i.count+=1,i.last=i.count-1,u&&v?void 0!==a?i.slides.eq(i.count-a).after(n):i.container.prepend(n):void 0!==a?i.slides.eq(a).before(n):i.container.append(n),i.update(a,"add"),i.slides=e(i.vars.selector+":not(.clone)",i),i.setup(),i.vars.added(i)},i.removeSlide=function(t){var a=isNaN(t)?i.slides.index(e(t)):t;i.count-=1,i.last=i.count-1,isNaN(t)?e(t,i.slides).remove():u&&v?i.slides.eq(i.last).remove():i.slides.eq(t).remove(),i.doMath(),i.update(a,"remove"),i.slides=e(i.vars.selector+":not(.clone)",i),i.setup(),i.vars.removed(i)},i.editItemWidth=function(e){i.vars.itemWidth=e,h.resize()},h.init()},e(window).blur(function(e){t=!1}).focus(function(e){t=!0}),e.tourmaster_flexslider.defaults={namespace:"tourmaster-flex-",selector:".slides > li",animation:"fade",easing:"swing",direction:"horizontal",reverse:!1,animationLoop:!0,smoothHeight:!1,startAt:0,slideshow:!0,slideshowSpeed:7e3,animationSpeed:600,initDelay:0,randomize:!1,fadeFirstSlide:!0,thumbCaptions:!1,pauseOnAction:!0,pauseOnHover:!1,pauseInvisible:!0,useCSS:!0,touch:!0,video:!1,controlNav:!0,directionNav:!0,prevText:"Previous",nextText:"Next",keyboard:!0,multipleKeyboard:!1,mousewheel:!1,pausePlay:!1,pauseText:"Pause",playText:"Play",controlsContainer:"",manualControls:"",customDirectionNav:"",sync:"",asNavFor:"",itemWidth:0,itemMargin:0,minItems:1,maxItems:0,move:0,allowOneSlide:!0,start:function(){},before:function(){},after:function(){},end:function(){},added:function(){},removed:function(){},init:function(){}},e.fn.tourmaster_flexslider=function(t){if(void 0===t&&(t={}),"object"==typeof t)return this.each(function(){var a=e(this),n=t.selector?t.selector:".slides > li",i=a.find(n);1===i.length&&!0===t.allowOneSlide||0===i.length?(i.fadeIn(400),t.start&&t.start(a)):void 0===a.data("tourmaster_flexslider")&&new e.tourmaster_flexslider(this,t)});var a=e(this).data("tourmaster_flexslider");switch(t){case"play":a.play();break;case"pause":a.pause();break;case"stop":a.stop();break;case"next":a.flexAnimate(a.getTarget("next"),!0);break;case"prev":case"previous":a.flexAnimate(a.getTarget("prev"),!0);break;default:"number"==typeof t&&a.flexAnimate(t,!0)}}}(jQuery);