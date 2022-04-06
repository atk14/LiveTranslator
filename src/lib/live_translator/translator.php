<?php
namespace LiveTranslator;

require_once(__DIR__ . "/constants.php");

class Translator {

	protected $source_lang;
	protected $target_lang;

	function __construct($source_lang,$target_lang){
		$this->source_lang = $source_lang;
		$this->target_lang = $target_lang;
	}

	function translate($text,&$translation_data = []){
		$translation_data = [];

		$provider = LIVE_TRANSLATOR_DEEPL_API_AUTH_KEY ? "deepl" : "google";

		$text = \LiveTranslator\BeforeFilter::Filter($text,$back_replaces,["provider" => $provider]);

		if(trim($text)==""){
			$translation_data["provider"] = "none";
			$translation_data["duration"] = 0.0;
			return "";
		}

		$sw = new \StopWatch();
		$sw->start();

		if(LIVE_TRANSLATOR_DEEPL_API_AUTH_KEY){
			$result = $this->_translate_using_deepl($text);
			$provider = "deepl";
		}else{
			$result = $this->_translate_using_google($text);
			$provider = "google";
		}

		$sw->stop();

		$translation_data["provider"] = $provider;
		$translation_data["duration"] = round($sw->getResult(),3);

		$result = \LiveTranslator\AfterFilter::Filter($text,$result);

		if($back_replaces){
			$result = strtr($result,$back_replaces);
		}

		return $result;
	}

	protected function _translate_using_google($text){
		$res = [];
		$tr = new \Stichoza\GoogleTranslate\TranslateClient( $this->source_lang, $this->target_lang, [
			# pomoci on_stats ziskame neco o requestu do translate.google.com
			"on_stats" => function (\GuzzleHttp\TransferStats $stats) use (&$res) {
				$res["url"] = $stats->getEffectiveUri()."\n";
				if ($stats->hasResponse()) {
					$res["status_code"] = $stats->getResponse()->getStatusCode();
				}
			}
		]);
		return $tr->translate($text);
	}

	protected function _translate_using_deepl($text){
		$adf = new \ApiDataFetcher("https://api".(LIVE_TRANSLATOR_DEEPL_API_PRO ? "" : "-free").".deepl.com/v2/",["lang" => "", "automatically_add_trailing_slash" => false]);

		$data = $adf->post("translate",[
			"text" => $text,
			"target_lang" => strtoupper($this->target_lang),
			"source_lang" => strtoupper($this->source_lang),
			"auth_key" => LIVE_TRANSLATOR_DEEPL_API_AUTH_KEY,
		]);
		return $data["translations"][0]["text"];
	}
}
