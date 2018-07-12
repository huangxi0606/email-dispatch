<div class="form-group {!! !$errors->has($label) ?: 'has-error' !!}">

    <label for="{{$id}}" class="col-sm-2 control-label">{{$label}}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div id="{{$id}}" style="width: 100%; height: 100%;">
        </div>

        <input type="hidden" name="{{$name}}" value="{{ old($column, $value) }}"/>


    </div>
</div>

<div class="form-group">
    <div class="col-sm-12">
        <div class="col-sm-12 text-center">
            <button id="refresh-{{$id}}" class="btn btn-primary btn-autoset" type="button" >
                重新生成曲线
            </button>
        </div>
    </div>

</div>

<script>

    $(function(){
        var targetRateLine=[];
        var natureRateLine = [
            3.68,
            2.91,
            1.46,
            1.23,
            1.13,
            1.41,
            2.95,
            4.54,
            4.9,
            4.49,
            4.77,
            4.97,
            6.06,
            5.83,
            4.66,
            3.55,
            3.57,
            4.78,
            4.9,
            5.09,
            5.48,
            6.8,
            6.32,
            4.51
        ];

        var baiduRateLine = [
            3.68,
            2.91,
            2.46,
            2.23,
            2.13,
            2.41,
            2.95,
            3.54,
            3.9,
            4.49,
            4.77,
            4.97,
            5.06,
            4.83,
            4.66,
            4.55,
            4.57,
            4.78,
            4.9,
            5.09,
            5.48,
            5.8,
            5.32,
            4.51
        ];

        console.log('natureRateLine:'+sum(natureRateLine));
        console.log('baiduRateLine:'+sum(baiduRateLine));

        function getTargetRateLine(){
            rateData = $('input[name={{$name}}]').val();
            console.log('rateData:'+rateData);
            if(rateData) targetRateLine=JSON.parse(rateData);
            else{
                targetRateLine = natureRateLine;
            }
            console.log('targetRateLine:'+targetRateLine);
        }

        function generateTargetRateLine() {
            var increaseDuration =[7,8,9,12,13,21,22];//上升区间
            var reduceDuration =[2,3,4,5,15,16];//下降区间
            targetRateLine = baiduRateLine.concat();
            $.each(reduceDuration,function (key,val) {
                targetRateLine[val]=parseFloat((baiduRateLine[val] - Math.random()*2).toPrecision(2));
            });
            $.each(increaseDuration,function (key,val) {
                targetRateLine[val]=parseFloat((baiduRateLine[val] + Math.random()*2).toPrecision(2));
            });
            console.log('targetRateLine:'+targetRateLine);
            chart.series[2].setData(targetRateLine);
            $('input[name={{$name}}]').val(JSON.stringify(targetRateLine));
        }

        function sum(arr) {
            amount=0;
            for(var i=0;i<arr.length;i++){
                amount+=arr[i]
            }
            return amount;
        }

        getTargetRateLine();

        $('#refresh-{{$id}}').click(function () {
            generateTargetRateLine();
        });

        var chartArg = {
            chart: {
                type: 'spline'
            },
            credits: {enabled: false}, // 去掉版权链接
            title: {
                text: '24小时增量分布曲线'
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: ['0:00', '1:00', '2:00', '3:00', '4:00', '5:00', '6:00', '7:00', '8:00', '9:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', '23:00']
            },
            yAxis: {
                title: {
                    text: '增量占比（%）'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },

            tooltip: {
                valueSuffix: '%',
                pointFormat: "Value: {point.y:.2f} %"
            },

            legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'top',
                borderWidth: 0,
                y: 35
            },

            series: [
                {
                    name: '自然分布',
                    data: natureRateLine,
//                    draggableX:true,
                    draggableY: true,
                    dragMinY: 0,
                    dragMaxY: 100,
                    minPointLength: 2
                },
                {
                    name: '百度的移动用户时段分布',
                    data: baiduRateLine,
//                    draggableX:true,
                    draggableY: true,
                    dragMinY: 0,
                    dragMaxY: 100,
                    minPointLength: 2
                },
                {
                    name: '目标速率',
                    data: targetRateLine,
                    //draggableX:true,
                    draggableY: true,
                    dragMinY: 0,
                    dragMaxY: 100,
                    color: "#ff0000",
//                             minPointLength: 2
                }
            ],

            plotOptions: {
                series: {
                    point: {
                        events: {

                            drag: function (e) {
//                                console.log(e);
                                targetRateLine[e.x] = parseFloat(e.y.toFixed(2));
                                $('input[name={{$name}}]').val(JSON.stringify(targetRateLine));
                            },
                            drop: function () {

                            }
                        }
                    },
                    stickyTracking: false
                },
                spline: {
                    cursor: 'ns-resize',
                    dataLabels: {
                        enabled: true,
                        align: "center",
                        formatter: function () {
                            return this.y.toFixed(2) + '%'
                        }
                    }
                }

            }
        };

        var chart = Highcharts.chart('{{$id}}', chartArg);
        var rateLineJson = $('input[name={{$name}}]').val();
        if(rateLineJson) targetRateLine=JSON.parse(rateLineJson);
        else $('input[name={{$name}}]').val(JSON.stringify(natureRateLine));
    });

</script>