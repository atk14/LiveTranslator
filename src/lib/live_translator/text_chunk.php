<?php
namespace LiveTranslator;

class TextChunk {

	protected $text;
	protected $is_translatable;

	function __construct($text,$is_translatable = true){
		$this->text = (string)$text;
		$this->is_translatable = (bool)$is_translatable;
	}

	function isTranslatable(){
		if(trim($this->text)===""){
			return false;
		}
		return $this->is_translatable;
	}

	function toString(){
		return $this->text;
	}

	function __toString(){
		return $this->toString();
	}
}
