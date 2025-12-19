const primary = '#6993FF';
const success = '#1BC5BD';
const info = '#8950FC';
const warning = '#FFA800';
const danger = '#F64E60';

function loadRestaurantCharts(){
    restMonthSaleBranchAjax();
    paymentMethodChartAjax();
    orderTypeChartAjax();
    topFoodItemsAjax();
    branchPerformanceAjax();
}

function restMonthSaleBranchAjax(){
    var formData = {
        chart_name : 'rest_month_sale_branch'
    };
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/dashboard/get-restaurant-chart-data',
        dataType: 'json',
        data: formData,
        success: function (response) {
            if(response['status'] == "success"){
                $('#rest_month_sale_branch').html("");
                var chartData = response['data']['rest_month_sale_branch'];
                restMonthSaleBranchChart(chartData);
            }
        }
    });
}

function restMonthSaleBranchChart(chartData){
    var months = [];
    var branches = {};
    var series = [];
    
    chartData.forEach(function(item){
        if(months.indexOf(item.month_name) === -1){
            months.push(item.month_name);
        }
        if(!branches[item.branch_name]){
            branches[item.branch_name] = [];
        }
    });
    
    Object.keys(branches).forEach(function(branchName){
        var branchData = [];
        months.forEach(function(month){
            var found = chartData.find(function(item){
                return item.month_name === month && item.branch_name === branchName;
            });
            branchData.push(found ? parseFloat(found.amount) : 0);
        });
        series.push({
            name: branchName,
            data: branchData
        });
    });
    
    var options = {
        series: series,
        chart: {
            type: 'line',
            height: 350,
            toolbar: {
                show: true
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            categories: months
        },
        yaxis: {
            title: {
                text: 'Sales Amount'
            }
        },
        legend: {
            position: 'top'
        },
        colors: [primary, success, info, warning, danger]
    };
    
    var chart = new ApexCharts(document.querySelector("#rest_month_sale_branch"), options);
    chart.render();
}

function paymentMethodChartAjax(){
    var formData = {
        chart_name : 'payment_method_chart'
    };
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/dashboard/get-restaurant-chart-data',
        dataType: 'json',
        data: formData,
        success: function (response) {
            if(response['status'] == "success"){
                $('#payment_method_chart').html("");
                var data = response['data']['payment_method_chart'];
                paymentMethodChart(data);
            }
        }
    });
}

function paymentMethodChart(data){
    var options = {
        series: [
            parseFloat(data.cash_sales || 0),
            parseFloat(data.card_sales || 0),
            parseFloat(data.credit_sales || 0)
        ],
        chart: {
            type: 'donut',
            height: 300
        },
        labels: ['Cash', 'Card', 'Credit'],
        colors: [primary, success, info],
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1) + "%";
            }
        }
    };
    
    var chart = new ApexCharts(document.querySelector("#payment_method_chart"), options);
    chart.render();
}

function orderTypeChartAjax(){
    var formData = {
        chart_name : 'order_type_chart'
    };
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/dashboard/get-restaurant-chart-data',
        dataType: 'json',
        data: formData,
        success: function (response) {
            if(response['status'] == "success"){
                $('#order_type_chart').html("");
                var data = response['data']['order_type_chart'];
                orderTypeChart(data);
            }
        }
    });
}

function orderTypeChart(data){
    var options = {
        series: [
            parseFloat(data.dine_in_sales || 0),
            parseFloat(data.takeaway_sales || 0),
            parseFloat(data.delivery_sales || 0)
        ],
        chart: {
            type: 'pie',
            height: 300
        },
        labels: ['Dine In', 'Takeaway', 'Delivery'],
        colors: [primary, success, warning],
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(1) + "%";
            }
        }
    };
    
    var chart = new ApexCharts(document.querySelector("#order_type_chart"), options);
    chart.render();
}

function topFoodItemsAjax(){
    var formData = {
        chart_name : 'top_food_items'
    };
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/dashboard/get-restaurant-chart-data',
        dataType: 'json',
        data: formData,
        success: function (response) {
            if(response['status'] == "success"){
                $('#top_food_items').html("");
                var chartData = response['data']['top_food_items'];
                topFoodItemsChart(chartData);
            }
        }
    });
}

function topFoodItemsChart(chartData){
    var labels = [];
    var amounts = [];
    
    chartData.forEach(function(item){
        labels.push(item.food_name);
        amounts.push(parseFloat(item.total_amount || 0));
    });
    
    var options = {
        series: [{
            name: 'Sales Amount',
            data: amounts
        }],
        chart: {
            type: 'bar',
            height: 300
        },
        plotOptions: {
            bar: {
                horizontal: true,
                dataLabels: {
                    position: 'top'
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val.toFixed(2);
            }
        },
        xaxis: {
            categories: labels,
            title: {
                text: 'Sales Amount'
            }
        },
        yaxis: {
            title: {
                text: 'Food Items'
            }
        },
        colors: [primary]
    };
    
    var chart = new ApexCharts(document.querySelector("#top_food_items"), options);
    chart.render();
}

function branchPerformanceAjax(){
    var formData = {
        chart_name : 'branch_performance'
    };
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/dashboard/get-restaurant-chart-data',
        dataType: 'json',
        data: formData,
        success: function (response) {
            if(response['status'] == "success"){
                $('#branch_performance').html("");
                var chartData = response['data']['branch_performance'];
                branchPerformanceChart(chartData);
            }
        }
    });
}

function branchPerformanceChart(chartData){
    var labels = [];
    var netSales = [];
    var totalOrders = [];
    
    chartData.forEach(function(item){
        labels.push(item.branch_name);
        netSales.push(parseFloat(item.net_sales || 0));
        totalOrders.push(parseFloat(item.total_orders || 0));
    });
    
    var options = {
        series: [{
            name: 'Net Sales',
            type: 'column',
            data: netSales
        }, {
            name: 'Total Orders',
            type: 'line',
            data: totalOrders
        }],
        chart: {
            height: 300,
            type: 'line',
            toolbar: {
                show: true
            }
        },
        stroke: {
            width: [0, 4]
        },
        dataLabels: {
            enabled: true,
            enabledOnSeries: [1]
        },
        labels: labels,
        xaxis: {
            type: 'category'
        },
        yaxis: [{
            title: {
                text: 'Net Sales'
            }
        }, {
            opposite: true,
            title: {
                text: 'Total Orders'
            }
        }],
        colors: [primary, success],
        legend: {
            position: 'top'
        }
    };
    
    var chart = new ApexCharts(document.querySelector("#branch_performance"), options);
    chart.render();
}


