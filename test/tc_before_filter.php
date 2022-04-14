<?php
class TcBeforeFilter extends TcBase {

	function test(){
		$lt = new LiveTranslator\Translator("en","cs");

		$trunk = LiveTranslator\BeforeFilter::Filter($lt,'Hello!',$back_replaces);
		$this->assertEquals('Hello!',$trunk->toString());
		$this->assertEquals([],$back_replaces);

		// &nbsp; is removed when using Google Translation Api
		$this->assertEquals('Papa Smurf',LiveTranslator\BeforeFilter::Filter($lt,'Papa&nbsp;Smurf',$back_replaces));
		$this->assertEquals('Papa Smurf',LiveTranslator\BeforeFilter::Filter($lt,'Papa&nbsp;Smurf',$back_replaces,["provider" => "google"]));
		$this->assertEquals('Papa&nbsp;Smurf',LiveTranslator\BeforeFilter::Filter($lt,'Papa&nbsp;Smurf',$back_replaces,["provider" => "deepl"]));

		$src = '[row][col][div class="h1"] Hello! [/div][/col][/row]';
		$expected = '<div class="c1"></div><div class="c2"></div><div class="c3"></div> Hello! <div class="c4"></div><div class="c5"></div><div class="c6"></div>';
		$this->assertEquals($expected,LiveTranslator\BeforeFilter::Filter($lt,$src,$back_replaces,["uniqid" => "c","provider" => "google"]));
		$this->assertEquals($expected,LiveTranslator\BeforeFilter::Filter($lt,$src,$back_replaces,["uniqid" => "c","provider" => "deepl"]));
		$this->assertEquals([
			'<div class="c1"></div>' => '[row]',
			'<div class="c2"></div>' => '[col]',
			'<div class="c3"></div>' => '[div class="h1"]',
			'<div class="c4"></div>' => '[/div]',
			'<div class="c5"></div>' => '[/col]',
			'<div class="c6"></div>' => '[/row]',
		],$back_replaces);
	}
}
