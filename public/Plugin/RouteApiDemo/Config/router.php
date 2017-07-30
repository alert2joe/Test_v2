<?php
use LLM\lib\appRouter;
use LLM\lib\common;


 appRouter::add('/^\/demo$/',function($uri){
        include(dirname(dirname(__FILE__))."/demo.php");
 });
