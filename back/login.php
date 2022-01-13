<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once dirname(__FILE__) . "/function/Util.php";
require_once dirname(__FILE__) . "/model/Account.php";

if (!empty($_POST)) {
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];

        $account = Account::getInstance()->getOneBy(["username", "password"], [$username, $password]);
        if (empty($account)) {
            Util::alert("用户名或密码错误，请重新输入");
        } else {
            // 保存登录状态
            Account::getInstance()->saveLoginStatus($account);
            return Util::jump2("index.php");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <title>后台登录</title>
    <link rel="shortcut icon" href="static/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="static/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="static/css/my.css">
    <style>
        body {
            background: linear-gradient(to right, #0072ff, #00c6ff);
            width: 100vw;
            height: 100vh;
        }

        @keyframes updown {
            from {
                top: 20%;
            }

            50% {
                top: 25%;
            }

            to {
                top: 20%;
            }
        }

        @-webkit-keyframes updown {
            from {
                top: 20%;
            }

            50% {
                top: 25%;
            }

            to {
                top: 20%;
            }
        }

        .login-pic {
            position: absolute;
            left: 10%;
            top: 20%;
            transition: updown 3s infinite;
            -webkit-animation: updown 3s infinite alternate;
        }

        .login-pic img {
            width: 600px;
        }

        .login-input-box {
            position: absolute;
            right: 30%;
            top: 30%;
            padding: 20px;
            border-radius: 15px;
            border: 10px solid white;
            background-color: rgb(255, 255, 255, 70%);
        }
    </style>
</head>

<body>
    <div class="login-pic">
        <img src="static/images/login.png" alt="登录图">
    </div>
    <div class="login-input-box">
        <h2 class="text-primary mb-5">后台登录</h2>
        <form role="form" action="login.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="username" placeholder="请输入用户名" autocomplete>
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="请输入密码" autocomplete>
            </div>
            <div class="form-group">
                <button type="submit" class="form-control btn btn-success">提交</button>
            </div>
        </form>
    </div>

    <script src="static/js/jquery.min.js"></script>
    <!-- https://www.runoob.com/bootstrap/bootstrap-tutorial.html -->
    <script src="static/js/bootstrap.min.js"></script>
    <!-- https://gasparesganga.com/labs/jquery-loading-overlay/#quick-demo -->
    <script src="static/js/loadingoverlay.min.js"></script>
    <!-- https://github.com/js-cookie/js-cookie -->
    <script src="static/js/js.cookie.min.js"></script>
    <script src="static/js/api.js"></script>
    <script>
        $(function() {

        });
    </script>
</body>

</html>