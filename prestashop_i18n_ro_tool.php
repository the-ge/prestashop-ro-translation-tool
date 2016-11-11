<?php

// Check PHP version
$php_min_ver = '5.4';
if (version_compare(preg_replace('/[^0-9.]/', '', PHP_VERSION), $php_min_ver, '<')) {
    die('You need at least PHP '.$php_min_ver.' to run this script. Your current PHP version is '.PHP_VERSION);
}

@set_time_limit(0);
if (!@ini_get('date.timezone')) {
    @date_default_timezone_set('UTC');
}
// Some hosting still have magic_quotes_runtime configured
ini_set('magic_quotes_runtime', 0);
// Try to improve memory limit if it's under 32M
if (psinstallGetMemoryLimit() < psinstallGetOctets('64M')) {
    ini_set('memory_limit', '64M');
}

if (!defined('_PS_CORE_DIR_')) {
    define('_PS_CORE_DIR_', realpath(__DIR__));
}

require_once _PS_CORE_DIR_.'/config/config.inc.php';

$defines = [
    '__PS_BASE_URI__' => substr($_SERVER['REQUEST_URI'], 0, -1 * (strlen($_SERVER['REQUEST_URI']) - strrpos($_SERVER['REQUEST_URI'], '/')) - strlen(substr(dirname($_SERVER['REQUEST_URI']), strrpos(dirname($_SERVER['REQUEST_URI']), '/') + 1))),
    '_THEME_NAME_' => 'default-bootstrap',
];
foreach ($defines as $constant => $definition) {
    if (!defined($constant)) {
        define($constant, $definition);
    }
}

$actions = [
    'Convertește diacriticele românești' => [
        'function' => 'replaceAccentedChars',
        'header' => 'Au fost înlocuite diacriticele din:',
    ],
    //'Restaurează traducerile' => [
    //    'function' => 'restoreTranslations',
    //    'header' => '',
    //],
    'Dezactivează traducerile goale' => [
        'function' => 'commentEmptyTranslations',
        'header' => 'Au fost comentate traducerile goale din:',
    ],
];
$current_action_label = Tools::getValue('form-action');

echo '<html>
    <head>
      <meta charset="UTF-8">
      <title>Unelte pentru traducerile romanânești de PrestaShop</title>
        <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/css/bootstrap-combined.min.css" rel="stylesheet">
        <style type="text/css">
            body {padding: 20px}
            input[type=text] {width:600px;margin:0;height:28px;line-height:28px;}
        </style>
    </head>
    <body>
      <h1>Unelte pentru traducerile romanânești de PrestaShop</h1>';

if (Tools::isSubmit('form-action')) {
    $bak_dir = _PS_CORE_DIR_.'/translations/export/ro_'.date('ymd-His');
    if (is_dir($bak_dir)) {
        die('Backup directory already created');
    } else {
        if (!mkdir($bak_dir, 0755)) {
            die('Failed to create folder: '.$bak_dir);
        }
    }

    $regex = '/->l\(\'(.*[^\\\\])\'(, ?\'(.+)\')?(, ?(.+))?\)/U';
    $dirs = ['modules', 'themes', 'translations'];
    $files = [];
    $translations = [];
    $translations_source = [];

    $files = array_merge(Tools::scandir('', 'php', _PS_CORE_DIR_.'/modules', true), Tools::scandir('', 'php', _PS_CORE_DIR_.'/themes', true));
    foreach ($files as $kf => $file) {
        if (basename($file) !== 'ro.php') {
            unset($files[$kf]);
        }
    }
    $files = array_merge($files, Tools::scandir('', 'php', _PS_CORE_DIR_.'/translations/ro'));
    if (!empty($files) && isset($actions[$current_action_label])) {
        echo '<h3>'.$actions[$current_action_label]['header'].'</h3>'."\n";
        foreach ($files as $file) {
            $filename = basename($file);
            $bak_subdir = explode('/', str_replace(_PS_CORE_DIR_.'/', '', dirname($file)));
            $tmpdir = $bak_dir;
            foreach ($bak_subdir as $dir) {
                if (!is_dir($tmpdir .= '/'.$dir)) {
                    mkdir($tmpdir, 0755);
                }
            }
            copy($file, $bak_dir.'/'.implode('/', $bak_subdir).'/'.$filename);
            echo '<p>'.$file.' ('.alterFile($file, $actions[$current_action_label]['function']).' octeti scriși)</p>'."\n";
        }
    }
}

echo '
        <form action="'.$_SERVER['REQUEST_URI'].'" method="post">';

$button_classes = 'btn btn-default button button-large';
foreach ($actions as $label => $action) {
    echo '
            <div class="form-group">
                <input name="form-action" class="'.$button_classes.'" type="submit" value="'.$label.'" />
            </div>';
    }

echo '
        </form>
    </body>
</html>';

function alterFile($file, $function)
{

    return file_put_contents($file, $function(file_get_contents($file)), LOCK_EX);
}

function psinstallGetOctets($option)
{
    if (preg_match('/[0-9]+k/i', $option)) {
        return 1024 * (int) $option;
    }

    if (preg_match('/[0-9]+m/i', $option)) {
        return 1024 * 1024 * (int) $option;
    }

    if (preg_match('/[0-9]+g/i', $option)) {
        return 1024 * 1024 * 1024 * (int) $option;
    }

    return $option;
}

function psinstallGetMemoryLimit()
{
    $memory_limit = @ini_get('memory_limit');

    if (preg_match('/[0-9]+k/i', $memory_limit)) {
        return 1024 * (int) $memory_limit;
    }

    if (preg_match('/[0-9]+m/i', $memory_limit)) {
        return 1024 * 1024 * (int) $memory_limit;
    }

    if (preg_match('/[0-9]+g/i', $memory_limit)) {
        return 1024 * 1024 * 1024 * (int) $memory_limit;
    }

    return $memory_limit;
}

/**
 * Taken from /classes/Tools.php:1165.
 *
 * Replace all accented chars by their equivalent non accented chars.
 *
 * @param string $str
 *
 * @return string
 */
function replaceAccentedChars($str)
{
    /* One source among others:
        http://www.tachyonsoft.com/uc0000.htm
        http://www.tachyonsoft.com/uc0001.htm
        http:/www.tachyonsoft.com/uc0002.htm
        http://www.tachyonsoft.com/uc0004.htm
    */
    $patterns = [/* c] */ '/[\x{00E7}\x{0107}\x{0109}\x{010D}\x{0446}]/u',
        /* d  */ '/[\x{010F}\x{0111}\x{0434}]/u',
        /* e  */ '/[\x{00E8}\x{00E9}\x{00EA}\x{00EB}\x{0113}\x{0115}\x{0117}\x{0119}\x{011B}\x{0435}\x{044D}]/u',
        /* f  */ '/[\x{0444}]/u',
        /* g  */ '/[\x{011F}\x{0121}\x{0123}\x{0433}\x{0491}]/u',
        /* h  */ '/[\x{0125}\x{0127}]/u',
        /* i  */ '/[\x{00EC}\x{00ED}\x{00EE}\x{00EF}\x{0129}\x{012B}\x{012D}\x{012F}\x{0131}\x{0438}\x{0456}]/u',
        /* j  */ '/[\x{0135}\x{0439}]/u',
        /* k  */ '/[\x{0137}\x{0138}\x{043A}]/u',
        /* l  */ '/[\x{013A}\x{013C}\x{013E}\x{0140}\x{0142}\x{043B}]/u',
        /* m  */ '/[\x{043C}]/u',
        /* n  */ '/[\x{00F1}\x{0144}\x{0146}\x{0148}\x{0149}\x{014B}\x{043D}]/u',
        /* o  */ '/[\x{00F2}\x{00F3}\x{00F4}\x{00F5}\x{00F6}\x{00F8}\x{014D}\x{014F}\x{0151}\x{043E}]/u',
        /* p  */ '/[\x{043F}]/u',
        /* r  */ '/[\x{0155}\x{0157}\x{0159}\x{0440}]/u',
        /* s  */ '/[\x{015B}\x{015D}\x{015F}\x{0161}\x{0219}\x{0441}]/u',
        /* ss */ '/[\x{00DF}]/u',
        /* t  */ '/[\x{0163}\x{0165}\x{0167}\x{021B}\x{0442}]/u',
        /* u  */ '/[\x{00F9}\x{00FA}\x{00FB}\x{00FC}\x{0169}\x{016B}\x{016D}\x{016F}\x{0171}\x{0173}\x{0443}]/u',
        /* v  */ '/[\x{0432}]/u',
        /* w  */ '/[\x{0175}]/u',
        /* y  */ '/[\x{00FF}\x{0177}\x{00FD}\x{044B}]/u',
        /* z  */ '/[\x{017A}\x{017C}\x{017E}\x{0437}]/u',
        /* ae */ '/[\x{00E6}]/u',
        /* ch */ '/[\x{0447}]/u',
        /* kh */ '/[\x{0445}]/u',
        /* oe */ '/[\x{0153}]/u',
        /* sh */ '/[\x{0448}]/u',
        /* shh*/ '/[\x{0449}]/u',
        /* ya */ '/[\x{044F}]/u',
        /* ye */ '/[\x{0454}]/u',
        /* yi */ '/[\x{0457}]/u',
        /* yo */ '/[\x{0451}]/u',
        /* yu */ '/[\x{044E}]/u',
        /* zh */ '/[\x{0436}]/u',

        /* Uppercase */
        /* A  */ '/[\x{0100}\x{0102}\x{0104}\x{00C0}\x{00C1}\x{00C2}\x{00C3}\x{00C4}\x{00C5}\x{0410}]/u',
        /* B  */ '/[\x{0411}]]/u',
        /* C  */ '/[\x{00C7}\x{0106}\x{0108}\x{010A}\x{010C}\x{0426}]/u',
        /* D  */ '/[\x{010E}\x{0110}\x{0414}]/u',
        /* E  */ '/[\x{00C8}\x{00C9}\x{00CA}\x{00CB}\x{0112}\x{0114}\x{0116}\x{0118}\x{011A}\x{0415}\x{042D}]/u',
        /* F  */ '/[\x{0424}]/u',
        /* G  */ '/[\x{011C}\x{011E}\x{0120}\x{0122}\x{0413}\x{0490}]/u',
        /* H  */ '/[\x{0124}\x{0126}]/u',
        /* I  */ '/[\x{0128}\x{012A}\x{012C}\x{012E}\x{0130}\x{0418}\x{0406}]/u',
        /* J  */ '/[\x{0134}\x{0419}]/u',
        /* K  */ '/[\x{0136}\x{041A}]/u',
        /* L  */ '/[\x{0139}\x{013B}\x{013D}\x{0139}\x{0141}\x{041B}]/u',
        /* M  */ '/[\x{041C}]/u',
        /* N  */ '/[\x{00D1}\x{0143}\x{0145}\x{0147}\x{014A}\x{041D}]/u',
        /* O  */ '/[\x{00D3}\x{014C}\x{014E}\x{0150}\x{041E}]/u',
        /* P  */ '/[\x{041F}]/u',
        /* R  */ '/[\x{0154}\x{0156}\x{0158}\x{0420}]/u',
        /* S  */ '/[\x{015A}\x{015C}\x{015E}\x{0160}\x{0218}\x{0421}]/u',
        /* T  */ '/[\x{0162}\x{0164}\x{0166}\x{021A\x{0422}]/u',
        /* U  */ '/[\x{00D9}\x{00DA}\x{00DB}\x{00DC}\x{0168}\x{016A}\x{016C}\x{016E}\x{0170}\x{0172}\x{0423}]/u',
        /* V  */ '/[\x{0412}]/u',
        /* W  */ '/[\x{0174}]/u',
        /* Y  */ '/[\x{0176}\x{042B}]/u',
        /* Z  */ '/[\x{0179}\x{017B}\x{017D}\x{0417}]/u',
        /* AE */ '/[\x{00C6}]/u',
        /* CH */ '/[\x{0427}]/u',
        /* KH */ '/[\x{0425}]/u',
        /* OE */ '/[\x{0152}]/u',
        /* SH */ '/[\x{0428}]/u',
        /* SHH*/ '/[\x{0429}]/u',
        /* YA */ '/[\x{042F}]/u',
        /* YE */ '/[\x{0404}]/u',
        /* YI */ '/[\x{0407}]/u',
        /* YO */ '/[\x{0401}]/u',
        /* YU */ '/[\x{042E}]/u',
        /* ZH */ '/[\x{0416}]/u',
    ];

        // ö to oe
        // å to aa
        // ä to ae

    $replacements = [
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 'ss', 't', 'u', 'v', 'w', 'y', 'z', 'ae', 'ch', 'kh', 'oe', 'sh', 'shh', 'ya', 'ye', 'yi', 'yo', 'yu', 'zh',
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'V', 'W', 'Y', 'Z', 'AE', 'CH', 'KH', 'OE', 'SH', 'SHH', 'YA', 'YE', 'YI', 'YO', 'YU', 'ZH',
    ];

    return preg_replace($patterns, $replacements, $str);
}

function commentEmptyTranslations($str)
{

    $pattern = '/^(\$_[A-Z]+\[\'.+\'\] = \'\';)$/m';
    return preg_replace($pattern, '//\1', $str);
}
