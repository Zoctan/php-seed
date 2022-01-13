<?php
require_once dirname(__FILE__) . "/../function/Util.php";
$selfName = Util::phpSelfName();
?>
<nav class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse">
                <span class="sr-only">切换</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <div class="navbar-brand">菜单</div>
        </div>
        <div class="collapse navbar-collapse" id="navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="<?php echo $selfName == 'index.php' ? 'active' : ''; ?>"><a href="index.php">图文</a></li>
                <li class="<?php echo $selfName == 'video.php' ? 'active' : ''; ?>"><a href="video.php" target="_blank">视频</a></li>
                <li class="dropdown <?php echo $selfName == 'miniprogram.php' || $selfName == 'miniprogramTabbar.php' ? 'active' : ''; ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"> 小程序配置 <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li class="<?php echo $selfName == 'miniprogram.php' ? 'active' : ''; ?>"><a href="miniprogram.php" target="_blank">基础配置</a></li>
                        <li class="<?php echo $selfName == 'miniprogramTabbar.php' ? 'active' : ''; ?>"><a href="miniprogramTabbar.php" target="_blank">底部导航栏</a></li>
                    </ul>
                </li>
                <li class="dropdown <?php echo $selfName == 'accountAdmin.php' || $selfName == 'systemKeyValue.php' ? 'active' : ''; ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        系统 <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <?php if ($_COOKIE["role"] == "superadmin") { ?>
                            <li class="<?php echo $selfName == 'accountAdmin.php' ? 'active' : ''; ?>"><a href="accountAdmin.php" target="_blank">账户管理</a></li>
                            <li class="<?php echo $selfName == 'systemKeyValue.php' ? 'active' : ''; ?>"><a href="systemKeyValue.php" target="_blank">系统键值对</a></li>
                            <li class="<?php echo $selfName == 'log.php' ? 'active' : ''; ?>"><a href="log.php" target="_blank">日志列表</a></li>
                        <?php } ?>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="accountInfo.php" target="_blank"><span class="glyphicon glyphicon-user"></span> 我的</a></li>
                <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> 登出</a></li>
            </ul>
        </div>
    </div>
</nav>