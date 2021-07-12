<?php
/**
 * Test pouziti Google Translatoru.
 * URL translate.googleapis.com/translate_a/single lze pouzit i bez tokenu, ale pri pouziti na webfaction.com google vyzaduje token,
 * bez toho vyzaduje vyplneni captcha kodu.
 *
 * Dalsi moznost - knihovna Stichoza\GoogleTranslate si generuje token vlastnim zpusobem. Do budoucna, Google muze delat problemy.
 */

definedef("LIVE_TRANSLATOR_DEEPL_API_AUTH_KEY","");
definedef("LIVE_TRANSLATOR_DEEPL_API_PRO",true);

use Stichoza\GoogleTranslate\TranslateClient;

class LiveTranslatorController extends ApiController {

	function translate() {
		if ($this->request->post() && ($d=$this->form->validate($this->params))) {

			if(trim($d["q"])===""){
				$this->api_data = array("result" => "");
				return;
			}

			try{
				$sw = new StopWatch();
				$sw->start();
				if(LIVE_TRANSLATOR_DEEPL_API_AUTH_KEY){
					$result = $this->_translate_using_deepl($d);
					$provider = "deepl";
				}else{
					$result = $this->_translate_using_google($d);
					$provider = "google";
				}
				$sw->stop();
				$this->api_data = array("result" => $result, "provider" => $provider, "duration" => round($sw->getResult(),3));
			}catch(Exception $e){
				$this->_report_fail(array("error" => _("Translator error"), "exception" => get_class($e), "message" => $e->getMessage()), 400);
			}

		}
	}

	function _translate_using_google($d){
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
		return $tr->translate($d["q"]);
	}

	function _translate_using_deepl($d){
		$adf = new ApiDataFetcher("https://api".(LIVE_TRANSLATOR_DEEPL_API_PRO ? "" : "-free").".deepl.com/v2/",["lang" => "", "automatically_add_trailing_slash" => false]);

		$data = $adf->post("translate",[
			"text" => $d["q"],
			"target_lang" => strtoupper($d["target_lang"]),
			"source_lang" => strtoupper($d["source_lang"]),
			"auth_key" => LIVE_TRANSLATOR_DEEPL_API_AUTH_KEY,
		]);
		return $data["translations"][0]["text"];
	}

	function _before_filter() {
		if (! ($this->logged_user && $this->logged_user->isAdmin()) ) {
			return $this->_execute_action("error403");
		}
	}
}
