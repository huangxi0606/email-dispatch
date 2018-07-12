<div class="btn-group" data-toggle="buttons">
    <a class="btn btn-sm btn-info" id="syn-redis-data"><i class="fa fa-save"></i> 同步数据</a>
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
</script>