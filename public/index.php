<?php
session_start();
use LLM\lib\common;
//error_reporting(E_ALL & ~E_NOTICE);
 include(dirname(__FILE__).DIRECTORY_SEPARATOR."LLM".DIRECTORY_SEPARATOR."Config".DIRECTORY_SEPARATOR."core.php");
//include('/application/public/Config/core.php');
common::Dispatcher();

