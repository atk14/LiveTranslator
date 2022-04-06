<?php
class TcAfterFilter extends TcBase {

	function test(){
		$src = 'Foo&nbsp;Bar';
		$trn = 'Foo & nbsp; Bar';
		$exp = 'Foo&nbsp;Bar';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($src,$trn));

		$src = 'cena %s Kč';
		$trn = 'price% s CZK';
		$exp = 'price %s CZK';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($src,$trn));

		$src = 'Nakupte ještě za %1 a dostanete dopravu zdarma.';
		$trn = 'Buy for% 1 and get free shipping.';
		$exp = 'Buy for %1 and get free shipping.';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($src,$trn));

		$src = 'Nemáte oprávnění pro přístup k <em>%1</em> na tomto serveru.';
		$trn = 'You don`t have a permission to access <em> %1</em> on this server.';
		$exp = 'You don`t have a permission to access <em>%1</em> on this server.';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($src,$trn));

		$src = 'Klikněte <a href="%s">zde</a>';
		$trn = 'Click <a href=" %s">here</a>';
		$exp = 'Click <a href="%s">here</a>';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($src,$trn));

		$src = '%1 Support Team';
		$trn = ' %1 Екип за поддръжка';
		$exp = '%1 Екип за поддръжка';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($src,$trn));

		$src = 'Team %1';
		$trn = 'Tým %1 ';
		$exp = 'Tým %1';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($src,$trn));

		$src = 'Záznamy (%s) byly úspěšně smazány';
		$trn = 'Записите ( %s) са успешно изтрити';
		$exp = 'Записите (%s) са успешно изтрити';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($src,$trn));

		$src = 'You are signed in as <em>%1</em>.';
		$trn = 'Prijavljeni ste kao < em>%1</ em>.';
		$exp = 'Prijavljeni ste kao <em>%1</em>.';
		$this->assertEquals($exp,LiveTranslator\AfterFilter::Filter($src,$trn));
	}
}
