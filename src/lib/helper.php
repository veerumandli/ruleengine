<?php


function pr($data){
	echo "<pre />";
	print_r($data);
	exit;
}

function find_api_endpoint(){
	$endpoint = explode('/',$_SERVER['REQUEST_URI']);
	$endpoint = end($endpoint);
	$endpoint();
}
