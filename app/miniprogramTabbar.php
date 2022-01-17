<?php
require_once dirname(__FILE__) . "/module/header.php";
?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <title><?php echo $web["webName"]; ?>后台-小程序底部导航栏配置</title>
    <link rel="shortcut icon" href="static/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="static/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="static/css/my.css">
    <link rel="stylesheet" type="text/css" href="static/css/page.css">
</head>

<body>
    <!-- 导航栏 -->
    <?php require_once dirname(__FILE__) . "/module/navbar.php"; ?>
    <div class="tip-group">
        <div class="tip-title">提示</div>
        <div class="tip-item">1.顺序：按数字从大到小排列</div>
        <div class="tip-item">2.导航栏图标参考：<a href="https://vant-contrib.gitee.io/vant-weapp/#/icon" target="_blank">前往查看</a></div>
        <div class="tip-item">3.切换导航参考：<a href="https://developers.weixin.qq.com/miniprogram/dev/api/route/wx.switchTab.html" target="_blank">wx.switchTab</a></div>
        <div class="tip-item">4.跳转到小程序参考：<a href="https://developers.weixin.qq.com/miniprogram/dev/api/navigate/wx.navigateToMiniProgram.html" target="_blank">wx.navigateToMiniProgram</a></div>
    </div>

    <div class="data-top">
        <button id="change-order" class="btn btn-warning">更改顺序</button>
        <button id="submit-order" class="btn btn-primary">提交顺序</button>
        <button id="add" class="btn btn-primary">添加</button>
    </div>

    <div id="data-list"></div>

    <div class="modal fade" id="tabbarModal" tabindex="-1" role="dialog" aria-labelledby="tabbarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="tabbarModalLabel">小程序底部导航栏配置</h4>
                </div>
                <div class="modal-body">
                    <form role="form" id="form">
                        <div class="form-group">
                            <label for="order">顺序</label>
                            <input id="order" type="number" class="form-control" name="order" min="1" max="5" maxlength="1" placeholder="请输入顺序">
                        </div>
                        <div class="form-group">
                            <label for="title">名称</label>
                            <input id="title" type="text" class="form-control" name="title" placeholder="请输入名称">
                        </div>
                        <div class="form-group">
                            <label for="icon">图标</label>
                            <input id="icon" type="text" class="form-control" name="icon" placeholder="请输入图标">
                        </div>
                        <div class="form-group">
                            <label for="jumpType">跳转方式</label>
                            <input id="jumpType" type="text" class="form-control" name="jumpType" placeholder="请输入跳转方式">
                        </div>
                        <div class="form-group">
                            <label for="path">跳转路径</label>
                            <input id="path" type="text" class="form-control" name="path" placeholder="请输入跳转路径">
                        </div>
                    </form>
                    <div id="tips"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button id="submit-tabbar" type="button" class="btn btn-primary">提交</button>
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
    <script src="static/js/api.js"></script>
    <script>
        $(function() {
            var maxTabbarNum = 5;
            // 后期优化：转成对象，请求时再转回数组
            var tabbarList = [];

            function checkMaxTabbar() {
                if (tabbarList.length >= maxTabbarNum) {
                    $('#add').hide();
                } else {
                    $('#add').show();
                }
            }

            // 渲染系统键值对列表
            function renderTabbarList(tabbarList) {
                var result = `<div class="data-item">
                                    <div class="data-item-left">顺序</div>
                                    <div class="data-item-center">名称</div>
                                    <div class="data-item-right"></div>
                                </div>`;
                $.each(tabbarList, function(_, item) {
                    result += `<div class="data-item">
                                    <div class="data-item-left text-danger">
                                        <div class="read-order">
                                            ${item.order}
                                        </div>
                                        <div class="edit-order" hidden>
                                            <input class="edit-order-input" type="number" name="order" min="1" max="100" maxlength="3" value="${item.order}" data-id="${item.id}">
                                        </div>
                                    </div>
                                    <div class="data-item-center">
                                        ${item.title}
                                    </div>
                                    <div class="data-item-right">
                                        <button class="btn btn-success show-tabbar" data-id="${item.id}">查看</button>
                                        <button class="btn btn-warning edit-tabbar" data-id="${item.id}">编辑</button>
                                        <button class="btn btn-danger delete-tabbar" data-id="${item.id}">删除</button>
                                    </div>
                                </div>`;
                });
                $("#data-list").empty();
                $("#data-list").html(result);
            }

            // 获取底部导航栏
            function getTabbarList() {
                API.System.getValue({
                    data: {
                        key: "miniprogramTabbar",
                    },
                    beforeCallback: function() {
                        $.LoadingOverlay("show", {
                            text: "获取中..."
                        });
                    },
                    successCallback: function(data) {
                        tabbarList = data;
                        renderTabbarList(tabbarList);
                        checkMaxTabbar();
                    },
                    errorCallback: function() {
                        alert("获取失败");
                    },
                    completeCallback: function() {
                        $.LoadingOverlay("hide");
                    }
                });
            };
            getTabbarList();

            $('#data-list').on('click', '.show-tabbar', function() {
                var id = $(this).data('id');
                for (var i = 0; i < tabbarList.length; i++) {
                    if (tabbarList[i].id == id) {
                        $("#order").val(tabbarList[i].order);
                        $("#order").attr("readonly", "readonly");
                        $("#title").val(tabbarList[i].title);
                        $("#title").attr("readonly", "readonly");
                        $("#icon").val(tabbarList[i].icon);
                        $("#icon").attr("readonly", "readonly");
                        $("#jumpType").val(tabbarList[i].jumpType);
                        $("#jumpType").attr("readonly", "readonly");
                        $("#path").val(tabbarList[i].path);
                        $("#path").attr("readonly", "readonly");
                        $('#submit-tabbar').removeData('id');
                        $('#submit-tabbar').hide();
                        break;
                    }
                }
                $("#tabbarModal").modal("toggle");
            });

            $('#data-list').on('click', '.edit-tabbar', function() {
                var id = $(this).data('id');
                for (var i = 0; i < tabbarList.length; i++) {
                    if (tabbarList[i].id == id) {
                        $("#order").val(tabbarList[i].order);
                        $("#order").removeAttr("readonly");
                        $("#title").val(tabbarList[i].title);
                        $("#title").removeAttr("readonly");
                        $("#icon").val(tabbarList[i].icon);
                        $("#icon").removeAttr("readonly");
                        $("#jumpType").val(tabbarList[i].jumpType);
                        $("#jumpType").removeAttr("readonly");
                        $("#path").val(tabbarList[i].path);
                        $("#path").removeAttr("readonly");
                        $('#submit-tabbar').data('id', id);
                        $('#submit-tabbar').show();
                        break;
                    }
                }
                $("#tabbarModal").modal("toggle");
            });

            $('#data-list').on('click', '.delete-tabbar', function() {
                var id = $(this).data('id');
                for (var i = 0; i < tabbarList.length; i++) {
                    if (tabbarList[i].id == id) {
                        if (confirm(`确定删除导航【${tabbarList[i].title}】吗？`)) {
                            // 删除
                            tabbarList.splice(i, 1);

                            // 重新分配ID
                            for (var i = 0; i < tabbarList.length; i++) {
                                tabbarList[i].id = i + 1;
                            }

                            API.System.updateValue({
                                data: {
                                    key: "miniprogramTabbar",
                                    value: JSON.stringify(tabbarList),
                                },
                                beforeCallback: function() {
                                    $.LoadingOverlay("show", {
                                        text: "删除导航栏中..."
                                    });
                                },
                                successCallback: function(data) {
                                    alert("删除导航栏成功");
                                    location.reload();
                                },
                                errorCallback: function() {
                                    alert("删除导航栏失败");
                                },
                                completeCallback: function() {
                                    $.LoadingOverlay("hide");
                                }
                            });
                        }
                        break;
                    }
                }
            });

            $('#submit-order').hide();
            $('#change-order').click(function(e) {
                $('.read-order').hide();
                $('.edit-order').show();
                $('#change-order').hide();
                $('#submit-order').show();
            });

            $('#data-list').on('keydown', 'input[class="edit-order-input"]', function() {
                if (!$(this).val() || (Number($(this).val()) <= 100 && Number($(this).val()) >= 1))
                    $(this).data("old", $(this).val());
            });

            $('#data-list').on('keyup', 'input[class="edit-order-input"]', function() {
                if (!$(this).val() || (Number($(this).val()) <= 100 && Number($(this).val()) >= 1));
                else $(this).val($(this).data("old"));
            });

            $('#submit-order').click(function(e) {
                $('input[class="edit-order-input"]').each(function(index, item) {
                    var id = $(this).data("id");
                    var order = Number($(this).val());
                    for (var i = 0; i < tabbarList.length; i++) {
                        if (tabbarList[i].id == id) {
                            tabbarList[i].order = order;
                            break;
                        }
                    }
                });

                API.System.updateValue({
                    data: {
                        key: "miniprogramTabbar",
                        value: JSON.stringify(tabbarList),
                    },
                    beforeCallback: function() {
                        $.LoadingOverlay("show", {
                            text: "更新导航栏顺序中..."
                        });
                    },
                    successCallback: function(data) {
                        alert("更新导航栏顺序成功");
                        location.reload();
                    },
                    errorCallback: function() {
                        alert("更新导航栏顺序失败");
                    },
                    completeCallback: function() {
                        $.LoadingOverlay("hide");
                    }
                });
            });

            $('#add').click(function(e) {
                $("#order").val(null);
                $("#order").removeAttr("readonly");
                $("#title").val(null);
                $("#title").removeAttr("readonly");
                $("#icon").val(null);
                $("#icon").removeAttr("readonly");
                $("#jumpType").val(null);
                $("#jumpType").removeAttr("readonly");
                $("#path").val(null);
                $("#path").removeAttr("readonly");

                $('#submit-tabbar').removeData('id');
                $('#submit-tabbar').show();
                $("#tabbarModal").modal("toggle");
            });

            $('#submit-tabbar').click(function(e) {
                var id = $(this).data('id');
                // 表单处理
                var form = $('#form').serializeArray();
                var formDict = {};
                // 数据检查
                for (var i = 0; i < form.length; i++) {
                    var item = form[i];
                    var name = item.name;
                    var value = item.value.trim();
                    if (value == null || value == '') {
                        return alert('请补全所有输入项');
                    }
                    formDict[name] = value;
                }

                if (id == undefined) {
                    formDict.id = tabbarList.length + 1;
                } else {
                    formDict.id = id;
                }
                formDict.order = Number(formDict.order);

                for (var i = 0; i < tabbarList.length; i++) {
                    if (tabbarList[i].id == id) {
                        for (var name in formDict) {
                            tabbarList[i][name] = formDict[name];
                        }
                        break;
                    }
                }

                API.System.updateValue({
                    data: {
                        key: "miniprogramTabbar",
                        value: JSON.stringify(tabbarList),
                    },
                    beforeCallback: function() {
                        $.LoadingOverlay("show", {
                            text: "更新导航栏中..."
                        });
                    },
                    successCallback: function(data) {
                        alert("更新导航栏成功");
                        location.reload();
                    },
                    errorCallback: function() {
                        alert("更新导航栏失败");
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