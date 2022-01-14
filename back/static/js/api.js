/* 接口 */
(function () {
    var API = API || {};

    // 基础请求
    API.Request = function (methodType, url, args = {}) {
        // 设置回调函数
        args.beforeCallback = args.beforeCallback || console.debug('request args', args);
        args.successCallback = args.successCallback || (response => console.debug('request success', response));
        args.errorCallback = args.errorCallback || (response => console.debug('request error', response));
        args.completeCallback = args.completeCallback || (response => console.debug('request complete', response));
        // 请求
        args.beforeCallback && args.beforeCallback();
        $.ajax({
            url: url,
            type: methodType,
            data: args.data,
            dataType: 'json',
            success: function (response) {
                if (response.errno) {
                    alert(response.msg);
                } else {
                    args.successCallback && args.successCallback(response.data);
                }
            },
            error: function (xhr, textStatus, errorThrown) {
                args.errorCallback && args.errorCallback(xhr.responseText);
            },
            complete: function (xhr, textStatus) {
                args.completeCallback && args.completeCallback(xhr.responseText);
            }
        })
    }

    API.Get = function (args, url) {
        API.Request('GET', url, args);
    }

    API.Post = function (args, url) {
        API.Request('POST', url, args);
    }

    API.Head = function (args, url) {
        API.Request('HEAD', url, args);
    }

    // API.BaseUrl = 'https://digitalduhu.tsbtv.cn/';
    API.BaseUrl = 'http://127.0.0.1/back/';

    // System
    API.System = {
        // 获取所有配置数据
        getAll: function (args) {
            API.Post(args, 'controller/System.php?action=getAll');
        },
        // 获取配置数据
        getValue: function (args) {
            API.Post(args, 'controller/System.php?action=getValue');
        },
        // 获取配置数据列表
        getValues: function (args) {
            API.Post(args, 'controller/System.php?action=getValues');
        },
        // 添加配置数据
        create: function (args) {
            API.Post(args, 'controller/System.php?action=create');
        },
        // 更新配置数据
        update: function (args) {
            API.Post(args, 'controller/System.php?action=update');
        },
        // 更新配置数据
        updateValue: function (args) {
            API.Post(args, 'controller/System.php?action=updateValue');
        },
        // 删除配置数据
        delete: function (args) {
            API.Post(args, 'controller/System.php?action=delete');
        },
    }

    // Upload
    API.Upload = {
        // 上传文件
        create: function (args) {
            API.Post(args, 'controller/Upload.php?action=create');
        },
        // 删除文件
        delete: function (args) {
            API.Post(args, 'controller/Upload.php?action=delete');
        },
    }

    // Member
    API.Member = {
        // 刷新用户登陆状态
        refresh: function (args) {
            API.Post(args, 'controller/Member.php?action=refresh');
        },

        // 创建用户
        create: function (args) {
            API.Post(args, 'controller/Member.php?action=create');
        },

        // 获取单个用户信息
        get: function (args) {
            API.Post(args, 'controller/Member.php?action=get');
        },

        // 搜索用户
        search: function (args) {
            API.Post(args, 'controller/Member.php?action=search');
        },

        // 修改用户信息
        update: function (args) {
            API.Post(args, 'controller/Member.php?action=update');
        },

        // 修改用户角色
        updateRole: function (args) {
            API.Post(args, 'controller/Member.php?action=updateRole');
        },

        // 用户登出
        logout: function (args) {
            API.Get(args, 'controller/Member.php?action=logout');
        },
    }

    // Article
    API.Article = {
        // 搜索文章
        search: function (args) {
            API.Post(args, 'controller/Article.php?action=search');
        },

        // 创建文章
        create: function (args) {
            API.Post(args, 'controller/Article.php?action=create');
        },

        // 修改文章
        update: function (args) {
            API.Post(args, 'controller/Article.php?action=update');
        },

        // 删除文章封面
        deleteImage: function (args) {
            API.Post(args, 'controller/Article.php?action=deleteImage');
        },

        // 修改文章状态
        changeStatus: function (args) {
            API.Post(args, 'controller/Article.php?action=changeStatus');
        },

        // 修改文章展示状态
        changeShow: function (args) {
            API.Post(args, 'controller/Article.php?action=changeShow');
        },
    }

    // Video
    API.Video = {
        // 搜索视频
        search: function (args) {
            API.Post(args, 'controller/Video.php?action=search');
        },

        // 创建视频
        create: function (args) {
            API.Post(args, 'controller/Video.php?action=create');
        },

        // 修改视频
        update: function (args) {
            API.Post(args, 'controller/Video.php?action=update');
        },

        // 删除视频封面
        deleteImage: function (args) {
            API.Post(args, 'controller/Video.php?action=deleteImage');
        },

        // 删除视频
        deleteVideo: function (args) {
            API.Post(args, 'controller/Video.php?action=deleteVideo');
        },

        // 修改视频状态
        changeStatus: function (args) {
            API.Post(args, 'controller/Video.php?action=changeStatus');
        },

        // 修改视频展示状态
        changeShow: function (args) {
            API.Post(args, 'controller/Video.php?action=changeShow');
        },
    }

    // Log
    API.Log = {
        // 搜索日志
        search: function (args) {
            API.Post(args, 'controller/Log.php?action=search');
        },
        // 删除日志
        delete: function (args) {
            API.Post(args, 'controller/Log.php?action=delete');
        },
    }


    window['API'] = API;
})();