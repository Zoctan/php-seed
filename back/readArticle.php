<?php
require_once dirname(__FILE__) . "/module/header.php";
require_once dirname(__FILE__) . "/model/Article.php";

if (empty($_GET) || !isset($_GET["id"])) {
    return Util::alert2("文章ID为空", "index.php");
}

$article = Article::getInstance()->getOneBy(["id"], [$_GET["id"]]);
if (empty($article)) {
    return Util::alert2("文章不存在", "index.php");
}
$images = json_decode($article["images"], true);
?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <title><?php echo $web["webName"]; ?>后台-查看文章-<?php echo $article["title"]; ?></title>
    <link rel="shortcut icon" href="static/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="static/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="static/css/my.css">
    <style>
        .box {
            border: dashed;
            margin-bottom: 2rem;
            border: 2px dashed lightblue;
        }

        .box .des {
            font-weight: bold;
            font-size: 2rem;
        }

        .fix-btn {
            width: 6rem;
            height: 6rem;
            border-radius: 50%;
            position: fixed;
            right: 5rem;
            bottom: 5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .article-images {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .article-images img {
            width: 30rem;
            height: 30rem;
            margin: 2rem;
        }

        .article-title {
            text-align: center;
        }

        .article-brief,
        .article-content {
            margin: 2rem auto;
            width: 100rem;
        }
    </style>
</head>

<body>
    <!-- 导航栏 -->
    <?php require_once dirname(__FILE__) . "/module/navbar.php"; ?>
    <div class="box">
        <div class="des">封面</div>
        <div class="article-images">
            <?php
            for ($i = 0, $len = count($images); $i < $len; $i++) {
            ?>
                <img src="<?php echo $images[$i]["url"]; ?>" alt="<?php echo $images[$i]["name"]; ?>">
            <?php } ?>
        </div>
    </div>
    <div class="box">
        <div class="des">标题</div>
        <h3 class="article-title"><?php echo $article["title"]; ?></h3>
    </div>
    <div class="box">
        <div class="des">简介</div>
        <div class="article-brief"><?php echo $article["brief"]; ?></div>
    </div>
    <div class="box">
        <div class="des">正文</div>
        <div class="article-content"><?php echo $article["content"]; ?></div>
    </div>
    <a href="editArticle.php?id=<?php echo $article["id"]; ?>" target="_blank" class="fix-btn text-white bg-warning">编辑</a>

    <script src="static/js/jquery.min.js"></script>
    <!-- https://www.runoob.com/bootstrap/bootstrap-tutorial.html -->
    <script src="static/js/bootstrap.min.js"></script>
    <!-- https://gasparesganga.com/labs/jquery-loading-overlay/#quick-demo -->
    <script src="static/js/loadingoverlay.min.js"></script>
    <script>
        $(function() {

        });
    </script>
</body>

</html>