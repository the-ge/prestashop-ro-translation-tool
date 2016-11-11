Prestashop Romanian Translation Tool
====================================

PHP script to batch process Romanian translation:

1. convert the the ăâîșțĂÂÎȘȚ diacritics to plain aaistAAIST

2. comment empty translations to fix the lack of empty string check in PrestaShop translation code

USAGE

Copy prestashop_i18n_ro_tool.php in your Prestashop root directory, then run it.

Right now it saves the old translations in /translations/export/ro_yymmdd-hhmmss/. The old translations can be then restored by copying them over the prestashop root directory.


WARNING

IT WAS ONLY TESTED ON MY LOCAL MACHINE. MAKE BACKUPS AND BE SAFE. ULTIMATELY, IF ANYTHING GOES WRONG, IT IS YOUR SITE AND YOUR RESPONSABILITY - I'LL TRY TO HELP, BUT I'LL NOT ACCEPT ANY RESPONSABILITY.

HISTORY

2014-06-23 First version tested on a 1.6.0.6 install

2016-11-11 Added commenting of empty translations
