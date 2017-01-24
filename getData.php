<?php
/**
*	Build ticket data, as well as associated end-user accounts.
*   This is going t o likely be a long and slowww run.
*
*	This is probably going to be an utter shitshow.
*
*	@author 	cbyerly <cbyerly@liquidweb.com>
*	@license	MIT maybe idk
*	@package	ZenDump
*	@link 		https://git.liquidweb.com/cbyerly/ZenDump
*
*	¯\_(ツ)_/¯
*/
namespace ZenDump;
require 'vendor/autoload.php';
include("inc/curl.inc.php");
include("inc/database.inc.php");
include("inc/models.inc.php");

set_time_limit(0);
