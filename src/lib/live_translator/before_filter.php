<?php
namespace LiveTranslator;

class BeforeFilter {

	static function Filter($source_text,&$back_replaces = [],$options = []){
		$options += [
			"provider" => "google", // "google", "deepl"
			"uniqid" => "c".md5(uniqid()),
		];

		$back_replaces = [];
		$uniqid = $options["uniqid"];
			
		if($options["provider"] === "deepl"){
			// The following replace markdown shortcuts [row][/row], [col][/col] and [div][/div]
			// to HTML tags <div class="...."></div>
			// While Deepl.com leaves HTML tags intact, Google Translator changes them.
			// So it's not suitable for Google Translator!
			$well_known_markdown_shortcodes = ["div","row","col"];
			$well_known_markdown_shortcodes = "(".join("|",$well_known_markdown_shortcodes).")";
			$counter = 1;
			$source_text = preg_replace_callback(
				'/(\['.$well_known_markdown_shortcodes.'\b[^\]]*\]|\[\/'.$well_known_markdown_shortcodes.'\])/',
				function($matches) use($uniqid,&$counter,&$back_replaces){
					$replace = '<div class="'.$uniqid.$counter.'"></div>';
					//$replace = "<$uniqid$counter>";
					$back_replaces[$replace] = $matches[0];
					$counter++;
					return $replace;
				},
				$source_text
			);
		}

		if($options["provider"] === "google"){
			$source_text = str_replace("&nbsp;"," ",$source_text);
		}

		return $source_text;
	}
}
