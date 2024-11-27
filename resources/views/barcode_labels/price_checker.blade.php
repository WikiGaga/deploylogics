<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ataybat Hypermarket Price Checker</title>
    <style>

        .container{
            max-width: 95%;
            margin-left: auto;
            margin-right: auto;
        }
        .w-100{
            width: 100%;
        }

        .price-input::placeholder{
            color: #fff;
        }

        input[type="text"]{
            font-size:32px;
            text-align: center;
            color: #fff;
        }
        .price-input,.final-price{
            height: 48px;
            width: 100%;
            text-align: center;
            background-color: #1da921;
        }
        .h-38px{
            height: 48px;
            background-color: #1da921;
        }
        .w-50{
            width: 50%;
        }
        .pointerEventsNone {
            opacity: 0.5;
            pointer-events: none;
            touch-action: none;
            user-select: none;
        }
    </style>
</head>
<body>
    
    <div class="container">
        <h1 style="text-align: center ;color:#1da921;font-family: sans-serif;">
            Price Checker
        </h1>
        <table align="center" class="w-100">
            <tr>
                <td colspan="2">
                    <input type="text" id="price-input" class="price-input" autofocus placeholder="Scan Barcode">
                    <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="text" id="productBarcode" readonly class="h-38px w-100">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="text" id="productNameArabic" readonly class="h-38px w-100">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="text" id="productNameEnglish" readonly class="h-38px w-100">
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" id="priceWithoutVAT" readonly class="h-38px w-100">
                </td>
                <td>
                    <input type="text" id="priceWithVat" readonly class="h-38px w-100">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="text" id="final-price" class="final-price" readonly style="background-color: red;">
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <img src="{{ asset('images') }}/1601992238.jpeg" alt="Atayebat Logo" style="margin-top:20px" width="130" height="130">
                </td>
            </tr>
        </table>
    </div>
    <script>
        function notNullEmpty(val,deci){
            if(val == null || val == '' || val == NaN || val == undefined){
                return Number(0).toFixed(deci);
            }else{
                return js__number_format(Number(val) , deci);
            }
        }
        if (!String.prototype.repeat) {
            String.prototype.repeat = function(count) {
                'use strict';
                if (this == null)
                throw new TypeError('can\'t convert ' + this + ' to object');

                var str = '' + this;
                // To convert string to integer.
                count = +count;
                // Check NaN
                if (count != count)
                count = 0;

                if (count < 0)
                throw new RangeError('repeat count must be non-negative');

                if (count == Infinity)
                throw new RangeError('repeat count must be less than infinity');

                count = Math.floor(count);
                if (str.length == 0 || count == 0)
                return '';

                // Ensuring count is a 31-bit integer allows us to heavily optimize the
                // main part. But anyway, most current (August 2014) browsers can't handle
                // strings 1 << 28 chars or longer, so:
                if (str.length * count >= 1 << 28)
                throw new RangeError('repeat count must not overflow maximum string size');

                var maxCount = str.length * count;
                count = Math.floor(Math.log(count) / Math.log(2));
                while (count) {
                str += str;
                count--;
                }
                str += str.substring(0, maxCount - str.length);
                return str;
            }
        }
        function toFixedNoRounding(num , n) {
            if(num == null || num == "" || num == undefined || isNaN(num)) { num = 0; }
            const reg = new RegExp("^-?\\d+(?:\\.\\d{0," + n + "})?", "g")
            const a = num.toString().match(reg)[0];
            const dot = a.indexOf(".");
            if (dot === -1) { // integer, insert decimal dot and pad up zeros
                return a + "." + "0".repeat(n);
            }
            const b = n - (a.length - dot) + 1;
            return b > 0 ? (a + "0".repeat(b)) : a;
        }
        function js__number_format(num) {
            var m = Number((Math.abs(num) * 1000).toPrecision(15)); // 1000 Means 3 Decimals
            var sign = num?num<0?-1:1:0
            m =  Math.round(m) / 1000 * sign; // 1000 Means 3 Decimals
            return toFixedNoRounding(m , 3);
        }
        function resetValue(){

            document.getElementById('productNameArabic').value = '';
            document.getElementById('productBarcode').value = '';
            document.getElementById('productNameEnglish').value = '';
            document.getElementById('priceWithoutVAT').value = '';
            document.getElementById('priceWithVat').value = '';
            document.getElementById('final-price').value = '';

            document.getElementById('price-input').value = '';
            document.getElementById('price-input').focus();
        }
        document.addEventListener('DOMContentLoaded', function(){
            var barcode = document.getElementById('price-input');
            var token = document.getElementById('csrf_token');
            barcode.addEventListener('change' , function(){

                document.body.classList.add("pointerEventsNone");

                var productArabicName = document.getElementById('productNameArabic');
                var productBarcode = document.getElementById('productBarcode');
                var productEnglishName = document.getElementById('productNameEnglish');
                var priceWithoutVat = document.getElementById('priceWithoutVAT');
                var priceWithVat = document.getElementById('priceWithVat');
                var fullPrice = document.getElementById('final-price');

                var data = "form_type=barcode_labels&val=" + barcode.value;
                var xmlHttp = new XMLHttpRequest();
                xmlHttp.onreadystatechange = function()
                {
                    if(xmlHttp.readyState == 4 && xmlHttp.status == 200)
                    {   
                        

                        var response = this.responseText;
                        response = JSON.parse(response);
                        if(response.status == 'success'){

                            document.body.classList.remove("pointerEventsNone");

                            var rate = response.data.rate.product_barcode_sale_rate_rate;
                            var vatApply = response.data.vat.product_barcode_tax_apply;
                            var vatPerc = response.data.vat.product_barcode_tax_value;
                            if (vatPerc == null || vatPerc == NaN || vatPerc == undefined) {
                                var vatPerc = 0;
                            }
                            var vatValue = 0;
                            
                            productBarcode.value = response.data.current_product.product_barcode_barcode;
                            productArabicName.value = response.data.current_product.product.product_arabic_name;
                            productEnglishName.value = response.data.current_product.product.product_name;
                            priceWithoutVat.value = 'Price : OMR ' + js__number_format(rate) + ' (ر.ع)';
                            
                            if(vatApply == "1"){
                                vatValue = ((parseFloat(rate) / 100) * parseFloat(vatPerc));
                                vatValue = notNullEmpty(vatValue, 3);
                                priceWithVat.value = 'VAT : OMR ' + vatValue + ' (ضريبة ر.ع)';
                                rate = (parseFloat(rate) + parseFloat(vatValue));
                            }else{
                                vatValue = 0;
                                priceWithVat.value = 'VAT FREE (صفر ضريبة)';
                            }
                            
                            fullPrice.value = 'TOTAL : OMR ' + js__number_format(rate) + ' (ر.ع)';

                            barcode.value = '';
                            barcode.focus();
                            setTimeout(resetValue , 10000);
                        }
                    }
                    if(xmlHttp.readyState == 4 && xmlHttp.status != 200){
                            
                        document.body.classList.remove("pointerEventsNone");

                        productBarcode.value = barcode.value;
                        productArabicName.value = 'الصنف غير موجود';
                        productEnglishName.value = 'Product Not Found!';
                        priceWithoutVat.value = '';
                        priceWithVat.value = '';
                        fullPrice.value = '';
                        
                        barcode.value = '';
                        barcode.focus();
                    }
                }
                xmlHttp.open("post", "{{route('BarcodePriceCheck')}}"+"/"+barcode.value); 
                xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
                // xmlHttp.setRequestHeader("X-CSRF-TOKEN", token.value);
                xmlHttp.send(JSON.stringify({ "val": barcode.value, "form_type": "barcode_labels" })); 
            });
        }, false);
    </script>
</body>
</html>