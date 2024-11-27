<?php

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            []
        ];
        $i = 1;
        $p = 7;
        $product_id = 12324;
        $myArr = [];
        foreach ($data as $item) {

            try{
                $name = trim($item[0]);
                $myArr['name'] = $name;
                $group_item_name = trim(strtolower(strtoupper($item[2])));
                $group_item = null;
                if(\App\Models\TblPurcGroupItem::where(\Illuminate\Support\Facades\DB::raw("lower(group_item_name)"),$group_item_name)->exists()){
                    $group_item = \App\Models\TblPurcGroupItem::where(\Illuminate\Support\Facades\DB::raw("lower(group_item_name)"),$group_item_name)->first();
                }

                $product_brand_name = trim(strtolower(strtoupper($item[3])));
                $product_brand = null;
                if(\App\Models\TblPurcBrand::where(\Illuminate\Support\Facades\DB::raw("lower(brand_name)"),$product_brand_name)->exists()){
                    $product_brand = \App\Models\TblPurcBrand::where(\Illuminate\Support\Facades\DB::raw("lower(brand_name)"),$product_brand_name)->first();
                }

                $product_item_type_name = trim(strtolower(strtoupper($item[4])));
                $product_item_type = null;
                if(\App\Models\TblSoftProductTypeGroup::where(\Illuminate\Support\Facades\DB::raw("lower(product_type_group_name)"),$product_item_type_name)->exists()){
                    $product_item_type = \App\Models\TblSoftProductTypeGroup::where(\Illuminate\Support\Facades\DB::raw("lower(product_type_group_name)"),$product_item_type_name)->first();
                }

                $country_name = trim(strtolower(strtoupper($item[5])));
                $country = null;
                if(\App\Models\TblDefiCountry::where(\Illuminate\Support\Facades\DB::raw("lower(country_name)"),$country_name)->exists()){
                    $country= \App\Models\TblDefiCountry::where(\Illuminate\Support\Facades\DB::raw("lower(country_name)"),$country_name)->first();
                }

                $manufacturer_name = trim(strtolower(strtoupper($item[6])));
                $manufacturer = null;
                if(\App\Models\TblPurcManufacturer::where(\Illuminate\Support\Facades\DB::raw("lower(manufacturer_name)"),$manufacturer_name)->exists()){
                    $manufacturer= \App\Models\TblPurcManufacturer::where(\Illuminate\Support\Facades\DB::raw("lower(manufacturer_name)"),$manufacturer_name)->first();
                }

                \App\Models\TblPurcProduct::create([
                    'product_id' => $product_id,
                    'product_code' => 'P-'.sprintf("%'07d", $p),
                    'product_name' => $name,
                    'group_item_id' => isset($group_item->group_item_id)?$group_item->group_item_id:"",
                    'group_item_parent_id' => isset($group_item->parent_group_id)?$group_item->parent_group_id:"",
                    'product_brand_id' => isset($product_brand->brand_id)?$product_brand->brand_id:"",
                    'product_item_type' => isset($product_item_type->product_type_group_id)?$product_item_type->product_type_group_id:"",
                    'country_id' => isset($country->country_id)?$country->country_id:"",
                    'product_manufacturer_id' => isset($manufacturer->manufacturer_id)?$manufacturer->manufacturer_id:"",
                    'product_perishable' => 1,
                    'product_can_sale' => 1,
                    'product_entry_status' => 1,
                    'business_id' => 1,
                    'company_id' => 1,
                    'branch_id' => 1,
                    'product_user_id' => 81,
                ]);
                $barcode_id = $product_id + $i;
                $barcode_barcode = trim($item[7]);

                $uom_name = trim(strtolower(strtoupper($item[10])));
                $uom = null;
                if(\App\Models\TblDefiUom::where(\Illuminate\Support\Facades\DB::raw("lower(uom_name)"),$uom_name)->exists()){
                    $uom= \App\Models\TblDefiUom::where(\Illuminate\Support\Facades\DB::raw("lower(uom_name)"),$uom_name)->first();
                }

                $variant_name = trim(strtolower(strtoupper($item[9])));
                $variant = null;
                if(\App\Models\Defi\TblDefiVariant::where(\Illuminate\Support\Facades\DB::raw("lower(variant_name)"),$variant_name)->exists()){
                    $variant= \App\Models\Defi\TblDefiVariant::where(\Illuminate\Support\Facades\DB::raw("lower(variant_name)"),$variant_name)->first();
                }
                \App\Models\TblPurcProductBarcode::create([
                    'product_id' => $product_id,
                    'product_barcode_id' => $barcode_id,
                    'product_barcode_barcode' => $barcode_barcode,
                    'uom_id' => isset($uom->uom_id)?$uom->uom_id:"",
                    'uom_name' => isset($uom->uom_name)?$uom->uom_name:"",
                    'product_barcode_packing' => 1,
                    'product_barcode_weight_apply' => $item[8] == 1?1:0,
                    'product_barcode_entry_status' => 1,
                    'variant_id' => isset($variant->variant_id)?$variant->variant_id:"",
                    'product_barcode_sr_no' => 1,
                    'base_barcode' => 1,
                    'business_id' => 1,
                    'product_barcode_user_id' => 81,
                ]);

                $purch_id= $barcode_id + $i;

                $tax_group_name = trim(strtolower(strtoupper($item[13])));
                $tax_group = null;
                if(\App\Models\Defi\TblDefiTaxGroup::where(\Illuminate\Support\Facades\DB::raw("lower(tax_group_name)"),$tax_group_name)->exists()){
                    $tax_group= \App\Models\Defi\TblDefiTaxGroup::where(\Illuminate\Support\Facades\DB::raw("lower(tax_group_name)"),$tax_group_name)->first();
                }
                $cost_rate = (float)$item[11];
                $sale_rate = (float)$item[12];
                $gp_perc = 0;
                $gp_amount = 0;
                if(!empty($cost_rate) && !empty($sale_rate)){
                    $gp_amount = (float)$sale_rate - (float)$cost_rate;
                    $gp_perc = ((float)$gp_amount / (float)$cost_rate) * 100;
                    if(!empty($tax_group->tax_group_value)){
                        $calc_tax = (float)$sale_rate / 100 * $tax_group->tax_group_value;
                        $inclusive_tax_price = (float)$sale_rate + $calc_tax;
                    }
                }

                \App\Models\TblPurcProductBarcodePurchRate::create([
                    'product_barcode_purch_id' => $purch_id,
                    'product_id' => $product_id,
                    'product_barcode_id' => $barcode_id,
                    'branch_id' => 1,
                    'product_barcode_cost_rate' => number_format($cost_rate,3,'.',''),
                    'sale_rate' => number_format($sale_rate,3,'.',''),
                    'gp_perc' => number_format($gp_perc,3,'.',''),
                    'gp_amount' => number_format($gp_amount,3,'.',''),
                    'product_barcode_barcode' => $barcode_barcode,
                    'tax_group_id' =>  isset($tax_group->tax_group_id)?$tax_group->tax_group_id:"",
                    'tax_rate' =>  isset($tax_group->tax_group_value)?$tax_group->tax_group_value:"",
                    'inclusive_tax_price' =>  isset($inclusive_tax_price)?$inclusive_tax_price:"",
                    'company_id' => 1,
                    'business_id' => 1,
                ]);
                $product_id = $purch_id + $i;
                $i = $i + 1;
                $p = $p + 1;

            }catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollback();
                dump($myArr);
                dd($e->getMessage());
            }
            \Illuminate\Support\Facades\DB::commit();
            echo $i." - ";
        }
    }
}
