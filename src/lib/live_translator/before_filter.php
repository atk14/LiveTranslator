<?php
namespace LiveTranslator;

class BeforeFilter {

	static function Filter($live_translator,$source_text,&$back_replaces = [],$options = []){
		$options += [
			"provider" => "google", // "google", "deepl"
			"uniqid" => "c".md5(uniqid()),
		];

		$back_replaces = [];
		$uniqid = $options["uniqid"];
			
		// The following replace markdown shortcuts [row][/row], [col][/col] and [div][/div]
		// to HTML tags <div class="...."></div>
		$well_known_markdown_shortcodes = ["div","span","row","col"];
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

		if($options["provider"] === "google"){
			$source_text = str_replace("&nbsp;"," ",$source_text);
		}

		$out = new TextTrunk();

		if($options["provider"] === "google"){
			// While Deepl.com leaves HTML tags intact, Google Translator changes them.
			// For Google we need to split source_text by HTML tags.
			while(preg_match('/^([^<]*)(<[^>]*>)([^<]*)/s',$source_text,$matches)){
				self::_AppendChunk($out,$matches[1]);
				$out->append(new TextChunk($matches[2],false)); // HTML tag
				self::_AppendChunk($out,$matches[3]);

				$source_text = substr($source_text,strlen($matches[0]));
			}
		}

		if(strlen($source_text)){
			$out->append(new TextChunk($source_text,true));
		}

		return $out;
	}

	static protected function _AppendChunk(&$trunk,$text){
		preg_match('/^(\s*)(.*?)(\s*)$/s',$text,$matches);
		foreach([1,2,3] as $i){
			strlen($matches[$i])>0 && $trunk->append($matches[$i]);
		}
	}
}
