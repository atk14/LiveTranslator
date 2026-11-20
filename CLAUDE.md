# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

LiveTranslator is a PHP library/plugin for the ATK14 framework. It provides an API endpoint and a jQuery widget that let logged-in admin users translate form field text (e.g. CMS content) on the fly using Google Translate or DeepL. It's installed into a host ATK14 project via Composer and symlinked into that project's `app/` directory (see README.md for the install steps) — this repo itself is not a runnable app.

## Commands

Install dependencies:

    composer update

Run the full test suite (uses `atk14/tester`, invoked from inside `test/`):

    cd test && ../vendor/bin/run_unit_tests

Run a single test file:

    cd test && ../vendor/bin/run_unit_tests tc_translator.php

Tests make live calls to Google Translate and the DeepL API (`test/initialize.php` defines a DeepL sandbox key), so running the suite requires network access and expects specific translations back from the real services — flaky/changed translations can break assertions that aren't really "bugs" in this code.

CI (`.github/workflows/tests.yml`) runs this same test command across PHP 5.6 through 8.5, so avoid syntax/features that break on PHP 5.6 unless the composer constraint is deliberately changed.

## Architecture

Request flow: `src/app/controllers/api/live_translator_controller.php` (`LiveTranslatorController::translate()`) validates input via `src/app/forms/api/live_translator/translate_form.php`, then delegates to `LiveTranslator\Translator` in `src/lib/live_translator/`. The controller requires a logged-in admin (`_logged_admin_required()` / `_before_filter()`); everything else is framework glue from ATK14 (`ApiController`, `ApiForm`, `_()`gettext calls, `$ATK14_GLOBAL`) that isn't part of this repo.

Core translation pipeline lives in `src/lib/live_translator/` and runs in three stages, all orchestrated by `Translator::translate()`:

1. **BeforeFilter** (`before_filter.php`) — prepares the source text before it's sent to a translation provider:
   - Replaces markdown shortcodes (`[div]`, `[span]`, `[row]`, `[col]` and their closing tags) with placeholder `<div class="UNIQID_N">` tags, recording the original shortcode in `$back_replaces` so it can be restored after translation.
   - For Google (which mangles HTML), splits the text into a `TextTrunk` of `TextChunk`s so that HTML tags are kept untranslated and only the text between them is sent to the API. DeepL preserves HTML natively, so no splitting is needed for it.
   - Strips `&nbsp;` for Google (Google chokes on it); DeepL keeps it.

2. **Translator::translate()** picks the provider per-call: DeepL is used only if `LIVE_TRANSLATOR_DEEPL_API_AUTH_KEY` is configured *and* both source and target languages are in DeepL's supported-language list; otherwise it falls back to Google (`stichoza/google-translate-php`). It iterates the `TextTrunk` chunks, skips untranslatable chunks (pure whitespace or raw HTML tags) verbatim, calls the provider for translatable chunks (throttled with a 0.1s `usleep` between calls), and tracks timing via `atk14/stop-watch` and an API-call counter. Results are reported back to the caller via `$translation_data` (`provider`, `api_calls`, `duration`).

3. **AfterFilter** (`after_filter.php`) — post-processes each translated chunk to fix up artifacts introduced by translation APIs: stray spaces around `%1`-style placeholders, `&nbsp;` spacing, broken markdown link syntax (`] (` → `](`), leading/trailing whitespace that shouldn't have been added, and `< em>`-style broken tags. Also latinizes Serbian Cyrillic output when the target language is `sr` (via `Translator`'s `latinize_serbian` option, on by default).

Finally, `Translator::translate()` rejoins the chunks and applies `$back_replaces` (via `strtr`) to restore the original markdown shortcodes.

Frontend: `src/public/scripts/utils/live_translator.js` is a jQuery plugin (`$.fn.translate`, `$.fn.appendTranslateButton`, `$.fn.appendSourceLangBadge`) that auto-activates on any element with `data-translatable="yes"`, pairing source/target language inputs by DOM id convention (`<field>_<lang>`) and POSTing to `/api/<currentLang>/live_translator/translate`. It must be added to the host project's `vendorScripts` in `gulpfile-admin.js` manually — it isn't bundled by this repo's build.

## Configuration

- `LIVE_TRANSLATOR_DEEPL_API_AUTH_KEY` / `LIVE_TRANSLATOR_DEEPL_API_PRO` — defined by the host app (defaults set in `src/lib/live_translator/constants.php` if absent). Presence of the DeepL key is what switches the provider from Google to DeepL for supported language pairs.
- Source language is auto-detected from `<html lang="...">`, overridable via `data-live_translator_source_lang` on the same element (see `live_translator.js`).
