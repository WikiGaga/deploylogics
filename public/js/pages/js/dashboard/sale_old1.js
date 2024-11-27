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
            //*** start month_year_sale_comparison
                var arr = [];
                arr['series'] = [
                    {
                        "name":"Al Zaiba",
                        "type":"column",
                        "data":["1200","1500","2000"]
                    },
                    {
                        "name":"Al Hail",
                        "type":"column",
                        "data":["2200","2500","3000"]
                    }
                ];
                arr['categories'] = ['Jan-21','Feb-21','Mar-21'];
                month_year_sale_comparison(arr);
            // end month_year_sale_comparison

            //*** start month_sale_branch
            var arr = [];
            arr['series'] = [
                {
                    "name":"Al Azaiba",
                    "type":"column",
                    "data":["1000","2000","3000"]
                }
            ];
            arr['categories'] = ['Jan-21','Feb-21','Mar-21'];
            month_sale_branch(arr);
            // end month_sale_branch

            //*** start top_item_sales
            var arr = [];
            var top_sale_product = data['top_sale_product'];
            arr['series'] = [];
            arr['labels'] = [];
            for(var i=0;i<top_sale_product.length;i++){
                arr['series'].push(parseInt(top_sale_product[i]['sales_dtl_quantity']));
                arr['labels'].push(top_sale_product[i]['product_name']);
            }
            /*arr['series'] = [50,100,105,60,57];
            arr['labels'] = ['Apple','Banana','Milk','Black Stone','Labello'];*/
            top_item_sales(arr)
            // end top_item_sales

            //*** start hours_branch_wise
            var arr = [];
            arr['series'] = [{
                name: 'Income',
                type: 'column',
                data: [100,200,300,100,200,300,100,200,300,100,200,300,100,200,300,100,200,300,100,200,300,100,200,300]
            }];
            arr['categories'] = ["1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24"];
            hours_branch_wise(arr)
            // end hours_branch_wise

            //*** start sale_purchase_ratio
            var arr = [];
            arr['series'] = [155,388];
            arr['labels'] = ['Sale','Purchase'];
            sale_purchase_ratio(arr)
            // end sale_purchase_ratio

            //*** start top_customers
            var arr = [];
            arr['series'] = [{
                data: [1200,1500,1000]
            }];
            arr['categories'] = ["Walk In Customer","Salim Khalfan","Al Shabibi"];
            product_group_wise(arr)
            // end top_customers
        }
    });
});
function month_year_sale_comparison(arr){
    //*** start month_year_sale_comparison
    var apexChart = "#month_year_sale_comparison";
    var series = arr['series'];
    var categories = arr['categories'];
    var  options = {
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
            /* text: 'XYZ - Stock Analysis (2009 - 2016)',
             align: 'left',
             offsetX: 110*/
        },
        xaxis: {
            categories: categories,
        },
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
    // end month_year_sale_comparison
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
function sale_purchase_ratio(arr){
    var apexChart = "#sale_purchase_ratio";
    var series = arr['series'];
    var labels = arr['labels'];
    var options = {
        series: series,
        labels: labels,
        chart: {
            height: 200,
            type: 'donut',
        },
        /* with this code hide labels
        legend: {
            show: false
        },*/
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
