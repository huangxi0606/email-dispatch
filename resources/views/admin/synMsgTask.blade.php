<div class="btn-group" data-toggle="buttons">
    <a class="btn btn-sm btn-info" id="syn-redis-data"><i class="fa fa-save"></i> 同步数据</a>
    <a class="btn btn-sm btn-info" id="clear-redis-data"><i class="fa fa-trash"></i> 清空设备记录</a>
</div>
<script>
    $(function () {
        $("#syn-redis-data").click(function (e) {
            $.ajax({
                url: "/{{Route::current()->uri}}/sync",
                success: function(data){
                    $.pjax.reload('#pjax-container');
                    toastr.success(data.message);
                },
                error :function () {
                    toastr.error('请求失败，请稍后再试');
                }
            });
        });
    });
    $(function () {
        $("#clear-redis-data").click(function (e) {
            $.ajax({
                url: "/{{Route::current()->uri}}/clear",
                success: function(data){
                    $.pjax.reload('#pjax-container');
                    toastr.success(data.message);
                },
                error :function () {
                    toastr.error('请求失败，请稍后再试');
                }
            });
        });
    });
</script>