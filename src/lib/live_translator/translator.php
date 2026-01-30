<?php
namespace LiveTranslator;

require_once(__DIR__ . "/constants.php");

class Translator {

	protected $source_lang;
	protected $target_lang;
	protected $options;

	function __construct($source_lang,$target_lang,$options = []){
		$options += [
			"latinize_serbian" => true,
		];

		$this->source_lang = $source_lang;
		$this->target_lang = $target_lang;
		$this->options = $options;
	}

	function getSourceLang(){
		return $this->source_lang;
	}

	function getTargetLang(){
		return $this->target_lang;
	}

	function getOptions(){
		return $this->options;
	}

	function translate($text,&$translation_data = []){
		$translation_data = [];

		// see https://www.deepl.com/docs-api/other-functions/listing-supported-languages/
		$deepl_supported_langs = [
			"BG", // Bulgarian
			"CS", // Czech
			"DA", // Danish
			"DE", // German
			"EL", // Greek
			"EN-GB", // English (British)
			"EN-US", // English (American)
			"ES", // Spanish
			"ET", // Estonian
			"FI", // Finnish
			"FR", // French
			"HU", // Hungarian
			"IT", // Italian
			"JA", // Japanese
			"LT", // Lithuanian
			"LV", // Latvian
			"NL", // Dutch
			"PL", // Polish
			"PT-BR", // Portuguese (Brazilian)
			"PT-PT", // Portuguese (European)
			"RO", // Romanian
			"RU", // Russian
			"SK", // Slovak
			"SL", // Slovenian
			"SV", // Swedish
			"ZH", // Chinese
		];

		$deepl_supported_langs[] = "EN";
		$deepl_supported_langs[] = "PT";

		$provider = "google";
		if(
			LIVE_TRANSLATOR_DEEPL_API_AUTH_KEY &&
			in_array(strtoupper($this->source_lang),$deepl_supported_langs) &&
			in_array(strtoupper($this->target_lang),$deepl_supported_langs)
		){
			$provider = "deepl";
		}

		$trunk = \LiveTranslator\BeforeFilter::Filter($this,$text,$back_replaces,["provider" => $provider]);

		$out = [];

		$sw = new \StopWatch();

		$api_calls_counter = 0;

		foreach($trunk->getChunks() as $chunk){

			if(!$chunk->isTranslatable()){
				$out[] = $chunk->toString();
				continue;
			}

			if($api_calls_counter>0){
				usleep(100000); // 0.1 sec
			}

			$sw->start();
			if($provider == "deepl"){
				$result = $this->_translate_using_deepl($chunk->toString());
			}else{
				$result = $this->_translate_using_google($chunk->toString());
			}
			$sw->stop();

			$api_calls_counter++;

			$result = \LiveTranslator\AfterFilter::Filter($this,$text,$result);

			$out[] = $result;
		}

		$translation_data["provider"] = $api_calls_counter>0 ? $provider : "none";
		$translation_data["api_calls"] = $api_calls_counter;
		$translation_data["duration"] = round($sw->getResult(),3);

		$out = join("",$out);

		if($back_replaces){
			$out = strtr($out,$back_replaces);
		}

		return $out;
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
		$adf = new \ApiDataFetcher("https://api".(LIVE_TRANSLATOR_DEEPL_API_PRO ? "" : "-free").".deepl.com/v2/",[
			"lang" => "",
			"automatically_add_trailing_slash" => false,
			"additional_headers" => [
				"Authorization: DeepL-Auth-Key ".LIVE_TRANSLATOR_DEEPL_API_AUTH_KEY,
			],
			"get_content_callback" => function($url_fetcher){
				// Filtering this:
				//
				// 55
				//	{"translations":[{"detected_source_language":"CS","text":"Testing is so beautiful"}]}
				// 0
				//
				// into this:
				//
				// {"translations":[{"detected_source_language":"CS","text":"Testing is so beautiful"}]}
				$content = trim($url_fetcher->getContent());
				$content = preg_replace('/^[0-9A-F]+\s*{/s','{',$content);
				$content = preg_replace('/}\s*[0-9A-F]+/s','}',$content);
				return $content;
			}
		]);

		$data = $adf->post("translate",[
			"text" => $text,
			"target_lang" => strtoupper($this->target_lang),
			"source_lang" => strtoupper($this->source_lang),
		]);
		return $data["translations"][0]["text"];
	}
}
