<?php
require_once dirname(__FILE__) . "/module/header.php";

if ($_COOKIE["role"] != "superadmin") {
    return Util::alert2("非超级管理员请勿查看");
}

// 角色列表
$roleList = System::getInstance()->getValue("role");

// 权限列表
$permissionList = System::getInstance()->getValue("permission");
?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <title><?php echo $web["webName"]; ?>后台-角色管理</title>
    <link rel="shortcut icon" href="static/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="static/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="static/css/my.css">
    <link rel="stylesheet" type="text/css" href="static/css/page.css">
</head>

<body>
    <!-- 导航栏 -->
    <?php require_once dirname(__FILE__) . "/module/navbar.php"; ?>
    <div class="data-top">
        <button class="btn btn-primary" data-toggle="modal" data-target="#role-permission-modal">新建角色</button>
    </div>

    <div id="data-list"></div>

    <!-- 页码 -->
    <div id="data-page"></div>

    <!-- 角色-权限框 -->
    <div class="modal fade" id="role-permission-modal" tabindex="-1" role="dialog" aria-labelledby="role-permission-modal-label" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h5 class="modal-title" id="role-permission-modal-label">角色信息</h5>
                </div>
                <div class="modal-body">
                    <form role="form">
                        <div class="form-group">
                            <label for="name">角色值</label>
                            <input id="name" type="text" class="form-control" name="name" placeholder="请输入角色值">
                        </div>
                        <div class="form-group">
                            <label for="des">角色名</label>
                            <input id="des" type="text" class="form-control" name="des" placeholder="请输入角色名">
                        </div>
                        <div class="form-group">
                            <label for="permission">权限</label>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button id="modal-edit" type="button" class="btn btn-primary">修改</button>
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
            var permissionList = JSON.parse('<?php echo json_encode($permissionList); ?>');

            // 渲染角色列表
            function renderRoleList(roleList) {
                var result = `<div class="data-item">
                                    <div class="data-item-left">角色</div>
                                    <div class="data-item-center">用户数</div>
                                    <div class="data-item-right"></div>
                                </div>`;
                $.each(roleList, function(_, item) {
                    result += `<div class="data-item">
                                    <div class="data-item-left">${item.des}</div>
                                    <div class="data-item-center">${1}</div>
                                    <div class="data-item-right">
                                        <button class="btn btn-info show-member" data-id="${item.id}">查看用户</button>
                                        <button class="btn btn-success show-role-permission" data-id="${item.id}">查看权限</button>
                                        <button class="btn btn-danger delete-role" data-id="${item.id}">删除角色</button>
                                    </div>
                                </div>`;
                })
                $("#data-list").empty();
                $("#data-list").html(result);
            }
            renderRoleList(roleList);

            $('#data-list').on('click', '.show-role-permission', function() {
                var id = $(this).attr('data-id');

                $("#role-permission-modal").modal("toggle");
                $("#modal-edit").hide();
            });

            $('#data-list').on('click', '.delete-role', function() {
                if (confirm('确定删除角色？')) {
                    var id = $(this).attr('data-id');

                    // 将该角色的用户改成默认的普通用户

                    // 删除系统里的角色
                }
            });

            $("#modal-edit").click(function() {
                $("#modal-role").hide();
                $("#modal-edit").hide();
                $("#modal-cancel").show();
                $("#modal-ensure").show();
            });

            $("#modal-cancel").click(function() {
                $("#modal-cancel").hide();
                $("#modal-ensure").hide();
                $("#modal-role").show();
                $("#modal-edit").show();
            });

        });
    </script>
</body>

</html>