<?php

namespace App\Models;

use App\Models\Settings\TblDefiExpenseAccounts;
use Illuminate\Database\Eloquent\Model;

class TblPurcGrn extends Model
{
    protected $table = 'tbl_purc_grn';
    protected $primaryKey = 'grn_id';

    protected static function primaryKeyName() {
        return (new static)->getKeyName();
    }

    public function grn_dtl()
    {
        return $this->hasMany(TblPurcGrnDtl::class,"grn_id")
            ->with('product','barcode','uom','packing','supplier','constants','purchase_order')
            ->orderBy('sr_no','asc');
    }

    public function simp_dtls() {
        return $this->hasMany(TblPurcGrnDtl::class,"grn_id")
            ->select(['purc_grn_dtl_id','grn_id','tbl_purc_grn_dtl_amount','tbl_purc_grn_dtl_disc_amount','tbl_purc_grn_dtl_total_amount','tbl_purc_grn_dtl_vat_amount'])
            ->orderBy('sr_no','asc');
    }

    function supplier(){
        return $this->belongsTo(TblPurcSupplier::class, 'supplier_id');
    }

    function PO(){
        return $this->belongsTo(TblPurcPurchaseOrder::class, 'purchase_order_id');
    }
    public function grn_expense() {
        return $this->hasMany(TblPurcGrnExpense::class,"grn_id")->with('accounts','exp_acc_dtl');
    }
    function refPurcReturn(){
        return $this->belongsTo(TblPurcGrn::class , 'purc_return_ref' , 'grn_id');
    }

    public function grn_dtl_smpl_data()
    {
        return $this->hasMany(TblPurcGrnDtl::class,"grn_id")
            ->with('product_smpl_data','barcode_smpl_data')
            ->select('grn_id','purc_grn_dtl_id','product_barcode_id','product_id','tbl_purc_grn_dtl_sale_rate')
            ->orderBy('sr_no','asc');
    }
}
