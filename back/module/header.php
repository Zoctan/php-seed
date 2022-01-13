<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once dirname(__FILE__) . "/../function/Util.php";
require_once dirname(__FILE__) . "/../model/Account.php";

// 未登录跳转
if (!Account::isLogin()) {
    return Util::alert2("请先登录", "login.php");
}

require_once dirname(__FILE__) . "/../model/System.php";

$web = System::getInstance()->getValue("web");

require_once dirname(__FILE__) . "/../function/Predis/autoload.php";

$redisCache = System::getInstance()->getValue("redisCache");
if ($redisCache["status"]) {
    $client = new Predis\Client($redisCache["option"]);

    $client->set('foo', 'bar');
    $value = $client->get('foo');
    var_dump($value);
}
