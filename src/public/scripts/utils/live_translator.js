( function( window, undefined ) {

	var $ = window.jQuery;

	var Translator = {};

	var currentLang = $( "html" ).attr( "lang" );

	Translator = {
		Translate: function( sourceElement, targetElement ) {

			targetElement = $( targetElement );
			sourceElement = $( sourceElement );
			var sourceLang = sourceElement.data( "translatable_lang" );
			var targetLang = targetElement.data( "translatable_lang" );

			var queryParams = {
				source_lang: sourceLang,
				target_lang: targetLang,
				q: sourceElement.val(),
				format: "json"
			};

			$.ajax( {
				url: "/api/" + currentLang + "/live_translator/translate",
				type: "POST",
				data: queryParams,
				dataType: "json",
				success: function( json ) {
					if (
						targetElement.data( "provide" ) === "markdown" &&
						targetElement.hasClass( "md-textarea-hidden" )
					) {
						targetElement.markdownEditor( "setContent", json.result );
					} else {
						targetElement.val( json.result );
					}
					return true;
				},
				error: function( xhr, statusTxt, errorTxt ) {
					window.alert( "Problém s GoogleTranslate, zkuste překlad použít později\n\n" +
							statusTxt + ": " + errorTxt +
							"\nstatus: " + xhr.status + "\nsee javascript console for details" );

					console.log( statusTxt + ": " + errorTxt );
					console.log( "translation url: " + xhr.responseJSON.url );
					console.log( "translation status_code: " + xhr.responseJSON.status_code );

					//					Uncomment if needed
//					console.log( xhr.responseJSON );
				}
			} );
		}
	};
	window.Translator = Translator;

	$.fn.extend( {
		translate: function( sourceElement ) {

			// Tady by se mohl dat nejaky filtr, ktery propusti jen inputy s nejakou vlastnosti
			this.each( function() {
				var thisElement = $( this );
				Translator.Translate( sourceElement, thisElement );
			} );
		},

		appendTranslateButton: function() {
			var linkText = currentLang === "cs" ? "Přeložit" : "Translate";
			this.each( function() {
				var link = $( "<a>", { href: "#", text: linkText, tabindex: "-1000" } )
					.addClass( "btn btn-info btn-sm btn-xs" )
					.addClass( "pull-right" ) // Bootstrap3
					.addClass( "float-right" ) // Bootstrap4
					.attr( "role", "button" );

				var selfId = $( this ).attr( "id" );
				selfId = selfId.substr( 0, selfId.lastIndexOf( "_" ) );
				var sourceInputId = selfId + "_" + $( "html" ).attr( "lang" );

				var targetInput = $( this );
				link.click( function() {
					targetInput.translate( $( "#" + sourceInputId ) );
					return false;
				} );

				var parentFormGroup = $( this ).closest( ".form-group" );

				// If .help-block element doesn`t exist in .form-group just create it.
				if ( parentFormGroup.find( ".help-block" ).length < 1 ) {
					var helpBlock = $( "<div>", { text: "" } );
					helpBlock.addClass( "help-block" );
					helpBlock.addClass( "form-text" );
					parentFormGroup.append( helpBlock );
				}

				parentFormGroup.find( ".help-block" ).prepend( link );

			} );
		}
	} );

	// @NOTE: Change data-translatable to 'yes' to turn on translator initialization,
	// 'no' turns it off.
	$( "[data-translatable='yes']" ).each( function( idx, el ) {
		var input = $( el );

		var targetLang = input.data( "translatable_lang" );
		if ( targetLang === currentLang ) {
			return;
		}
		input.appendTranslateButton();
	} );

} )( this );
