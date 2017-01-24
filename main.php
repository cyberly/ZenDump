<?php
/**
*	Core functionality of the ZenDump package goes in this file. Tertiary jobs,
*   functions and classes will probably go in the inc/ directory.
*
*	This is probably going to be an utter shitshow.
*
*	@author 	cbyerly <cbyerly@liquidweb.com>
*	@license	http://opensource.org/licenses/Apache-2.0 Apache License, Version 2.0
*	@package	ZenDump
*	@link 		https://git.liquidweb.com/cbyerly/ZenDump
*
*	¯\_(ツ)_/¯
*/

include("inc/curl.inc.php");
set_time_limit(0);

$prod = new zdCurl("production");
echo "lol hi";
