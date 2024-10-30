(function($) {

	$(document).ready(function(){

		// Modal objects
		var modal_object = {
			'modal_content'		: '#popup-content-wrap',
			'modal_container'	: '.pdfi-modal-content',
			'modal_class'		: '.pdfi-modal'
		}

		// Trigger Modal
		$(document).on('click', '[data-modal]',function(e){
			e.preventDefault();

			// Setup data
			var data = {
				action		:	'pdfi_get_user_profile',
				security	:	pdfi_ojbect.security,
				id			:	$(this).data('modal')
			};

			// Add loading status
			pdfi_Modal.open_modal( true );

			// Run Ajax
			$.post( pdfi_ojbect.ajax_url, data, function(response){
				if( typeof response.success != 'undefined' && response.success ) {

					// Remove loading class
					$( modal_object.modal_container ).removeClass('loading').addClass('active');

					// Empty content and Append a new profile Content
					$( modal_object.modal_content ).html('');
					$( modal_object.modal_content ).append( response.data );					

					// Open a modal @param ( bolean : show loading, object : popup content )
					pdfi_Modal.open_modal( false);
				}
			});

		});

		// Modal Close
		$('span.close').on("click", function(){
			pdfi_Modal.close_modal();
		});

		// Close modal when pressing Esc
		$(document).keyup(function(e) {
			if(e.keyCode== 27) {
				pdfi_Modal.close_modal();
			}
		});

		// Modal Object
		var pdfi_Modal = {
			// Open Modal
			open_modal : function( show_loading ){
				// Show loading...
				if( show_loading ) {
					$( modal_object.modal_container ).addClass('loading');
					$( modal_object.modal_content ).append('<img alt="loading..." src="'+ pdfi_ojbect.plugin_url +'/images/loader.gif">');
					$( modal_object.modal_class ).fadeIn();
				}
				$( modal_object.modal_class ).fadeIn();
			},
			// Close modal
			close_modal : function(){
				$( modal_object.modal_class ).fadeOut();
				$( modal_object.modal_content ).html('');
			}
		};

		// Custom - Filter
		$('.pdfi-filter ul.filter li a').click(function(e){
			e.preventDefault();

			// Setup element and assign in variable
			var filter_key = $(this).data('filter');
			var filter_content = $('[data-id="p-filter-content"]');
			var found_column = filter_content.find( "[class*='"+ filter_key +"']" );
			
			// Remove active class
			$('.pdfi-filter ul.filter li:not(disabled) a').removeClass('active');
			
			// Add default height of container
			filter_content.css('min-height', 300);
			
			// Add active class
			$(this).addClass('active');
			
			// Remove class
			filter_content.find('.column').removeClass('filtered');			
			
			// If filter is * means show All
			if( filter_key == '*' ) {
				filter_content.find('.column').fadeIn();
				return true;
			}

			// If we found something
			if( found_column.length > 0 ) {

				// Hide column items
				filter_content.find('.column').hide();

				// Show Columns matched on filter key.
				$.each( found_column, function( index ){
					$(this).delay( 250 * index ).addClass('filtered').fadeIn();
				});
			}
			
			return false;
		});

		// Load More
		$('[data-action="load-more"]').on("click", function(e){
			e.preventDefault();

			// Declare default variable
			var load_more = $(this),
				paged = ( load_more.data('paged') ? load_more.attr('data-paged') : 1 );

			// add loading text
			load_more.text('Loading...');

			// Setup data
			var data = {
				action 		: 'load_more_profile',
				security 	: pdfi_ojbect.security,
				page 		: paged,
				per_page	: pdfi_options.limit,
				filter_by	: pdfi_options.filter_by
			}

			// Process Ajax request
			$.post( pdfi_ojbect.ajax_url, data, function(response){
				if( response.success ) {
					var profile_content = $(response.data);

					$('[data-id="p-filter-content"]').append( profile_content );
					$.each( profile_content, function(index) {
						$(this).hide().delay( 60 * index ).fadeIn();
					});

					load_more.attr('data-paged', parseInt(paged) + 1);
					load_more.text('Load More');
				}else {
					load_more.parent('div').text('No more posts to show.')
				}
			});
		});

	});

})(jQuery);