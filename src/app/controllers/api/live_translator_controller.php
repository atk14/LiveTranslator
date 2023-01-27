<?php
/**
 * Test pouziti Google Translatoru.
 * URL translate.googleapis.com/translate_a/single lze pouzit i bez tokenu, ale pri pouziti na webfaction.com google vyzaduje token,
 * bez toho vyzaduje vyplneni captcha kodu.
 *
 * Dalsi moznost - knihovna Stichoza\GoogleTranslate si generuje token vlastnim zpusobem. Do budoucna, Google muze delat problemy.
 */

// definedef("LIVE_TRANSLATOR_DEEPL_API_AUTH_KEY","");
// definedef("LIVE_TRANSLATOR_DEEPL_API_PRO",true);

class LiveTranslatorController extends ApiController {

	function translate() {
		if ($this->request->post() && ($d=$this->form->validate($this->params))) {

			try{
				$translator = new LiveTranslator\Translator($d["source_lang"],$d["target_lang"]);
				$result = $translator->translate($d["q"],$translation_data);
				$this->api_data = [
					"result" => $result,
					"provider" => $translation_data["provider"],
					"api_calls" => $translation_data["api_calls"],
					"duration" => $translation_data["duration"],
				];
			}catch(Exception $e){
				$this->_report_fail(array("error" => _("Translator error"), "exception" => get_class($e), "message" => $e->getMessage()), 400);
				trigger_error(sprintf("api/live_translator/translate: Translator error (exception: %s, message: %s)",get_class($e),$e->getMessage()),E_USER_WARNING);
			}

		}
	}

	function _before_filter() {
		if (! ($this->logged_user && $this->logged_user->isAdmin()) ) {
			return $this->_execute_action("error403");
		}
	}
}
