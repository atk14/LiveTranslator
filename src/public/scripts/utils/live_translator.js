( function( window, undefined ) {

	var $ = window.jQuery;

	var Translator = {};

	var currentLang = $( "html" ).attr( "lang" );

	var sourceLang = $( "html" ).data( "live_translator_source_lang" ) || currentLang;

	Translator = {
		Translate: function( sourceElement, targetElement ) {

			targetElement = $( targetElement );
			sourceElement = $( sourceElement );

			var queryParams = {
				source_lang: sourceElement.data( "translatable_lang" ),
				target_lang: targetElement.data( "translatable_lang" ),
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
					} else if (
						typeof tinymce !== 'undefined' &&
						tinymce.get( targetElement.attr( "id" ))
					)  {
						// TinyMCE version 4
						tinymce.get( targetElement.attr( "id" ) ).setContent( json.result );
					} else {
						targetElement.val( json.result );
					}
					return true;
				},
				error: function( xhr, statusTxt, errorTxt ) {
					window.alert( "An issue occurred when using the translator API. Please try it later.\n\n" +
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
			var linkTitle = currentLang === "cs" ?
				"Přeložit překladačem z " + sourceLang :
				"Translate from " + sourceLang + " using the translator";
			this.each( function() {
				var link = $( "<a>", { href: "#", text: linkText, tabindex: "-1000" } )
					.addClass( "btn btn-info btn-sm btn-xs" )
					.addClass( "pull-right" ) // Bootstrap3
					.addClass( "float-right" ) // Bootstrap4
					.attr( "role", "button" )
					.attr( "title", linkTitle );

				var selfId = $( this ).attr( "id" );
				selfId = selfId.substr( 0, selfId.lastIndexOf( "_" ) );
				var sourceInputId = selfId + "_" + sourceLang;

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
		},

		appendSourceLangBadge: function() {
			var badgeText = currentLang === "cs" ? "Zdroj" : "Source";
			var badgeTitle = currentLang === "cs" ?
				"Zdrojový text pro překlad překladačem" :
				"Source text for translation using the translator";
			this.each( function() {
				var badge = $( "<span>", { text: badgeText, tabindex: "-1000" } )
					.addClass( "badge badge-light" )
					.addClass( "pull-right" ) // Bootstrap3
					.addClass( "float-right" ) // Bootstrap4
					.attr( "title", badgeTitle );

				var parentFormGroup = $( this ).closest( ".form-group" );

				// If .help-block element doesn`t exist in .form-group just create it.
				if ( parentFormGroup.find( ".help-block" ).length < 1 ) {
					var helpBlock = $( "<div>", { text: "" } );
					helpBlock.addClass( "help-block" );
					helpBlock.addClass( "form-text" );
					parentFormGroup.append( helpBlock );
				}

				parentFormGroup.find( ".help-block" ).prepend( badge );
			} );
		}
	} );

	// @NOTE: Change data-translatable to 'yes' to turn on translator initialization,
	// 'no' turns it off.
	$( "[data-translatable='yes']" ).each( function( idx, el ) {
		var input = $( el );

		var targetLang = input.data( "translatable_lang" );
		if ( targetLang === sourceLang ) {
			input.appendSourceLangBadge();
			return;
		}
		input.appendTranslateButton();
	} );

} )( this );
