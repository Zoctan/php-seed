<?php
require_once dirname(__FILE__) . "/module/header.php";

$member = Member::getInstance()->getOneBy(["id"], [$_COOKIE["id"]]);
?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <title><?php echo $web["webName"]; ?>后台-用户中心</title>
    <link rel="shortcut icon" href="static/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="static/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="static/css/my.css">
    <style>
        .info-box {
            margin-top: 2rem;
            margin-bottom: 10rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .info-box .info-input {
            display: none;
        }
    </style>
</head>

<body>
    <div class="info-box">
        <form role="form" id="form">
            <div class="form-group">
                <label for="username">用户名</label>
                <div class="info-text"><?php echo $member["username"]; ?></div>
                <input id="username" type="text" class="form-control info-input" name="username" placeholder="请输入新用户名，不输入表示不更改">
            </div>
            <div class="form-group">
                <label for="password">密码</label>
                <div class="info-text"><?php echo !empty($member["password"]) ? "***" : ""; ?></div>
                <input id="password" type="password" class="form-control info-input" name="password" placeholder="请输入新密码，不输入表示不更改">
                <input id="password2" type="password" class="form-control info-input" name="password2" placeholder="第二次输入新密码，不输入表示不更改">
            </div>
            <div class="form-group">
                <label for="password">注册时间</label>
                <div><?php echo $member["create_time"]; ?></div>
            </div>
            <div class="form-group d-flex align-items-center justify-content-between">
                <button id="edit" type="button" class="btn btn-primary">修改信息</button>
                <button id="cancel" type="button" class="btn btn-danger">取消</button>
                <button id="ensure" type="button" class="btn btn-warning">确认</button>
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
            // 点击修改按钮才显示
            $("#cancel").hide();
            $("#ensure").hide();
            $(".info-input").hide();

            $("#edit").click(function() {
                $("#edit").hide();
                $("#cancel").show();
                $("#ensure").show();

                $(".info-text").hide();
                $(".info-input").show();
            });

            $("#cancel").click(function() {
                $("#cancel").hide();
                $("#ensure").hide();
                $("#edit").show();

                $(".info-text").show();
                $(".info-input").hide();
            });

            $("#ensure").click(function() {
                var username = $("#username").val().trim();
                var password = $("#password").val().trim();
                var password2 = $("#password2").val().trim();

                var form = {};
                if (username != '' && username != null) {
                    form['username'] = username;
                }
                if (password != password2) {
                    return alert('两次密码不一致');
                }
                if (password != '' && password != null) {
                    form['password'] = password;
                }
                // 表单不为空才修改
                if ($.isEmptyObject(form)) {
                    return alert('无修改内容');
                }
                API.Member.update({
                    data: {
                        "info": form,
                    },
                    beforeCallback: function() {
                        $.LoadingOverlay("show", {
                            text: "修改中..."
                        });
                    },
                    successCallback: function(data) {
                        alert("修改成功，请重新登录");
                        location.href = 'logout.php';
                    },
                    errorCallback: function() {
                        alert("修改失败");
                    },
                    completeCallback: function() {
                        $.LoadingOverlay("hide");
                    }
                });
            });
        });
    </script>
</body>

</html>