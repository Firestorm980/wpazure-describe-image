(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	var
		$document = $( document ),
		data      = {},
		threshold = 0;

	var getDescriptions = function () {
		var
			$button       = $( this ),
			region        = 'westcentralus',
			maxCandidates = 5,
			dataImageURL  = $button.attr( 'data-image-url' ) || $( '[data-setting="url"] input' ).val();

		$( '#jsWPADIGetDescriptions' ).prop( 'disabled', true );
		$( '#jsWPADIResults' ).removeClass( 'has-choices' );
		$( '#jsWPADINotices' ).removeClass( 'has-notices' );
		$( '#jsWPADILoader' ).addClass( 'is-active' );

		$.ajax({
			method: 'POST',
			data: {
				action: 'azure_describe_image',
				image: dataImageURL
			},
			dataType: 'json',
			url: ajaxurl
		})
		.done( getDescriptionsSuccess );
	};

	var getDescriptionsSuccess = function ( response, textStatus, jqXHR ) {
		var
			body = null,
			choices = [];

		if ( response.success ) {
			body = JSON.parse( response.data.api.body );
		} else {
			getDescriptionsNotice( WPADIi18n.notices.error_general + ' ' + response.data.message, 'error' );
		}

		if ( 200 !== response.data.api.response.code ) {
			getDescriptionsNotice( WPADIi18n.notices.error_general + ' ' + body.message, 'error' );
		} else {
			data = body;
			threshold = parseFloat( response.data.threshold );
			choices = getChoicesHTML();

			if ( ! choices ) {
				getDescriptionsNotice( WPADIi18n.notices.info_threshold, 'info' );
			} else {
				$( '#jsWPADIResults' ).addClass( 'has-choices' );
				$( '#jsWPADINotices' ).removeClass( 'has-notices' );
				$( '#jsWPADIChoices' ).append( choices );
				$( '#jsWPADISetDescription' ).prop( 'disabled', false );				
			}			
		}

		$( '#jsWPADILoader' ).removeClass( 'is-active' );
	};

	var getDescriptionsNotice = function ( message, type ) {$( '#jsWPADIResults' )
		var
			type    = type || '',
			classes = [ 'notice' ];
		
		if ( 'error' === type ) {
			classes.push( 'notice-error' );
		} else if ( 'info' === type) {
			classes.push( 'notice-info' );
		} else if ( 'warning' === type ) {
			classes.push( 'notice-warning' );
		}

		classes = classes.join( ' ' );
		
		$( '#jsWPADINotice' ).addClass( classes );
		$( '#jsWPADINoticeText' ).text( message );
		$( '#jsWPADIResults' ).removeClass( 'has-choices' );
		$( '#jsWPADINotices' ).addClass( 'has-notices' );
		$( '#jsWPADIGetDescriptions' ).prop( 'disabled', false );
	};

	var getChoicesHTML = function () {
		var
			captions = data.description.captions,
			HTML     = [];
		
		for ( var index = 0; index < captions.length; index++ ) {
			var
				caption         = captions[index],
				text            = caption.text,
				confidence      = parseFloat( caption.confidence ),
				confidenceClass = '',
				checked         = ( 0 === index ) ? 'checked' : '';

			if ( threshold > confidence ) {
				continue;
			}

			if ( confidence > 0.67 ) {
				confidenceClass = 'wpazure-describe-image__choice--high';
			} else if ( confidence > 0.33 ) {
				confidenceClass = 'wpazure-describe-image__choice--med';
			} else {
				confidenceClass = 'wpazure-describe-image__choice--low';
			}

			HTML = HTML.concat( [
				'<li class="wpazure-describe-image__choice ' + confidenceClass + '">',
					'<input id="caption_' + index + '" type="radio" name="generated_caption" value="' + text + '" class="screen-reader-text" ' + checked + '>',
					'<label for="caption_' + index + '" aria-label="' + WPADIi18n.choice.description + ' ' + text + '. ' + WPADIi18n.choice.confidence +  ' ' + (confidence * 100).toFixed(2) + '%">',
					'<span class="caption__text">&ldquo;' + text + '&rdquo;</span>',
					'<span class="caption__confidence">',
					'<span class="caption__confidence__bar" style="width: ' + (confidence * 100).toFixed(2) + '%;">',
					(confidence * 100).toFixed(2) + '%',
					'</span>',
					'</span>',
					'</label>',
				'</li>'
			] );

		}

		if ( !HTML.length ) {
			return false;
		}

		HTML = HTML.join( '\n' );

		return HTML;
	};

	var getDescriptionsFail = function ( jqXHR, textStatus, noticeThrown ) {$( '#jsWPADIResults' )
		$( '#jsWPADIResults' ).removeClass( 'has-choices' );
	};

	var setDescription = function ( event ) {
		var
			value = $( 'input[type="radio"][name="generated_caption"]:checked' ).val(),
			$alt  = ( $( '#attachment_alt' ).length ) ? $( '#attachment_alt' ) : $( '[data-setting="alt"] input' );

		$alt.val( value );

		closeChoices();
	};

	var closeChoices = function () {$( '#jsWPADIResults' )
		$( '#jsWPADIResults' ).removeClass( 'has-choices' );
		$( '#jsWPADIGetDescriptions' ).prop( 'disabled', false );
		$( '#jsWPADISetDescription' ).prop( 'disabled', true );
		$( '#jsWPADIChoices' ).html( '' );
	};

	var buttonCancelClick = function () {
		closeChoices();	
	};

	// Bind
	$document.on( 'click', '#jsWPADIGetDescriptions', getDescriptions );
	$document.on( 'click', '#jsWPADISetDescription', setDescription );
	$document.on( 'click', '#jsWPADICancel', buttonCancelClick );

})( jQuery );
