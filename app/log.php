<?php
require_once dirname(__FILE__) . "/module/header.php";
?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <title><?php echo $web["webName"]; ?>后台-日志列表</title>
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
        <div class="tip-item">1.按颜色分等级：<span class="text-info">信息</span>，<span class="text-warning">警告</span>，<span class="text-danger">错误</span></div>
    </div>

    <div class="data-top">
        <!-- 搜索框 -->
        <div class="input-group">
            <div class="input-group-btn">
                <select id="targetColumn" class="form-control" style="width: 10rem;">
                    <option value="member_name" selected="selected">用户名</option>
                    <option value="content">内容</option>
                </select>
            </div>
            <input type="text" class="form-control" id="searchValue" placeholder="请输入关键字">
            <span class="input-group-btn">
                <button id="search" class="form-control btn btn-primary" disabled="disabled">搜索</button>
            </span>
        </div>
    </div>

    <!-- 列表 -->
    <div id="data-list"></div>

    <!-- 页码 -->
    <div id="data-page"></div>

    <!-- 日志框 -->
    <div class="modal fade" id="logModal" tabindex="-1" role="dialog" aria-labelledby="logModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h5 class="modal-title" id="logModalLabel">日志信息</h5>
                </div>
                <div class="modal-body">
                    <form role="form">
                        <div class="form-group">
                            <label for="level">等级</label>
                            <div id="modal-level"></div>
                        </div>
                        <div class="form-group">
                            <label for="memberId">用户ID</label>
                            <div id="modal-memberId"></div>
                        </div>
                        <div class="form-group">
                            <label for="memberName">用户名</label>
                            <div id="modal-memberName"></div>
                        </div>
                        <div class="form-group">
                            <label for="content">内容</label>
                            <div id="modal-content"></div>
                        </div>
                        <div class="form-group">
                            <label for="ip">IP</label>
                            <div id="modal-ip"></div>
                        </div>
                        <div class="form-group">
                            <label for="ipCity">IP所属城市</label>
                            <div id="modal-ipCity"></div>
                        </div>
                        <div class="form-group">
                            <label for="extra">额外信息</label>
                            <div id="modal-extra"></div>
                        </div>
                        <div class="form-group">
                            <label for="createTime">创建时间</label>
                            <div id="modal-createTime"></div>
                        </div>
                    </form>
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
            var logList = [];

            setPageId('data-page');
            setPageFunction(getLogListByPage);
            renderPage();

            // 渲染文章列表
            function renderLogList(logList) {
                var result = `<div class="data-item">
                                    <div class="data-item-left">时间</div>
                                    <div class="data-item-center">日志</div>
                                    <div class="data-item-right"></div>
                                </div>`;
                $.each(logList, function(_, item) {
                    //var textColor = ;
                    result += `<div class="data-item ${item.level == 0 ? 'text-info' : item.level == 1 ? 'text-warning' : 'text-danger'}">
                                    <div class="data-item-left">
                                        ${item.create_time}
                                    </div>
                                    <div class="data-item-center">
                                        ${item.member_name}
                                        ${item.content}
                                    </div>
                                    <div class="">
                                        <button class="btn btn-success show-log" data-id=${item.id}>查看</button>
                                        <button class="btn btn-danger delete-log" data-id=${item.id}>删除</button>
                                    </div>
                                </div>`;
                });
                $("#data-list").empty();
                $("#data-list").html(result);
            };

            // 搜索文章
            function searchLog() {
                API.Log.search({
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
                        logList = data.data;
                        renderLogList(logList);
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
            searchLog();

            function getLogListByPage() {
                // 如果搜索框有内容，则应该是搜索过的内容分页
                if ($("#searchValue").val().trim()) {
                    $("#search").click();
                } else {
                    searchLog();
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
                searchLog();
            });

            // 查看日志
            $('#data-list').on('click', '.show-log', function() {
                var id = $(this).attr('data-id');
                for (var i = 0; i < logList.length; i++) {
                    if (id == logList[i].id) {
                        $("#modal-level").text(logList[i].level == 0 ? '信息' : logList[i].level == 1 ? '警告' : '错误');
                        $("#modal-memberId").text(logList[i].member_id);
                        $("#modal-memberName").text(logList[i].member_name);
                        $("#modal-content").text(logList[i].content);
                        $("#modal-ip").text(Utils.Ip.long2ip(logList[i].ip));
                        $("#modal-ipCity").text(logList[i].ip_city);
                        $("#modal-extra").text(logList[i].extra);
                        $("#modal-createTime").text(logList[i].create_time);

                        $("#logModal").modal("toggle");
                        break;
                    }
                }
            });

            // 删除日志
            $('#data-list').on('click', '.delete-log', function() {
                if (confirm('确定删除日志？')) {
                    API.Log.delete({
                        data: {
                            "id": $(this).attr('data-id'),
                        },
                        beforeCallback: function() {
                            $.LoadingOverlay("show", {
                                text: "删除中..."
                            });
                        },
                        successCallback: function(data) {
                            location.reload();
                        },
                        errorCallback: function() {
                            alert("删除失败");
                        },
                        completeCallback: function() {
                            $.LoadingOverlay("hide");
                        }
                    });
                }
            });

        });
    </script>
</body>

</html>