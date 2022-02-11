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
	}
}
