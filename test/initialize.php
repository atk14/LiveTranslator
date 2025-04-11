<?php
define("TEST",true);
define("LIVE_TRANSLATOR_DEEPL_API_AUTH_KEY","3e6dafdd-a341-4a4f-e6be-f50ee1331a4b:fx");
define("LIVE_TRANSLATOR_DEEPL_API_PRO",false); // true or false

$HTTP_REQUEST = new HTTPRequest();
$HTTP_RESPONSE = new HTTPResponse();

require(__DIR__ . "/../vendor/autoload.php");
