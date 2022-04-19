<?php
class TcAfterFilter extends TcBase {

	function test(){
		$lt = new LiveTranslator\Translator("en","cs");

		$src = 'Foo&nbsp;Bar';
		$trn = 'Foo & nbsp; Bar';
		$exp = 'Foo&nbsp;Bar';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($lt,$src,$trn));

		$src = 'cena %s Kč';
		$trn = 'price% s CZK';
		$exp = 'price %s CZK';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($lt,$src,$trn));

		$src = 'Nakupte ještě za %1 a dostanete dopravu zdarma.';
		$trn = 'Buy for% 1 and get free shipping.';
		$exp = 'Buy for %1 and get free shipping.';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($lt,$src,$trn));

		$src = 'Nemáte oprávnění pro přístup k <em>%1</em> na tomto serveru.';
		$trn = 'You don`t have a permission to access <em> %1</em> on this server.';
		$exp = 'You don`t have a permission to access <em>%1</em> on this server.';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($lt,$src,$trn));

		$src = 'Klikněte <a href="%s">zde</a>';
		$trn = 'Click <a href=" %s">here</a>';
		$exp = 'Click <a href="%s">here</a>';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($lt,$src,$trn));

		$src = '%1 Support Team';
		$trn = ' %1 Екип за поддръжка';
		$exp = '%1 Екип за поддръжка';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($lt,$src,$trn));

		$src = 'Team %1';
		$trn = 'Tým %1 ';
		$exp = 'Tým %1';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($lt,$src,$trn));

		$src = 'Záznamy (%s) byly úspěšně smazány';
		$trn = 'Записите ( %s) са успешно изтрити';
		$exp = 'Записите (%s) са успешно изтрити';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($lt,$src,$trn));

		$src = 'You are signed in as <em>%1</em>.';
		$trn = 'Prijavljeni ste kao < em>%1</ em>.';
		$exp = 'Prijavljeni ste kao <em>%1</em>.';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($lt,$src,$trn));

		$src = '[#16 Picture: Me teeme teie eest tollivormistuse]';
		$trn = '[# 16 Слика: Обавићемо царињење за вас]';
		$exp = '[#16 Слика: Обавићемо царињење за вас]';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($lt,$src,$trn));

		$src = '[Duck Duck Go](https://duckduckgo.com)';
		$trn = '[Duck Duck Go] (https://duckduckgo.com)';
		$exp = '[Duck Duck Go](https://duckduckgo.com)';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($lt,$src,$trn));

		$src = '[Duck Duck Go](https://duckduckgo.com){.blank}';
		$trn = '[Duck Duck Go](https://duckduckgo.com) {.blank}';
		$exp = '[Duck Duck Go](https://duckduckgo.com){.blank}';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($lt,$src,$trn));
	}

	function test_serbian_latinization(){
		$lt = new LiveTranslator\Translator("en","sr",["latinize_serbian" => true]);
		$src = "Hello World!";
		$trn = "Здраво Свете!";
		$exp = "Zdravo Svete!";
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($lt,$src,$trn));

		$lt = new LiveTranslator\Translator("en","sr",["latinize_serbian" => false]);
		$src = "Hello World!";
		$trn = "Здраво Свете!";
		$exp = "Здраво Свете!";
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($lt,$src,$trn));

		// default is to latinize
		$lt = new LiveTranslator\Translator("en","sr");
		$src = "Hello World!";
		$trn = "Здраво Свете!";
		$exp = "Zdravo Svete!";
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($lt,$src,$trn));

		$lt = new LiveTranslator\Translator("en","bg");
		$src = "Hello World!";
		$trn = "Здраво Свете!";
		$exp = "Здраво Свете!";
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($lt,$src,$trn));
	}
}
