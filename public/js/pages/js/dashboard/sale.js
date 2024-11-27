const primary = '#6993FF';
const success = '#1BC5BD';
const info = '#8950FC';
const warning = '#FFA800';
const danger = '#F64E60';

$('#sale_dashboard').click(function(){
    $('.erp-widget').css('opacity','');
    $(this).css('opacity','1.0');
    var dummy = "/dashboard/dummy/sale";
    $('#dashboard_data').load(dummy);
    var formData = {

    };
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type        : 'POST',
        url         : '/dashboard/get-sale-dashboard-detail',
        dataType	: 'json',
        data        : formData,
        success: function(response) {
            console.log(response);
            var data = response['data'];
            var view = data['view'];
            $('#dashboard_data').html(view);

            //*** start month_sale_branch_ajax
            month_sale_branch_ajax()
            // end month_sale_branch_ajax
        }
    });


});

function top_item_sales_ajax(){
    var formData = {
        chart_name : 'top_item_sales'
    };
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/dashboard/get-chart-data',
        dataType: 'json',
        data: formData,
        success: function (response) {
            if(response['status'] == "success"){
                $('#top_item_sales').html("");
                $('#profit_margin').html("");
                var branch_name = response['data']['branch_name'];
                //*** start top_item_sales
                var top_sale_product = response['data']['top_sale_product'];
                var arr = [];
                arr['series'] = [];
                arr['labels'] = [];
                for(var i=0;i<top_sale_product.length;i++){
                    arr['series'].push(parseInt(top_sale_product[i]['sales_dtl_quantity']));
                    arr['labels'].push(top_sale_product[i]['product_name']);
                }
                top_item_sales(arr)
                // end top_item_sales


                //*** start profit_margin
                var arr = [];
                arr['series'] = ["50"];
                arr['labels'] = [branch_name];
                profit_margin(arr);
                // end profit_margin


                //*** start product_group_wise & sale_purchase_ratio
                ratio_and_product_group_wise_ajax()
                // end product_group_wise & sale_purchase_ratio
            }
        }
    })
}
function top_item_sales(arr){
    var apexChart = "#top_item_sales";
    var series = arr['series'];
    var labels = arr['labels'];
    var options = {
        series:  series,
        labels: labels,
        chart: {
            height:200,
            type: 'donut',
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }],
        colors: [primary, success, warning, danger, info]
    };
    var chart = new ApexCharts(document.querySelector(apexChart), options);
    chart.render();
}
function profit_margin(arr){
    //*** start profit_margin
    var apexChart = "#profit_margin";
    var series = arr['series'];
    var labels = arr['labels'];
    var options = {
        series: series,
        labels: labels,
        chart: {
            height: 200,
            type: 'radialBar',
        },
        plotOptions: {
            radialBar: {
                startAngle: -90,
                endAngle: 90,
                hollow: {
                    margin: 0,
                    size: "70%"
                },
                dataLabels: {
                    showOn: "always",
                    name: {
                        show: true,
                        fontSize: "13px",
                        fontWeight: "700",
                        offsetY: -5,
                        color: '#B5B5C3'
                    },
                    value: {
                        color: '#5E6278',
                        fontSize: "30px",
                        fontWeight: "700",
                        offsetY: -40,
                        show: true
                    }
                },
                track: {
                    background: '#E1F0FF',
                    strokeWidth: '100%'
                }
            }
        },
        colors: ['#d54937'],
        stroke: {
            lineCap: "round",
        }
    };
    var chart = new ApexCharts(document.querySelector(apexChart), options);
    chart.render();
    // end profit_margin
}

function hours_branch_wise_ajax(){
    var formData = {
        chart_name : 'hours_branch_wise'
    };
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/dashboard/get-chart-data2',
        dataType: 'json',
        data: formData,
        success: function (response) {
            if(response['status'] == "success"){
                $('#hours_branch_wise').html('');
                //*** start hours_branch_wise
                var hours_branch = response['data']['hours_branch_wise'];
                var len = response['data']['hours_branch_wise_count'];
                var arr = [];
                if(len != 0){
                    var series_data = [];
                    arr['categories'] = [];
                    for(var i=1; i < len; i++){
                        series_data.push(parseInt(hours_branch["h"+i]));
                        arr['categories'].push("H"+i);
                    }
                    arr['series'] = [{
                        name: 'Income',
                        type: 'column',
                        data: series_data
                    }];
                }else{
                    arr['series'] = [{
                        name: 'Income',
                        type: 'column',
                        data: [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
                    }];
                    arr['categories'] = ["1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24"];
                }
                hours_branch_wise(arr)
                // end hours_branch_wise
            }
        }
    })
}
function hours_branch_wise(arr){
    var apexChart = "#hours_branch_wise";
    var series = arr['series'];
    var categories = arr['categories'];
    var options = {
        series: series,
        chart: {
            height: 200,
            type: 'line',
            stacked: false
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: [1, 1, 4]
        },
        title: {
            /*text: 'XYZ - Stock Analysis (2009 - 2016)',
            align: 'left',
            offsetX: 110*/
        },
        xaxis: {
            categories: categories,
        },
        yaxis: [
            {
                axisTicks: {
                    show: true,
                },
                axisBorder: {
                    show: true,
                    color: primary
                },
                labels: {
                    style: {
                        colors: primary,
                    }
                },
                title: {
                    text: "thousand crores",
                    style: {
                        color: primary,
                    }
                },
                tooltip: {
                    enabled: true
                }
            },
            {
                seriesName: 'Income',
                opposite: true,
                axisTicks: {
                    show: true,
                },
                axisBorder: {
                    show: true,
                    color: success
                },
                labels: {
                    style: {
                        colors: success,
                    }
                },
                title: {
                    text: "thousand crores",
                    style: {
                        color: success,
                    }
                },
            },
        ],
        tooltip: {
            fixed: {
                enabled: true,
                position: 'topLeft', // topRight, topLeft, bottomRight, bottomLeft
                offsetY: 30,
                offsetX: 60
            },
        },
        legend: {
            horizontalAlign: 'left',
            offsetX: 40
        }
    };
    var chart = new ApexCharts(document.querySelector(apexChart), options);
    chart.render();
}

function ratio_and_product_group_wise_ajax(){
    var formData = {
        chart_name : 'ratio_and_product_group_wise_ajax'
    };
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/dashboard/get-chart-data3',
        dataType: 'json',
        data: formData,
        success: function (response) {
            if(response['status'] == "success"){
                $('#sale_purchase_ratio').html('');
                $('#product_group_wise').html('');
                //*** start hours_branch_wise
                var ratio = response['data']['ratio_and_product_group_wise_ajax'];
                //*** start sale_purchase_ratio
                var arr = [];
                if(parseFloat(ratio.total_amount) == null || parseFloat(ratio.total_amount) == NaN || parseFloat(ratio.total_amount) == undefined){
                    var t_amount = 0
                }else{
                    var t_amount = parseFloat(ratio.total_amount).toFixed(3);
                }
                if(parseFloat(ratio.net_profit) == null || parseFloat(ratio.net_profit) == NaN || parseFloat(ratio.net_profit) == undefined){
                    var net_profit = 0
                }else{
                    var net_profit = parseFloat(ratio.net_profit).toFixed(3);
                }
                arr['series'] = [{
                    data: [t_amount,net_profit]
                }];
                arr['categories'] = ['Gross Sale','Net Profit'];

                sale_purchase_ratio(arr)
                // end sale_purchase_ratio

                //*** start top_customers
                var product_group = response['data']['product_group'];
                var arr = [];
                var series_data = [];
                arr['categories'] = [];
                for(var i=0;i<product_group.length;i++){
                    series_data.push(parseFloat(product_group[i]['amount']).toFixed(3));
                    arr['categories'].push(product_group[i]['group_item_name']);
                }
                arr['series'] = [{
                    name: "Product Group",
                    data: series_data
                }];
                product_group_wise(arr)
                // end top_customers


                //*** start hours_branch_wise_ajax
                hours_branch_wise_ajax()
                // end hours_branch_wise_ajax

            }
        }
    })
}
function sale_purchase_ratio(arr){
    var apexChart = "#sale_purchase_ratio";
    var series = arr['series'];
    var categories = arr['categories'];
    var options = {
        series: series,
        chart: {
            type: 'bar',
            height: 200
        },
        plotOptions: {
            bar: {
                horizontal: true,
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: categories,
        },
        fill: {
            colors: ['#c767dc', '#67dcbb'],
        }
    };
    var chart = new ApexCharts(document.querySelector(apexChart), options);
    chart.render();
}
function product_group_wise(arr){
    var apexChart = "#product_group_wise";
    var series = arr['series'];
    var categories = arr['categories'];
    var options = {
        series: series,
        chart: {
            type: 'bar',
            height: 200
        },
        plotOptions: {
            bar: {
                horizontal: true,
            }
        },
        dataLabels: {
            enabled: false
        },
        xaxis: {
            categories: categories,
        }
    };
    var chart = new ApexCharts(document.querySelector(apexChart), options);
    chart.render();
}

function month_sale_branch_ajax(){
    var formData = {
        chart_name : 'month_sale_branch'
    };
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/dashboard/get-chart-data4',
        dataType: 'json',
        data: formData,
        success: function (response) {
            if(response['status'] == "success"){
                $('#month_sale_branch').html("");
                var branch_name = response['data']['branch_name'];
                //*** start month_sale_branch
                var sale_branch = response['data']['month_sale_branch'];
                var arr = [];
                var series_data = [];
                arr['categories'] = [];
                for(var i=0;i<sale_branch.length;i++){
                    series_data.push(parseInt(sale_branch[i]['amount']));
                    arr['categories'].push(sale_branch[i]['month']);
                }
                arr['series'] = [
                    {
                        "name": branch_name,
                        "type":"column",
                        "data": series_data
                    }
                ];
                month_sale_branch(arr);
                // end month_sale_branch

                //*** start top_item_sales_ajax
                top_item_sales_ajax()
                // end top_item_sales_ajax
            }
        }
    })
}
function month_sale_branch(arr){
    var apexChart = "#month_sale_branch";
    var series = arr['series'];
    var categories = arr['categories'];
    var options = {
        series: series,
        chart: {
            type: 'bar',
            height: 200
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: categories,
        },
        yaxis: {
            /*title: {
                text: branches_sale_Yaxis
            }*/
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val
                }
            }
        },
        colors: ['#ff0042',primary, success, warning]
    };
    var chart = new ApexCharts(document.querySelector(apexChart), options);
    chart.render();
}
