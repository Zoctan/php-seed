<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once dirname(__FILE__) . "/function/Util.php";

Util::deleteSessionAndCookie();
Util::alert2("成功登出","login.php");
