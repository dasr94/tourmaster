// for page option
(function($){
	"use strict";

	/******************************************
	*	rating section
	******************************************/

	// rating click
	function tourmaster_admin_rating( container ){

		container.find('.tourmaster-review-form-rating, .tourmaster-tour-search-field-rating').each(function(){

			$(this).children('.tourmaster-rating-select').click(function(){
				$(this).siblings('input').val($(this).attr("data-rating-score"));

				if($(this).is('i')){ $(this).removeClass().addClass('tourmaster-rating-select fa fa-star-half-empty'); }
				$(this).prevAll('i').removeClass().addClass('tourmaster-rating-select fa fa-star');
				$(this).nextAll('i').removeClass().addClass('tourmaster-rating-select fa fa-star-o');
			});

		});

	}	

	// tourmaster lightbox
	function tourmaster_admin_lightbox( content, admin_review ){

		var lightbox_wrap = $('<div class="tourmaster-admin-lightbox-wrapper" ></div>').hide();
		var lightbox_content_wrap = $('<div class="tourmaster-admin-lightbox-content-cell" ></div>');
		lightbox_wrap.append(lightbox_content_wrap);
		lightbox_content_wrap.wrap($('<div class="tourmaster-admin-lightbox-content-row" ></div>'));

		lightbox_content_wrap.append(content);
		$('body').append(lightbox_wrap);
		lightbox_wrap.fadeIn(300);

		// rating action
		tourmaster_admin_rating(lightbox_wrap);

		// bind datepicker
		lightbox_wrap.find('.tourmaster-html-option-datepicker').each(function(){
			$(this).datepicker({
				dateFormat : 'yy-mm-dd',
				changeMonth: true,
				changeYear: true 
			});
		});

		// do a lightbox action
		lightbox_wrap.on('click', '.tourmaster-admin-lightbox-close', function(){
			lightbox_wrap.fadeOut(300, function(){
				$(this).remove();
			});
		});

		// edit ajax action
		lightbox_wrap.on('click', '.tourmaster-submit-review', function(){

			var button = $(this);
			button.addClass('tourmaster-now-loading');

			var data_sent = new Object();
			$(this).closest('.tourmaster-review-form').find('[name]').each(function(){
				if( $(this).attr('name') == 'review_action' ){
					data_sent['action'] = $(this).val();
				}else{
					data_sent[$(this).attr('name')] = $(this).val();
				}
			});

			// ajax to obtain value
			$.ajax({
				type: 'POST',
				url: button.attr('data-ajax-url'),
				data: data_sent,
				dataType: 'json',
				error: function(jqXHR, textStatus, errorThrown){
					button.removeClass('tourmaster-now-loading');

					// for displaying the debug text
					console.log(jqXHR, textStatus, errorThrown);
				},
				success: function(data){
					button.removeClass('tourmaster-now-loading');

					if( typeof(data.status) != 'undefined' && typeof(data.status) ){
						tourmaster_alert_box({
							status: data.status,
							message: data.message,
						});
					}

					if( data.status == 'success' ){
						admin_review.trigger('change');
					}

					lightbox_wrap.fadeOut(300, function(){
						$(this).remove();
					});
				}
			});
		});

	} // tourmaster_lightbox

	// rating script
	function tourmaster_bind_admin_rating(){

		var admin_review = $('#tourmaster-review-option');
		if( admin_review.length ){
			tourmaster_admin_rating(admin_review);

			// script to edit review
			admin_review.on('click', '.tourmaster-single-review-edit', function(){

				if( $(this).hasClass('tourmaster-active') ){ return; }
				$(this).addClass('tourmaster-active');

				var edit_button = $(this);
				var item_holder = $(this).closest('.tourmaster-html-option-admin-manage-review');

				var now_loading = $('<div class="tourmaster-single-review-edit-loading" ></div>').css('display', 'none');
				$('body').append(now_loading);
				now_loading.fadeIn(200);

				$.ajax({
					type: 'POST',
					url: item_holder.attr('data-ajax-url'),
					data: { 
						action: 'tourmaster_get_edit_admin_review_item', 
						review_id: edit_button.attr('data-id')
					},
					dataType: 'json',
					error: function(jqXHR, textStatus, errorThrown){
						edit_button.removeClass('tourmaster-active');
						now_loading.fadeOut(200, function(){ $(this).remove(); });

						// for displaying the debug text
						console.log(jqXHR, textStatus, errorThrown);
					}, 
					success: function(data){
						edit_button.removeClass('tourmaster-active');
						now_loading.fadeOut(200, function(){ $(this).remove(); });

						if( typeof(data.content) != 'undefined' ){
							tourmaster_admin_lightbox(data.content, admin_review);
						}else if( typeof(data.status) != 'undefined' && typeof(data.message) != 'undefined' ){
							tourmaster_alert_box({
								status: data.status,
								message: data.message,
							});
						}
					}
				});
			});

			// script to remove review
			admin_review.on('click', '.tourmaster-single-review-remove', function(){

				var remove_button = $(this);
				var item_holder = $(this).closest('.tourmaster-html-option-admin-manage-review');

				tourmaster_confirm_box({ 
					success: function(){
						$.ajax({
							type: 'POST',
							url: item_holder.attr('data-ajax-url'),
							data: { 
								action: 'tourmaster_remove_admin_review_item', 
								review_id: remove_button.attr('data-id')
							},
							dataType: 'json',
							error: function(jqXHR, textStatus, errorThrown){

								// for displaying the debug text
								console.log(jqXHR, textStatus, errorThrown);
							}, 
							success: function(data){

							}
						});

						remove_button.closest('.tourmaster-single-review-content-item').slideUp(200, function(){ $(this).remove(); });
					}
				});

				
			});

			// script to refresh the review content
			admin_review.change(function(){
				var item_holder = $(this).find('.tourmaster-html-option-admin-manage-review');
				item_holder.slideUp(200);

				// ajax to obtain value
				$.ajax({
					type: 'POST',
					url: item_holder.attr('data-ajax-url'),
					data: { 
						action: 'tourmaster_get_admin_review_item', 
						tour_id: item_holder.attr('data-tour-id'), 
						paged: 1
					},
					dataType: 'json',
					error: function(jqXHR, textStatus, errorThrown){

						// for displaying the debug text
						console.log(jqXHR, textStatus, errorThrown);
					},
					success: function(data){
						if( typeof(data.content) != 'undefined' ){
							item_holder.html(data.content).slideDown();
						}else if( typeof(data.status) != 'undefined' && typeof(data.message) != 'undefined' ){
							tourmaster_alert_box({
								status: data.status,
								message: data.message,
							});
						}
					}
				});
			});

			// script for review pagination
			admin_review.on('click', '.tourmaster-review-content-pagination span', function(){
				
				var item_holder = $(this).closest('.tourmaster-html-option-admin-manage-review');
				item_holder.slideUp(200);

				// ajax to obtain value
				$.ajax({
					type: 'POST',
					url: item_holder.attr('data-ajax-url'),
					data: { 
						action: 'tourmaster_get_admin_review_item', 
						tour_id: item_holder.attr('data-tour-id'), 
						paged: $(this).attr('data-paged') 
					},
					dataType: 'json',
					error: function(jqXHR, textStatus, errorThrown){

						// for displaying the debug text
						console.log(jqXHR, textStatus, errorThrown);
					},
					success: function(data){
						if( typeof(data.content) != 'undefined' ){
							item_holder.html(data.content).slideDown();
						}else if( typeof(data.status) != 'undefined' && typeof(data.message) != 'undefined' ){
							tourmaster_alert_box({
								status: data.status,
								message: data.message,
							});
						}
					}
				});
			});


			// script to add new review
			admin_review.on('click', '.tourmaster-submit-review', function(){

				var button = $(this);
				button.addClass('tourmaster-now-loading');

				var data_sent = new Object();
				$(this).closest('.tourmaster-review-form').find('[name]').each(function(){
					if( $(this).attr('name') == 'review_action' ){
						data_sent['action'] = $(this).val();
					}else{
						data_sent[$(this).attr('name')] = $(this).val();
					}
				});

				// ajax to obtain value
				$.ajax({
					type: 'POST',
					url: button.attr('data-ajax-url'),
					data: data_sent,
					dataType: 'json',
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

						if( data.status == 'success' ){
							admin_review.trigger('change');
						}
					}
				});
			});
		}

	} // tourmaster_bind_admin_rating

	/******************************************
	*	html option section
	******************************************/

	$(document).ready(function(){
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
			
		}); // tourmaster-page-option-content
		
		$('#edittag, #addtag').each(function(){
			new tourmasterHtmlOption($(this));
		});

		// bind page option review
		tourmaster_bind_admin_rating();

	}); // document ready
	
})(jQuery);	

// html option
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
			
			t.container.find('.tourmaster-html-option-colorpicker').wpColorPicker();

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

				t.tabs_container.find('.tourmaster-html-option-colorpicker').wpColorPicker({
					change: tourmaster_debounce(function(event, ui){
						t.update_data();
					}, 500),
				});

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
				template.find('.tourmaster-html-option-colorpicker').wpColorPicker({
					change: tourmaster_debounce(function(event, ui){
						t.update_data();
					}, 500),
				});

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

		t.gallery_container = $('<div class="tourmaster-html-option-gallery-container" ></div>');
		t.add_button = $('<div class="tourmaster-html-option-gallery-add" ><i class="icon_plus" ></i></div>');
		
		t.template = $('<div class="tourmaster-html-option-gallery-template">\
				<img class="tourmaster-html-option-gallery-template-thumbnail" src="" alt="" /> \
				<div class="tourmaster-html-option-gallery-template-remove" ><i class="fa fa-remove" ></i></div>\
			</div>');
		
		t.init();
		
	}
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