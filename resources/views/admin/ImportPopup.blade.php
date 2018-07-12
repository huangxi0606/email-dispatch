<script>
    function initLayer() {
        console.log(850)
        var importLayer = layer.open({
            zIndex: 1999,
            type: 1,
            skin: 'grid-map-modal', //样式类名
            title: '批量导入',
            anim: 2,
            area: [200, 350],
            shadeClose: true, //开启遮罩关闭
            content: $('#importLayer')
        });
    }
    var Upload = function (file) {
        console.log("file");
        this.file = file;
    };
    Upload.prototype.getType = function () {
        return this.file.type;
    };
    Upload.prototype.getSize = function () {
        return this.file.size;
    };
    Upload.prototype.getName = function () {
        return this.file.name;
    };
    Upload.prototype.doUpload = function () {
        var that = this;
        var formData = new FormData();
        // add assoc key values, this will be posts values
        formData.append("file", this.file, this.getName());
        formData.append("upload_file", true);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            }
        });
        $.ajax({
            type: "POST",
            url: "/{{Route::current()->uri}}/import",
            xhr: function () {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    myXhr.upload.addEventListener('progress', that.progressHandling, false);
                }
                return myXhr;
            },
            success: function (data) {
                alert(data.message);
            },
            error: function (error) {
                try {
                    json = error.responseJSON;
                    alert(json.message);
                } catch (e) {
                    console.log(e);
                    alert('系统错误请重试');
                }
            },
            complete: function () {
                $('#importConfirm').attr("disabled", false);
                $('#importConfirm').html("确定");
            },
            async: true,
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        });
    };

    $(function () {
        $('#importConfirm').click(function (e) {
            var file = $('#importFile')[0].files[0];
            if (!file) alert('请选择文件');
            var upload = new Upload(file);
            $(this).attr("disabled", true);
            $(this).html("处理中");
            upload.doUpload();
        });
    });

</script>
<div id="importLayer" style="display: none;clear: both">

    <div class="box-body" style="text-align: center">
        <div class="form-group" style="margin-top: 10px">
            <p><a target="_blank" href="/static/import_{{Route::currentRouteName()}}.csv">下载实例csv文件</a></p>
        </div>
        <hr>
        <div class="form-group" style="text-align: center">
            <input type="file" id="importFile" style="margin-left: 50px">
        </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer" style="text-align: center">
        <a type="submit" class="btn btn-primary" id="importConfirm">确定</a>
    </div>
</div>
