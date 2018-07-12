<div class="btn-group" data-toggle="buttons">
    <a class="btn btn-sm btn-info" id="delete-redis-data"><i class="fa fa-trash"></i> 删除所有</a>
</div>
<script>
    $(function () {
        $("#delete-redis-data").click(function (e) {
            var msg = "您真的确定要删除吗？\n\n请确认！";
            if (confirm("确认删除所有?")){
                $.ajax({
                    url: "/{{Route::current()->uri}}/trash",
                    success: function (data) {
                        $.pjax.reload('#pjax-container');
                        toastr.success(data.message);
                    },
                    error: function () {
                        toastr.error('请求失败，请稍后再试');
                    }
                });
            }
        });
    });
</script>