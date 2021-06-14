LiveTranslator
--------------

Installation
============

    cd path/to/your/project/
    composer require atk14/live-translator

    ln -s ../../../vendor/atk14/live-translator/src/app/forms/api/live_translator app/forms/api/
    ln -s ../../../vendor/atk14/live-translator/src/app/controllers/api/live_translator_controller.php app/controllers/api/
    ln -s ../../../vendor/atk14/live-translator/src/public/scripts/utils/live_translator.js public/scripts/utils/

Edit gulpfile-admin.js and add live_translator.js to applicationScripts

    var applicationScripts = [
      ...
      "public/scripts/utils/live_translator.js"
    ];

TODO: Describe changes in app/forms/admin/admin_form.php 

[//]: # ( vim: set ts=2 et: )

