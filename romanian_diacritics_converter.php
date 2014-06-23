<?php

echo '<html>
	<head>
	  <meta charset="UTF-8">
	  <title>Diacritics Remover</title>
		<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/css/bootstrap-combined.min.css" rel="stylesheet">
		<style type="text/css">
			body {padding: 20px}
			input[type=text] {width:600px;margin:0;height:28px;line-height:28px;}
		</style>
	</head>
	<body>
	  <h1>Script pentru conversia diacriticelor românești</h1>';



// Check PHP version
if (version_compare(preg_replace('/[^0-9.]/', '', PHP_VERSION), '5.1.3', '<'))
	die('You need at least PHP 5.1.3 to run PrestaShop. Your current PHP version is '.PHP_VERSION);

if (!defined('__PS_BASE_URI__'))
        define('__PS_BASE_URI__', substr($_SERVER['REQUEST_URI'], 0, -1 * (strlen($_SERVER['REQUEST_URI']) - strrpos($_SERVER['REQUEST_URI'], '/')) - strlen(substr(dirname($_SERVER['REQUEST_URI']), strrpos(dirname($_SERVER['REQUEST_URI']), '/') + 1))));

if (!defined('_THEME_NAME_'))
        define('_THEME_NAME_', 'default-bootstrap');

require_once(realpath(dirname(__FILE__)).'/config/defines.inc.php');
require_once(realpath(dirname(__FILE__)).'/config/defines_uri.inc.php');

if (!defined('_PS_CORE_DIR_'))
	define('_PS_CORE_DIR_', realpath(dirname(__FILE__)));

require_once(_PS_CORE_DIR_.'/config/autoload.php');
require_once(_PS_CORE_DIR_.'/config/alias.php');

@set_time_limit(0);
if (!@ini_get('date.timezone'))
	@date_default_timezone_set('UTC');

// Some hosting still have magic_quotes_runtime configured
ini_set('magic_quotes_runtime', 0);

// Try to improve memory limit if it's under 32M
if (psinstall_get_memory_limit() < psinstall_get_octets('64M'))
	ini_set('memory_limit', '64M');

//$iso = Tools::getValue('iso');

if (Tools::isSubmit('removeRomanianDiacritics')) {
  $bak_dir = _PS_CORE_DIR_.'/translations/export/ro_diacritics_'.date('ymd-His');
  if (is_dir($bak_dir)) {
    die('Backup directory already created');
  } else {
    if (!mkdir($bak_dir, 0755))
      die('Failed to create folder: '.$bak_dir);
  }

  $regex = '/->l\(\'(.*[^\\\\])\'(, ?\'(.+)\')?(, ?(.+))?\)/U';
  $dirs = array('modules', 'themes', 'translations');
  $files = $translations = $translations_source = array();
  
  $files = array_merge(Tools::scandir('', 'php', _PS_CORE_DIR_.'/modules', true), Tools::scandir('', 'php', _PS_CORE_DIR_.'/themes', true));
  foreach ($files as $kf => $file)
    if (basename($file) !== 'ro.php')
      unset($files[$kf]);
  $files = array_merge($files, Tools::scandir('', 'php', _PS_CORE_DIR_.'/translations/ro'));
  
  $diacritics = array('ă', 'Ă', 'â', 'Â', 'î', 'Î', 'ș', 'Ș', 'ț', 'ţ', 'Ț');
  $replacements = array('a', 'A', 'a', 'A', 'i', 'I', 's', 'S', 't', 't', 'T');
  foreach ($files as $file) {
    $filename = basename($file);
    $bak_subdir = explode('/', str_replace(_PS_CORE_DIR_.'/', '', dirname($file)));
    $tmpdir = $bak_dir;
    foreach ($bak_subdir as $dir)
      if (!is_dir($tmpdir .= '/'.$dir))
        mkdir($tmpdir, 0755);
    copy($file, $bak_dir.'/'.implode('/', $bak_subdir).'/'.$filename);
    file_put_contents($file, str_replace($diacritics, $replacements, file_get_contents($file)), LOCK_EX);
    echo '<p>'.$file.' nu mai are diacritice</p>'."\n";
  }
  
}

echo '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
echo '
			<input class="btn btn-primary" type="submit" name="removeRomanianDiacritics" value="Convertește diacriticele românești" />';
/*echo '
			<input class="btn btn-primary" type="submit" name="restoreRomanianDiacritics" value="Restaurează traducerile" />';*/
echo '
		</form>';
echo '
	</body>
</html>';


function psinstall_get_octets($option)
{
	if (preg_match('/[0-9]+k/i', $option))
		return 1024 * (int)$option;

	if (preg_match('/[0-9]+m/i', $option))
		return 1024 * 1024 * (int)$option;

	if (preg_match('/[0-9]+g/i', $option))
		return 1024 * 1024 * 1024 * (int)$option;

	return $option;
}

function psinstall_get_memory_limit()
{
	$memory_limit = @ini_get('memory_limit');
	
	if (preg_match('/[0-9]+k/i', $memory_limit))
		return 1024 * (int)$memory_limit;
	
	if (preg_match('/[0-9]+m/i', $memory_limit))
		return 1024 * 1024 * (int)$memory_limit;
	
	if (preg_match('/[0-9]+g/i', $memory_limit))
		return 1024 * 1024 * 1024 * (int)$memory_limit;
	
	return $memory_limit;
}

