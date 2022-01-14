<?php
require_once dirname(__FILE__) . "/module/header.php";

// 频道配置列表
$channelList = System::getInstance()->getValue("channel");
?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <title><?php echo $web["webName"]; ?>后台-图文</title>
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
        <div class="tip-item">2.频道：文章所属频道</div>
    </div>

    <div class="data-top">
        <!-- 搜索框 -->
        <div class="input-group">
            <div class="input-group-btn">
                <select id="targetColumn" class="form-control" style="width: 8rem;">
                    <option value="title" selected="selected">标题</option>
                    <option value="brief">简介</option>
                    <option value="content">内容</option>
                </select>
            </div>
            <input type="text" class="form-control" id="searchValue" placeholder="请输入关键字">
            <span class="input-group-btn">
                <button id="search" class="form-control btn btn-primary" disabled="disabled">搜索</button>
            </span>
        </div>
        <!-- 新建按钮 -->
        <a href="editArticle.php" target="_blank" class="btn btn-primary" role="button">发表文章</a>
    </div>

    <!-- 列表 -->
    <div id="data-list"></div>

    <!-- 页码 -->
    <div id="data-page"></div>

    <script src="static/js/jquery.min.js"></script>
    <!-- https://www.runoob.com/bootstrap/bootstrap-tutorial.html -->
    <script src="static/js/bootstrap.min.js"></script>
    <!-- https://gasparesganga.com/labs/jquery-loading-overlay/#quick-demo -->
    <script src="static/js/loadingoverlay.min.js"></script>
    <!-- https://github.com/js-cookie/js-cookie -->
    <script src="static/js/js.cookie.min.js"></script>
    <script src="static/js/page.js"></script>
    <script src="static/js/api.js"></script>
    <script>
        $(function() {
            var channelList = JSON.parse('<?php echo json_encode($channelList); ?>');
            var channelNameDict = {};
            for (var i = 0; i < channelList.length; i++) {
                channelNameDict[channelList[i].id] = channelList[i].title;
            }

            var articleList = [];

            setPageId('data-page');
            setPageFunction(getArticleListByPage);
            renderPage();

            // 渲染文章列表
            function renderArticleList(articleList) {
                var result = `<div class="data-item">
                                    <div class="data-item-left">频道 / 顺序</div>
                                    <div class="data-item-center">文章</div>
                                    <div class="data-item-right"></div>
                                </div>`;
                $.each(articleList, function(_, item) {
                    var images = JSON.parse(item.images);
                    result += `<div class="data-item">
                                    <div class="data-item-left text-danger">
                                        ${channelNameDict[item.channel_id]} / ${item.order==0 ? '默认' : item.order}
                                    </div>
                                    <div class="data-item-center">
                                        <div class="data-images">`;
                    for (var i = 0; i < images.length; i++) {
                        result += `         <img src="${images[i].url}" alt="${images[i].name}">`;
                    }
                    result += `         </div>
                                        <div class="data-title">${item.title}</div>
                                    </div>
                                    <div class="data-item-right">
                                        <a href="readArticle.php?id=${item.id}" target="_blank" class="btn btn-success" role="button">查看</a>
                                        <a href="editArticle.php?id=${item.id}" target="_blank" class="btn btn-warning" role="button">编辑</a>
                                        <button class="btn btn-danger change-show" data-id=${item.id} data-show=${item.show}>${item.show == 0 ? '转为展示' : '转为下架'}</button>
                                    </div>
                                </div>`;
                });
                $("#data-list").empty();
                $("#data-list").html(result);
            };

            // 搜索文章
            function searchArticle() {
                API.Article.search({
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
                        articleList = data.data;
                        renderArticleList(articleList);
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
            searchArticle();

            function getArticleListByPage() {
                // 如果搜索框有内容，则应该是搜索过的内容分页
                if ($("#searchValue").val().trim()) {
                    $("#search").click();
                } else {
                    searchArticle();
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
                searchArticle();
            });

            // 改变文章展示状态
            $('#data-list').on('click', '.change-show', function() {
                API.Article.changeShow({
                    data: {
                        "id": $(this).attr('data-id'),
                        "show": (parseInt($(this).attr('data-show')) + 1) % 2,
                    },
                    beforeCallback: function() {
                        $.LoadingOverlay("show", {
                            text: "修改中..."
                        });
                    },
                    successCallback: function(data) {
                        location.reload();
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