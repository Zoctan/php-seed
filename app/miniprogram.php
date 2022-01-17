<?php
require_once dirname(__FILE__) . "/module/header.php";

$miniProgramValue = System::getInstance()->getValue("miniprogram");
?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <title><?php echo $web["webName"]; ?>后台-小程序配置</title>
    <link rel="shortcut icon" href="static/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="static/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="static/css/my.css">
    <style>
        .setting-box {
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .setting-box form {
            width: 50%;
            text-align: center;
        }

        .setting-box form button {
            width: 50%;
        }
    </style>
</head>

<body>
    <!-- 导航栏 -->
    <?php require_once dirname(__FILE__) . "/module/navbar.php"; ?>
    <div class="setting-box">
        <form role="form" id="form" class="form-horizontal">
            <div class="form-group">
                <label for="name" class="col-sm-2 control-label">小程序名称</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="name" placeholder="请输入小程序名称" value="<?php echo $miniProgramValue["name"]; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="appid" class="col-sm-2 control-label">小程序appid</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="appid" placeholder="请输入小程序appid" value="<?php echo $miniProgramValue["appid"]; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="path" class="col-sm-2 control-label">小程序路径path</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="path" placeholder="请输入小程序路径path" value="<?php echo $miniProgramValue["path"]; ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="extraData" class="col-sm-2 control-label">小程序额外数据extraData</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="extraData" placeholder="请输入小程序额外数据extraData" value="<?php echo $miniProgramValue["extraData"]; ?>">
                </div>
            </div>
            <div class="form-group" id="validPeriodAuth" style="display: none;">
                <label for="validPeriodAuth" class="col-sm-2 control-label">用户登录有效时长（天）</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="validPeriodAuth" placeholder="请输入用户登录有效时长" value="<?php echo $miniProgramValue["auth"]["validPeriod"] / 60 / 60 / 24 / 1000; ?>">
                </div>
            </div>
            <div class="form-group">
                <div class="checkbox control-label">
                    <label>
                        <input type="checkbox" name="needAuth" <?php echo $miniProgramValue["auth"]["need"] ? "checked" : ""; ?>>是否需要小程序用户登录
                    </label>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-success">提交</button>
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
            function onNeedAuthCheckChange() {
                var isChecked = $("input[name='needAuth']").prop("checked");
                console.info('needAuth', isChecked)
                if (isChecked) {
                    $("#validPeriodAuth").show();
                } else {
                    $("#validPeriodAuth").hide();
                }
                return isChecked;
            }
            onNeedAuthCheckChange();

            // 改变了小程序登录checkbox
            $("input[name='needAuth']").change(function() {
                onNeedAuthCheckChange();
            });

            $('#form').submit(function(e) {
                // 禁止form默认的提交事件
                e.preventDefault();

                // 整理数据
                var form = $('#form').serializeArray();
                var miniprogram = {
                    auth: {
                        need: false,
                    }
                };
                for (var i = 0; i < form.length; i++) {
                    var isAuthArgs = false;
                    if (form[i].name == "validPeriodAuth") {
                        form[i].value *= 60 * 60 * 24 * 1000;
                        isAuthArgs = true;
                    }
                    if (form[i].name == "needAuth") {
                        form[i].value = form[i].value == "on";
                        isAuthArgs = true;
                    }
                    if (isAuthArgs) {
                        var name = form[i].name.replace("Auth", "");
                        miniprogram.auth[name] = form[i].value;
                    } else {
                        miniprogram[form[i].name] = form[i].value;
                    }
                }
                // 检查数据
                var errorMsg = [];
                var errorNum = 0;
                if (miniprogram.name == "" || miniprogram.name == null) {
                    errorMsg.push('小程序名称为空');
                    errorNum++;
                }
                if (miniprogram.appid == "" || miniprogram.appid == null) {
                    errorMsg.push('小程序appid为空');
                    errorNum++;
                }
                // if (miniprogram.path == "" || miniprogram.path == null) {
                //     errorMsg.push('小程序路径path为空');
                //     errorNum++;
                // }
                // if (miniprogram.extraData == "" || miniprogram.extraData == null) {
                //     errorMsg.push('小程序额外数据extraData为空');
                //     errorNum++;
                // }
                if (miniprogram.auth.need == true && (miniprogram.auth.validPeriod == "" || miniprogram.auth.validPeriod == null)) {
                    errorMsg.push('用户登录有效时长（天）为空');
                    errorNum++;
                }
                if (errorNum > 0) {
                    return alert(errorMsg.join('，'));
                }
                
                API.System.updateValue({
                    data: {
                        key: "miniprogram",
                        value: JSON.stringify(miniprogram),
                    },
                    beforeCallback: function() {
                        $.LoadingOverlay("show", {
                            text: "更新小程序配置中..."
                        });
                    },
                    successCallback: function(data) {
                        alert("更新小程序配置成功");
                        location.reload();
                    },
                    errorCallback: function() {
                        alert("更新小程序配置失败");
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