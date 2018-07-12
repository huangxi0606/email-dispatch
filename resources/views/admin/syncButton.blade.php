<div class="btn-group" data-toggle="buttons">
    <a class="btn btn-sm btn-info" id="syn-redis-data"><i class="fa fa-save"></i> 同步数据</a>
</div>
<script>
    $(function () {
        $("#syn-redis-data").click(function (e) {
            if ("admin/apple/account" === "{{Route::current()->uri()}}" || "admin/apple/comment" === "{{Route::current()->uri()}}") {
                toastr.options.timeOut = 1000 * 60 * 3;
                toastr.info('数据较多,请耐心等待');
            }
            $.ajax({
                url: "/{{Route::current()->uri}}/sync",
                function(data){
                    toastr.remove();
                    $.pjax.reload('#pjax-container');
                    toastr.options.timeOut = 4000;
                    toastr.success('后台同步中。。。');
                },
                success: function(data){
                    toastr.remove();
                    $.pjax.reload('#pjax-container');
                    toastr.options.timeOut = 4000;
                    toastr.success(data.message);
                },
                error :function () {
                    toastr.remove();
                    toastr.options.timeOut = 4000;
                    toastr.error('请求失败，请稍后再试');
                }
            });
        });
    });
</script>