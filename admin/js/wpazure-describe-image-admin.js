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
		$alt          = $( '#attachment_alt' ),
		$buttonGet    = $( '#jsWPADIGetDescriptions' ),
		$buttonSet    = $( '#jsWPADISetDescription' ),
		$buttonCancel = $( '#jsWPADICancel' ),
		$results      = $( '#jsWPADIResults' ),
		$error        = $( '#jsWPADIError' ),
		$errors       = $( '#jsWPADIErrors' ),
		$errorText    = $( '#jsWPADIErrorText' ),
		$choices      = $( '#jsWPADIChoices' ),
		data          = {},
		threshold     = 0;

	var getDescriptions = function () {
		var
			$button       = $( this ),
			region        = 'westcentralus',
			maxCandidates = 5,
			dataImageURL  = $button.attr( 'data-image-url' );

		$buttonGet.prop( 'disabled', true );
		$results.removeClass( 'has-choices' );
		$errors.removeClass( 'has-errors' );

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
		var body = null;

		if ( response.success ) {
			body = JSON.parse( response.data.api.body );
		} else {
			getDescriptionsError( response.data.message );
		}

		if ( 200 !== response.data.api.response.code ) {
			getDescriptionsError( body.message );
		} else {
			data = body;
			threshold = parseFloat( response.data.threshold );

			$results.addClass( 'has-choices' );
			$errors.removeClass( 'has-errors' );
			$choices.append( getChoicesHTML() );
			$buttonSet.prop( 'disabled', false );			
		}
	};

	var getDescriptionsError = function ( message ) {
		$results.removeClass( 'has-choices' );
		$error.addClass( 'error' );
		$errors.addClass( 'has-errors' );
		$errorText.text( message );
		$buttonGet.prop( 'disabled', false );		
	};

	var getChoicesHTML = function () {
		var
			captions = data.description.captions,
			HTML     = [];

		for ( var index = 0; index < captions.length; index++ ) {
			var
				caption    = captions[index],
				text       = caption.text,
				confidence = parseFloat( caption.confidence ),
				confidenceClass = '';

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
					'<input id="caption_' + index + '" type="radio" name="generated_caption" value="' + text + '" class="screen-reader-text">',
					'<label for="caption_' + index + '" aria-label="Description: ' + text + '. Description is ' + (confidence * 100).toFixed(2) + '% confidence.">',
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

		HTML = HTML.join( '\n' );

		return HTML;
	};

	var getDescriptionsFail = function ( jqXHR, textStatus, errorThrown ) {
		$results.removeClass( 'has-choices' );
	};

	var setDescription = function ( event ) {
		var value = $( 'input:radio[name="generated_caption"]:checked' ).val();

		$alt.val( value );

		closeChoices();
	};

	var closeChoices = function () {
		$results.removeClass( 'has-choices' );
		$buttonGet.prop( 'disabled', false );
		$buttonSet.prop( 'disabled', true );
		$choices.html( '' );
	};

	var buttonCancelClick = function () {
		closeChoices();	
	};

	// Bind
	$buttonGet.on( 'click', getDescriptions );
	$buttonSet.on( 'click', setDescription );
	$buttonCancel.on( 'click', buttonCancelClick );

})( jQuery );
