<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('purchase', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });
   // Purchase tbl_purc_purchasing

 $tbl_purc_purchasing = 'tbl_purc_purchasing';
 if (!Schema::hasTable($tbl_purc_purchasing)) {
     Schema::create('tbl_purc_purchasing', function (Blueprint $table) {
         $table->bigInteger('purchasing_id')->primary();
         $table->timestamps();
     });
 }
 Schema::table($tbl_purc_purchasing, function (Blueprint $table) use ($tbl_purc_purchasing) {
     if (!Schema::hasColumn($tbl_purc_purchasing,'purchasing_id')) {
         $table->text('purchasing_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_purc_purchasing,'purchasing_code')) {
         $table->string('purchasing_code')->nullable();
     }
     if (!Schema::hasColumn($tbl_purc_purchasing,'purchasing_type')) {
         $table->string('purchasing_type')->nullable();
     }
     if (!Schema::hasColumn($tbl_purc_purchasing,'purchasing_entry_date')) {
         $table->bigInteger('purchasing_entry_date')->nullable();
     }
     if (!Schema::hasColumn($tbl_purc_purchasing,'salesman_id')) {
      $table->bigInteger('salesman_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_purc_purchasing,'business_id')) {
         $table->bigInteger('business_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_purc_purchasing,'company_id')) {
         $table->bigInteger('company_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_purc_purchasing,'branch_id')) {
         $table->bigInteger('branch_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_purc_purchasing,'purchasing_user_id')) {
         $table->bigInteger('purchasing_user_id')->nullable();
     }
 });

//Note:*____________In this table branch_id , Business_id , company_id and user_id is not exist so i skip this table

 // Purchase tbl_purc_purchasing_dtl 

//  $tbl_purc_purchasing_dtl = 'tbl_purc_purchasing_dtl';
//  if (!Schema::hasTable($tbl_purc_purchasing_dtl)) {
//      Schema::create('tbl_purc_purchasing_dtl', function (Blueprint $table) {
//          $table->bigInteger('purchasing_dtl_id')->primary();
//          $table->timestamps();
//      });
//  }
//  Schema::table($tbl_purc_purchasing_dtl, function (Blueprint $table) use ($tbl_purc_purchasing_dtl) {
//      if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'purchasing_dtl_id')) {
//          $table->text('purchasing_dtl_id')->nullable();
//      }
//      if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'PURCHASING_ID')) {
//          $table->bigInteger('PURCHASING_ID')->nullable();
//      }
//      if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'stock_id')) {
//          $table->bigInteger('stock_id')->nullable();
//      }
//      if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'stock_no')) {
//          $table->string('stock_no')->nullable();
//      }
//      if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'purchasing_dtl_no')) {
//       $table->bigInteger('purchasing_dtl_no')->nullable();
//      }
   
//      if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'purchasing_dtl_sr_no')) {
//         $table->bigInteger('purchasing_dtl_sr_no')->nullable();
//     }
//     if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'product_id')) {
//         $table->bigInteger('product_id')->nullable();
//     }
//     if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'product_barcode_id')) {
//         $table->bigInteger('product_barcode_id')->nullable();
//     }
//     if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'uom_id')) {
//      $table->bigInteger('uom_id')->nullable();
//     }   

//     if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'purchasing_dtl_barcode')) {
//         $table->string('purchasing_dtl_barcode')->nullable();
//     }
//     if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'purchasing_dtl_packing')) {
//         $table->string('purchasing_dtl_packing')->nullable();
//     }
//     if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'purchasing_dtl_branch_id')) {
//         $table->bigInteger('purchasing_dtl_branch_id')->nullable();
//     }
//     if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'purchasing_dtl_demand_quantity')) {
//      $table->bigInteger('purchasing_dtl_demand_quantity')->nullable();
//     }  

//     if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'purchasing_dtl_total_quantity')) {
//         $table->bigInteger('purchasing_dtl_total_quantity')->nullable();
//     }
//     if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'purchasing_dtl_purc_quantity')) {
//         $table->bigInteger('purchasing_dtl_purc_quantity')->nullable();
//     }
//     if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'purchasing_dtl_diff_quantity')) {
//         $table->bigInteger('purchasing_dtl_diff_quantity')->nullable();
//     }
//      if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'business_id')) {
//          $table->bigInteger('business_id')->nullable();
//      }
//      if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'company_id')) {
//          $table->bigInteger('company_id')->nullable();
//      }
//      if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'branch_id')) {
//          $table->bigInteger('branch_id')->nullable();
//      }
//      if (!Schema::hasColumn($tbl_purc_purchasing_dtl,'purchasing_user_id')) {
//          $table->bigInteger('purchasing_user_id')->nullable();
//      }
//  });




// Purchase tbl_purc_purchasing_dtl_dtl 

$tbl_purc_purchasing_dtl_dtl = 'tbl_purc_purchasing_dtl_dtl';
if (!Schema::hasTable($tbl_purc_purchasing_dtl_dtl)) {
    Schema::create('tbl_purc_purchasing_dtl_dtl', function (Blueprint $table) {
        $table->bigInteger('purchasing_dtl_dtl_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_purchasing_dtl_dtl, function (Blueprint $table) use ($tbl_purc_purchasing_dtl_dtl) {
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'purchasing_dtl_dtl_id')) {
        $table->text('purchasing_dtl_dtl_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'purchasing_dtl_id')) {
        $table->bigInteger('purchasing_dtl_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'purchasing_dtl_dtl_sr_no')) {
        $table->string('purchasing_dtl_dtl_sr_no')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'product_id')) {
        $table->bigInteger('product_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'product_barcode_id')) {
     $table->bigInteger('product_barcode_id')->nullable();
    }
  
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'uom_id')) {
        $table->bigInteger('uom_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'purchasing_dtl_dtl_barcode')) {
        $table->string('purchasing_dtl_dtl_barcode')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'purchasing_dtl_dtl_packing')) {
        $table->string('purchasing_dtl_dtl_packing')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'purchasing_dtl_dtl_quantity')) {
     $table->bigInteger('purchasing_dtl_dtl_quantity')->nullable();
    }

    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'purchasing_dtl_dtl_foc_quantity')) {
        $table->bigInteger('purchasing_dtl_dtl_foc_quantity')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'purchasing_dtl_dtl_fc_rate')) {
        $table->bigInteger('purchasing_dtl_dtl_fc_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'purchasing_dtl_dtl_rate')) {
        $table->bigInteger('purchasing_dtl_dtl_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'purchasing_dtl_dtl_amount')) {
     $table->bigInteger('purchasing_dtl_dtl_amount')->nullable();
    } 
    
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'purchasing_dtl_dtl_disc_percent')) {
        $table->bigInteger('purchasing_dtl_dtl_disc_percent')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'purchasing_dtl_dtl_disc_amount')) {
        $table->bigInteger('purchasing_dtl_dtl_disc_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'purchasing_dtl_dtl_vat_percent')) {
        $table->bigInteger('purchasing_dtl_dtl_vat_percent')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'purchasing_dtl_dtl_vat_amount')) {
     $table->bigInteger('purchasing_dtl_dtl_vat_amount')->nullable();
    }  
    
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'purchasing_dtl_dtl_net_amount')) {
        $table->bigInteger('purchasing_dtl_dtl_net_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchasing_dtl_dtl,'purchasing_dtl_dtl_user_id')) {
        $table->bigInteger('purchasing_dtl_dtl_user_id')->nullable();
    }
});

   //supplier   tbl_purc_supplier
   $tbl_purc_supplier = 'tbl_purc_supplier';
if (!Schema::hasTable($tbl_purc_supplier)) {
    Schema::create('tbl_purc_supplier', function (Blueprint $table) {
        $table->bigInteger('supplier_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_supplier, function (Blueprint $table) use ($tbl_purc_supplier) {
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_id')) {
        $table->text('supplier_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_code')) {
        $table->string('supplier_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_type')) {
        $table->string('supplier_type')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_name')) {
        $table->string('supplier_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_local_name')) {
     $table->string('supplier_local_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_suffix')) {
        $table->string('supplier_suffix')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_address')) {
        $table->string('supplier_address')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'country_id')) {
        $table->bigInteger('country_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'city_id')) {
     $table->bigInteger('city_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'region_id')) {
        $table->bigInteger('region_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_zip_code')) {
        $table->string('supplier_zip_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_po_box')) {
        $table->string('supplier_po_box')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_bar_code')) {
     $table->string('supplier_bar_code')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_phone_1')) {
        $table->string('supplier_phone_1')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_mobile_no')) {
        $table->string('supplier_mobile_no')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_fax')) {
        $table->string('supplier_fax')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_whatapp_no')) {
     $table->string('supplier_whatapp_no')->nullable();
    }   
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_email')) {
        $table->string('supplier_email')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_website')) {
        $table->string('supplier_website')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_account_id')) {
        $table->string('supplier_account_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_tax_no')) {
        $table->string('supplier_tax_no')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_entry_status')) {
     $table->bigInteger('supplier_entry_status')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_tax_rate')) {
        $table->bigInteger('supplier_tax_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_tax_status')) {
        $table->string('supplier_tax_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'payment_term_id')) {
        $table->bigInteger('payment_term_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_ageing_terms_value')) {
     $table->bigInteger('supplier_ageing_terms_value')->nullable();
    }   
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_div_name')) {
        $table->string('supplier_div_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_cheque_beneficry_name')) {
        $table->string('supplier_cheque_beneficry_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_credit_limit')) {
        $table->bigInteger('supplier_credit_limit')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_credit_period')) {
        $table->bigInteger('supplier_credit_period')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_credit_period_type')) {
        $table->bigInteger('supplier_credit_period_type')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_debit_limit')) {
        $table->bigInteger('supplier_debit_limit')->nullable();
    }    
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_can_scale')) {
        $table->bigInteger('supplier_can_scale')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_mode_of_payment')) {
        $table->bigInteger('supplier_mode_of_payment')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_image')) {
        $table->string('supplier_image')->nullable();
    }  
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_branch_id')) {
        $table->string('supplier_branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_contact_person')) {
        $table->string('supplier_contact_person')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_contact_person_mobile')) {
        $table->string('supplier_contact_person_mobile')->nullable();
    }  
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_bank_name')) {
        $table->string('supplier_bank_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_bank_account_title')) {
        $table->string('supplier_bank_account_title')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_bank_account_no')) {
        $table->string('supplier_bank_account_no')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_contact_person_designation')) {
        $table->string('supplier_contact_person_designation')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_longitude')) {
        $table->string('supplier_longitude')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_latitude')) {
        $table->string('supplier_latitude')->nullable();
    }  
    if (!Schema::hasColumn($tbl_purc_supplier,'notes')) {
        $table->string('notes')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_reference_code')) {
        $table->string('supplier_reference_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier,'supplier_user_id')) {
        $table->bigInteger('supplier_user_id')->nullable();
    }
});

//supplier   tbl_purc_supplier_account

$tbl_purc_supplier_account = 'tbl_purc_supplier_account';
if (!Schema::hasTable($tbl_purc_supplier_account)) {
    Schema::create('tbl_purc_supplier_account', function (Blueprint $table) {
        $table->bigInteger('supplier_account_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_supplier_account, function (Blueprint $table) use ($tbl_purc_supplier_account) {
    if (!Schema::hasColumn($tbl_purc_supplier_account,'supplier_account_id')) {
        $table->text('supplier_account_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_account,'supplier_id')) {
        $table->bigInteger('supplier_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_account,'supplier_bank_name')) {
        $table->string('supplier_bank_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_account,'supplier_account_no')) {
        $table->string('supplier_account_no')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_account,'supplier_account_title')) {
     $table->string('supplier_account_title')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_account,'supplier_iban_no')) {
        $table->string('supplier_iban_no')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_account,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_account,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_account,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_account,'supplier_account_user_id')) {
        $table->bigInteger('supplier_account_user_id')->nullable();
    }
});


//supplier   tbl_purc_supplier_branch

$tbl_purc_supplier_branch = 'tbl_purc_supplier_branch';
if (!Schema::hasTable($tbl_purc_supplier_branch)) {
    Schema::create('tbl_purc_supplier_branch', function (Blueprint $table) {
        $table->bigInteger('supplier_branch_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_supplier_branch, function (Blueprint $table) use ($tbl_purc_supplier_branch) {
    if (!Schema::hasColumn($tbl_purc_supplier_branch,'supplier_branch_id')) {
        $table->text('supplier_branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_branch,'supplier_id')) {
        $table->bigInteger('supplier_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_branch,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_branch,'supplier_branch_entry_status')) {
     $table->bigInteger('supplier_branch_entry_status')->nullable();
    }
    // if (!Schema::hasColumn($tbl_purc_supplier_branch,'business_id')) {
    //     $table->bigInteger('business_id')->nullable();
    // }
    // if (!Schema::hasColumn($tbl_purc_supplier_branch,'company_id')) {
    //     $table->bigInteger('company_id')->nullable();
    // }
    // if (!Schema::hasColumn($tbl_purc_supplier_branch,'branch_id')) {
    //     $table->bigInteger('branch_id')->nullable();
    // }
    // if (!Schema::hasColumn($tbl_purc_supplier_branch,'supplier_branch_user_id')) {
    //     $table->bigInteger('supplier_branch_user_id')->nullable();
    // }
});



//supplier   tbl_purc_supplier_sub

$tbl_purc_supplier_sub = 'tbl_purc_supplier_sub';
if (!Schema::hasTable($tbl_purc_supplier_sub)) {
    Schema::create('tbl_purc_supplier_sub', function (Blueprint $table) {
        $table->bigInteger('supplier_dtl_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_supplier_sub, function (Blueprint $table) use ($tbl_purc_supplier_sub) {
    if (!Schema::hasColumn($tbl_purc_supplier_sub,'supplier_dtl_id')) {
        $table->text('supplier_dtl_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_sub,'supplier_id')) {
        $table->bigInteger('supplier_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_sub,'supplier_dtl_name')) {
        $table->string('supplier_dtl_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_sub,'supplier_dtl_cont_no')) {
     $table->string('supplier_dtl_cont_no')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_sub,'supplier_dtl_address')) {
        $table->string('supplier_dtl_address')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_sub,'supplier_dtl_entry_status')) {
        $table->bigInteger('supplier_dtl_entry_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_sub,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_sub,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_sub,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_sub,'supplier_dtl_user_id')) {
        $table->bigInteger('supplier_dtl_user_id')->nullable();
    }
});


//purchase Demand   tbl_purc_demand

$tbl_purc_demand = 'tbl_purc_demand';
if (!Schema::hasTable($tbl_purc_demand)) {
    Schema::create('tbl_purc_demand', function (Blueprint $table) {
        $table->bigInteger('demand_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_demand, function (Blueprint $table) use ($tbl_purc_demand) {
    if (!Schema::hasColumn($tbl_purc_demand,'demand_id')) {
        $table->text('demand_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand,'DEMAND_NO')) {
        $table->string('DEMAND_NO')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand,'demand_entry_type')) {
        $table->string('demand_entry_type')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand,'salesman_id')) {
     $table->bigInteger('salesman_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand,'demand_type')) {
        $table->string('demand_type')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand,'demand_forward_for_approval')) {
        $table->string('demand_forward_for_approval')->nullable();
    }

    if (!Schema::hasColumn($tbl_purc_demand,'demand_entry_status')) {
        $table->bigInteger('demand_entry_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand,'notes')) {
        $table->string('notes')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand,'demand_date')) {
     $table->string('demand_date')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand,'supplier_id')) {
        $table->bigInteger('supplier_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand,'demand_notes')) {
        $table->bigInteger('demand_notes')->nullable();
    }

    if (!Schema::hasColumn($tbl_purc_demand,'demand_branch_to')) {
        $table->bigInteger('demand_branch_to')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand,'demand_device_id')) {
        $table->bigInteger('demand_device_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand,'demand_user_id')) {
        $table->bigInteger('demand_user_id')->nullable();
    }
});


//purchase Demand Dtl  tbl_purc_demand_dtl

$tbl_purc_demand_dtl = 'tbl_purc_demand_dtl';
if (!Schema::hasTable($tbl_purc_demand_dtl)) {
    Schema::create('tbl_purc_demand_dtl', function (Blueprint $table) {
        $table->bigInteger('demand_dtl_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_demand_dtl, function (Blueprint $table) use ($tbl_purc_demand_dtl) {
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'demand_dtl_id')) {
        $table->text('demand_dtl_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'product_id')) {
        $table->string('product_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'demand_dtl_bar_code')) {
        $table->string('demand_dtl_bar_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'demand_dtl_uom')) {
     $table->string('demand_dtl_uom')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'demand_dtl_packing')) {
        $table->string('demand_dtl_packing')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'demand_dtl_physical_stock')) {
        $table->bigInteger('demand_dtl_physical_stock')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'demand_dtl_store_stock')) {
        $table->bigInteger('demand_dtl_store_stock')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'demand_dtl_stock_match')) {
        $table->string('demand_dtl_stock_match')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'demand_dtl_suggest_quantity1')) {
     $table->bigInteger('demand_dtl_suggest_quantity1')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'demand_dtl_suggest_quantity2')) {
        $table->bigInteger('demand_dtl_suggest_quantity2')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'demand_dtl_demand_quantity')) {
        $table->bigInteger('demand_dtl_demand_quantity')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'demand_dtl_wip_lpo_stock')) {
        $table->bigInteger('demand_dtl_wip_lpo_stock')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'demand_dtl_pur_ret_in_waiting')) {
        $table->bigInteger('demand_dtl_pur_ret_in_waiting')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'demand_dtl_pending_status')) {
        $table->bigInteger('demand_dtl_pending_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'demand_dtl_approve_status')) {
        $table->bigInteger('demand_dtl_approve_status')->nullable();
    }    
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'demand_dtl_reject_status')) {
        $table->bigInteger('demand_dtl_reject_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'demand_dtl_entry_status')) {
        $table->bigInteger('demand_dtl_entry_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'product_barcode_id')) {
        $table->bigInteger('product_barcode_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'product_barcode_barcode')) {
        $table->string('product_barcode_barcode')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'sr_no')) {
        $table->bigInteger('sr_no')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_dtl,'demand_dtl_user_id')) {
        $table->bigInteger('demand_dtl_user_id')->nullable();
    }
});


//purchase Demand approval  tbl_purc_demand_approval

$tbl_purc_demand_approval = 'tbl_purc_demand_approval';
if (!Schema::hasTable($tbl_purc_demand_approval)) {
    Schema::create('tbl_purc_demand_approval', function (Blueprint $table) {
        $table->bigInteger('demand_approval_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_demand_approval, function (Blueprint $table) use ($tbl_purc_demand_approval) {
    if (!Schema::hasColumn($tbl_purc_demand_approval,'demand_approval_id')) {
        $table->text('demand_approval_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval,'demand_approval_entry_type')) {
        $table->string('demand_approval_entry_type')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval,'salesman_id')) {
        $table->bigInteger('salesman_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval,'supplier_id')) {
     $table->bigInteger('supplier_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval,'demand_approval_entry_date_time')) {
        $table->date('demand_approval_entry_date_time')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval,'demand_approval_demand_type')) {
        $table->string('demand_approval_demand_type')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval,'demand_approval_forward_for_approval')) {
        $table->string('demand_approval_forward_for_approval')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval,'demand_approval_entry_status')) {
        $table->string('demand_approval_entry_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval,'demand_approval_code')) {
     $table->string('demand_approval_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval,'demand_approval_user_id')) {
        $table->bigInteger('demand_approval_user_id')->nullable();
    }
});


//purchase Demand approval Dtl  tbl_purc_demand_approval_dtl

$tbl_purc_demand_approval_dtl = 'tbl_purc_demand_approval_dtl';
if (!Schema::hasTable($tbl_purc_demand_approval_dtl)) {
    Schema::create('tbl_purc_demand_approval_dtl', function (Blueprint $table) {
        $table->bigInteger('demand_approval_dtl_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_demand_approval_dtl, function (Blueprint $table) use ($tbl_purc_demand_approval_dtl) {
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_id')) {
        $table->text('demand_approval_dtl_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_code')) {
        $table->string('demand_approval_dtl_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'product_id')) {
        $table->bigInteger('product_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_bar_code')) {
     $table->string('demand_approval_dtl_bar_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'uom_id')) {
        $table->bigInteger('uom_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_packing')) {
        $table->string('demand_approval_dtl_packing')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_physical_stock')) {
        $table->bigInteger('demand_approval_dtl_physical_stock')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_store_stock')) {
        $table->bigInteger('demand_approval_dtl_store_stock')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_stock_match')) {
     $table->string('demand_approval_dtl_stock_match')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_suggest_quantity1')) {
        $table->bigInteger('demand_approval_dtl_suggest_quantity1')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_suggest_quantity2')) {
        $table->bigInteger('demand_approval_dtl_suggest_quantity2')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_demand_quantity')) {
        $table->bigInteger('demand_approval_dtl_demand_quantity')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_wip_lpo_stock')) {
        $table->bigInteger('demand_approval_dtl_wip_lpo_stock')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_pur_ret_in_waiting')) {
     $table->bigInteger('demand_approval_dtl_pur_ret_in_waiting')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_demand_qty')) {
        $table->date('demand_approval_dtl_demand_qty')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_approve_qty')) {
        $table->bigInteger('demand_approval_dtl_approve_qty')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_notes')) {
        $table->string('demand_approval_dtl_notes')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_pending_status')) {
        $table->string('demand_approval_dtl_pending_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_approve_status')) {
     $table->string('demand_approval_dtl_approve_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_reject_status')) {
        $table->string('demand_approval_dtl_reject_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_entry_status')) {
        $table->string('demand_approval_dtl_entry_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_remarks')) {
        $table->bigInteger('demand_approval_dtl_remarks')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_remarks_id')) {
        $table->bigInteger('demand_approval_dtl_remarks_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_date')) {
     $table->date('demand_approval_dtl_date')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_id')) {
        $table->bigInteger('demand_approval_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_dtl_id')) {
        $table->bigInteger('demand_dtl_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_id')) {
        $table->bigInteger('demand_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_branch_id')) {
        $table->bigInteger('demand_approval_dtl_branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'product_barcode_barcode')) {
     $table->string('product_barcode_barcode')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_entry_notes')) {
        $table->string('demand_approval_dtl_entry_notes')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'product_barcode_id')) {
     $table->bigInteger('product_barcode_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'sr_no')) {
        $table->bigInteger('sr_no')->nullable();
       }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_demand_approval_dtl,'demand_approval_dtl_user_id')) {
        $table->bigInteger('demand_approval_dtl_user_id')->nullable();
    }
});

//purchase  Comparative Quotation (tbl_purc_comparative_quotation)
//*************************************************************** */

$tbl_purc_comparative_quotation = 'tbl_purc_comparative_quotation';
if (!Schema::hasTable($tbl_purc_comparative_quotation)) {
    Schema::create('tbl_purc_comparative_quotation', function (Blueprint $table) {
        $table->bigInteger('comparative_quotation_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_comparative_quotation, function (Blueprint $table) use ($tbl_purc_comparative_quotation) {
    if (!Schema::hasColumn($tbl_purc_comparative_quotation,'comparative_quotation_id')) {
        $table->text('comparative_quotation_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation,'quotation_id')) {
        $table->string('quotation_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation,'comparative_quotation_entry_date')) {
        $table->date('comparative_quotation_entry_date')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation,'comparative_quotation_exchange_rate')) {
     $table->bigInteger('comparative_quotation_exchange_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation,'comparative_quotation_credit_days')) {
        $table->bigInteger('comparative_quotation_credit_days')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation,'comparative_quotation_remarks')) {
        $table->string('comparative_quotation_remarks')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation,'comparative_quotation_entry_status')) {
        $table->bigInteger('comparative_quotation_entry_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation,'comparative_quotation_code')) {
        $table->string('comparative_quotation_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation,'comparative_quotation_terms')) {
     $table->string('comparative_quotation_terms')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation,'payment_mode_id')) {
        $table->bigInteger('payment_mode_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation,'currency_id')) {
        $table->bigInteger('currency_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation,'comparative_quotation_user_id')) {
        $table->bigInteger('comparative_quotation_user_id')->nullable();
    }
});

//purchase  Comparative Quotation Dtl (tbl_purc_comparative_quotation_dtl)
//*************************************************************** */

$tbl_purc_comparative_quotation_dtl = 'tbl_purc_comparative_quotation_dtl';
if (!Schema::hasTable($tbl_purc_comparative_quotation_dtl)) {
    Schema::create('tbl_purc_comparative_quotation_dtl', function (Blueprint $table) {
        $table->bigInteger('comparative_quotation_dtl_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_comparative_quotation_dtl, function (Blueprint $table) use ($tbl_purc_comparative_quotation_dtl) {
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'comparative_quotation_dtl_id')) {
        $table->text('comparative_quotation_dtl_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'comparative_quotation_id')) {
        $table->bigInteger('comparative_quotation_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'comparative_quotation_dtl_barcode')) {
        $table->string('comparative_quotation_dtl_barcode')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'product_id')) {
     $table->bigInteger('product_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'uom_id')) {
        $table->bigInteger('uom_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'comparative_quotation_dtl_packing')) {
        $table->string('comparative_quotation_dtl_packing')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'comparative_quotation_dtl_quantity')) {
        $table->bigInteger('comparative_quotation_dtl_quantity')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'comparative_quotation_dtl_foc_quantity')) {
        $table->bigInteger('comparative_quotation_dtl_foc_quantity')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'comparative_quotation_dtl_fc_rate')) {
     $table->bigInteger('comparative_quotation_dtl_fc_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'comparative_quotation_dtl_rate')) {
        $table->bigInteger('comparative_quotation_dtl_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'comparative_quotation_dtl_amount')) {
        $table->bigInteger('comparative_quotation_dtl_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'comparative_quotation_dtl_disc_percent')) {
        $table->bigInteger('comparative_quotation_dtl_disc_percent')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'comparative_quotation_dtl_disc_amount')) {
     $table->bigInteger('comparative_quotation_dtl_disc_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'comparative_quotation_dtl_vat_percent')) {
        $table->bigInteger('comparative_quotation_dtl_vat_percent')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'comparative_quotation_dtl_vat_amount')) {
        $table->bigInteger('comparative_quotation_dtl_vat_amount')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'comparative_quotation_dtl_total_amount')) {
        $table->bigInteger('comparative_quotation_dtl_total_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'supplier_id')) {
     $table->bigInteger('supplier_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'comparative_quotation_dtl_approve')) {
        $table->bigInteger('comparative_quotation_dtl_approve')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'product_barcode_id')) {
        $table->bigInteger('product_barcode_id')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_dtl,'comparative_quotation_dtl_user_id')) {
        $table->bigInteger('comparative_quotation_dtl_user_id')->nullable();
    }
});


//purchase  Comparative Quotation account (tbl_purc_comparative_quotation_acc)
//*************************************************************** */

$tbl_purc_comparative_quotation_acc = 'tbl_purc_comparative_quotation_acc';
if (!Schema::hasTable($tbl_purc_comparative_quotation_acc)) {
    Schema::create('tbl_purc_comparative_quotation_acc', function (Blueprint $table) {
        $table->bigInteger('comparative_quotation_acc_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_comparative_quotation_acc, function (Blueprint $table) use ($tbl_purc_comparative_quotation_acc) {
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_acc,'comparative_quotation_acc_id')) {
        $table->text('comparative_quotation_acc_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_acc,'chart_account_id')) {
        $table->bigInteger('chart_account_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_acc,'comparative_quotation_acc_amount')) {
        $table->bigInteger('comparative_quotation_acc_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_acc,'comparative_quotation_acc_chart_code')) {
     $table->string('comparative_quotation_acc_chart_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_acc,'comparative_quotation_acc_name')) {
        $table->string('comparative_quotation_acc_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_acc,'comparative_quotation_dtl_id')) {
        $table->bigInteger('comparative_quotation_dtl_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_acc,'comparative_quotation_id')) {
        $table->bigInteger('comparative_quotation_id')->nullable();
    }
    
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_acc,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_acc,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_acc,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_comparative_quotation_acc,'comparative_quotation_acc_user_id')) {
        $table->bigInteger('comparative_quotation_acc_user_id')->nullable();
    }
});

//purchase  LPO Generation (tbl_purc_lpo)
//*************************************************************** */

$tbl_purc_lpo = 'tbl_purc_lpo';
if (!Schema::hasTable($tbl_purc_lpo)) {
    Schema::create('tbl_purc_lpo', function (Blueprint $table) {
        $table->bigInteger('lpo_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_lpo, function (Blueprint $table) use ($tbl_purc_lpo) {
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_id')) {
        $table->text('lpo_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_code')) {
        $table->string('lpo_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_date')) {
        $table->date('lpo_date')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_entry_status')) {
     $table->bigInteger('lpo_entry_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_exchange_rate')) {
        $table->bigInteger('lpo_exchange_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_remarks')) {
        $table->string('lpo_remarks')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_user_id')) {
        $table->bigInteger('lpo_user_id')->nullable();
    }
});


//purchase  LPO Generation Dtl (tbl_purc_lpo_dtl)
//*************************************************************** */

$tbl_purc_lpo_dtl = 'tbl_purc_lpo_dtl';
if (!Schema::hasTable($tbl_purc_lpo_dtl)) {
    Schema::create('tbl_purc_lpo_dtl', function (Blueprint $table) {
        $table->bigInteger('lpo_dtl_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_lpo, function (Blueprint $table) use ($tbl_purc_lpo) {
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_dtl_id')) {
        $table->text('lpo_dtl_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'demand_approval_dtl_id')) {
        $table->bigInteger('demand_approval_dtl_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'demand_id')) {
        $table->bigInteger('demand_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_dtl_amount')) {
     $table->bigInteger('lpo_dtl_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_dtl_approv_quantity')) {
        $table->bigInteger('lpo_dtl_approv_quantity')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_dtl_barcode')) {
        $table->string('lpo_dtl_barcode')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_dtl_branch_id')) {
        $table->bigInteger('lpo_dtl_branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_dtl_disc_amount')) {
        $table->bigInteger('lpo_dtl_disc_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_dtl_disc_percent')) {
     $table->bigInteger('lpo_dtl_disc_percent')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_dtl_fc_rate')) {
        $table->bigInteger('lpo_dtl_fc_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_dtl_foc_quantity')) {
        $table->bigInteger('lpo_dtl_foc_quantity')->nullable();
    }  
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_dtl_generate_lpo')) {
        $table->string('lpo_dtl_generate_lpo')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_dtl_generate_quotation')) {
        $table->string('lpo_dtl_generate_quotation')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_dtl_gross_amount')) {
     $table->bigInteger('lpo_dtl_gross_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_dtl_packing')) {
        $table->bigInteger('lpo_dtl_packing')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_dtl_quantity')) {
        $table->bigInteger('lpo_dtl_quantity')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_dtl_rate')) {
        $table->bigInteger('lpo_dtl_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_dtl_vat_amount')) {
     $table->bigInteger('lpo_dtl_vat_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_dtl_vat_percent')) {
        $table->bigInteger('lpo_dtl_vat_percent')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_id')) {
        $table->bigInteger('lpo_id')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_lpo,'payment_mode_id')) {
        $table->bigInteger('payment_mode_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'prod_barcode_id')) {
        $table->bigInteger('prod_barcode_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'product_barcode_barcode')) {
     $table->string('product_barcode_barcode')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'product_barcode_id')) {
        $table->bigInteger('product_barcode_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'product_id')) {
        $table->bigInteger('product_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'supplier_id')) {
        $table->bigInteger('supplier_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'uom_id')) {
        $table->bigInteger('uom_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo,'lpo_dtl_user_id')) {
        $table->bigInteger('lpo_dtl_user_id')->nullable();
    }
});


//purchase  LPO Generation Dtl Dtl   (tbl_purc_lpo_dtl_dtl)
//*************************************************************** */

$tbl_purc_lpo_dtl_dtl = 'tbl_purc_lpo_dtl_dtl';
if (!Schema::hasTable($tbl_purc_lpo_dtl_dtl)) {
    Schema::create('tbl_purc_lpo_dtl_dtl', function (Blueprint $table) {
        $table->bigInteger('lpo_dtl_dtl_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_lpo_dtl_dtl, function (Blueprint $table) use ($tbl_purc_lpo_dtl_dtl) {
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_dtl_id')) {
        $table->text('lpo_dtl_dtl_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'LPO_ID')) {
        $table->bigInteger('LPO_ID')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'demand_id')) {
        $table->bigInteger('demand_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'product_id')) {
     $table->bigInteger('product_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'uom_id')) {
        $table->bigInteger('uom_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_packing')) {
        $table->string('lpo_dtl_packing')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'supplier_id')) {
        $table->bigInteger('supplier_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'payment_mode_id')) {
        $table->bigInteger('payment_mode_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_quantity')) {
     $table->bigInteger('lpo_dtl_quantity')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_foc_quantity')) {
        $table->bigInteger('lpo_dtl_foc_quantity')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_fc_rate')) {
        $table->bigInteger('lpo_dtl_fc_rate')->nullable();
    }  
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_amount')) {
        $table->bigInteger('lpo_dtl_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_disc_percent')) {
        $table->bigInteger('lpo_dtl_disc_percent')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_disc_amount')) {
     $table->bigInteger('lpo_dtl_disc_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_approv_quantity')) {
        $table->bigInteger('lpo_dtl_approv_quantity')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_vat_percent')) {
        $table->bigInteger('lpo_dtl_vat_percent')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_vat_amount')) {
        $table->bigInteger('lpo_dtl_vat_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_generate_quotation')) {
     $table->string('lpo_dtl_generate_quotation')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_generate_lpo')) {
        $table->string('lpo_dtl_generate_lpo')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_id')) {
        $table->bigInteger('lpo_dtl_id')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_gross_amount')) {
        $table->bigInteger('lpo_dtl_gross_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_rate')) {
        $table->bigInteger('lpo_dtl_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'product_barcode_id')) {
        $table->bigInteger('product_barcode_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_barcode')) {
        $table->string('lpo_dtl_barcode')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'product_barcode_barcode')) {
        $table->string('product_barcode_barcode')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_branch_id')) {
        $table->bigInteger('lpo_dtl_branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'demand_approval_dtl_id')) {
        $table->bigInteger('demand_approval_dtl_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_lpo_dtl_dtl,'lpo_dtl_user_id')) {
        $table->bigInteger('lpo_dtl_user_id')->nullable();
    }
});



//purchase order  (tbl_purc_purchase_order)
//*************************************************************** */

$tbl_purc_purchase_order = 'tbl_purc_purchase_order';
if (!Schema::hasTable($tbl_purc_purchase_order)) {
    Schema::create('tbl_purc_purchase_order', function (Blueprint $table) {
        $table->bigInteger('purchase_order_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_purchase_order, function (Blueprint $table) use ($tbl_purc_purchase_order) {
    if (!Schema::hasColumn($tbl_purc_purchase_order,'purchase_order_id')) {
        $table->text('purchase_order_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order,'comparative_quotation_id')) {
        $table->bigInteger('comparative_quotation_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order,'lpo_id')) {
        $table->bigInteger('lpo_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order,'purchase_order_entry_date')) {
     $table->date('purchase_order_entry_date')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order,'purchase_order_exchange_rate')) {
        $table->bigInteger('purchase_order_exchange_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order,'purchase_order_credit_days')) {
        $table->bigInteger('purchase_order_credit_days')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order,'purchase_order_remarks')) {
        $table->string('purchase_order_remarks')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order,'purchase_order_entry_status')) {
        $table->bigInteger('purchase_order_entry_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order,'purchase_order_entry_date_time')) {
     $table->date('purchase_order_entry_date_time')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order,'purchase_order_code')) {
        $table->string('purchase_order_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order,'supplier_id')) {
        $table->bigInteger('supplier_id')->nullable();
    }  
    if (!Schema::hasColumn($tbl_purc_purchase_order,'payment_mode_id')) {
        $table->bigInteger('payment_mode_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order,'currency_id')) {
        $table->bigInteger('currency_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order,'purchase_order_user_id')) {
        $table->bigInteger('purchase_order_user_id')->nullable();
    }
});


//purchase order Dtl  (tbl_purc_purchase_order_dtl)
//*************************************************************** */

$tbl_purc_purchase_order_dtl = 'tbl_purc_purchase_order_dtl';
if (!Schema::hasTable($tbl_purc_purchase_order_dtl)) {
    Schema::create('tbl_purc_purchase_order_dtl', function (Blueprint $table) {
        $table->bigInteger('purchase_order_dtl_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_purchase_order_dtl, function (Blueprint $table) use ($tbl_purc_purchase_order_dtl) {
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'purchase_order_dtl_id')) {
        $table->text('purchase_order_dtl_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'purchase_order_id')) {
        $table->bigInteger('purchase_order_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'purchase_order_dtlsr_no')) {
        $table->bigInteger('purchase_order_dtlsr_no')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'purchase_order_dtlbarcode')) {
     $table->string('purchase_order_dtlbarcode')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'product_id')) {
        $table->bigInteger('product_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'uom_id')) {
        $table->bigInteger('uom_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'purchase_order_dtlpacking')) {
        $table->string('purchase_order_dtlpacking')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'purchase_order_dtlquantity')) {
        $table->bigInteger('purchase_order_dtlquantity')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'purchase_order_dtlfoc_quantity')) {
     $table->bigInteger('purchase_order_dtlfoc_quantity')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'purchase_order_dtlfc_rate')) {
        $table->bigInteger('purchase_order_dtlfc_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'purchase_order_dtlrate')) {
        $table->bigInteger('purchase_order_dtlrate')->nullable();
    }  
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'purchase_order_dtlamount')) {
        $table->bigInteger('purchase_order_dtlamount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'purchase_order_dtldisc_percent')) {
        $table->bigInteger('purchase_order_dtldisc_percent')->nullable();
    }
    

    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'purchase_order_dtldisc_amount')) {
        $table->bigInteger('purchase_order_dtldisc_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'purchase_order_dtlvat_percent')) {
        $table->bigInteger('purchase_order_dtlvat_percent')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'purchase_order_dtlvat_amount')) {
        $table->bigInteger('purchase_order_dtlvat_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'purchase_order_dtltotal_amount')) {
        $table->bigInteger('purchase_order_dtltotal_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'comparative_quotation_id')) {
     $table->bigInteger('comparative_quotation_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'lpo_id')) {
        $table->bigInteger('lpo_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'prod_barcode_id')) {
        $table->bigInteger('prod_barcode_id')->nullable();
    }  
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'product_barcode_id')) {
        $table->bigInteger('product_barcode_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'product_barcode_barcode')) {
        $table->string('product_barcode_barcode')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'lpo_dtl_id')) {
        $table->bigInteger('lpo_dtl_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'purchase_order_dtl_remarks')) {
        $table->string('purchase_order_dtl_remarks')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_purchase_order_dtl,'purchase_order_dtluser_id')) {
        $table->bigInteger('purchase_order_dtluser_id')->nullable();
    }
});


//purchase GRN (tbl_purc_grn)
//*************************************************************** */

$tbl_purc_grn = 'tbl_purc_grn';
if (!Schema::hasTable($tbl_purc_grn)) {
    Schema::create('tbl_purc_grn', function (Blueprint $table) {
        $table->bigInteger('grn_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_grn, function (Blueprint $table) use ($tbl_purc_grn) {
    if (!Schema::hasColumn($tbl_purc_grn,'grn_id')) {
        $table->text('grn_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'purchase_order_id')) {
        $table->bigInteger('purchase_order_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'grn_type')) {
        $table->string('grn_type')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'grn_id')) {
     $table->bigInteger('grn_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'grn_receiving_date')) {
        $table->date('grn_receiving_date')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'store_id')) {
        $table->bigInteger('store_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'supplier_id')) {
        $table->bigInteger('supplier_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'currency_id')) {
        $table->bigInteger('currency_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'grn_freight')) {
     $table->bigInteger('grn_freight')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'grn_bill_no')) {
        $table->string('grn_bill_no')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'grn_other_expense')) {
        $table->bigInteger('grn_other_expense')->nullable();
    }  
    if (!Schema::hasColumn($tbl_purc_grn,'grn_date')) {
        $table->date('grn_date')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'grn_exchange_rate')) {
        $table->bigInteger('grn_exchange_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'grn_entry_date_time')) {
        $table->date('grn_entry_date_time')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'grn_entry_status')) {
        $table->bigInteger('grn_entry_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'grn_code')) {
        $table->string('grn_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'grn_remarks')) {
        $table->string('grn_remarks')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'payment_mode_id')) {
     $table->bigInteger('payment_mode_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'payment_term_id')) {
        $table->bigInteger('payment_term_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'grn_ageing_term_id')) {
        $table->bigInteger('grn_ageing_term_id')->nullable();
    }  
    if (!Schema::hasColumn($tbl_purc_grn,'grn_ageing_term_value')) {
        $table->bigInteger('grn_ageing_term_value')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'voucher_id')) {
        $table->bigInteger('voucher_id')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_grn,'payment_type_id')) {
        $table->bigInteger('payment_type_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'grn_total_qty')) {
        $table->bigInteger('grn_total_qty')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_grn,'grn_total_amount')) {
        $table->bigInteger('grn_total_amount')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_grn,'grn_total_expense_amount')) {
        $table->bigInteger('grn_total_expense_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'grn_total_net_amount')) {
        $table->bigInteger('grn_total_net_amount')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_grn,'grn_overall_disc_amount')) {
        $table->bigInteger('grn_overall_disc_amount')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_grn,'grn_overall_discount')) {
        $table->bigInteger('grn_overall_discount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'grn_device_id')) {
        $table->bigInteger('grn_device_id')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_grn,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn,'grn_user_id')) {
        $table->bigInteger('grn_user_id')->nullable();
    }
});



//purchase GRN Dtl (tbl_purc_grn_dtl)
//*************************************************************** */

$tbl_purc_grn_dtl = 'tbl_purc_grn_dtl';
if (!Schema::hasTable($tbl_purc_grn_dtl)) {
    Schema::create('tbl_purc_grn_dtl', function (Blueprint $table) {
        $table->bigInteger('purc_grn_dtl_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_grn_dtl, function (Blueprint $table) use ($tbl_purc_grn_dtl) {
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'purc_grn_dtl_id')) {
        $table->text('purc_grn_dtl_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'grn_type')) {
        $table->string('grn_type')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_sr_no')) {
        $table->string('tbl_purc_grn_dtl_sr_no')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_supplier_barcode')) {
     $table->string('tbl_purc_grn_dtl_supplier_barcode')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_barcode')) {
        $table->string('tbl_purc_grn_dtl_barcode')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'product_id')) {
        $table->bigInteger('product_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'uom_id')) {
        $table->bigInteger('uom_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_packing')) {
        $table->string('tbl_purc_grn_dtl_packing')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_quantity')) {
     $table->bigInteger('tbl_purc_grn_dtl_quantity')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_foc_quantity')) {
        $table->bigInteger('tbl_purc_grn_dtl_foc_quantity')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_fc_rate')) {
        $table->bigInteger('tbl_purc_grn_dtl_fc_rate')->nullable();
    }  
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_rate')) {
        $table->bigInteger('tbl_purc_grn_dtl_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_amount')) {
        $table->bigInteger('tbl_purc_grn_dtl_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_disc_percent')) {
        $table->bigInteger('tbl_purc_grn_dtl_disc_percent')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_disc_amount')) {
        $table->bigInteger('tbl_purc_grn_dtl_disc_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_gst_percent')) {
        $table->bigInteger('tbl_purc_grn_dtl_gst_percent')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_vat_percent')) {
        $table->bigInteger('tbl_purc_grn_dtl_vat_percent')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_vat_amount')) {
     $table->bigInteger('tbl_purc_grn_dtl_vat_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_net_amount')) {
        $table->bigInteger('tbl_purc_grn_dtl_net_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_batch_no')) {
        $table->string('tbl_purc_grn_dtl_batch_no')->nullable();
    }  
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_production_date')) {
        $table->date('tbl_purc_grn_dtl_production_date')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_expiry_date')) {
        $table->date('tbl_purc_grn_dtl_expiry_date')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_total_amount')) {
        $table->bigInteger('tbl_purc_grn_dtl_total_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'purchase_order_id')) {
        $table->bigInteger('purchase_order_id')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'prod_barcode_id')) {
        $table->bigInteger('prod_barcode_id')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'product_barcode_id')) {
        $table->bigInteger('product_barcode_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'product_barcode_barcode')) {
        $table->string('product_barcode_barcode')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'grn_dtl_life')) {
        $table->bigInteger('grn_dtl_life')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'supplier_id')) {
        $table->bigInteger('supplier_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'purchase_order_dtl_id')) {
        $table->bigInteger('purchase_order_dtl_id')->nullable();
    } 

    if (!Schema::hasColumn($tbl_purc_grn_dtl,'grn_dtl_product_expense')) {
        $table->bigInteger('grn_dtl_product_expense')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'qty_base_unit')) {
        $table->float('qty_base_unit')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'dtl_prod_total_qty')) {
        $table->bigInteger('dtl_prod_total_qty')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'dtl_prod_gross_amount')) {
        $table->bigInteger('dtl_prod_gross_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'dtl_prod_gross_rate')) {
        $table->bigInteger('dtl_prod_gross_rate')->nullable();
    }  

    if (!Schema::hasColumn($tbl_purc_grn_dtl,'dtl_prod_rate_expense')) {
        $table->bigInteger('dtl_prod_rate_expense')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'dtl_prod_net_rate')) {
        $table->bigInteger('dtl_prod_net_rate')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'sr_no')) {
        $table->bigInteger('sr_no')->nullable();
    } 
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'grn_dtl_po_rate')) {
        $table->bigInteger('grn_dtl_po_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_sale_rate')) {
        $table->bigInteger('tbl_purc_grn_dtl_sale_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_rate_inc_foc')) {
        $table->bigInteger('tbl_purc_grn_dtl_rate_inc_foc')->nullable();
    }  
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_user_id')) {
        $table->bigInteger('tbl_purc_grn_dtl_user_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_retable_qty')) {
        $table->bigInteger('tbl_purc_grn_dtl_retable_qty')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_dtl,'tbl_purc_grn_dtl_retpend_qty')) {
        $table->bigInteger('tbl_purc_grn_dtl_retpend_qty')->nullable();
    }
});



//purchase GRN Expense (tbl_purc_grn_expense)
//*************************************************************** */

$tbl_purc_grn_expense = 'tbl_purc_grn_expense';
if (!Schema::hasTable($tbl_purc_grn_expense)) {
    Schema::create('tbl_purc_grn_expense', function (Blueprint $table) {
        $table->bigInteger('grn_expense_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_grn_expense, function (Blueprint $table) use ($tbl_purc_grn_expense) {
    if (!Schema::hasColumn($tbl_purc_grn_expense,'grn_expense_id')) {
        $table->text('grn_expense_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_expense,'grn_id')) {
        $table->bigInteger('grn_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_expense,'grn_expense_account_code')) {
        $table->string('grn_expense_account_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_expense,'grn_expense_account_name')) {
     $table->string('grn_expense_account_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_expense,'chart_account_id')) {
        $table->bigInteger('chart_account_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_expense,'grn_expense_amount')) {
        $table->bigInteger('grn_expense_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_expense,'grn_expense_perc')) {
        $table->bigInteger('grn_expense_perc')->nullable();
    }  
    if (!Schema::hasColumn($tbl_purc_grn_expense,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_expense,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_expense,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_grn_expense,'grn_expense_user_id')) {
        $table->bigInteger('grn_expense_user_id')->nullable();
    }
});

//purchase Brand (tbl_purc_brand)
//*************************************************************** */

$tbl_purc_brand = 'tbl_purc_brand';
if (!Schema::hasTable($tbl_purc_brand)) {
    Schema::create('tbl_purc_brand', function (Blueprint $table) {
        $table->bigInteger('brand_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_brand, function (Blueprint $table) use ($tbl_purc_brand) {
    if (!Schema::hasColumn($tbl_purc_brand,'brand_id')) {
        $table->text('brand_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_brand,'BRAND_NAME')) {
        $table->string('BRAND_NAME')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_brand,'BRAND_ENTRY_STATUS')) {
        $table->string('BRAND_ENTRY_STATUS')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_brand,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_brand,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_brand,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_brand,'BRAND_USER_ID')) {
        $table->bigInteger('BRAND_USER_ID')->nullable();
    }
});




//purchase manufacturer (tbl_purc_manufacturer)
//*************************************************************** */

$tbl_purc_manufacturer = 'tbl_purc_manufacturer';
if (!Schema::hasTable($tbl_purc_manufacturer)) {
    Schema::create('tbl_purc_manufacturer', function (Blueprint $table) {
        $table->bigInteger('manufacturer_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_manufacturer, function (Blueprint $table) use ($tbl_purc_manufacturer) {
    if (!Schema::hasColumn($tbl_purc_manufacturer,'manufacturer_id')) {
        $table->text('manufacturer_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_manufacturer,'manufacturer_name')) {
        $table->string('manufacturer_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_manufacturer,'manufacturer_entry_status')) {
        $table->string('manufacturer_entry_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_manufacturer,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_manufacturer,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_manufacturer,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_manufacturer,'manufacturer_user_id')) {
        $table->bigInteger('manufacturer_user_id')->nullable();
    }
});




//purchase supplier type (tbl_purc_supplier_type)
//*************************************************************** */

$tbl_purc_supplier_type = 'tbl_purc_supplier_type';
if (!Schema::hasTable($tbl_purc_supplier_type)) {
    Schema::create('tbl_purc_supplier_type', function (Blueprint $table) {
        $table->bigInteger('supplier_type_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_supplier_type, function (Blueprint $table) use ($tbl_purc_supplier_type) {
    if (!Schema::hasColumn($tbl_purc_supplier_type,'supplier_type_id')) {
        $table->text('supplier_type_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_type,'supplier_type_name')) {
        $table->string('supplier_type_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_type,'supplier_type_entry_status')) {
        $table->bigInteger('supplier_type_entry_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_type,'supplier_type_account_id')) {
        $table->bigInteger('supplier_type_account_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_type,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_type,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_type,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_type,'supplier_type_user_id')) {
        $table->bigInteger('supplier_type_user_id')->nullable();
    }
});




//purchase Group Item (tbl_purc_group_item)
//*************************************************************** */

$tbl_purc_group_item = 'tbl_purc_group_item';
if (!Schema::hasTable($tbl_purc_group_item)) {
    Schema::create('tbl_purc_group_item', function (Blueprint $table) {
        $table->bigInteger('group_item_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_group_item, function (Blueprint $table) use ($tbl_purc_group_item) {
    if (!Schema::hasColumn($tbl_purc_group_item,'group_item_id')) {
        $table->text('group_item_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_group_item,'group_item_entry_status')) {
        $table->bigInteger('group_item_entry_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_group_item,'group_item_entry_date_time')) {
        $table->date('group_item_entry_date_time')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_group_item,'group_item_name')) {
     $table->string('group_item_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_group_item,'group_item_mother_language_name')) {
        $table->string('group_item_mother_language_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_group_item,'parent_group_id')) {
        $table->bigInteger('parent_group_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_group_item,'group_item_sales_status')) {
        $table->string('group_item_sales_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_group_item,'group_item_brand_validation')) {
        $table->string('group_item_brand_validation')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_group_item,'group_item_expiry')) {
     $table->string('group_item_expiry')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_group_item,'group_item_stock_type')) {
        $table->string('group_item_stock_type')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_group_item,'group_item_level')) {
        $table->bigInteger('group_item_level')->nullable();
    }  
    if (!Schema::hasColumn($tbl_purc_group_item,'product_type_group_id')) {
        $table->bigInteger('product_type_group_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_group_item,'group_item_code')) {
        $table->bigInteger('group_item_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_group_item,'group_item_number')) {
        $table->string('group_item_number')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_group_item,'parent_group_item_number')) {
        $table->string('parent_group_item_number')->nullable();
    }  
    if (!Schema::hasColumn($tbl_purc_group_item,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_group_item,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_group_item,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_group_item,'group_item_user_id')) {
        $table->bigInteger('group_item_user_id')->nullable();
    }
});


//purchase Stock Location (tbl_purc_stock_location)
//*************************************************************** */

$tbl_purc_stock_location = 'tbl_purc_stock_location';
if (!Schema::hasTable($tbl_purc_stock_location)) {
    Schema::create('tbl_purc_stock_location', function (Blueprint $table) {
        $table->bigInteger('stock_location_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_stock_location, function (Blueprint $table) use ($tbl_purc_stock_location) {
    if (!Schema::hasColumn($tbl_purc_stock_location,'stock_location_id')) {
        $table->text('stock_location_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_stock_location,'stock_location_name')) {
        $table->string('stock_location_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_stock_location,'stock_location_parent_group_id')) {
        $table->bigInteger('stock_location_parent_group_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_stock_location,'stock_location_level')) {
        $table->bigInteger('stock_location_level')->nullable();
    }

    if (!Schema::hasColumn($tbl_purc_stock_location,'stock_location_entry_status')) {
        $table->bigInteger('stock_location_entry_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_stock_location,'stock_location_entry_datetime')) {
        $table->date('stock_location_entry_datetime')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_stock_location,'stock_location_code')) {
        $table->bigInteger('stock_location_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_stock_location,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_stock_location,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_stock_location,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_stock_location,'stock_location_user_id')) {
        $table->bigInteger('stock_location_user_id')->nullable();
    }
});

//purchase supplier contract (tbl_purc_supplier_contract)
//*************************************************************** */

$tbl_purc_supplier_contract = 'tbl_purc_supplier_contract';
if (!Schema::hasTable($tbl_purc_supplier_contract)) {
    Schema::create('tbl_purc_supplier_contract', function (Blueprint $table) {
        $table->bigInteger('contract_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_supplier_contract, function (Blueprint $table) use ($tbl_purc_supplier_contract) {
    if (!Schema::hasColumn($tbl_purc_supplier_contract,'contract_id')) {
        $table->text('contract_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract,'supplier_id')) {
        $table->bigInteger('supplier_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract,'contract_start_date')) {
        $table->date('contract_start_date')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract,'contract_end_date')) {
        $table->date('contract_end_date')->nullable();
    }

    if (!Schema::hasColumn($tbl_purc_supplier_contract,'contract_entry_date_time')) {
        $table->date('contract_entry_date_time')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract,'contract_entry_status')) {
        $table->bigInteger('contract_entry_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract,'contract_code')) {
        $table->string('contract_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract,'contract_notes')) {
        $table->string('contract_notes')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract,'contract_rebete_level')) {
        $table->string('contract_rebete_level')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract,'contract_user_id')) {
        $table->bigInteger('contract_user_id')->nullable();
    }
});

//purchase supplier contract Dtl (tbl_purc_supplier_contract_dtl)
//*************************************************************** */

$tbl_purc_supplier_contract_dtl = 'tbl_purc_supplier_contract_dtl';
if (!Schema::hasTable($tbl_purc_supplier_contract_dtl)) {
    Schema::create('tbl_purc_supplier_contract_dtl', function (Blueprint $table) {
        $table->bigInteger('contract_dtl_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_purc_supplier_contract_dtl, function (Blueprint $table) use ($tbl_purc_supplier_contract_dtl) {
    if (!Schema::hasColumn($tbl_purc_supplier_contract_dtl,'contract_dtl_id')) {
        $table->text('contract_dtl_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract_dtl,'supplier_id')) {
        $table->bigInteger('supplier_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract_dtl,'contract_dtl_sr_no')) {
        $table->string('contract_dtl_sr_no')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract_dtl,'contract_dtl_group')) {
        $table->string('contract_dtl_group')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract_dtl,'contract_dtl_brand')) {
        $table->string('contract_dtl_brand')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract_dtl,'contract_dtl_example_remarks')) {
        $table->string('contract_dtl_example_remarks')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract_dtl,'contract_dtl_quantity')) {
        $table->bigInteger('contract_dtl_quantity')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract_dtl,'contract_dtl_disc_percent')) {
        $table->bigInteger('contract_dtl_disc_percent')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract_dtl,'contract_id')) {
        $table->bigInteger('contract_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract_dtl,'product_barcode_id')) {
        $table->bigInteger('product_barcode_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract_dtl,'product_barcode_barcode')) {
        $table->string('product_barcode_barcode')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract_dtl,'product_id')) {
        $table->bigInteger('product_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract_dtl,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract_dtl,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract_dtl,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_purc_supplier_contract_dtl,'contract_dtl_user_id')) {
        $table->bigInteger('contract_dtl_user_id')->nullable();
    }
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       // Schema::dropIfExists('purchase');
    }
}
