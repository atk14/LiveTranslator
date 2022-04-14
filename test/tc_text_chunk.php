<?php
class TcTextChunk extends TcBase {

	function test(){
		$chunk = new LiveTranslator\TextChunk("Hello");
		$this->assertEquals(true,$chunk->isTranslatable());

		$chunk = new LiveTranslator\TextChunk("World",false);
		$this->assertEquals(false,$chunk->isTranslatable());

		$chunk = new LiveTranslator\TextChunk("\n\n");
		$this->assertEquals(false,$chunk->isTranslatable());
	}
}
