<?php
namespace Ruleengine;

require_once(dirname(__FILE__) . '/init.php');

abstract class Ruleengine{

	private static $group;

	private static $channel;
	
	public static function init($group=0){
		self::$group = $group;
	}

	public static function zdb(){
        return \MysqliDb::getInstance();
	}

	function check_channel_active(){

	}

}


