<?php
require_once dirname(__FILE__) . "/module/header.php";
require_once dirname(__FILE__) . "/model/Video.php";

$action = "create";
$actionTitle = "创建";
// 修改视频
if (!empty($_GET) && isset($_GET["id"])) {
    $action = "update";
    $actionTitle = "修改";
    $video = Video::getInstance()->getOneBy(["id"], [$_GET["id"]]);
}

// 频道配置列表
$channelSettingList = System::getInstance()->getValue("channel");
// 展示配置列表
$showSettingList = System::getInstance()->getValue("worksShow");
?>
<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <title><?php echo $web["webName"]; ?>后台-发表视频</title>
    <link rel="shortcut icon" href="static/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="static/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="static/css/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="static/css/bootstrap-fileinput/fileinput.min.css">
    <link rel="stylesheet" type="text/css" href="static/css/filepond/filepond.min.css">
    <link rel="stylesheet" type="text/css" href="static/css/filepond/filepond-plugin-media-preview.min.css">
    <link rel="stylesheet" type="text/css" href="static/css/video-js.min.css">
    <link rel="stylesheet" type="text/css" href="static/css/my.css">
    <style>
        .edit-box {
            margin-top: 2rem;
            margin-bottom: 10rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .edit-box form {
            width: 100rem;
        }

        #video-list {
            display: none;
        }

        #video-list .video-item {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        #replace-button {
            display: none;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <!-- 导航栏 -->
    <?php require_once dirname(__FILE__) . "/module/navbar.php"; ?>
    <div class="edit-box">
        <form role="form" id="form">
            <div class="form-group">
                <label for="images">封面</label>
                <input type="file" id="imageUploader" name="images" multiple accept="image/jpeg,image/jpg,image/png">
                <p class="help-block"><span class="text-danger">封面</span>和<span class="text-danger">视频</span>在<span class="text-danger">【数量】</span>和<span class="text-danger">【顺序】</span>上要对应</p>
            </div>
            <div class="form-group">
                <label for="name">标题</label>
                <input type="text" class="form-control" name="title" placeholder="请输入标题" value="<?php echo isset($video) ? $video["title"] : ""; ?>">
            </div>
            <div class="form-group">
                <label for="videos">视频</label>
                <div id="video-list"></div>
                <input type="file" id="videoUploader" name="videos" accept="video/mp4,video/avi">
            </div>
            <div class="form-group">
                <label for="brief">简介</label>
                <textarea class="form-control" rows="3" name="brief" placeholder="请输入简介"><?php echo isset($video) ? $video["brief"] : ""; ?></textarea>
            </div>
            <div class="form-group">
                <label for="order">顺序</label>
                <input type="number" class="form-control" name="order" placeholder="请输入顺序，数字越大，排得越前" value="<?php echo isset($video) ? $video["order"] : "0"; ?>">
            </div>
            <div class="form-group">
                <label for="channelId">频道</label>
                <select id="channelIdSelect" class="form-control" name="channelId">
                    <?php for ($i = 0, $len = count($channelSettingList); $i < $len; $i++) { ?>
                        <option value="<?php echo $channelSettingList[$i]["id"]; ?>"><?php echo $channelSettingList[$i]["title"]; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="show">是否展示</label>
                <select id="showSelect" class="form-control" name="show">
                    <?php for ($i = 0, $len = count($showSettingList); $i < $len; $i++) { ?>
                        <option value="<?php echo $showSettingList[$i]["value"]; ?>"><?php echo $showSettingList[$i]["title"]; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <button id="replace-button" class="form-control btn btn-warning" onclick="replaceOldVideo">替换原视频</button>
                <button type="submit" class="form-control btn btn-success">提交</button>
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
    <!-- https://github.com/kartik-v/bootstrap-fileinput -->
    <script src="static/js/bootstrap-fileinput/plugins/piexif.min.js"></script>
    <script src="static/js/bootstrap-fileinput/plugins/sortable.min.js"></script>
    <script src="static/js/bootstrap-fileinput/fileinput.min.js"></script>
    <script src="static/js/bootstrap-fileinput/themes/fas/theme.min.js"></script>
    <script src="static/js/bootstrap-fileinput/locales/zh.js"></script>
    <!-- https://github.com/pqina/filepond -->
    <!-- https://pqina.nl/filepond/docs -->
    <script src="static/js/filepond/filepond.min.js"></script>
    <!-- https://github.com/nielsboogaard/filepond-plugin-media-preview -->
    <script src="static/js/filepond/filepond-plugin-media-preview.min.js"></script>
    <script src="static/js/filepond/filepond-plugin-file-validate-size.min.js"></script>
    <script src="static/js/filepond/filepond-plugin-file-validate-type.min.js"></script>
    <!-- https://docs.videojs.com -->
    <script src="static/js/video/video.min.js"></script>
    <script src="static/js/video/lang/zh-CN.js"></script>
    <script src="static/js/api.js"></script>
    <script>
        $(function() {
            var videoId = <?php echo isset($video) ? $video["id"] : 0; ?>;
            $("#channelIdSelect").val(<?php echo isset($video) ? $video["channel_id"] : 1; ?>);
            $("#showSelect").val(<?php echo isset($video) ? $video["show"] : 0; ?>);

            // --------------------------图片上传配置START-----------------------------------
            var uploadImageSelect = 0;
            var uploadImage = JSON.parse('<?php echo isset($video) ? $video["images"] : "[]"; ?>');
            // 初始图像数据
            var initialImage = [];
            var initialImageConfig = [];
            for (var i = 0; i < uploadImage.length; i++) {
                if (uploadImage[i].name.indexOf("mp4") > 0) {
                    initialImage.push(`<video src="${API.BaseUrl + uploadImage[i].url}" style="width: auto">`);
                } else {
                    initialImage.push(`<img src="${API.BaseUrl + uploadImage[i].url}" class="kv-preview-data file-preview-image" style="width: auto; height: auto; max-width: 100%; max-height: 100%; image-orientation: from-image;">`);
                }
                initialImageConfig.push({
                    caption: uploadImage[i].name,
                    key: uploadImage[i].name,
                    extra: {
                        url: uploadImage[i].url,
                    }
                });
            }
            console.debug(uploadImage);
            console.debug(initialImage);
            $('#imageUploader').fileinput({
                    uploadUrl: 'controller/Upload.php?action=create&dir=image', //上传地址
                    deleteUrl: 'controller/Upload.php?action=delete', //删除地址
                    language: 'zh', //设置语言
                    initialPreview: initialImage, //初始图像数据
                    initialPreviewConfig: initialImageConfig,
                    browseOnZoneClick: true, //选区可按
                    showCaption: false, //是否显示标题
                    showRemove: true, //是否显示删除按钮
                    showPreview: true, //是否显示文件的预览图
                    showClose: false, //是否显示关闭按钮
                    showUpload: true, // 是否显示上传按钮
                    overwriteInitial: false, //不覆盖已存在的图片
                    autoReplace: true, //再次选择,覆盖之前图片内容
                    maxFileCount: 1, //允许最大上传文件的数量
                    minFileCount: 0, //允许最小上传文件的数量
                    msgFilesTooMany: '只允许上传最多{m}个文件！',
                    maxFileSize: 200, //单位为kb，如果为0表示不限制文件大小
                    allowedFileExtensions: ['jpeg', 'jpg', 'png'], //接收的文件后缀
                    previewFileType: 'image', //预览文件类型，内置['image', 'html', 'text', 'video', 'audio', 'flash', 'object', 'other']等格式
                    fileActionSettings: {
                        downloadClass: 'hidden',
                        zoomClass: 'hidden'
                    },
                })
                .on('fileselect', function(event, numFiles, label) {
                    // 避免封面图没上传就提交创建视频了
                    console.debug('文件已选择', numFiles);
                    uploadImageSelect = numFiles;
                })
                .on('fileuploaded', function(event, data, previewId, index) {
                    console.debug(data);
                    // 一次上传一张的回调
                    uploadImage.push(data.response.data[0]);
                    uploadImageSelect--;
                })
                .on('filedeleted', function(event, key, xhr) {
                    console.debug('删除文件', xhr);
                    if (!xhr.responseJSON.errno) {
                        console.debug('删除成功', key);
                    } else {
                        console.debug('删除失败，可能服务器资源已经不存在', key);
                    }
                    // 更新数据库记录
                    deleteVideoImage(key);
                });
            // --------------------------图片上传配置END-----------------------------------

            // --------------------------视频上传配置START-----------------------------------
            var videoUploader = document.getElementById('videoUploader');
            // 初始视频数据
            var uploadVideo = JSON.parse('<?php echo isset($video) ? $video["videos"] : "[]"; ?>');
            // 渲染视频列表
            renderVideoList(uploadVideo);

            // 注册插件
            FilePond.registerPlugin(
                FilePondPluginFileValidateSize, // 文件大小限制
                FilePondPluginFileValidateType, // 文件类型验证
                FilePondPluginMediaPreview //视频预览
            );

            var pond = FilePond.create(videoUploader, {
                // https://pqina.nl/filepond/docs/api/server/#url
                server: {
                    process: 'controller/Upload.php?action=create&dir=video', //上传地址，默认POST
                    revert: 'controller/Upload.php?action=deleteVideo', //删除地址，默认DELETE
                },
                // https://pqina.nl/filepond/docs/api/instance/properties/#files
                // files: [], //初始视频数据，不能用，查文档也不清楚怎么用
                allowMultiple: false, //只允许上传一个
                allowFileTypeValidation: true, //文件类型验证
                acceptedFileTypes: ['video/mp4', 'video/avi'], //支持的文件类型
                allowFileSizeValidation: true, // 启用文件大小限制
                maxFileSize: '100MB', // 单个文件大小限制
                maxTotalFileSize: '100MB', // 所有文件的总大小限制
                labelMaxFileSize: '超出大小限制，最大文件大小为{filesize}',
                labelFileLoading: "初始化...",
                labelFileLoadError: '初始过程中出现错误',
                labelFileProcessing: "上传中...",
                labelFileProcessingError: '上传过程中出现错误',
                labelFileProcessingRevertError: 'Error during revert',
                labelTapToRetry: "重新尝试",
                labelTapToCancel: "点击取消上传",
                labelFileProcessingComplete: "上传已完成",
                labelFileProcessingAborted: "上传被中断",
                labelTapToUndo: "点击右上角删除",
                labelIdle: '拖拽文件 或者 <span class="filepond--label-action"> 浏览本地文件 </span>',
                labelInvalidField: '包含无效文件',
                labelFileWaitingForSize: '文件大小等待计算中',
                labelFileSizeNotAvailable: '文件大小超出限制',
                labelFileRemoveError: '移除过程中出现错误',
                labelButtonRemoveItem: '移除',
                labelButtonAbortItemLoad: '中断',
                labelButtonRetryItemLoad: '重试',
                labelButtonAbortItemProcessing: '取消',
                labelButtonUndoItemProcessing: '重试',
                labelButtonRetryItemProcessing: '重试',
                labelButtonProcessItem: '上传',
            });
            pond.onprocessfile = (error, file) => {
                console.debug('onprocessfile', error, file);
                if (error) {
                    return alert('上传失败');
                }
                // 已存在旧视频，显示替换按钮
                if (uploadVideo.length > 0) {
                    $('#replace-button').show();
                }
                // 服务器的响应{errno:x,data:xx}
                var json = JSON.parse(file.serverId);
                var data = json.data[0];
                // 添加上传控件的文件id，方便之后删除文件用
                data.id = file.id;
                // 是否已在数据库保存
                data.existInDB = false;
                uploadVideo.push(data);
            };
            pond.onremovefile = (error, file) => {
                console.debug('removefile', error, file);
                if (error) {
                    return alert('删除失败');
                }
                // 更新本地和数据库记录
                deleteVideo('id', file.id);
            };
            $('.filepond--credits').hide();
            // --------------------------视频上传配置END-----------------------------------
            // 渲染已上传视频列表
            function renderVideoList(videoList) {
                var result = "";
                for (var i = 0; i < videoList.length; i++) {
                    // result += `<video controls preload="auto" loop poster="${uploadImage.length > 0 ? uploadImage[i].url : ''}">
                    result += `<video controls preload="auto" loop>
                                    <source src="${videoList[i].url}"></source>
                                    <p class="vjs-no-js">
                                        请启用 JavaScript 以支持观看该视频，或者考虑升级浏览器以<a href="https://videojs.com/html5-video-support/" target="_blank">支持 HTML5 视频播放</a>
                                    </p>
                                </video>`;
                }
                $("#video-list").empty();
                $("#video-list").html(result)
                $("#video-list").show();
            };

            // 更新删除视频
            function deleteVideo(key, targetKey, msg = '删除') {
                // 更新本地记录
                for (var i = 0; i < uploadVideo.length; i++) {
                    if (uploadVideo[i][key] == targetKey) {
                        var existInDB = uploadVideo[i].existInDB;
                        uploadVideo.splice(i, 1);
                        if (!existInDB) {
                            // 已上传，但是没提交更新
                            return;
                        }
                        break;
                    }
                }
                if (videoId == 0) {
                    return;
                }
                // 更新数据库记录
                API.Video.deleteVideo({
                    data: {
                        id: videoId,
                        videos: JSON.stringify(uploadVideo),
                    },
                    beforeCallback: function() {
                        $.LoadingOverlay("show", {
                            text: `${msg}视频中...`
                        });
                    },
                    successCallback: function(data) {
                        alert(`${msg}成功`);
                    },
                    errorCallback: function() {
                        alert(`${msg}失败`);
                    },
                    completeCallback: function() {
                        $.LoadingOverlay("hide");
                    }
                });
            }

            // 更新删除视频的封面
            function deleteVideoImage(name) {
                // 更新本地记录
                for (var i = 0; i < uploadImage.length; i++) {
                    if (uploadImage[i].name == name) {
                        uploadImage.splice(i, 1);
                        break;
                    }
                }
                if (articleId == 0) {
                    return;
                }
                // 更新数据库记录
                API.Article.deleteImage({
                    data: {
                        id: articleId,
                        images: JSON.stringify(uploadImage),
                    },
                    beforeCallback: function() {
                        $.LoadingOverlay("show", {
                            text: "删除视频封面中..."
                        });
                    },
                    successCallback: function(data) {
                        console.debug('删除视频封面成功')
                        // alert("删除成功");
                        // location.reload();
                    },
                    errorCallback: function() {
                        alert("删除失败");
                    },
                    completeCallback: function() {
                        $.LoadingOverlay("hide");
                    }
                });
            }

            replaceOldVideo = function() {
                if (confirm('确定替换为新视频吗？')) {
                    // 删除视频列表中旧的视频
                    var video = uploadVideo.shift();
                    console.debug('replaceOldVideo', video);
                    deleteVideo('name', video.name, '替换');
                    // 重新渲染视频列表
                    renderVideoList(uploadVideo);
                } else {
                    $.LoadingOverlay("show", {
                        image: '',
                        text: '如不需要替换新视频，请删除',
                    });
                    setTimeout(function() {
                        $.LoadingOverlay("hide");
                    }, 3000);
                    // 删除上传控件中已经上传的视频
                    // https://pqina.nl/filepond/docs/api/instance/methods/#removing-files
                    // pond.removeFile(video.id);
                }
            }

            $('#form').submit(function(e) {
                // 禁止form默认的提交事件
                e.preventDefault();
                // 已上传视频，但是没替换旧视频
                if (uploadVideo.length > 1) {
                    replaceOldVideo();
                }
                // 表单处理
                //[{name: 'a1', value: 'xx'},{name: 'a2', value: 'xx'}...]
                var form = $('#form').serializeArray();
                var formDict = {};
                // 数据检查
                for (var i = 0; i < form.length; i++) {
                    var item = form[i];
                    var name = item.name;
                    var value = item.value.trim();
                    if (value == null || value == '') {
                        return alert('请补全内容');
                    }
                    formDict[name] = value;
                }
                if (uploadVideo.length == 0) {
                    return alert('请上传视频');
                }
                if (uploadImageSelect > 0) {
                    if (confirm('封面图未全部上传，确定提交吗？') == false) {
                        return;
                    }
                }
                // 视频进数据库标记
                for (var i = 0; i < uploadVideo.length; i++) {
                    uploadVideo[i].existInDB = true;
                }

                formDict['id'] = videoId;
                formDict['images'] = JSON.stringify(uploadImage);
                formDict['videos'] = JSON.stringify(uploadVideo);
                console.debug(formDict);

                API.Video.<?php echo $action; ?>({
                    data: formDict,
                    beforeCallback: function() {
                        $.LoadingOverlay("show", {
                            text: "<?php echo $actionTitle; ?>视频中..."
                        });
                    },
                    successCallback: function(data) {
                        alert("<?php echo $actionTitle; ?>成功");
                        location.reload();
                    },
                    errorCallback: function() {
                        alert("<?php echo $actionTitle; ?>失败");
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