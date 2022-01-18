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
		}
		return $out;	
	}
}