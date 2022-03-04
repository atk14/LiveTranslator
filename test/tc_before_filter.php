<?php
class TcBeforeFilter extends TcBase {

	function test(){
		$this->assertEquals('Hello!',LiveTranslator\BeforeFilter::Filter('Hello!',$back_replaces));
		$this->assertEquals([],$back_replaces);

		$this->assertEquals('<div class="c1"></div><div class="c2"></div><div class="c3"></div> Hello! <div class="c4"></div><div class="c5"></div><div class="c6"></div>',LiveTranslator\BeforeFilter::Filter('[row][col][div class="h1"] Hello! [/div][/col][/row]',$back_replaces,["uniqid" => "c"]));
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
