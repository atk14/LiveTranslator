<?php
class TcTextTrunk extends TcBase {

	function test(){
		$trunk = new LiveTranslator\TextTrunk();
		$this->assertEquals("",$trunk->toString());
		
		$trunk->append(new LiveTranslator\TextChunk("Hello"));
		$this->assertEquals("Hello",$trunk->toString());

		$trunk->append(new LiveTranslator\TextChunk(""));
		$this->assertEquals("Hello",$trunk->toString());

		$trunk->append(new LiveTranslator\TextChunk(" World!"));
		$this->assertEquals("Hello World!",$trunk->toString());
	}
}
