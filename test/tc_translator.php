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

		$translator = new LiveTranslator\Translator("cs","sr");
		$result = $translator->translate('<a href="https://testovani.com/">Testování</a> je tak <em>krásné</em>',$data);
		$this->assertEquals('<a href="https://testovani.com/">Testiranje</a> je tako <em>lepo</em>',$result);
		$this->assertEquals(3,$data["api_calls"]);

		$translator = new LiveTranslator\Translator("cs","en");
		$result = $translator->translate('[row class="nice-row"][col] Testování je tak krásné [/col][/row]',$data);
		$this->assertEquals('[row class="nice-row"][col] Testing is so beautiful [/col][/row]',$result);
		$this->assertEquals(1,$data["api_calls"]);
	}
}
