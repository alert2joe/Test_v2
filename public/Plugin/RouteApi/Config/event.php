<?php
use Evenement\EventEmitter;
use LLM\lib\common;


/*
EventEmitter::on('RouteApi.getToken', function ($params) {
	//common::log($params);
});
*/



EventEmitter::on('RouteApi.track_failure', function ($data,$errorLog) {
	common::log($data);
	common::log($errorLog);
});




