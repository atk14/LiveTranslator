<?php
/**
 * Test pouziti Google Translatoru.
 * URL translate.googleapis.com/translate_a/single lze pouzit i bez tokenu, ale pri pouziti na webfaction.com google vyzaduje token,
 * bez toho vyzaduje vyplneni captcha kodu.
 *
 * Dalsi moznost - knihovna Stichoza\GoogleTranslate si generuje token vlastnim zpusobem. Do budoucna, Google muze delat problemy.
 */

use Stichoza\GoogleTranslate\TranslateClient;

class LiveTranslatorController extends ApiController {

	function translate() {
		if ($this->request->post() && ($d=$this->form->validate($this->params))) {

			if(trim($d["q"])===""){
				$this->api_data = array("result" => "");
				return;
			}

			$res = [];
			$tr = new TranslateClient( $d["source_lang"], $d["target_lang"], [
				# pomoci on_stats ziskame neco o requestu do translate.google.com
				"on_stats" => function (GuzzleHttp\TransferStats $stats) use (&$res) {
					$res["url"] = $stats->getEffectiveUri()."\n";
					if ($stats->hasResponse()) {
						$res["status_code"] = $stats->getResponse()->getStatusCode();
					}
				}
			]);
			try {
				$translation = $tr->translate($d["q"]);
				$this->api_data = array("result" => $translation);
			} catch(Exception $e) {
				$this->_report_fail(array("error" => _("Translator error"), "exception" => get_class($e), "message" => $e->getMessage()) + $res, 400);
			}

		}
	}

	function _before_filter() {
		if (! ($this->logged_user && $this->logged_user->isAdmin()) ) {
			return $this->_execute_action("error403");
		}
	}
}
