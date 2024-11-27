<?php
namespace App\Helpers;

use App\Models\Defi\TblDefiConstants;

class Helper
{
    public static $DefaultBranch = 1;

    public static function constantValue($key)
    {
        $constants = \Illuminate\Support\Facades\Cache::rememberForever('constantValue', function () {
            return TblDefiConstants::where('constants_status',1)->pluck('constants_value','constants_key');
        });
        return isset($constants[$key])?$constants[$key]:"";
    }
    public static function shout(string $string)
    {
        return strtoupper($string);
    }
    public static function valEmpty($string)
    {
        if($string === "" || $string === null || $string === 0
            || $string === "0" || $string === 0.0 || $string === false || count((array)$string) === 0){
            return true;
        }
        return false;
    }
    public static function NumberEmpty($num){
        if($num === ''
            || $num === ""
            || $num === []
            || count((array)$num) === 0
            || $num === NULL
            || $num === FALSE
            || $num === "0"
        ){
            return true;
        }
        return false;
    }
    public static function conversionBaseUnitQty($arr){

        $packing = isset($arr['pd_packing']) && is_numeric($arr['pd_packing']) ? (float)$arr['pd_packing'] : 0;
        $quantity = isset($arr['quantity']) && is_numeric($arr['quantity']) ? (float)$arr['quantity'] : 0;
        $foc_qty = isset($arr['foc_qty']) && is_numeric($arr['foc_qty']) ? (float)$arr['foc_qty'] : 0;

        $qty = $packing * ($quantity + $foc_qty);
        return $qty;
    }


}
