<?php
namespace LiveTranslator;

class BeforeFilter {

	static function Filter($source_text,&$back_replaces = [],$options = []){
		$options += [
			"uniqid" => "c".md5(uniqid()),
		];

		$back_replaces = [];

		$uniqid = $options["uniqid"];
		$counter = 1;

		$well_known_markdown_shortcodes = ["div","row","col"];
		$well_known_markdown_shortcodes = "(".join("|",$well_known_markdown_shortcodes).")";
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

		return $source_text;
	}
}
