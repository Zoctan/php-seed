<?php
require_once dirname(__FILE__) . "/module/header.php";

if ($_COOKIE["role"] != "superadmin") {
    return Util::alert2("非超级管理员请勿查看");
}

// 角色列表
$roleList = System::getInstance()->getValue("role");
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
                <button id="search" class="form-control btn btn-primary" disabled="disabled">搜索</button>
            </span>
        </div>
        <!-- 新建按钮 -->
        <button class="btn btn-primary" data-toggle="modal" data-target="#newMemberModal">新建用户</button>
    </div>

    <div id="data-list"></div>

    <!-- 页码 -->
    <div id="data-page"></div>

    <!-- 新建用户框 -->
    <div class="modal fade" id="newMemberModal" tabindex="-1" role="dialog" aria-labelledby="newMemberModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h5 class="modal-title" id="newMemberModalLabel">新建用户信息</h5>
                </div>
                <div class="modal-body">
                    <form role="form">
                        <div class="form-group">
                            <label for="username">用户名</label>
                            <input id="newMemberUsername" type="text" class="form-control" name="username" placeholder="请输入用户名">
                        </div>
                        <div class="form-group">
                            <label for="password">密码</label>
                            <input id="newMemberPassword" type="text" class="form-control" name="password" placeholder="请输入密码">
                        </div>
                        <div class="form-group">
                            <label for="role">角色</label>
                            <select id="newMemberRoleSelect" class="form-control" name="role">
                                <?php for ($i = 0, $len = count($roleList); $i < $len; $i++) { ?>
                                    <option value="<?php echo $roleList[$i]["name"]; ?>"><?php echo $roleList[$i]["des"]; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="newMemberEnsure" type="button" class="btn btn-success">确认</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 用户框 -->
    <div class="modal fade" id="memberModal" tabindex="-1" role="dialog" aria-labelledby="memberModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h5 class="modal-title" id="memberModalLabel">用户信息</h5>
                </div>
                <div class="modal-body">
                    <form role="form">
                        <div class="form-group">
                            <label for="username">用户名</label>
                            <div id="modal-username"></div>
                        </div>
                        <div class="form-group">
                            <label for="create_time">注册时间</label>
                            <div id="modal-create_time"></div>
                        </div>
                        <div class="form-group">
                            <label for="role">角色</label>
                            <div>
                                <div id="modal-role"></div>

                                <select id="modal-role-select">
                                    <?php for ($i = 0, $len = count($roleList); $i < $len; $i++) { ?>
                                        <option value="<?php echo $roleList[$i]["name"]; ?>"><?php echo $roleList[$i]["des"]; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="modal-change-role" type="button" class="btn btn-primary">修改角色</button>
                    <button id="modal-cancel" type="button" class="btn btn-danger">取消</button>
                    <button id="modal-ensure" type="button" class="btn btn-warning">确认</button>
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
            var roleList = JSON.parse('<?php echo json_encode($roleList); ?>');
            var roleNameDict = {};
            for (var i = 0; i < roleList.length; i++) {
                roleNameDict[roleList[i].name] = roleList[i].des;
            }

            var memberList = [];

            setPageId('data-page');
            setPageFunction(getMemberListByPage);
            renderPage();

            // 渲染用户列表
            function renderMemberList(memberList) {
                var result = `<div class="data-item">
                                    <div class="data-item-left">用户名</div>
                                    <div class="data-item-center">角色</div>
                                    <div class="data-item-right"></div>
                                </div>`;
                $.each(memberList, function(_, item) {
                    result += `<div class="data-item">
                                    <div class="data-item-left">${item.username}</div>
                                    <div class="data-item-center">${roleNameDict[item.role]}</div>
                                    <div class="data-item-right">
                                        <button class="btn btn-success show-member" data-id="${item.id}">查看</button>
                                    </div>
                                </div>`;
                })
                $("#data-list").empty();
                $("#data-list").html(result);
            };

            // 搜索用户
            function searchMember() {
                API.Member.search({
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
                        memberList = data.data;
                        renderMemberList(memberList);
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
            searchMember();

            function getMemberListByPage() {
                // 如果搜索框有内容，则应该是搜索过的内容分页
                if ($("#searchValue").val().trim()) {
                    $("#search").click();
                } else {
                    searchMember();
                }
            }
            // 搜索框
            $("#searchValue").bind("input propertychange", function() {
                var text = $(this).val().trim()
                if (text === "") {
                    $("#search").attr({
                        "disabled": "disabled"
                    })
                } else {
                    $("#search").removeAttr("disabled")
                }
            });
            // 搜索按钮
            $("#search").click(function() {
                searchMember();
            });

            $('#data-list').on('click', '.show-member', function() {
                API.Member.get({
                    data: {
                        "id": $(this).attr('data-id'),
                    },
                    beforeCallback: function() {
                        $.LoadingOverlay("show", {
                            text: "获取中..."
                        });
                    },
                    successCallback: function(data) {
                        $("#modal-username").text(data.username);
                        $("#modal-create_time").text(data.create_time);
                        $("#modal-role").text(roleNameDict[data.role]);

                        // 角色选择框默认值
                        $("#modal-role-select").val(data.role);
                        // 确认按钮添加用户id
                        $("#modal-ensure").attr("data-id", data.id);

                        // 显示模态框
                        $("#memberModal").modal("toggle");

                        // 点击修改角色按钮才显示
                        $("#modal-cancel").click();

                        // 不能修改自己
                        if (data.id == <?php echo $_COOKIE["id"]; ?>) {
                            $("#modal-change-role").hide();
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

            $("#modal-change-role").click(function() {
                $("#modal-role").hide();
                $("#modal-change-role").hide();
                $("#modal-cancel").show();
                $("#modal-ensure").show();
                $("#modal-role-select").show();
            });

            $("#modal-cancel").click(function() {
                $("#modal-cancel").hide();
                $("#modal-ensure").hide();
                $("#modal-role-select").hide();
                $("#modal-role").show();
                $("#modal-change-role").show();
            });

            // 修改角色
            $("#modal-ensure").click(function() {
                var id = $(this).attr('data-id');
                var role = $("#modal-role-select").val();
                API.Member.updateRole({
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
                        for (var i = 0; i < memberList.length; i++) {
                            if (memberList[i].id == id) {
                                memberList[i].role = role;
                                $("#modal-role").text(roleNameDict[role]);
                                break;
                            }
                        }
                        alert("修改成功");
                        $("#modal-cancel").click();
                        renderMemberList(memberList);
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
            $("#newMemberEnsure").click(function() {
                var username = $("#newMemberUsername").val().trim();
                var password = $("#newMemberPassword").val().trim();
                var role = $("#newMemberRoleSelect").val().trim();

                if (username == null || username == '') {
                    return alert('请补全用户名');
                }
                if (password == null || password == '') {
                    return alert('请补全密码');
                }
                if (role == null || role == '') {
                    return alert('请选择角色');
                }

                API.Member.create({
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