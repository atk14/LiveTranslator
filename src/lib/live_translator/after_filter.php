<?php
namespace LiveTranslator;

class AfterFilter {

	static function Filter($source_text,$translation) {
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

		return $out;	
	}
}
