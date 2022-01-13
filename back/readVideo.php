<?php
require_once dirname(__FILE__) . "/module/header.php";
require_once dirname(__FILE__) . "/model/Video.php";

if (empty($_GET) || !isset($_GET["id"])) {
    return Util::alert2("视频ID为空", "index.php");
}

$video = Video::getInstance()->getOneBy(["id"], [$_GET["id"]]);
if (empty($video)) {
    return Util::alert2("视频不存在", "index.php");
}
$images = json_decode($video["images"], true);
$videos = json_decode($video["videos"], true);
?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <title><?php echo $web["webName"]; ?>后台-查看视频-<?php echo $video["title"]; ?></title>
    <link rel="shortcut icon" href="static/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="static/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="static/css/video-js.min.css">
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

        .video-images {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .video-images img {
            width: 30rem;
            height: 30rem;
            margin: 2rem;
        }

        .video-title {
            text-align: center;
        }

        .video-brief,
        .video-list {
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
        <div class="video-images">
            <?php
            for ($i = 0, $len = count($images); $i < $len; $i++) {
            ?>
                <img src="<?php echo $images[$i]["url"]; ?>" alt="<?php echo $images[$i]["name"]; ?>">
            <?php } ?>
        </div>
    </div>
    <div class="box">
        <div class="des">标题</div>
        <h3 class="video-title"><?php echo $video["title"]; ?></h3>
    </div>
    <div class="box">
        <div class="des">简介</div>
        <div class="video-brief"><?php echo $video["brief"]; ?></div>
    </div>
    <div class="box">
        <div class="des">视频</div>
        <div class="video-list">
            <?php
            for ($i = 0, $len = count($videos); $i < $len; $i++) {
            ?>
                <video controls preload="auto" loop poster="<?php echo !empty($images) ? $images[$i]["url"] : ""; ?>">
                    <source src="<?php echo $videos[$i]["url"]; ?>">
                    </source>
                    <p class="vjs-no-js">
                        请启用 JavaScript 以支持观看该视频，或者考虑升级浏览器以<a href="https://videojs.com/html5-video-support/" target="_blank">支持 HTML5 视频播放</a>
                    </p>
                </video>
            <?php } ?>
        </div>
    </div>
    <a href="editVideo.php?id=<?php echo $video["id"]; ?>" target="_blank" class="fix-btn text-white bg-warning">编辑</a>

    <script src="static/js/jquery.min.js"></script>
    <!-- https://www.runoob.com/bootstrap/bootstrap-tutorial.html -->
    <script src="static/js/bootstrap.min.js"></script>
    <!-- https://gasparesganga.com/labs/jquery-loading-overlay/#quick-demo -->
    <script src="static/js/loadingoverlay.min.js"></script>
    <!-- https://docs.videojs.com -->
    <script src="static/js/video/video.min.js"></script>
    <script src="static/js/video/lang/zh-CN.js"></script>
    <script>
        $(function() {

        });
    </script>
</body>

</html>