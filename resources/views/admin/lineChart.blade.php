<script>
    $(function() {
        $(".lineChartRender").each(function () {
            var $this = $(this);
            $this.sparkline('html',{
                    height: '1.5em', width: '12em', lineColor: '#f00', fillColor: '#ffa',
                    minSpotColor: true, maxSpotColor: true, spotColor: '#77f', spotRadius: 3
                });
            });
    });
</script>