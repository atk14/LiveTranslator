<?php
class TranslateForm extends ApiForm {
	function set_up() {
		global $ATK14_GLOBAL;

		$this->add_field("q", new TextField(array(
			"help_text" => _("Text k překladu"),
		)));

		foreach($ATK14_GLOBAL->getSupportedLangs() as $l) {
			$source_langs[$l] = $target_langs[$l] = $l;
		}
		$source_langs["auto"] = _("Rozpoznat jazyk");

		$this->add_field("source_lang", new ChoiceField(array(
			"choices" => $source_langs,
			"initial" => $ATK14_GLOBAL->getDefaultLang(),
			"help_text" => _("Výchozí Jazyk"),
		)));

		$target_langs[""] = _("Zvol cílový jazyk");
		$this->add_field("target_lang", new ChoiceField(array(
			"choices" => $target_langs,
			"initial" => "",
			"help_text" => _("Výchozí Jazyk"),
		)));
	}

	function clean() {
		list($err, $values) = parent::clean();
		if ($values["source_lang"] == $values["target_lang"]) {
			$this->set_error("target_lang", _("V target_lang zvolte jinou hodnotu než v source_lang"));
		}
		return array($err,$values);
	}
}
