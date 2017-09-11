<?php

$db = array();

$active_group = 'default';

$db['default']['host'] = env('DB_HOST');
$db['default']['username'] = env('DB_USERNAME');
$db['default']['password'] = env('DB_PASSWORD');
$db['default']['db'] = 'ruleengine';
$db['default']['prefix'] = '';
$db['default']['charset'] = 'utf8';
$db['default']['port'] = 3306;