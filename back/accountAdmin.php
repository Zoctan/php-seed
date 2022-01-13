<?php
require_once dirname(__FILE__) . "/module/header.php";

if ($_COOKIE["role"] != "superadmin") {
    return Util::alert2("非超级管理员请勿查看");
}

// 角色配置列表
$roleSettingList = System::getInstance()->getValue("role");
?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <title><?php echo $web["webName"]; ?>后台-用户管理</title>
    <link rel="shortcut icon" href="static/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="static/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="static/css/my.css">
    <link rel="stylesheet" type="text/css" href="static/css/page.css">
</head>

<body>
    <!-- 导航栏 -->
    <?php require_once dirname(__FILE__) . "/module/navbar.php"; ?>
    <div class="data-top">
        <!-- 搜索框 -->
        <div class="input-group">
            <div class="input-group-btn">
                <select id="targetColumn" class="form-control" style="width: 10rem;">
                    <option value="username" selected="selected">用户名</option>
                    <option value="role">角色</option>
                    <!-- <option value="nickname">微信昵称</option> -->
                </select>
            </div>
            <input type="text" class="form-control" id="searchValue" placeholder="请输入关键字">
            <span class="input-group-btn">
                <button id="onSearch" class="form-control btn btn-primary" disabled="disabled">搜索</button>
            </span>
        </div>
        <!-- 新建按钮 -->
        <button class="btn btn-primary" data-toggle="modal" data-target="#newAccountModal">新建用户</button>
    </div>

    <div id="data-list"></div>

    <!-- 页码 -->
    <div id="data-page"></div>

    <!-- 新建用户框 -->
    <div class="modal fade" id="newAccountModal" tabindex="-1" role="dialog" aria-labelledby="newAccountModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h5 class="modal-title" id="newAccountModalLabel">新建用户信息</h5>
                </div>
                <div class="modal-body">
                    <form role="form">
                        <div class="form-group">
                            <label for="username">用户名</label>
                            <input id="newAccountUsername" type="text" class="form-control" name="username" placeholder="请输入用户名">
                        </div>
                        <div class="form-group">
                            <label for="password">密码</label>
                            <input id="newAccountPassword" type="text" class="form-control" name="password" placeholder="请输入密码">
                        </div>
                        <div class="form-group">
                            <label for="role">角色</label>
                            <select id="newAccountRoleSelect" class="form-control" name="role">
                                <?php for ($i = 0, $len = count($roleSettingList); $i < $len; $i++) { ?>
                                    <option value="<?php echo $roleSettingList[$i]["name"]; ?>"><?php echo $roleSettingList[$i]["des"]; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="newAccountEnsure" type="button" class="btn btn-success">确认</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 用户框 -->
    <div class="modal fade" id="accountModal" tabindex="-1" role="dialog" aria-labelledby="accountModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h5 class="modal-title" id="accountModalLabel">用户信息</h5>
                </div>
                <div class="modal-body">
                    <form role="form">
                        <div class="form-group">
                            <label for="username">用户名</label>
                            <div id="modal_username"></div>
                        </div>
                        <div class="form-group">
                            <label for="createTime">注册时间</label>
                            <div id="modal_createTime"></div>
                        </div>
                        <div class="form-group">
                            <label for="role">角色</label>
                            <div>
                                <div id="modal_role"></div>

                                <select id="modal_roleSelect">
                                    <?php for ($i = 0, $len = count($roleSettingList); $i < $len; $i++) { ?>
                                        <option value="<?php echo $roleSettingList[$i]["name"]; ?>"><?php echo $roleSettingList[$i]["des"]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="modal_changeRole" type="button" class="btn btn-primary">修改角色</button>
                    <button id="modal_cancel" type="button" class="btn btn-danger">取消</button>
                    <button id="modal_ensure" type="button" class="btn btn-warning">确认</button>
                </div>
            </div>
        </div>
    </div>

    <script src="static/js/jquery.min.js"></script>
    <!-- https://www.runoob.com/bootstrap/bootstrap-tutorial.html -->
    <script src="static/js/bootstrap.min.js"></script>
    <!-- https://gasparesganga.com/labs/jquery-loading-overlay/#quick-demo -->
    <script src="static/js/loadingoverlay.min.js"></script>
    <!-- https://github.com/js-cookie/js-cookie -->
    <script src="static/js/js.cookie.min.js"></script>
    <script src="static/js/utils.js"></script>
    <script src="static/js/page.js"></script>
    <script src="static/js/api.js"></script>
    <script>
        $(function() {
            var roleSettingList = JSON.parse('<?php echo json_encode($roleSettingList); ?>');
            var roleNameDict = {};
            for (var i = 0; i < roleSettingList.length; i++) {
                roleNameDict[roleSettingList[i].name] = roleSettingList[i].des;
            }

            var accountList = [];

            setPageId('data-page');
            setPageFunction(getAccountListByPage);
            renderPage();

            // 渲染用户列表
            function renderAccountList(accountList) {
                var result = `<div class="data-item">
                                    <div class="data-item-left">用户名</div>
                                    <div class="data-item-center">角色</div>
                                    <div class="data-btn-group"></div>
                                </div>`;
                $.each(accountList, function(_, item) {
                    result += `<div class="data-item">
                                    <div class="data-item-left">${item.username}</div>
                                    <div class="data-item-center">${roleNameDict[item.role]}</div>
                                    <div class="data-btn-group">
                                        <button class="btn btn-success show-account" data-id="${item.id}">查看</button>
                                    </div>
                                </div>`;
                })
                $("#data-list").empty();
                $("#data-list").html(result);
            };

            // 搜索用户
            function searchAccount() {
                API.Account.search({
                    data: {
                        "currentPage": currentPage,
                        "pageSize": pageSize,
                        "targetColumn": $("#targetColumn").val(),
                        "searchValue": $("#searchValue").val(),
                    },
                    beforeCallback: function() {
                        $.LoadingOverlay("show", {
                            text: "获取中..."
                        });
                    },
                    successCallback: function(data) {
                        accountList = data.data;
                        renderAccountList(accountList);
                        setPage(data);
                    },
                    errorCallback: function() {
                        alert("获取失败");
                    },
                    completeCallback: function() {
                        $.LoadingOverlay("hide");
                    }
                });
            };
            searchAccount();

            function getAccountListByPage() {
                // 如果搜索框有内容，则应该是搜索过的内容分页
                if ($("#searchValue").val().trim()) {
                    $("#onSearch").click();
                } else {
                    searchAccount();
                }
            }
            // 搜索框
            $("#searchValue").bind("input propertychange", function() {
                var text = $(this).val().trim()
                if (text === "") {
                    $("#onSearch").attr({
                        "disabled": "disabled"
                    })
                } else {
                    $("#onSearch").removeAttr("disabled")
                }
            });
            // 搜索按钮
            $("#onSearch").click(function() {
                searchAccount();
            });

            $('#data-list').on('click', '.show-account', function() {
                API.Account.get({
                    data: {
                        "id": $(this).attr('data-id'),
                    },
                    beforeCallback: function() {
                        $.LoadingOverlay("show", {
                            text: "获取中..."
                        });
                    },
                    successCallback: function(data) {
                        $("#modal_username").text(data.username);
                        $("#modal_createTime").text(data.create_time);
                        $("#modal_role").text(roleNameDict[data.role]);

                        // 角色选择框默认值
                        $("#modal_roleSelect").val(data.role);
                        // 确认按钮添加用户id
                        $("#modal_ensure").attr("data-id", data.id);

                        // 显示模态框
                        $("#accountModal").modal("toggle");

                        // 点击修改角色按钮才显示
                        $("#modal_cancel").click();

                        // 不能修改自己
                        if (data.id == <?php echo $_COOKIE["id"]; ?>) {
                            $("#modal_changeRole").hide();
                        }
                    },
                    errorCallback: function() {
                        alert("获取失败");
                    },
                    completeCallback: function() {
                        $.LoadingOverlay("hide");
                    }
                });
            });

            $("#modal_changeRole").click(function() {
                $("#modal_role").hide();
                $("#modal_changeRole").hide();
                $("#modal_cancel").show();
                $("#modal_ensure").show();
                $("#modal_roleSelect").show();
            });

            $("#modal_cancel").click(function() {
                $("#modal_cancel").hide();
                $("#modal_ensure").hide();
                $("#modal_roleSelect").hide();
                $("#modal_role").show();
                $("#modal_changeRole").show();
            });

            // 修改角色
            $("#modal_ensure").click(function() {
                var id = $(this).attr('data-id');
                var role = $("#modal_roleSelect").val();
                API.Account.updateRole({
                    data: {
                        "id": id,
                        "role": role,
                    },
                    beforeCallback: function() {
                        $.LoadingOverlay("show", {
                            text: "获取中..."
                        });
                    },
                    successCallback: function(data) {
                        for (var i = 0; i < accountList.length; i++) {
                            if (accountList[i].id == id) {
                                accountList[i].role = role;
                                $("#modal_role").text(roleNameDict[role]);
                                break;
                            }
                        }
                        alert("修改成功");
                        $("#modal_cancel").click();
                        renderAccountList(accountList);
                    },
                    errorCallback: function() {
                        alert("获取失败");
                    },
                    completeCallback: function() {
                        $.LoadingOverlay("hide");
                    }
                });
            });

            // 新建用户
            $("#newAccountEnsure").click(function() {
                var username = $("#newAccountUsername").val().trim();
                var password = $("#newAccountPassword").val().trim();
                var role = $("#newAccountRoleSelect").val().trim();

                if (username == null || username == '') {
                    return alert('请补全用户名');
                }
                if (password == null || password == '') {
                    return alert('请补全密码');
                }
                if (role == null || role == '') {
                    return alert('请选择角色');
                }

                API.Account.create({
                    data: {
                        "info": {
                            "username": username,
                            "password": password,
                            "role": role,
                        }
                    },
                    beforeCallback: function() {
                        $.LoadingOverlay("show", {
                            text: "创建中..."
                        });
                    },
                    successCallback: function(data) {
                        alert("创建成功");
                        // location.reload();
                    },
                    errorCallback: function() {
                        alert("创建失败");
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