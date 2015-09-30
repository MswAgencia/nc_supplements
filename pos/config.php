<?php
// DIR
$dir = dirname(__FILE__);
define('DIR_APPLICATION', $dir.'/');
define('DIR_SYSTEM', dirname($dir).'/system/');
define('DIR_DATABASE', DIR_SYSTEM.'database/');
define('DIR_LANGUAGE', dirname(DIR_APPLICATION).'/admin/language/');
define('DIR_TEMPLATE', DIR_APPLICATION.'view/template/');
define('DIR_CONFIG', DIR_SYSTEM.'config/');
define('DIR_IMAGE', dirname($dir).'/image/');
define('DIR_CACHE', DIR_SYSTEM.'cache/');
define('DIR_DOWNLOAD', dirname($dir).'/download/');
define('DIR_LOGS', DIR_SYSTEM.'logs/');
define('DIR_CATALOG', dirname($dir).'/catalog/');
define('DIR_MODIFICATION', DIR_SYSTEM.'/modification/');

$fh = fopen(dirname($dir)."/config.php",'r');
while ($line = fgets($fh)) {

	if( strpos($line, 'HTTP_SERVER'))
	{
		$line = str_replace('HTTP_SERVER', 'HTTP_CATALOG', $line);
		eval($line);
	}

	if( strpos($line, 'HTTPS_SERVER'))
	{
		$line = str_replace('HTTPS_SERVER', 'HTTPS_CATALOG', $line);
		eval($line);
	}


	if( strpos($line, 'DB_DRIVER'))
	{
		eval($line);
	}

	if( strpos($line, 'DB_HOSTNAME'))
	{
		eval($line);
	}

	if( strpos($line, 'DB_USERNAME'))
	{
		eval($line);
	}

	if( strpos($line, 'DB_PASSWORD'))
	{
		eval($line);
	}

	if( strpos($line, 'DB_DATABASE'))
	{
		eval($line);
	}

	if( strpos($line, 'DB_PREFIX'))
	{
		eval($line);
	}

}
fclose($fh);

// HTTP
define('HTTP_SERVER', HTTP_CATALOG.'pos/');
define('HTTPS_SERVER', HTTPS_CATALOG.'pos/');