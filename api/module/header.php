<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once dirname(__FILE__) . "/../function/Util.php";
require_once dirname(__FILE__) . "/../model/Member.php";

// 未登录跳转
if (!Member::isLogin()) {
    return Util::alert2("请先登录", "login.php");
}

require_once dirname(__FILE__) . "/../model/System.php";

$web = System::getInstance()->getValue("web");
