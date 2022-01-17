<?php
require_once dirname(__FILE__) . "/module/header.php";
?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <title><?php echo $web["webName"]; ?>后台-系统键值对</title>
    <link rel="shortcut icon" href="static/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="static/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="static/css/my.css">
    <link rel="stylesheet" type="text/css" href="static/css/page.css">
    <link rel="stylesheet" type="text/css" href="static/css/jsoneditor/jsoneditor.min.css">
</head>

<body>
    <!-- 导航栏 -->
    <?php require_once dirname(__FILE__) . "/module/navbar.php"; ?>
    <div class="data-top">
        <button id="add" class="btn btn-primary">添加</button>
    </div>

    <div id="data-list"></div>

    <div class="modal fade" id="systemModal" tabindex="-1" role="dialog" aria-labelledby="systemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="systemModalLabel">系统配置</h4>
                </div>
                <div class="modal-body">
                    <form role="form" id="form">
                        <div class="form-group">
                            <label for="description">描述</label>
                            <input id="description" type="text" class="form-control" name="description" placeholder="请输入描述">
                        </div>
                        <div class="form-group">
                            <label for="key">键</label>
                            <input id="key" type="text" class="form-control" name="key" placeholder="请输入键">
                        </div>
                    </form>
                    <div id="jsoneditor" style="height: 600px;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                    <button id="submit-system" type="button" class="btn btn-primary">提交</button>
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
    <!-- https://github.com/josdejong/jsoneditor/blob/master/docs/api.md -->
    <script src="static/js/jsoneditor/jsoneditor.min.js"></script>
    <script src="static/js/api.js"></script>
    <script>
        $(function() {
            // json编辑器
            var jsoneditor = new JSONEditor(document.getElementById("jsoneditor"));

            var systemList = [];
            // 渲染系统键值对列表
            function renderSystemList(systemList) {
                var result = `<div class="data-item">
                                    <div class="data-item-left">说明</div>
                                    <div class="data-item-center">键</div>
                                    <div class="data-item-right"></div>
                                </div>`;
                $.each(systemList, function(_, item) {
                    result += `<div class="data-item">
                                    <div class="data-item-left">
                                        ${item.description}
                                    </div>
                                    <div class="data-item-center">
                                        ${item.key}
                                    </div>
                                    <div class="data-item-right">
                                        <button class="btn btn-success show-system" data-id="${item.id}">查看</button>
                                        <button class="btn btn-warning edit-system" data-id="${item.id}">编辑</button>
                                        <button class="btn btn-danger delete-system" data-id="${item.id}">删除</button>
                                    </div>
                                </div>`;
                });
                $("#data-list").empty();
                $("#data-list").html(result);
            }

            // 获取所有系统配置
            function getAllSystem() {
                API.System.getAll({
                    beforeCallback: function() {
                        $.LoadingOverlay("show", {
                            text: "获取中..."
                        });
                    },
                    successCallback: function(data) {
                        systemList = data;
                        renderSystemList(systemList);
                    },
                    errorCallback: function() {
                        alert("获取失败");
                    },
                    completeCallback: function() {
                        $.LoadingOverlay("hide");
                    }
                });
            };
            getAllSystem();

            $('#data-list').on('click', '.show-system', function() {
                var id = $(this).data('id');
                for (var i = 0; i < systemList.length; i++) {
                    if (systemList[i].id == id) {
                        $("#description").val(systemList[i].description);
                        $("#description").attr("readonly", "readonly");
                        $("#key").val(systemList[i].key);
                        $("#key").attr("readonly", "readonly");
                        jsoneditor.setMode('preview');
                        jsoneditor.set(JSON.parse(systemList[i].value));
                        $('#submit-system').removeData('id');
                        $('#submit-system').hide();
                        break;
                    }
                }
                $("#systemModal").modal("toggle");
            });

            $('#data-list').on('click', '.edit-system', function() {
                var id = $(this).data('id');
                for (var i = 0; i < systemList.length; i++) {
                    if (systemList[i].id == id) {
                        $("#description").val(systemList[i].description);
                        $("#description").removeAttr("readonly");
                        $("#key").val(systemList[i].key);
                        $("#key").removeAttr("readonly");
                        jsoneditor.setMode('code');
                        jsoneditor.set(JSON.parse(systemList[i].value));
                        $('#submit-system').data('id', id);
                        $('#submit-system').show();
                        break;
                    }
                }
                $("#systemModal").modal("toggle");
            });

            $('#data-list').on('click', '.delete-system', function() {
                var id = $(this).data('id');
                for (var i = 0; i < systemList.length; i++) {
                    if (systemList[i].id == id) {
                        if (confirm(`确定删除${systemList[i].key}吗？`)) {
                            API.System.delete({
                                data: {
                                    id: id
                                },
                                beforeCallback: function() {
                                    $.LoadingOverlay("show", {
                                        text: '删除系统键值对中...'
                                    });
                                },
                                successCallback: function(data) {
                                    alert('删除系统键值成功');
                                    location.reload();
                                },
                                errorCallback: function() {
                                    alert('删除系统键值失败');
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

            $('#add').click(function(e) {
                $("#description").val(null);
                $("#description").removeAttr("readonly");
                $("#key").val(null);
                $("#key").removeAttr("readonly");
                jsoneditor.setMode('code');
                jsoneditor.set({});
                $('#submit-system').removeData('id');
                $('#submit-system').show();
                $("#systemModal").modal("toggle");
            });

            $('#submit-system').click(function(e) {
                var id = $(this).data('id');
                var requestTip = '更新';
                var requestMethod = API.System.update;
                var jsonValue = jsoneditor.getText();
                if (jsonValue == null) {
                    return alert('请补全值');
                }
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
                    if (name == 'key') {
                        for (var i = 0; i < systemList.length; i++) {
                            if (systemList[i].key == value) {
                                if (id == undefined) {
                                    // 添加时
                                    return alert('已存在相同的键');
                                } else {
                                    // 更新时
                                    if (systemList[i].id != id) {
                                        return alert('已存在相同的键');
                                    }
                                }
                            }

                        }
                    }
                    formDict[name] = value;
                }
                formDict.value = jsonValue;

                if (id == undefined) {
                    formDict.id = systemList.length + 1;
                    requestTip = '添加';
                    requestMethod = API.System.create;
                } else {
                    formDict.id = id;
                    requestTip = '更新';
                    requestMethod = API.System.update;
                }

                requestMethod({
                    data: formDict,
                    beforeCallback: function() {
                        $.LoadingOverlay("show", {
                            text: `${requestTip}系统键值对中...`
                        });
                    },
                    successCallback: function(data) {
                        alert(`${requestTip}系统键值成功`);
                        location.reload();
                    },
                    errorCallback: function() {
                        alert(`${requestTip}系统键值失败`);
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