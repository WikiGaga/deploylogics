const primary = '#6993FF';
const success = '#1BC5BD';
const info = '#8950FC';
const warning = '#FFA800';
const danger = '#F64E60';

function loadRestaurantCharts(){
    restMonthSaleBranchAjax();
    salesByDayAjax();
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
                if(chartData && chartData.length > 0){
                    restMonthSaleBranchChart(chartData);
                } else {
                    $('#rest_month_sale_branch').html('<div class="text-center p-5">No data available</div>');
                }
            }
        },
        error: function(xhr, status, error) {
            $('#rest_month_sale_branch').html('<div class="text-center p-5">Error loading chart data</div>');
        }
    });
}

function restMonthSaleBranchChart(chartData){
    if(!chartData || chartData.length === 0){
        $('#rest_month_sale_branch').html('<div class="text-center p-5">No data available</div>');
        return;
    }

    var months = [];
    var branches = {};
    var series = [];

    chartData.forEach(function(item){
        var monthName = item.month_name || item.MONTH_NAME || '';
        var branchName = item.branch_name || item.BRANCH_NAME || '';

        if(monthName && months.indexOf(monthName) === -1){
            months.push(monthName);
        }
        if(branchName && !branches[branchName]){
            branches[branchName] = [];
        }
    });

    if(months.length === 0){
        $('#rest_month_sale_branch').html('<div class="text-center p-5">No data available</div>');
        return;
    }


    months.sort(function(a, b) {
        var monthNames = {
            'Jan': 1, 'Feb': 2, 'Mar': 3, 'Apr': 4, 'May': 5, 'Jun': 6,
            'Jul': 7, 'Aug': 8, 'Sep': 9, 'Oct': 10, 'Nov': 11, 'Dec': 12
        };

        var parseMonth = function(monthStr) {
            var parts = monthStr.split('-');
            if (parts.length === 2) {
                var monthAbbr = parts[0].trim();
                var year = parseInt(parts[1].trim());
                var monthNum = monthNames[monthAbbr] || 0;
                return { year: year, month: monthNum, original: monthStr };
            }
            return { year: 0, month: 0, original: monthStr };
        };

        var aDate = parseMonth(a);
        var bDate = parseMonth(b);

        if (aDate.year !== bDate.year) {
            return aDate.year - bDate.year;
        }
        return aDate.month - bDate.month;
    });

    Object.keys(branches).forEach(function(branchName){
        var branchData = [];
        months.forEach(function(month){
            var found = chartData.find(function(item){
                var itemMonth = item.month_name || item.MONTH_NAME || '';
                var itemBranch = item.branch_name || item.BRANCH_NAME || '';
                return itemMonth === month && itemBranch === branchName;
            });
            var amount = found ? (found.amount || found.AMOUNT || 0) : 0;
            branchData.push(parseFloat(amount));
        });
        series.push({
            name: branchName,
            data: branchData
        });
    });

    var chartElement = document.querySelector("#rest_month_sale_branch");
    if(!chartElement){
        return;
    }

    var containerWidth = chartElement.offsetWidth || chartElement.parentElement.offsetWidth;
    var containerHeight = 300;

    var options = {
        series: series,
        chart: {
            type: 'line',
            height: containerHeight,
            width: containerWidth || '100%',
            toolbar: {
                show: true
            },
            zoom: {
                enabled: true
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
        colors: [primary, success, info, warning, danger],
        tooltip: {
            y: {
                formatter: function (val) {
                    return val.toFixed(2);
                }
            }
        }
    };

    var chart = new ApexCharts(chartElement, options);
    chart.render();

    window.addEventListener('resize', function() {
        chart.updateOptions({
            chart: {
                width: chartElement.offsetWidth || '100%'
            }
        });
    });
}

function salesByDayAjax(){
    var formData = {
        chart_name : 'sales_by_day'
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
                $('#sales_by_day_chart').html("");
                var chartData = response['data']['sales_by_day'];
                var summary = response['data']['sales_by_day_summary'];
                var breakdown = response['data']['sales_by_day_breakdown'];

                if(chartData && chartData.length > 0){
                    salesByDayChart(chartData, summary, breakdown);
                } else {
                    $('#sales_by_day_chart').html('<div class="text-center p-5">No data available</div>');
                }
            }
        },
        error: function(xhr, status, error) {
            $('#sales_by_day_chart').html('<div class="text-center p-5">Error loading chart data</div>');
        }
    });
}

var salesByDayChartInstance = null;

function salesByDayChart(chartData, summary, breakdown){
    if(!chartData || chartData.length === 0){
        $('#sales_by_day_chart').html('<div class="text-center p-5">No data available</div>');
        if(salesByDayChartInstance){
            salesByDayChartInstance.destroy();
            salesByDayChartInstance = null;
        }
        return;
    }

    var days = [];
    var salesAmounts = [];
    var orderCounts = [];

    chartData.forEach(function(item){
        days.push(item.day_label || item.DAY_LABEL || '');
        salesAmounts.push(parseFloat(item.sales_amount || item.SALES_AMOUNT || 0));
        orderCounts.push(parseInt(item.order_count || item.ORDER_COUNT || 0));
    });

    if(summary){
        var totalOrders = summary.total_orders || summary.TOTAL_ORDERS || 0;
        var totalSales = parseFloat(summary.total_sales || summary.TOTAL_SALES || 0);
        var salesLabel = (typeof translations !== 'undefined' && translations.sales) ? translations.sales : 'Sales';
        $('#sales_by_day_summary').text(salesLabel + ' (' + totalOrders + ') - ' + totalSales.toFixed(3));
    }

    if(breakdown){
        var onlineSales = parseFloat(breakdown.online_sales || breakdown.ONLINE_SALES || 0);
        var cashSales = parseFloat(breakdown.cash_sales || breakdown.CASH_SALES || 0);
        var deliverySales = parseFloat(breakdown.delivery_sales || breakdown.DELIVERY_SALES || 0);
        var pickupSales = parseFloat(breakdown.pickup_sales || breakdown.PICKUP_SALES || 0);
        var totalBreakdown = onlineSales + cashSales + deliverySales + pickupSales;

        $('#online_sales_amount').text(onlineSales.toFixed(3));
        $('#cash_sales_amount').text(cashSales.toFixed(3));
        $('#delivery_sales_amount').text(deliverySales.toFixed(3));
        $('#pickup_sales_amount').text(pickupSales.toFixed(3));

        if(totalBreakdown > 0){
            $('#online_sales_bar').css('width', (onlineSales / totalBreakdown * 100) + '%');
            $('#cash_sales_bar').css('width', (cashSales / totalBreakdown * 100) + '%');
            $('#delivery_sales_bar').css('width', (deliverySales / totalBreakdown * 100) + '%');
            $('#pickup_sales_bar').css('width', (pickupSales / totalBreakdown * 100) + '%');
        }
    }

    var chartElement = document.querySelector("#sales_by_day_chart");
    if(!chartElement){
        return;
    }

    // Clear the container first
    $(chartElement).html('');

    // Ensure container has dimensions - wait a bit if needed
    setTimeout(function() {
        var containerWidth = chartElement.offsetWidth || $(chartElement).width() || $(chartElement).parent().width() || '100%';
        var containerHeight = 300;

        // If container has no width, use parent or default
        if(containerWidth === 0 || !containerWidth){
            containerWidth = $(chartElement).parent().width() || '100%';
        }

        // Ensure minimum dimensions
        if(typeof containerWidth === 'number' && containerWidth < 100){
            containerWidth = '100%';
        }

        var options = {
            series: [{
                name: 'Sales Amount',
                data: salesAmounts
            }],
            chart: {
                type: 'column',
                height: containerHeight,
                width: containerWidth,
                toolbar: {
                    show: true
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '60%',
                    horizontal: false,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: function (val) {
                    return val.toFixed(0);
                },
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: ["#304758"]
                }
            },
            xaxis: {
                categories: days,
                position: 'bottom',
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                crosshairs: {
                    fill: {
                        type: 'gradient',
                        gradient: {
                            colorFrom: '#D8E3F0',
                            colorTo: '#BED1E6',
                            stops: [0, 100],
                            opacityFrom: 0.4,
                            opacityTo: 0.5
                        }
                    }
                },
                tooltip: {
                    enabled: true
                }
            },
            yaxis: {
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    show: true,
                    formatter: function (val) {
                        return val.toFixed(0);
                    }
                },
                title: {
                    text: 'Amount'
                }
            },
            colors: [warning],
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (val, opts) {
                        var index = opts.dataPointIndex;
                        var orderCount = orderCounts[index] || 0;
                        var dayName = chartData[index] ? (chartData[index].day_name || chartData[index].DAY_NAME || days[index]) : days[index];
                        return val.toFixed(3) + ' ' + dayName + '<br/>' + orderCount + ' Orders';
                    }
                }
            },
            grid: {
                borderColor: '#e7e7e7',
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                }
            }
        };

        // Destroy existing chart if any
        if(salesByDayChartInstance){
            try {
                salesByDayChartInstance.destroy();
            } catch(e) {
                console.log('Error destroying chart:', e);
            }
            salesByDayChartInstance = null;
        }

        try {
            salesByDayChartInstance = new ApexCharts(chartElement, options);
            salesByDayChartInstance.render().then(function() {
                // Chart rendered successfully
            }).catch(function(error) {
                console.error('Error rendering chart:', error);
            });
        } catch(e) {
            console.error('Error creating chart:', e);
        }

        // Remove existing resize listeners and add new one
        $(window).off('resize.salesByDay');
        $(window).on('resize.salesByDay', function() {
            if(salesByDayChartInstance){
                try {
                    var newWidth = chartElement.offsetWidth || $(chartElement).width() || '100%';
                    salesByDayChartInstance.updateOptions({
                        chart: {
                            width: newWidth
                        }
                    });
                } catch(e) {
                    console.log('Error updating chart on resize:', e);
                }
            }
        });
    }, 100);
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
                if(data){
                    paymentMethodChart(data);
                } else {
                    $('#payment_method_chart').html('<div class="text-center p-5">No data available</div>');
                }
            }
        },
        error: function(xhr, status, error) {
            $('#payment_method_chart').html('<div class="text-center p-5">Error loading chart data</div>');
        }
    });
}

function paymentMethodChart(data){
    var cashSales = parseFloat(data.cash_sales || data.CASH_SALES || 0);
    var cardSales = parseFloat(data.card_sales || data.CARD_SALES || 0);
    var creditSales = parseFloat(data.credit_sales || data.CREDIT_SALES || 0);
    var total = cashSales + cardSales + creditSales;

    if(total === 0){
        $('#payment_method_chart').html('<div class="text-center p-5">No payment data available for today</div>');
        return;
    }

    var chartElement = document.querySelector("#payment_method_chart");
    if(!chartElement){
        return;
    }

    var containerWidth = chartElement.offsetWidth || chartElement.parentElement.offsetWidth;
    var containerHeight = 280;

    var options = {
        series: [cashSales, cardSales, creditSales],
        chart: {
            type: 'donut',
            height: containerHeight,
            width: containerWidth || '100%'
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
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val.toFixed(2);
                }
            }
        }
    };

    var chart = new ApexCharts(chartElement, options);
    chart.render();

    window.addEventListener('resize', function() {
        chart.updateOptions({
            chart: {
                width: chartElement.offsetWidth || '100%'
            }
        });
    });
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
                if(data){
                    orderTypeChart(data);
                } else {
                    $('#order_type_chart').html('<div class="text-center p-5">No data available</div>');
                }
            }
        },
        error: function(xhr, status, error) {
            $('#order_type_chart').html('<div class="text-center p-5">Error loading chart data</div>');
        }
    });
}

function orderTypeChart(data){
    var dineInSales = parseFloat(data.dine_in_sales || data.DINE_IN_SALES || 0);
    var takeawaySales = parseFloat(data.takeaway_sales || data.TAKEAWAY_SALES || 0);
    var deliverySales = parseFloat(data.delivery_sales || data.DELIVERY_SALES || 0);
    var total = dineInSales + takeawaySales + deliverySales;

    if(total === 0){
        $('#order_type_chart').html('<div class="text-center p-5">No order type data available for today</div>');
        return;
    }

    var chartElement = document.querySelector("#order_type_chart");
    if(!chartElement){
        return;
    }

    var containerWidth = chartElement.offsetWidth || chartElement.parentElement.offsetWidth;
    var containerHeight = 320;

    var options = {
        series: [dineInSales, takeawaySales, deliverySales],
        chart: {
            type: 'pie',
            height: containerHeight,
            width: containerWidth || '100%'
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
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val.toFixed(2);
                }
            }
        }
    };

    var chart = new ApexCharts(chartElement, options);
    chart.render();

    window.addEventListener('resize', function() {
        chart.updateOptions({
            chart: {
                width: chartElement.offsetWidth || '100%'
            }
        });
    });
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
                if(chartData && chartData.length > 0){
                    topFoodItemsChart(chartData);
                } else {
                    $('#top_food_items').html('<div class="text-center p-5">No data available</div>');
                }
            }
        },
        error: function(xhr, status, error) {
            $('#top_food_items').html('<div class="text-center p-5">Error loading chart data</div>');
        }
    });
}

function topFoodItemsChart(chartData){
    var labels = [];
    var amounts = [];

    chartData.forEach(function(item){
        var foodName = item.food_name || item.FOOD_NAME || '';
        var totalAmount = item.total_amount || item.TOTAL_AMOUNT || 0;
        labels.push(foodName);
        amounts.push(parseFloat(totalAmount));
    });

    var chartElement = document.querySelector("#top_food_items");
    if(!chartElement){
        return;
    }

    var containerWidth = chartElement.offsetWidth || chartElement.parentElement.offsetWidth;
    var containerHeight = 320;

    var options = {
        series: [{
            name: 'Sales Amount',
            data: amounts
        }],
        chart: {
            type: 'bar',
            height: containerHeight,
            width: containerWidth || '100%'
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

    var chart = new ApexCharts(chartElement, options);
    chart.render();

    window.addEventListener('resize', function() {
        chart.updateOptions({
            chart: {
                width: chartElement.offsetWidth || '100%'
            }
        });
    });
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
                if(chartData && chartData.length > 0){
                    branchPerformanceChart(chartData);
                } else {
                    $('#branch_performance').html('<div class="text-center p-5">No data available</div>');
                }
            }
        },
        error: function(xhr, status, error) {
            $('#branch_performance').html('<div class="text-center p-5">Error loading chart data</div>');
        }
    });
}

function branchPerformanceChart(chartData){
    var labels = [];
    var netSales = [];
    var totalOrders = [];

    chartData.forEach(function(item){
        var branchName = item.branch_name || item.BRANCH_NAME || '';
        var netSale = item.net_sales || item.NET_SALES || 0;
        var totalOrder = item.total_orders || item.TOTAL_ORDERS || 0;
        labels.push(branchName);
        netSales.push(parseFloat(netSale));
        totalOrders.push(parseFloat(totalOrder));
    });

    var chartElement = document.querySelector("#branch_performance");
    if(!chartElement){
        return;
    }

    var containerWidth = chartElement.offsetWidth || chartElement.parentElement.offsetWidth;
    var containerHeight = 320;

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
            height: containerHeight,
            width: containerWidth || '100%',
            type: 'line',
            toolbar: {
                show: true
            },
            zoom: {
                enabled: true
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

    var chart = new ApexCharts(chartElement, options);
    chart.render();

    window.addEventListener('resize', function() {
        chart.updateOptions({
            chart: {
                width: chartElement.offsetWidth || '100%'
            }
        });
    });
}


