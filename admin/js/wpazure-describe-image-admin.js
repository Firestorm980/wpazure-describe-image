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
		$buttonGet    = $( '#jsPluginNameGetDescriptions' ),
		$buttonSet    = $( '#jsPluginNameSetDescription' ),
		$buttonCancel = $( '#jsPluginNameCancel' ),
		$results      = $( '#jsPluginNameResults' ),
		$choices      = $( '#jsPluginNameChoices' ),
		data          = {};
/*
	var getDescriptions = function () {
		var
			$button       = $( this ),
			region        = 'westcentralus',
			maxCandidates = 5,
			dataImageURL  = $button.attr( 'data-image-url' );

		$buttonGet.prop( 'disabled', true );

		$.ajax({
			contentType: 'application/json',
			headers: {
				'Ocp-Apim-Subscription-Key': '80c03dfc4bd64cbbb7b44a2484493742'
			},
			data: '{"url": "https://media.wired.com/photos/5926538eaf95806129f4f0b6/master/pass/UnsplashHP.jpg"}',
			dataType: 'json',
			method: 'POST',
			url: 'https://' + region + '.api.cognitive.microsoft.com/vision/v1.0/describe?maxCandidates=' + maxCandidates
		})
		.done( getDescriptionsSuccess )
		.fail( getDescriptionsFail );		
	};
*/
	var getDescriptions = function () {
		var
			$button       = $( this ),
			region        = 'westcentralus',
			maxCandidates = 5,
			dataImageURL  = $button.attr( 'data-image-url' );

		$buttonGet.prop( 'disabled', true );

		$.ajax({
			method: 'POST',
			data: {
				action: 'azure_describe_image',
				image: dataImageURL
			},
			dataType: 'json',
			url: ajaxurl
		})
		.done( function( res ) { console.log( res ); } )
	};

	var getDescriptionsSuccess = function ( response ) {
		data = response;

		$results.addClass( 'has-choices' );
		$choices.append( getChoicesHTML() );
		$buttonSet.prop( 'disabled', false );
	};

	var getChoicesHTML = function () {
		var
			captions = data.description.captions,
			HTML     = [];

		for ( var index = 0; index < captions.length; index++ ) {
			var caption = captions[index].text;
			
			HTML = HTML.concat( [
				'<li>',
					'<input id="caption_' + index + '" type="radio" name="generated_caption" value="' + caption + '">',
					'<label for="caption_' + index + '">',
					'&ldquo;' + caption + '&rdquo;',
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
