<?php
class TcTranslator extends TcBase {

	function test(){
		$translator = new LiveTranslator\Translator("cs","en");
		
		$result = $translator->translate(" ",$data);
		$this->assertEquals(" ",$result);
		$this->assertEquals("none",$data["provider"]);
		$this->assertEquals(0,$data["api_calls"]);
		$this->assertEquals(0.0,$data["duration"]);

		$result = $translator->translate("Testování je tak krásné.",$data);
		$this->assertEquals("Testing is so wonderful.",$result);
		$this->assertEquals("deepl",$data["provider"]);
		$this->assertTrue(is_float($data["duration"]));
		$this->assertTrue($data["duration"]>0.0);

		$translator = new LiveTranslator\Translator("cs","sk");
		$result = $translator->translate("Testování je skutečně krásné",$data);
		$this->assertEquals("Testovanie je naozaj krásne",$result);

		$translator = new LiveTranslator\Translator("cs","sr");
		$result = $translator->translate('<a href="https://testovani.com/">Testování</a> je tak <em>krásné</em>',$data);
		$this->assertEquals('<a href="https://testovani.com/">Testiranje</a> tako je <em>prelepo</em>',$result);
		$this->assertEquals("google",$data["provider"]);
		$this->assertEquals(3,$data["api_calls"]);

		$translator = new LiveTranslator\Translator("cs","sr");
		$result = $translator->translate('<a href="https://testovani.com/"> <i class="fa fa-globe" aria-hidden="true"></i> </a>',$data);
		$this->assertEquals('<a href="https://testovani.com/"> <i class="fa fa-globe" aria-hidden="true"></i> </a>',$result);
		$this->assertEquals("none",$data["provider"]);
		$this->assertEquals(0,$data["api_calls"]);

		$translator = new LiveTranslator\Translator("cs","en");
		$result = $translator->translate('[row class="nice-row"][col] Testování je tak krásné. [/col][/row]',$data);
		$this->assertEquals('[row class="nice-row"][col] Testing is so wonderful. [/col][/row]',$result);
		$this->assertEquals("deepl",$data["provider"]);
		$this->assertEquals(1,$data["api_calls"]);
	}

	function test_url(){
		$translator = new LiveTranslator\Translator("cs","en");

		$result = $translator->translate("http://www.example.com",$data);
		$this->assertEquals("http://www.example.com",$result);

		$result = $translator->translate("http://www.example.com/?f_c%5B%5D=18&f_we%5Bmin%5D=&f_we%5Bmax%5D=&f_w%5Bmin%5D=&f_w%5Bmax%5D=&f_bp=",$data);
		$this->assertEquals("http://www.example.com/?f_c%5B%5D=18&f_we%5Bmin%5D=&f_we%5Bmax%5D=&f_w%5Bmin%5D=&f_w%5Bmax%5D=&f_bp=",$result);

		// a URL surrounded by translated text must survive intact
		$result = $translator->translate("Více informací najdete na http://www.example.com/?f_c%5B%5D=18&f_we%5Bmin%5D=&f_bp= a nezapomeňte si to uložit.",$data);
		$this->assertEquals("For more information, visit http://www.example.com/?f_c%5B%5D=18&f_we%5Bmin%5D=&f_bp=, and be sure to bookmark it.",$result);

		// multiple URLs in the same text must each survive intact
		$result = $translator->translate("Odkazy: http://foo.com/x?y=1 a http://bar.com/z?w=2 jsou platné.",$data);
		$this->assertEquals("Links: http://foo.com/x?y=1 and http://bar.com/z?w=2 are valid.",$result);
	}
}
