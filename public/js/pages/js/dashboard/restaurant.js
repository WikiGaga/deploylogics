const primary = '#6993FF';
const success = '#1BC5BD';
const info = '#8950FC';
const warning = '#FFA800';
const danger = '#F64E60';

function loadRestaurantCharts(){
    restMonthSaleBranchAjax();
    salesByDayAjax();
    salesByHourAjax();
    paymentMethodChartAjax();
    orderTypeChartAjax();
    topFoodItemsAjax();
    branchPerformanceAjax();
    salesByMenuItemAjax(1);
    salesByLocationAjax(1);
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

function salesByDayChart(chartData, summary, breakdown){
    var days = [];
    var salesAmounts = [];
    var orderCounts = [];

    chartData.forEach(function(item){
        var dayLabel = item.day_label || item.DAY_LABEL || '';
        var salesAmount = parseFloat(item.sales_amount || item.SALES_AMOUNT || 0);
        var orderCount = parseInt(item.order_count || item.ORDER_COUNT || 0);
        if(dayLabel && !isNaN(salesAmount) && !isNaN(orderCount)){
            days.push(dayLabel);
            salesAmounts.push(salesAmount);
            orderCounts.push(orderCount);
        }
    });

    if(days.length === 0 || salesAmounts.length === 0){
        $('#sales_by_day_chart').html('<div class="text-center p-5">No data available</div>');
        return;
    }

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

    var containerWidth = chartElement.offsetWidth || chartElement.parentElement.offsetWidth;
    var containerHeight = 300;

    var options = {
        series: [{
            name: '',
            data: salesAmounts
        }],
        chart: {
            type: 'bar',
            height: containerHeight,
            width: containerWidth || '100%',
            toolbar: {
                show: true
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '60%',
                borderRadius: 4,
                dataLabels: {
                    position: 'top'
                }
            }
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        fill: {
            opacity: 1
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
            categories: days
        },
        yaxis: {
            title: {
                text: 'Amount'
            }
        },
        colors: [warning],
        tooltip: {
            y: {
                formatter: function (val, opts) {
                    var index = opts.dataPointIndex;
                    var orderCount = orderCounts[index] || 0;
                    var dayName = chartData[index] ? (chartData[index].day_name || chartData[index].DAY_NAME || days[index]) : days[index];
                    return  'OMR ' + val.toFixed(3) + ' Orders ' + orderCount;
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

function salesByHourAjax(){
    var formData = {
        chart_name : 'sales_by_hour'
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
                $('#sales_by_hour_chart').html("");
                var chartData = response['data']['sales_by_hour'];
                if(chartData && chartData.length > 0){
                    salesByHourChart(chartData);
                } else {
                    $('#sales_by_hour_chart').html('<div class="text-center p-5">No data available</div>');
                }
            }
        },
        error: function(xhr, status, error) {
            $('#sales_by_hour_chart').html('<div class="text-center p-5">Error loading chart data</div>');
        }
    });
}

function salesByHourChart(chartData){
    if(!chartData || chartData.length === 0){
        $('#sales_by_hour_chart').html('<div class="text-center p-5">No data available</div>');
        return;
    }

    var hourDataMap = {};
    var daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    var dayOrder = { 'MON': 0, 'TUE': 1, 'WED': 2, 'THU': 3, 'FRI': 4, 'SAT': 5, 'SUN': 6 };
    var allDays = new Set();

    for(var h = 0; h < 24; h++){
        var hourLabel = (h < 10 ? '0' : '') + h + ':00';
        hourDataMap[h] = {
            hour: h,
            hourLabel: hourLabel,
            data: {},
            orderCounts: {}
        };
    }

    chartData.forEach(function(item){
        var hour = parseInt(item.hour || item.HOUR || 0);
        var hourLabel = (item.hour_label || item.HOUR_LABEL || ((hour < 10 ? '0' : '') + hour + ':00')).substring(0, 5);
        var dayName = (item.day_name || item.DAY_NAME || '').toUpperCase().substring(0, 3);
        var salesAmount = parseFloat(item.sales_amount || item.SALES_AMOUNT || 0);
        var orderCount = parseInt(item.order_count || item.ORDER_COUNT || 0);

        if(hour >= 0 && hour < 24){
            if(!hourDataMap[hour]){
                hourDataMap[hour] = {
                    hour: hour,
                    hourLabel: hourLabel,
                    data: {},
                    orderCounts: {}
                };
            }
            if(hourDataMap[hour].data[dayName]){
                hourDataMap[hour].data[dayName] += salesAmount;
                hourDataMap[hour].orderCounts[dayName] += orderCount;
            } else {
                hourDataMap[hour].data[dayName] = salesAmount;
                hourDataMap[hour].orderCounts[dayName] = orderCount;
            }
            allDays.add(dayName);
        }
    });

    var allDaysArray = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];
    allDaysArray.forEach(function(day){
        allDays.add(day);
    });

    var sortedDays = Array.from(allDays).sort(function(a, b){
        return (dayOrder[a] || 99) - (dayOrder[b] || 99);
    });

    var sortedHours = [];
    for(var h = 0; h < 24; h++){
        sortedHours.push(h);
    }

    var series = [];
    var maxValue = 0;
    var minValue = Infinity;
    var orderCountMap = {};

    sortedHours.forEach(function(hour){
        var hourInfo = hourDataMap[hour];
        if(!hourInfo) return;

        var dataPoints = [];
        sortedDays.forEach(function(day, dayIndex){
            var value = hourInfo.data[day] || 0;
            var orderCount = hourInfo.orderCounts[day] || 0;
            dataPoints.push({
                x: day,
                y: value
            });
            var key = hour + '_' + day;
            orderCountMap[key] = orderCount;
            if(value > maxValue) maxValue = value;
            if(value < minValue && value > 0) minValue = value;
        });

        series.push({
            name: hourInfo.hourLabel,
            data: dataPoints
        });
    });

    if(series.length === 0){
        $('#sales_by_hour_chart').html('<div class="text-center p-5">No data available</div>');
        return;
    }

    var chartElement = document.querySelector("#sales_by_hour_chart");
    if(!chartElement){
        return;
    }

    var containerWidth = chartElement.offsetWidth || chartElement.parentElement.offsetWidth;
    var containerHeight = 500;

    if(minValue === Infinity) minValue = 0;
    var rangeSize = maxValue > minValue ? (maxValue - minValue) / 4 : maxValue / 4;
    if(rangeSize === 0 && maxValue > 0) rangeSize = maxValue / 4;

    var ranges = [];
    if(maxValue > 0){
        ranges = [
            {
                from: 0,
                to: minValue + rangeSize,
                name: 'low',
                color: '#FFE5CC'
            },
            {
                from: minValue + rangeSize,
                to: minValue + (rangeSize * 2),
                name: 'medium',
                color: '#FFCC99'
            },
            {
                from: minValue + (rangeSize * 2),
                to: minValue + (rangeSize * 3),
                name: 'high',
                color: '#FFA800'
            },
            {
                from: minValue + (rangeSize * 3),
                to: maxValue,
                name: 'extreme',
                color: '#FF8C00'
            }
        ];
    } else {
        ranges = [{
            from: 0,
            to: 0,
            name: 'no data',
            color: '#F0F0F0'
        }];
    }

    var options = {
        series: series,
        chart: {
            type: 'heatmap',
            height: containerHeight,
            width: containerWidth || '100%',
            toolbar: {
                show: true
            }
        },
        plotOptions: {
            heatmap: {
                shadeIntensity: 0.5,
                colorScale: {
                    ranges: ranges
                }
            }
        },
        dataLabels: {
            enabled: true,
            formatter: function(val, opts) {
                return 'OMR ' + parseFloat(val).toFixed(2);
            },
            style: {
                fontSize: '11px',
                colors: ["#304758"]
            }
        },
        xaxis: {
            categories: sortedDays.map(function(day){
                var dayNames = { 'MON': 'Monday', 'TUE': 'Tuesday', 'WED': 'Wednesday',
                                'THU': 'Thursday', 'FRI': 'Friday', 'SAT': 'Saturday', 'SUN': 'Sunday' };
                return dayNames[day] || day;
            })
        },
        tooltip: {
            custom: function({series, seriesIndex, dataPointIndex, w}) {
                var hour = w.globals.seriesNames[seriesIndex];
                var day = w.globals.categoryLabels[dataPointIndex];
                var value = series[seriesIndex][dataPointIndex];
                var hourNum = parseInt(hour.split(':')[0]);
                var nextHour = (hourNum + 1) % 24;
                var nextHourStr = (nextHour < 10 ? '0' : '') + nextHour + ':00';
                var orderCount = 0;

                if(seriesIndex < sortedHours.length && dataPointIndex < sortedDays.length){
                    var hourKey = sortedHours[seriesIndex];
                    var dayKey = sortedDays[dataPointIndex];
                    var mapKey = hourKey + '_' + dayKey;
                    orderCount = orderCountMap[mapKey] || 0;
                }

                return '<div style="padding: 10px; background: #000; color: #fff; border-radius: 4px;">' +
                       '<div><strong>' + hour + ' - ' + nextHourStr + '</strong></div>' +
                       '<div><strong>' + orderCount + ' Orders</strong></div>' +
                       '<div>OMR ' + parseFloat(value).toFixed(3) + '</div>' +
                       '</div>';
            }
        },
        colors: [warning]
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

function showMenuItemShimmer(){
    var html = '<div class="table-responsive">';
    html += '<table class="table table-striped table-hover">';
    html += '<thead><tr>';
    html += '<th>Menu item</th>';
    html += '<th class="text-right">Sales</th>';
    html += '</tr></thead>';
    html += '<tbody>';
    for(var i = 0; i < 3; i++){
        html += '<tr>';
        html += '<td><div class="shimmer shimmer-cell" style="width: 70%;"></div></td>';
        html += '<td class="text-right"><div class="shimmer shimmer-cell" style="width: 50%; margin-left: auto;"></div></td>';
        html += '</tr>';
    }
    html += '</tbody></table>';
    html += '</div>';
    $('#sales_by_menu_item_table').html(html);
}

function showLocationShimmer(){
    var html = '<div class="table-responsive">';
    html += '<table class="table table-striped table-hover">';
    html += '<thead><tr>';
    html += '<th>Branch</th>';
    html += '<th class="text-right">Sales</th>';
    html += '</tr></thead>';
    html += '<tbody>';
    for(var i = 0; i < 5; i++){
        html += '<tr>';
        html += '<td><div class="shimmer shimmer-cell" style="width: 70%;"></div></td>';
        html += '<td class="text-right"><div class="shimmer shimmer-cell" style="width: 50%; margin-left: auto;"></div></td>';
        html += '</tr>';
    }
    html += '</tbody></table>';
    html += '</div>';
    $('#sales_by_location_table').html(html);
}

function salesByMenuItemAjax(page){
    var formData = {
        chart_name : 'sales_by_menu_item',
        page: parseInt(page) || 1,
        per_page: 5
    };

    showMenuItemShimmer();

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
                var data = response['data']['sales_by_menu_item'] || [];
                var total = parseInt(response['data']['sales_by_menu_item_total'] || 0);
                var currentPage = parseInt(response['data']['sales_by_menu_item_page'] || 1);
                var perPage = parseInt(response['data']['sales_by_menu_item_per_page'] || 5);

                renderMenuItemTable(data, total, currentPage, perPage);
            } else {
                $('#sales_by_menu_item_table').html('<div class="text-center p-5">No data available</div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading menu item data:', error);
            $('#sales_by_menu_item_table').html('<div class="text-center p-5">Error loading data</div>');
        }
    });
}

function renderMenuItemTable(data, total, currentPage, perPage){
    var html = '<div class="table-responsive">';
    html += '<table class="table table-striped table-hover">';
    html += '<thead><tr>';
    html += '<th>Menu item</th>';
    html += '<th class="text-right">Sales</th>';
    html += '</tr></thead>';
    html += '<tbody>';

    if(data && data.length > 0){
        data.forEach(function(item){
            var menuItem = item.menu_item || item.MENU_ITEM || '';
            var salesAmount = parseFloat(item.sales_amount || item.SALES_AMOUNT || 0);
            var itemCount = parseInt(item.item_count || item.ITEM_COUNT || 0);

            html += '<tr>';
            html += '<td>' + menuItem + '</td>';
            html += '<td class="text-right">OMR ' + salesAmount.toFixed(3) + ' <span class="kt-font-sm kt-font-muted">(' + itemCount + ' items)</span></td>';
            html += '</tr>';
        });
    } else {
        html += '<tr><td colspan="2" class="text-center">No data available</td></tr>';
    }

    html += '</tbody></table>';
    html += '</div>';

    // Pagination
    var totalPages = Math.ceil(total / perPage);
    if(totalPages > 1){
        html += '<div class="d-flex justify-content-between align-items-center mt-3">';
        html += '<div class="kt-font-sm kt-font-muted">Rows per page ' + perPage + '</div>';
        html += '<div class="kt-font-sm kt-font-muted">' + ((currentPage - 1) * perPage + 1) + '-' + Math.min(currentPage * perPage, total) + ' / ' + total + '</div>';
        html += '<div>';

        if(currentPage > 1){
            html += '<button type="button" class="btn btn-sm btn-secondary" onclick="salesByMenuItemAjax(' + (currentPage - 1) + ')">Previous</button> ';
        }
        if(currentPage < totalPages){
            html += '<button type="button" class="btn btn-sm btn-secondary" onclick="salesByMenuItemAjax(' + (currentPage + 1) + ')">Next</button>';
        }

        html += '</div>';
        html += '</div>';
    }

    $('#sales_by_menu_item_table').html(html);
}

function salesByLocationAjax(page){
    var formData = {
        chart_name : 'sales_by_location',
        page: parseInt(page) || 1,
        per_page: 5
    };

    showLocationShimmer();

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
                var data = response['data']['sales_by_location'] || [];
                var total = parseInt(response['data']['sales_by_location_total'] || 0);
                var currentPage = parseInt(response['data']['sales_by_location_page'] || 1);
                var perPage = parseInt(response['data']['sales_by_location_per_page'] || 5);

                renderLocationTable(data, total, currentPage, perPage);
            } else {
                $('#sales_by_location_table').html('<div class="text-center p-5">No data available</div>');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading location data:', error);
            $('#sales_by_location_table').html('<div class="text-center p-5">Error loading data</div>');
        }
    });
}

function renderLocationTable(data, total, currentPage, perPage){
    var html = '<div class="table-responsive">';
    html += '<table class="table table-striped table-hover">';
    html += '<thead><tr>';
    html += '<th>Branch</th>';
    html += '<th class="text-right">Sales</th>';
    html += '</tr></thead>';
    html += '<tbody>';

    if(data && data.length > 0){
        data.forEach(function(item){
            var branch = item.location || item.LOCATION || item.branch_name || item.BRANCH_NAME || '';
            var salesAmount = parseFloat(item.sales_amount || item.SALES_AMOUNT || 0);
            var orderCount = parseInt(item.order_count || item.ORDER_COUNT || 0);

            html += '<tr>';
            html += '<td>' + branch + '</td>';
            html += '<td class="text-right">OMR ' + salesAmount.toFixed(3) + ' <span class="kt-font-sm kt-font-muted">(' + orderCount + ' orders)</span></td>';
            html += '</tr>';
        });
    } else {
        html += '<tr><td colspan="2" class="text-center">No data available</td></tr>';
    }

    html += '</tbody></table>';
    html += '</div>';

    // Pagination
    var totalPages = Math.ceil(total / perPage);
    if(totalPages > 1){
        html += '<div class="d-flex justify-content-between align-items-center mt-3">';
        html += '<div class="kt-font-sm kt-font-muted">Rows per page ' + perPage + '</div>';
        html += '<div class="kt-font-sm kt-font-muted">' + ((currentPage - 1) * perPage + 1) + '-' + Math.min(currentPage * perPage, total) + ' / ' + total + '</div>';
        html += '<div>';

        if(currentPage > 1){
            html += '<button type="button" class="btn btn-sm btn-secondary" onclick="salesByLocationAjax(' + (currentPage - 1) + ')">Previous</button> ';
        }
        if(currentPage < totalPages){
            html += '<button type="button" class="btn btn-sm btn-secondary" onclick="salesByLocationAjax(' + (currentPage + 1) + ')">Next</button>';
        }

        html += '</div>';
        html += '</div>';
    }

    $('#sales_by_location_table').html(html);
}

