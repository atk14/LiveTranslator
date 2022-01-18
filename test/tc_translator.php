<?php
class TcTranslator extends TcBase {

	function test(){
		$translator = new LiveTranslator\Translator("cs","en");
		
		$result = $translator->translate(" ",$data);
		$this->assertEquals("",$result);
		$this->assertEquals("none",$data["provider"]);
		$this->assertEquals(0.0,$data["duration"]);

		$result = $translator->translate("Testování je tak krásné",$data);
		$this->assertEquals("Testing is so beautiful",$result);
		$this->assertEquals("google",$data["provider"]);
		$this->assertTrue(is_float($data["duration"]));
		$this->assertTrue($data["duration"]>0.0);

		$translator = new LiveTranslator\Translator("cs","sk");
		$result = $translator->translate("Testování je tak krásné",$data);
		$this->assertEquals("Testovanie je tak krásne",$result);
	}
}
