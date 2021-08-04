LiveTranslator
==============

Installation
------------

    cd path/to/your/project/
    composer require atk14/live-translator

    ln -s ../../../vendor/atk14/live-translator/src/app/forms/api/live_translator app/forms/api/
    ln -s ../../../vendor/atk14/live-translator/src/app/controllers/api/live_translator_controller.php app/controllers/api/
    ln -s ../../../vendor/atk14/live-translator/src/public/scripts/utils/live_translator.js public/scripts/utils/

Edit gulpfile-admin.js and add live_translator.js to vendorScripts

    var vendorScripts = [
      ...
      "public/scripts/utils/live_translator.js"
    ];

TODO: Describe changes in app/forms/admin/admin_form.php

Configuration
-------------

By default LiveTranslator is using Google API. If translation using DeepL.com is required, the following two constants must be defined:

    define("LIVE_TRANSLATOR_DEEPL_API_AUTH_KEY","34567012-9a78-debc-f012-789abc456de0:fx");
    define("LIVE_TRANSLATOR_DEEPL_API_PRO",false); // true or false

[//]: # ( vim: set ts=2 et: )

