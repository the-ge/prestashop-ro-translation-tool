Prestashop Romanian Translation Tool
====================================

PHP script to batch process Romanian translation:
1. convert the the ăâîșțĂÂÎȘȚ diacritics to plain aaistAAIST
2. comment empty translations to fix the lack of empty string check in PrestaShop translation code

USAGE

Copy prestashop_romanian_tranlsation_tool.php in your Prestashop root directory, then run it.

Right now it saves the old translations in /translations/export/ro_yymmdd-hhmmss/. The old translations can be then restored by copying them over the prestashop root directory.


WARNING

It was only tested on my local machine. Make backups and be safe. Ultimately, if anything goes wrong, it is your site and your responsability - I'll try to help, but I'll not accept any responsability.


HISTORY

2014-06-23 First version tested on a 1.6.0.6 install
2016-11-11 Added commenting of empty translations
