<?php
namespace LiveTranslator;

class AfterFilter {

	static protected $serbianCyrillicLetters = [ "љ",  "њ", "е",  "р", "т",  "з",  "у", "и",
		"о", "п", "ш", "ђ",  "ж",  "а",  "с", "д",  "ф", "г",  "х",  "ј", "к",
		"л", "ч", "ћ", "џ",  "ц",  "в",  "б", "н",  "м", "Љ",  "Њ",  "Е", "Р",
		"Т", "З", "У", "И",  "О",  "П",  "Ш", "Ђ",  "Ж", "А",  "С",  "Д", "Ф",
		"Г", "Х", "Ј", "К",  "Л",  "Ч",  "Ћ", "Џ",  "Ц", "В",  "Б",  "Н", "М" ];

	static protected $serbianLatinLetters = [ "lj", "nj", "e", "r",  "t", "z",  "u",  "i",
		"o", "p", "š", "đ",  "ž",  "a",  "s", "d",  "f", "g",  "h",  "j", "k",
		"l", "č", "ć", "dž", "c",  "v",  "b", "n",  "m", "Lj", "Nj", "E", "R",
		"T", "Z", "U", "I",  "O",  "P",  "Š", "Đ",  "Ž", "A",  "S",  "D", "F",
		"G", "H", "J", "K",  "L",  "Č",  "Đ", "Dž", "C", "V",  "B",  "N", "M" ];

	static function Filter($live_translator,$source_text,$translation) {
		$options = $live_translator->getOptions();
		$target_lang = $live_translator->getTargetLang();

		$out = $translation;

		if(preg_match('/&nbsp;/',$source_text)){
			$out = preg_replace('/ ?& ?nbsp; ?/','&nbsp;',$out);
		}

		foreach(["1","2","3","4","5","6","7","8","9","s","d"] as $k){
			if(preg_match("/%$k/",$source_text)){
				$out = preg_replace("/% ?$k/"," %$k",$out);
				$out = preg_replace("/  %$k/"," %$k",$out);
			}

			// <em> %1</em> -> <em>%1</em>
			if(preg_match("/>%$k</",$source_text)){
				$out = preg_replace("/> ?%$k ?</",">%$k<",$out);
			}

			// <a href=" %s"> -> <a href="%s">
			if(preg_match("/\"%$k\"/",$source_text)){
				$out = preg_replace("/\" ?%$k ?\"/","\"%$k\"",$out);
			}

			// Записите ( %s) са успешно изтрити -> Записите (%s) са успешно изтрити
			if(!preg_match("/\( %$k/",$source_text)){
				$out = preg_replace("/\( %$k/","(%$k",$out);
			}
		}

		if(!preg_match('/^\s/s',$source_text)){
			$out = ltrim($out);
		}

		if(!preg_match('/\s$/s',$source_text)){
			$out = rtrim($out);
		}

		// < em>Hello</ em> -> <em>Hello</ em>
		if(!preg_match('/<\/?\s+/',$source_text)){
			$out = preg_replace('/<(\/?)\s+/','<\1',$out);
		}

		// Iobject: [# 16 Слика: Обавићемо царињење за вас] -> [#16 Слика: Обавићемо царињење за вас]
		if(!preg_match('/\[ ?# +\d+/',$source_text)){
			$out = preg_replace('/\[ ?# +(\d+)/','[#\1',$out);
		}

		// [Duck Duck Go] (https://duckduckgo.com) -> [Duck Duck Go](https://duckduckgo.com)
		if(!preg_match('/\] \(/',$source_text)){
			$out = preg_replace('/\] \(/','](',$out);
		}

		// [Duck Duck Go](https://duckduckgo.com) {.blank} -> [Duck Duck Go](https://duckduckgo.com){.blank}
		if(!preg_match('/\) \{/',$source_text)){
			$out = preg_replace('/\) \{/','){',$out);
		}

		if($target_lang=="sr" && $options["latinize_serbian"]){
			$out = strtr($out,array_combine(self::$serbianCyrillicLetters,self::$serbianLatinLetters));
		}

		return $out;	
	}
}
