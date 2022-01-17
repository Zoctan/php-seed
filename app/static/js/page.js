var getListByPage = () => { };
var pageId = '';
var currentPage = 1;
var prePage = 1;
var nextPage = 1;
var totalPage = 1;
var totalData = 0;
var pageSize = 10;

// 设置获取分页数据的函数
function setPageId(id) {
    pageId = id;
}

function renderPage() {
    var html = `<ul class="pagination">
                    <li><a id="first-page" href="#">首页</a></li>
                    <li><a id="pre-page" href="#">&lt; 上一页</a></li>
                    <li><a id="next-page" href="#">下一页 &gt;</a></li>
                    <li><a id="end-page" href="#">尾页</a></li>
                    <li>
                        <select id="page-size" class="btn btn-default">
                            <option value="10" selected="selected">10条/页</option>
                            <option value="20">20条/页</option>
                            <option value="40">40条/页</option>
                            <option value="80">80条/页</option>
                            <option value="100">100条/页</option>
                            <option value="200">200条/页</option>
                        </select>
                    </li>
                </ul>
                <div>
                    <span id="current-page">当前：？</span>
                    <span id="total-page">总页数：？</span>
                    <span id="total-data">总数：？</span>
                </div>
                <a href="javascript:location.reload();">重置</a>`;
    $(`#${pageId}`).empty();
    $(`#${pageId}`).html(html);
}

// 设置获取分页数据的函数
function setPageFunction(func) {
    getListByPage = func;
}

// 设置分页
function setPage(data) {
    prePage = currentPage;
    currentPage = data.currentPage;
    totalData = data.total;
    totalPage = data.totalPage;
    // 如果当前是第一页，不显示首页和上一页
    if (currentPage == 1) {
        $("#first-page").hide();
        $("#pre-page").hide();
    } else {
        $("#first-page").show();
        $("#pre-page").show();
    }
    // 下一页没有超过总页数，显示按钮
    nextPage = currentPage + 1;
    if (nextPage <= totalPage) {
        $("#next-page").show();
        $("#end-page").show();
    } else {
        // 超过总页数，隐藏按钮
        $("#next-page").hide();
        $("#end-page").hide();
    }
    $("#total-page").text(`共${totalPage}页`);
    $("#current-page").text(`第${currentPage}页`);
    $("#total-data").text(`共${totalData}条数据`);
    if (totalData == 0) {
        $("#total-data").text("没有数据");
    }
}

// 首页
$(document).on('click', '#first-page', function () {
    currentPage = 1;
    getListByPage();
});

// 上一页
$(document).on('click', '#pre-page', function () {
    currentPage--;
    getListByPage();
});

// 下一页
$(document).on('click', '#next-page', function () {
    currentPage++;
    getListByPage();
});

// 尾页
$(document).on('click', '#end-page', function () {
    currentPage = totalPage;
    getListByPage();
});

// 页大小
$(document).on('change', '#page-size', function () {
    pageSize = $("#page-size").val();
    getListByPage();
});