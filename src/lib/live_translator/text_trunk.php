<?php
namespace LiveTranslator;

class TextTrunk {

	protected $chunks = [];

	function getChunks(){
		return $this->chunks;
	}

	function append($chunk){
		if(is_string($chunk)){
			$chunk = new TextChunk($chunk);
		}
		$this->chunks[] = $chunk;
	}

	function toString(){
		return join("",$this->chunks);
	}

	function __toString(){
		return $this->toString();
	}
}
