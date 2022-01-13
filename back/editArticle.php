<?php
require_once dirname(__FILE__) . "/module/header.php";
require_once dirname(__FILE__) . "/model/Article.php";

$action = "create";
$actionTitle = "创建";
// 修改文章
if (!empty($_GET) && isset($_GET["id"])) {
    $action = "update";
    $actionTitle = "修改";
    $article = Article::getInstance()->getOneBy(["id"], [$_GET["id"]]);
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
    <title><?php echo $web["webName"]; ?>后台-发表文章</title>
    <link rel="shortcut icon" href="static/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="static/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="static/css/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="static/css/bootstrap-fileinput/fileinput.min.css">
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
    </style>
</head>

<body>
    <!-- 导航栏 -->
    <?php require_once dirname(__FILE__) . "/module/navbar.php"; ?>
    <div class="edit-box">
        <form role="form" id="form">
            <div class="form-group">
                <label for="images">封面</label>
                <input type="file" id="uploader" name="images" multiple accept="image/jpeg,image/jpg,image/png">
                <p class="help-block">封面最少0张，最多3张</p>
            </div>
            <div class="form-group">
                <label for="title">标题</label>
                <input type="text" class="form-control" name="title" placeholder="请输入标题" value="<?php echo isset($article) ? $article["title"] : ""; ?>">
            </div>
            <div class="form-group">
                <label for="editor">内容</label>
                <div id="editor" name="editor"></div>
            </div>
            <div class="form-group">
                <label for="brief">简介</label>
                <textarea class="form-control" rows="3" name="brief" placeholder="请输入简介"><?php echo isset($article) ? $article["brief"] : ""; ?></textarea>
            </div>
            <div class="form-group">
                <label for="order">顺序</label>
                <input type="number" class="form-control" name="order" placeholder="请输入顺序，数字越大，排得越前" value="<?php echo isset($article) ? $article["order"] : "0"; ?>">
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
    <!-- https://www.wangeditor.com/doc -->
    <script src="static/js/wangEditor.min.js"></script>
    <!-- https://github.com/kartik-v/bootstrap-fileinput -->
    <script src="static/js/bootstrap-fileinput/plugins/piexif.min.js"></script>
    <script src="static/js/bootstrap-fileinput/plugins/sortable.min.js"></script>
    <script src="static/js/bootstrap-fileinput/fileinput.min.js"></script>
    <script src="static/js/bootstrap-fileinput/themes/fas/theme.min.js"></script>
    <script src="static/js/bootstrap-fileinput/locales/zh.js"></script>
    <script src="static/js/api.js"></script>
    <script>
        $(function() {
            var articleId = <?php echo isset($article) ? $article["id"] : 0; ?>;
            $("#channelIdSelect").val(<?php echo isset($article) ? $article["channel_id"] : 1; ?>);
            $("#showSelect").val(<?php echo isset($article) ? $article["show"] : 0; ?>);

            // --------------------------图片上传配置START-----------------------------------
            var uploadImageSelect = 0;
            var uploadImage = JSON.parse('<?php echo isset($article) ? $article["images"] : "[]"; ?>');
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
            $('#uploader').fileinput({
                    uploadUrl: 'controller/Upload.php?action=create&dir=image', //上传地址
                    deleteUrl: 'controller/Upload.php?action=delete', //删除地址
                    language: 'zh', //设置语言
                    initialPreview: initialImage, //初始图像数据
                    initialPreviewConfig: initialImageConfig,
                    //initialPreview: [
                    // 图像数据
                    // 'http://lorempixel.com/800/460/business/1',
                    // 图像原生标记语言
                    // '<img src="http://lorempixel.com/800/460/business/2" class="kv-preview-data file-preview-image" style="height:160px">',
                    // 文本数据
                    // "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ut mauris ut libero fermentum feugiat eu et dui. Mauris condimentum rhoncus enim, sed semper neque vestibulum id. Nulla semper, turpis ut consequat imperdiet, enim turpis aliquet orci, eget venenatis elit sapien non ante. Aliquam neque ipsum, rhoncus id ipsum et, volutpat tincidunt augue. Maecenas dolor libero, gravida nec est at, commodo tempor massa. Sed id feugiat massa. Pellentesque at est eu ante aliquam viverra ac sed est.",
                    // pdf数据
                    // 'http://kartik-v.github.io/bootstrap-fileinput-samples/samples/pdf-sample.pdf',
                    // 视频数据
                    // "http://kartik-v.github.io/bootstrap-fileinput-samples/samples/small.mp4",
                    //],
                    browseOnZoneClick: true, //选区可按
                    showCaption: false, //是否显示标题
                    showRemove: true, //是否显示删除按钮
                    showPreview: true, //是否显示文件的预览图
                    showClose: false, //是否显示关闭按钮
                    showUpload: true, // 是否显示上传按钮
                    overwriteInitial: false, //不覆盖已存在的图片
                    autoReplace: true, //再次选择,覆盖之前图片内容
                    maxFileCount: 3, //允许最大上传文件的数量
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
                    // 避免封面图没上传就提交创建文章了
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
                    // 更新本地和数据库记录
                    deleteArticleImage(key);
                });
            // --------------------------图片上传配置END-----------------------------------

            // --------------------------富文本编辑器配置START-----------------------------------
            var E = window.wangEditor;
            var editor = new E('#editor');
            // 设置编辑区域高度为 500px
            editor.config.height = 500;
            // 配置菜单栏，设置不需要的菜单
            editor.config.excludeMenus = [
                'emoticon',
                'code',
            ];
            // 配置上传图片服务接口
            editor.config.uploadImgServer = 'controller/Upload.php?action=create&dir=image';
            // 上传图片回调
            editor.config.uploadImgHooks = {
                // 上传图片之前
                before: function(xhr) {
                    console.debug('uploadImg before', xhr);

                    // 可阻止图片上传
                    // return {
                    //     prevent: true,
                    //     msg: '需要提示给用户的错误信息'
                    // }
                },
                // 图片上传并返回了结果，图片插入已成功
                success: function(xhr) {
                    console.debug('uploadImg success', xhr);
                },
                // 图片上传并返回了结果，但图片插入时出错了
                fail: function(xhr, editor, resData) {
                    console.debug('uploadImg fail', resData);
                },
                // 上传图片出错，一般为 http 请求的错误
                error: function(xhr, editor, resData) {
                    console.debug('uploadImg error', xhr, resData);
                },
                // 上传图片超时
                timeout: function(xhr) {
                    console.debug('uploadImg timeout');
                },
                // 图片上传并返回了结果，想要自己把图片插入到编辑器中
                // 例如服务器端返回的不是 { errno: 0, data: [...] } 这种格式，可使用 customInsert
                customInsert: function(insertImgFn, result) {
                    // result 即服务端返回的接口
                    console.debug('uploadImg customInsert', result);

                    for (var i = 0; i < result.data.length; i++) {
                        // insertImgFn 可把图片插入到编辑器，传入图片 url ，执行函数即可
                        insertImgFn(result.data[i].url);
                    }
                }
            }
            // 配置上传视频服务接口
            editor.config.uploadVideoServer = 'controller/Upload.php?action=create&dir=video';
            editor.config.uploadVideoHooks = {
                // 上传视频之前
                before: function(xhr) {
                    console.debug('uploadVideo before', xhr);

                    // 可阻止视频上传
                    // return {
                    //     prevent: true,
                    //     msg: '需要提示给用户的错误信息'
                    // }
                },
                // 视频上传并返回了结果，视频插入已成功
                success: function(xhr) {
                    console.debug('uploadVideo success', xhr);
                },
                // 视频上传并返回了结果，但视频插入时出错了
                fail: function(xhr, editor, resData) {
                    console.debug('uploadVideo fail', resData);
                },
                // 上传视频出错，一般为 http 请求的错误
                error: function(xhr, editor, resData) {
                    console.debug('uploadVideo error', xhr, resData);
                },
                // 上传视频超时
                timeout: function(xhr) {
                    console.debug('uploadVideo timeout');
                },
                // 视频上传并返回了结果，想要自己把视频插入到编辑器中
                // 例如服务器端返回的不是 { errno: 0, data: { url : '.....'} } 这种格式，可使用 customInsert
                customInsert: function(insertVideoFn, result) {
                    // result 即服务端返回的接口
                    console.debug('uploadVideo customInsert', result);

                    for (var i = 0; i < result.data.length; i++) {
                        // insertVideoFn 可把视频插入到编辑器，传入视频 url ，执行函数即可
                        insertVideoFn(result.data[i].url);
                    }
                }
            }
            editor.create();
            editor.txt.html('<?php echo isset($article) ? $article["content"] : ""; ?>');
            // --------------------------富文本编辑器配置END-----------------------------------

            // 更新删除文章的封面
            function deleteArticleImage(name) {
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
                            text: "删除文章封面中..."
                        });
                    },
                    successCallback: function(data) {
                        console.debug('删除文章封面成功')
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

            $('#form').submit(function(e) {
                // 禁止form默认的提交事件
                e.preventDefault();
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
                        return alert('请补全文章');
                    }
                    formDict[name] = value;
                }
                var content = editor.txt.html();
                if (content == null || content == '') {
                    return alert('请补全内容');
                }
                if (uploadImageSelect > 0) {
                    if (confirm('封面图未全部上传，确定提交吗？') == false) {
                        return;
                    }
                }
                if (uploadImage.length == 0) {
                    return alert('请补全封面图');
                }

                formDict['id'] = articleId;
                formDict['images'] = JSON.stringify(uploadImage);
                formDict['content'] = content;
                console.debug(formDict);

                API.Article.<?php echo $action; ?>({
                    data: formDict,
                    beforeCallback: function() {
                        $.LoadingOverlay("show", {
                            text: "<?php echo $actionTitle; ?>文章中..."
                        });
                    },
                    successCallback: function(data) {
                        alert("<?php echo $actionTitle; ?>成功");
                        location.href = `readArticle.php?id=${data}`;
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