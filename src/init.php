<?php

define('ENVIRONMENT','development');
//define('ENVIRONMENT','staging');
//define('ENVIRONMENT','production');


if (defined('ENVIRONMENT'))
{
	switch (ENVIRONMENT)
	{
		case 'development':
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			break;	
		case 'staging':
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			break;
		case 'production':
			error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
			ini_set('display_errors', 0);
			break;

		default:
			exit('The application environment is not set correctly.');
	}
}

include_once dirname(__FILE__).'/inc/database.php';
require_once dirname(__FILE__).'/lib/helper.php';


new MysqliDb ($db[$active_group]);