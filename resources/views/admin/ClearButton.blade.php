<div class="btn-group" data-toggle="buttons">
    <a class="btn btn-sm btn-info" id="clear-redis-data"><i class="fa fa-trash"></i> 清空内存数据</a>
</div>
<script>
    $(function () {
        $("#clear-redis-data").click(function (e) {
            $.ajax({
                url: "/{{Route::current()->uri}}/clear",
                success: function (data) {
                    $.pjax.reload('#pjax-container');
                    toastr.success(data.message);
                },
                error: function () {
                    toastr.error('请求失败，请稍后再试');
                }
            });
        });
    });
</script>