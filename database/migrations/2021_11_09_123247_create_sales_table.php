<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('sales', function (Blueprint $table) {
        //     $table->id();
        //     $table->timestamps();
        // });


        //customer
        $tbl_sale_customer = 'tbl_sale_customer';
        if (!Schema::hasTable($tbl_sale_customer)) {
            Schema::create('tbl_sale_customer', function (Blueprint $table) {
                $table->bigInteger('customer_id')->primary();
                $table->timestamps();
            });
        }
        Schema::table($tbl_sale_customer, function (Blueprint $table) use ($tbl_sale_customer) {
            if (!Schema::hasColumn($tbl_sale_customer,'customer_id')) {
                $table->text('customer_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_code')) {
                $table->string('customer_code')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_type')) {
                $table->string('customer_type')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_name')) {
                $table->string('customer_name')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_local_name')) {
                $table->string('customer_local_name')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_suffix')) {
                $table->string('customer_suffix')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_address')) {
                $table->string('customer_address')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'country_id')) {
                $table->bigInteger('country_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'city_id')) {
                $table->bigInteger('city_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'region_id')) {
                $table->bigInteger('region_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_zip_code')) {
                $table->string('customer_zip_code')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_po_box')) {
                $table->string('customer_po_box')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_bar_code')) {
                $table->string('customer_bar_code')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_phone_1')) {
                $table->string('customer_phone_1')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_mobile_no')) {
                $table->string('customer_mobile_no')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_fax')) {
                $table->string('customer_fax')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_whatapp_no')) {
                $table->string('customer_whatapp_no')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_email')) {
                $table->string('customer_email')->nullable();
            }

            if (!Schema::hasColumn($tbl_sale_customer,'customer_website')) {
                $table->string('customer_website')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_account_id')) {
                $table->string('customer_account_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_tax_no')) {
                $table->string('customer_tax_no')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_entry_status')) {
                $table->bigInteger('customer_entry_status')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_tax_rate')) {
                $table->bigInteger('customer_tax_rate')->nullable();
            }

            if (!Schema::hasColumn($tbl_sale_customer,'customer_tax_status')) {
                $table->string('customer_tax_status')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_select_days')) {
                $table->bigInteger('customer_select_days')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_no_of_days')) {
                $table->bigInteger('customer_no_of_days')->nullable();
            }

            if (!Schema::hasColumn($tbl_sale_customer,'customer_div_name')) {
                $table->string('customer_div_name')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_cheque_beneficry_name')) {
                $table->string('customer_cheque_beneficry_name')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_credit_limit')) {
                $table->bigInteger('customer_credit_limit')->nullable();
            }

            if (!Schema::hasColumn($tbl_sale_customer,'customer_credit_period')) {
                $table->bigInteger('customer_credit_period')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_credit_period_type')) {
                $table->bigInteger('customer_credit_period_type')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_debit_limit')) {
                $table->bigInteger('customer_debit_limit')->nullable();
            }

            if (!Schema::hasColumn($tbl_sale_customer,'customer_can_scale')) {
                $table->bigInteger('customer_can_scale')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_mode_of_payment')) {
                $table->bigInteger('customer_mode_of_payment')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_image')) {
                $table->string('customer_image')->nullable();
            }

            if (!Schema::hasColumn($tbl_sale_customer,'customer_branch_id')) {
                $table->string('customer_branch_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_contact_person')) {
                $table->string('customer_contact_person')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_contact_person_mobile')) {
                $table->string('customer_contact_person_mobile')->nullable();
            }

            
            if (!Schema::hasColumn($tbl_sale_customer,'customer_bank_name')) {
                $table->string('customer_bank_name')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_bank_account_title')) {
                $table->string('customer_bank_account_title')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_bank_account_no')) {
                $table->string('customer_bank_account_no')->nullable();
            }

            if (!Schema::hasColumn($tbl_sale_customer,'customer_delivery_address')) {
                $table->string('customer_delivery_address')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_billing_address')) {
                $table->string('customer_billing_address')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_default_customer')) {
                $table->bigInteger('customer_default_customer')->nullable();
            }

            if (!Schema::hasColumn($tbl_sale_customer,'customer_reference_code')) {
                $table->string('customer_reference_code')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'password')) {
                $table->string('password')->nullable();
            }
            
            if (!Schema::hasColumn($tbl_sale_customer,'business_id')) {
                $table->bigInteger('business_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'company_id')) {
                $table->bigInteger('company_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'branch_id')) {
                $table->bigInteger('branch_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_sale_customer,'customer_user_id')) {
                $table->bigInteger('customer_user_id')->nullable();
            }
        });



   //customer sale type
   $tbl_sale_customer_type = 'tbl_sale_customer_type';
   if (!Schema::hasTable($tbl_sale_customer_type)) {
       Schema::create('tbl_sale_customer_type', function (Blueprint $table) {
           $table->bigInteger('customer_type_id')->primary();
           $table->timestamps();
       });
   }
   Schema::table($tbl_sale_customer_type, function (Blueprint $table) use ($tbl_sale_customer_type) {
       if (!Schema::hasColumn($tbl_sale_customer_type,'customer_type_id')) {
           $table->text('customer_type_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_customer_type,'customer_type_name')) {
           $table->string('customer_type_name')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_customer_type,'customer_type_entry_status')) {
           $table->bigInteger('customer_type_entry_status')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_customer_type,'customer_type_account_id')) {
           $table->bigInteger('customer_type_account_id')->nullable();
       }
      
       if (!Schema::hasColumn($tbl_sale_customer_type,'business_id')) {
           $table->bigInteger('business_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_customer_type,'company_id')) {
           $table->bigInteger('company_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_customer_type,'branch_id')) {
           $table->bigInteger('branch_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_customer_type,'customer_type_user_id')) {
           $table->bigInteger('customer_type_user_id')->nullable();
       }
   });



   // sale Order
   $tbl_sale_sales_order = 'tbl_sale_sales_order';
   if (!Schema::hasTable($tbl_sale_sales_order)) {
       Schema::create('tbl_sale_sales_order', function (Blueprint $table) {
           $table->bigInteger('sales_order_id')->primary();
           $table->timestamps();
       });
   }
   Schema::table($tbl_sale_sales_order, function (Blueprint $table) use ($tbl_sale_sales_order) {
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_id')) {
           $table->text('sales_order_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_code')) {
           $table->string('sales_order_code')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_date')) {
           $table->date('sales_order_date')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_entry_status')) {
           $table->bigInteger('sales_order_entry_status')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'customer_id')) {
           $table->bigInteger('customer_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_address')) {
           $table->string('sales_order_address')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_remarks')) {
           $table->string('sales_order_remarks')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_sales_man')) {
           $table->string('sales_order_sales_man')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'payment_term_id')) {
           $table->bigInteger('payment_term_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_credit_days')) {
           $table->bigInteger('sales_order_credit_days')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_sales_type')) {
           $table->string('sales_order_sales_type')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'payment_mode_id')) {
           $table->bigInteger('payment_mode_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_delivery_id')) {
           $table->string('sales_order_delivery_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'currency_id')) {
           $table->bigInteger('currency_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_exchange_rate')) {
           $table->bigInteger('sales_order_exchange_rate')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_code_type')) {
           $table->string('sales_order_code_type')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_booking_id')) {
           $table->bigInteger('sales_order_booking_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_mobile_no')) {
           $table->string('sales_order_mobile_no')->nullable();
       }

       if (!Schema::hasColumn($tbl_sale_sales_order,'bank_id')) {
           $table->bigInteger('bank_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_contract_id')) {
           $table->bigInteger('sales_contract_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_rate_type')) {
           $table->string('sales_order_rate_type')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_rate_perc')) {
           $table->bigInteger('sales_order_rate_perc')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'store_id')) {
           $table->bigInteger('store_id')->nullable();
       }

       if (!Schema::hasColumn($tbl_sale_sales_order,'city_id')) {
           $table->bigInteger('city_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'area_id')) {
           $table->bigInteger('area_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sub_total')) {
           $table->bigInteger('sub_total')->nullable();
       }

       if (!Schema::hasColumn($tbl_sale_sales_order,'net_total')) {
           $table->bigInteger('net_total')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'schedule_status')) {
           $table->bigInteger('schedule_status')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_status')) {
           $table->bigInteger('sales_order_status')->nullable();
       }

       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_quotation_id')) {
           $table->bigInteger('sales_quotation_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'service_order_id')) {
        $table->bigInteger('service_order_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_order,'schedule_id')) {
        $table->bigInteger('schedule_id')->nullable();
    }
       if (!Schema::hasColumn($tbl_sale_sales_order,'business_id')) {
           $table->bigInteger('business_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'company_id')) {
           $table->bigInteger('company_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'branch_id')) {
           $table->bigInteger('branch_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order,'sales_order_user_id')) {
           $table->bigInteger('sales_order_user_id')->nullable();
       }
   });
// tbl_sale_sales_order_dtl

$tbl_sale_sales_order_dtl = 'tbl_sale_sales_order_dtl';
   if (!Schema::hasTable($tbl_sale_sales_order_dtl)) {
       Schema::create('tbl_sale_sales_order_dtl', function (Blueprint $table) {
           $table->bigInteger('sales_order_dtl_id')->primary();
           $table->timestamps();
       });
   }
   Schema::table($tbl_sale_sales_order_dtl, function (Blueprint $table) use ($tbl_sale_sales_order_dtl) {
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sales_order_dtl_id')) {
           $table->text('sales_order_dtl_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sales_order_id')) {
           $table->bigInteger('sales_order_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sales_order_dtl_barcode')) {
           $table->string('sales_order_dtl_barcode')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'product_id')) {
           $table->bigInteger('product_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'uom_id')) {
           $table->bigInteger('uom_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sales_order_dtl_packing')) {
           $table->bigInteger('sales_order_dtl_packing')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sales_order_dtl_quantity')) {
           $table->bigInteger('sales_order_dtl_quantity')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sales_order_dtl_foc_qty')) {
           $table->bigInteger('sales_order_dtl_foc_qty')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sales_order_dtl_rate')) {
           $table->bigInteger('sales_order_dtl_rate')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sales_order_dtl_amount')) {
           $table->bigInteger('sales_order_dtl_amount')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sales_order_dtl_disc_amount')) {
           $table->bigInteger('sales_order_dtl_disc_amount')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sales_order_dtl_disc_per')) {
           $table->bigInteger('sales_order_dtl_disc_per')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sales_order_dtl_vat_per')) {
           $table->bigInteger('sales_order_dtl_vat_per')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sales_order_dtl_vat_amount')) {
           $table->bigInteger('sales_order_dtl_vat_amount')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sales_order_dtl_total_amount')) {
           $table->bigInteger('sales_order_dtl_total_amount')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sales_order_dtl_fc_rate')) {
           $table->bigInteger('sales_order_dtl_fc_rate')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'product_barcode_id')) {
           $table->bigInteger('product_barcode_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sr_no')) {
           $table->bigInteger('sr_no')->nullable();
       }

       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sales_order_dtl_notes')) {
           $table->string('sales_order_dtl_notes')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sales_order_dtl_length')) {
           $table->bigInteger('sales_order_dtl_length')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sales_order_dtl_width')) {
           $table->string('sales_order_dtl_width')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'business_id')) {
           $table->bigInteger('business_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'company_id')) {
           $table->bigInteger('company_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'branch_id')) {
           $table->bigInteger('branch_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_dtl,'sales_order_dtl_user_id')) {
           $table->bigInteger('sales_order_dtl_user_id')->nullable();
       }
   });


   // sale order expense

   $tbl_sale_sales_order_expense = 'tbl_sale_sales_order_expense';
   if (!Schema::hasTable($tbl_sale_sales_order_expense)) {
       Schema::create('tbl_sale_sales_order_expense', function (Blueprint $table) {
           $table->bigInteger('sales_order_expense_id')->primary();
           $table->timestamps();
       });
   }
   Schema::table($tbl_sale_sales_order_expense, function (Blueprint $table) use ($tbl_sale_sales_order_expense) {
       if (!Schema::hasColumn($tbl_sale_sales_order_expense,'sales_order_expense_id')) {
           $table->text('sales_order_expense_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_expense,'sales_order_expense_amount')) {
           $table->bigInteger('sales_order_expense_amount')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_expense,'sales_order_expense_account_code')) {
           $table->string('sales_order_expense_account_code')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_expense,'sales_order_expense_account_name')) {
           $table->string('sales_order_expense_account_name')->nullable();
       }

       if (!Schema::hasColumn($tbl_sale_sales_order_expense,'chart_account_id')) {
        $table->bigInteger('chart_account_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_order_expense,'sales_order_id')) {
        $table->bigInteger('sales_order_id')->nullable();
    }  
       if (!Schema::hasColumn($tbl_sale_sales_order_expense,'business_id')) {
           $table->bigInteger('business_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_expense,'company_id')) {
           $table->bigInteger('company_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_expense,'branch_id')) {
           $table->bigInteger('branch_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_sales_order_expense,'sales_order_expense_user_id')) {
           $table->bigInteger('sales_order_expense_user_id')->nullable();
       }
   });



 // sale contract

 $tbl_sale_sales_contract = 'tbl_sale_sales_contract';
 if (!Schema::hasTable($tbl_sale_sales_contract)) {
     Schema::create('tbl_sale_sales_contract', function (Blueprint $table) {
         $table->bigInteger('sales_contract_dtl_id')->primary();
         $table->timestamps();
     });
 }
 Schema::table($tbl_sale_sales_contract, function (Blueprint $table) use ($tbl_sale_sales_contract) {
     if (!Schema::hasColumn($tbl_sale_sales_contract,'sales_contract_dtl_id')) {
         $table->text('sales_contract_dtl_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales_contract,'sales_contract_id')) {
         $table->bigInteger('sales_contract_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales_contract,'sales_contract_sr')) {
         $table->bigInteger('sales_contract_sr')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales_contract,'product_barcode_id')) {
         $table->bigInteger('product_barcode_id')->nullable();
     }

     if (!Schema::hasColumn($tbl_sale_sales_contract,'product_id')) {
      $table->bigInteger('product_id')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_contract,'uom_id')) {
      $table->bigInteger('uom_id')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_contract,'sales_contract_dtl_barcode')) {
    $table->string('sales_contract_dtl_barcode')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_contract,'sales_contract_dtl_packing')) {
        $table->bigInteger('sales_contract_dtl_packing')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_contract,'sales_contract_dtl_fc_rate')) {
        $table->bigInteger('sales_contract_dtl_fc_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_contract,'sales_contract_dtl_rate')) {
    $table->bigInteger('sales_contract_dtl_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_contract,'sales_contract_dtl_vat_per')) {
    $table->bigInteger('sales_contract_dtl_vat_per')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_contract,'sales_contract_dtl_vat_amount')) {
        $table->bigInteger('sales_contract_dtl_vat_amount')->nullable();
        }
        if (!Schema::hasColumn($tbl_sale_sales_contract,'sales_contract_dtl_net_rate')) {
            $table->bigInteger('sales_contract_dtl_net_rate')->nullable();
        }
     if (!Schema::hasColumn($tbl_sale_sales_contract,'business_id')) {
         $table->bigInteger('business_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales_contract,'company_id')) {
         $table->bigInteger('company_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales_contract,'branch_id')) {
         $table->bigInteger('branch_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales_contract,'sales_contract_dtl_user_id')) {
         $table->bigInteger('sales_contract_dtl_user_id')->nullable();
     }
 });


  // sale contract DTL

  $tbl_sale_sales_contract_dtl = 'tbl_sale_sales_contract_dtl';
  if (!Schema::hasTable($tbl_sale_sales_contract_dtl)) {
      Schema::create('tbl_sale_sales_contract_dtl', function (Blueprint $table) {
          $table->bigInteger('sales_contract_dtl_id')->primary();
          $table->timestamps();
      });
  }
  Schema::table($tbl_sale_sales_contract_dtl, function (Blueprint $table) use ($tbl_sale_sales_contract_dtl) {
      if (!Schema::hasColumn($tbl_sale_sales_contract_dtl,'sales_contract_id')) {
          $table->text('sales_contract_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_sales_contract_dtl,'sales_contract_code')) {
          $table->string('sales_contract_code')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_sales_contract_dtl,'sales_contract_date')) {
          $table->date('sales_contract_date')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_sales_contract_dtl,'currency_id')) {
          $table->bigInteger('currency_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_sales_contract_dtl,'sales_contract_exchange_rate')) {
       $table->bigInteger('sales_contract_exchange_rate')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_sales_contract_dtl,'customer_id')) {
       $table->bigInteger('customer_id')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_sales_contract_dtl,'sales_contract_start_date')) {
     $table->date('sales_contract_start_date')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales_contract_dtl,'sales_contract_end_date')) {
         $table->date('sales_contract_end_date')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales_contract_dtl,'payment_term_id')) {
         $table->bigInteger('payment_term_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales_contract_dtl,'sales_contract_credit_days')) {
     $table->bigInteger('sales_contract_credit_days')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales_contract_dtl,'sales_contract_rate_type')) {
     $table->string('sales_contract_rate_type')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales_contract_dtl,'sales_contract_rate_perc')) {
         $table->bigInteger('sales_contract_rate_perc')->nullable();
         }
         if (!Schema::hasColumn($tbl_sale_sales_contract_dtl,'sales_contract_remarks')) {
             $table->string('sales_contract_remarks')->nullable();
         }
         if (!Schema::hasColumn($tbl_sale_sales_contract_dtl,'sales_contract_entry_status')) {
             $table->bigInteger('sales_contract_entry_status')->nullable();
         }
      if (!Schema::hasColumn($tbl_sale_sales_contract_dtl,'business_id')) {
          $table->bigInteger('business_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_sales_contract_dtl,'company_id')) {
          $table->bigInteger('company_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_sales_contract_dtl,'branch_id')) {
          $table->bigInteger('branch_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_sales_contract_dtl,'sales_contract_user_id')) {
          $table->bigInteger('sales_contract_user_id')->nullable();
      }
  });



  // sale Sales  (Sale_invoice)

  $tbl_sale_sales = 'tbl_sale_sales';
  if (!Schema::hasTable($tbl_sale_sales)) {
      Schema::create('tbl_sale_sales', function (Blueprint $table) {
          $table->bigInteger('sales_id')->primary();
          $table->timestamps();
      });
  }
  Schema::table($tbl_sale_sales, function (Blueprint $table) use ($tbl_sale_sales) {
      if (!Schema::hasColumn($tbl_sale_sales,'sales_id')) {
          $table->text('sales_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_sales,'sales_entry_status')) {
          $table->bigInteger('sales_entry_status')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_sales,'sales_bill_no')) {
          $table->string('sales_bill_no')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_sales,'sales_date')) {
          $table->date('sales_date')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_sales,'customer_id')) {
       $table->bigInteger('customer_id')->nullable();
     }
    if (!Schema::hasColumn($tbl_sale_sales,'sales_address')) {
        $table->string('sales_address')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales,'sales_remarks')) {
        $table->string('sales_remarks')->nullable();
    }
     if (!Schema::hasColumn($tbl_sale_sales,'sales_sales_man')) {
         $table->bigInteger('sales_sales_man')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales,'sales_credit_days')) {
         $table->bigInteger('sales_credit_days')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales,'sales_sales_type')) {
     $table->string('sales_sales_type')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales,'payment_mode_id')) {
     $table->bigInteger('payment_mode_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales,'sales_order_booking_id')) {
    $table->string('sales_order_booking_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales,'sales_delivery_id')) {
        $table->string('sales_delivery_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales,'sales_code')) {
        $table->string('sales_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales,'currency_id')) {
    $table->bigInteger('currency_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales,'payment_term_id')) {
        $table->bigInteger('payment_term_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales,'sales_type')) {
        $table->string('sales_type')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales,'sales_exchange_rate')) {
    $table->bigInteger('sales_exchange_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales,'voucher_id')) {
        $table->bigInteger('voucher_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales,'sales_mobile_no')) {
        $table->string('sales_mobile_no')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales,'sales_net_amount')) {
    $table->bigInteger('sales_net_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales,'terminal_id')) {
        $table->bigInteger('terminal_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales,'total_expense')) {
        $table->bigInteger('total_expense')->nullable();
    }

    if (!Schema::hasColumn($tbl_sale_sales,'hold_id')) {
    $table->bigInteger('hold_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales,'cashreceived')) {
        $table->bigInteger('cashreceived')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales,'change')) {
        $table->bigInteger('change')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales,'ref_id')) {
        $table->bigInteger('ref_id')->nullable();
        }
    if (!Schema::hasColumn($tbl_sale_sales,'sale_total')) {
            $table->bigInteger('sale_total')->nullable();
        }
    if (!Schema::hasColumn($tbl_sale_sales,'mac_address')) {
            $table->string('mac_address')->nullable();
        }
    if (!Schema::hasColumn($tbl_sale_sales,'sub_total_amount')) {
    $table->bigInteger('sub_total_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales,'sub_total_qty')) {
        $table->bigInteger('sub_total_qty')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales,'customer_points')) {
        $table->bigInteger('customer_points')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales,'sales_store_id')) {
        $table->bigInteger('sales_store_id')->nullable();
        }
    if (!Schema::hasColumn($tbl_sale_sales,'sales_loyalty_points')) {
            $table->bigInteger('sales_loyalty_points')->nullable();
        }
    if (!Schema::hasColumn($tbl_sale_sales,'sales_visa_points')) {
            $table->string('sales_visa_points')->nullable();
        }
    if (!Schema::hasColumn($tbl_sale_sales,'common_sales_id')) {
        $table->bigInteger('common_sales_id')->nullable();
        }
    if (!Schema::hasColumn($tbl_sale_sales,'update_id')) {
            $table->bigInteger('update_id')->nullable();
        }
    if (!Schema::hasColumn($tbl_sale_sales,'sales_return_ref_no')) {
            $table->bigInteger('sales_return_ref_no')->nullable();
        }
    if (!Schema::hasColumn($tbl_sale_sales,'sales_contract_id')) {
        $table->bigInteger('sales_contract_id')->nullable();
        }
     if (!Schema::hasColumn($tbl_sale_sales,'sales_rate_type')) {
            $table->string('sales_rate_type')->nullable();
        }
     if (!Schema::hasColumn($tbl_sale_sales,'bank_id')) {
         $table->bigInteger('bank_id')->nullable();
        }

    if (!Schema::hasColumn($tbl_sale_sales,'sales_contract_person')) {
         $table->string('sales_contract_person')->nullable();
            }                          
      if (!Schema::hasColumn($tbl_sale_sales,'business_id')) {
          $table->bigInteger('business_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_sales,'company_id')) {
          $table->bigInteger('company_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_sales,'branch_id')) {
          $table->bigInteger('branch_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_sales,'sales_user_id')) {
          $table->bigInteger('sales_user_id')->nullable();
      }
  });


  // sale Sales Dtl  (Sale_invoice)

  $tbl_sale_sales_dtl = 'tbl_sale_sales_dtl';
  if (!Schema::hasTable($tbl_sale_sales_dtl)) {
      Schema::create('tbl_sale_sales_dtl', function (Blueprint $table) {
          $table->bigInteger('sales_dtl_id')->primary();
          $table->timestamps();
      });
  }
  Schema::table($tbl_sale_sales_dtl, function (Blueprint $table) use ($tbl_sale_sales_dtl) {
      if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_id')) {
          $table->text('sales_dtl_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_barcode')) {
          $table->string('sales_dtl_barcode')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_sales_dtl,'product_id')) {
          $table->bigInteger('product_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_sales_dtl,'uom_id')) {
          $table->bigInteger('uom_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_packing')) {
       $table->bigInteger('sales_dtl_packing')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_quantity')) {
        $table->bigInteger('sales_dtl_quantity')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_foc_qty')) {
        $table->bigInteger('sales_dtl_foc_qty')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_rate')) {
         $table->bigInteger('sales_dtl_rate')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_amount')) {
         $table->bigInteger('sales_dtl_amount')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_disc_amount')) {
     $table->string('sales_dtl_disc_amount')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_disc_per')) {
     $table->bigInteger('sales_dtl_disc_per')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_vat_per')) {
    $table->bigInteger('sales_dtl_vat_per')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_vat_amount')) {
        $table->bigInteger('sales_dtl_vat_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_total_amount')) {
        $table->bigInteger('sales_dtl_total_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_fc_rate')) {
    $table->bigInteger('sales_dtl_fc_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_type')) {
        $table->string('sales_type')->nullable();
        }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'product_barcode_id')) {
        $table->bigInteger('product_barcode_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'prod_active')) {
        $table->bigInteger('prod_active')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'cost_rate')) {
    $table->bigInteger('cost_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'qty_base_unit')) {
        $table->bigInteger('qty_base_unit')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'common_sale_id')) {
        $table->bigInteger('common_sale_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_gross_rate')) {
    $table->bigInteger('sales_dtl_gross_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'update_id')) {
        $table->bigInteger('update_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'sr_no')) {
        $table->bigInteger('sr_no')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_start_date')) {
    $table->date('sales_dtl_start_date')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_end_date')) {
        $table->date('sales_dtl_end_date')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_notes')) {
        $table->string('sales_dtl_notes')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'product_name')) {
        $table->string('product_name')->nullable();
        }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'item_description')) {
            $table->string('item_description')->nullable();
        }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'area_of_display')) {
            $table->string('area_of_display')->nullable();
        }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'display_rent_fee_month')) {
    $table->string('display_rent_fee_month')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_contract_person')) {
        $table->string('sales_contract_person')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'purc_amount')) {
        $table->bigInteger('purc_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_length')) {
        $table->bigInteger('sales_dtl_length')->nullable();
        }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_width')) {
            $table->bigInteger('sales_dtl_width')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_dtl,'sales_dtl_user_id')) {
        $table->bigInteger('sales_dtl_user_id')->nullable();
    }
  });



  // sale Day

  $tbl_sale_day = 'tbl_sale_day';
  if (!Schema::hasTable($tbl_sale_day)) {
      Schema::create('tbl_sale_day', function (Blueprint $table) {
          $table->bigInteger('day_id')->primary();
          $table->timestamps();
      });
  }
  Schema::table($tbl_sale_day, function (Blueprint $table) use ($tbl_sale_day) {
      if (!Schema::hasColumn($tbl_sale_day,'day_id')) {
          $table->text('day_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_day,'day_date')) {
          $table->date('day_date')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_day,'day_case_type')) {
          $table->string('day_case_type')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_day,'saleman_id')) {
          $table->bigInteger('saleman_id')->nullable();
      }
 
      if (!Schema::hasColumn($tbl_sale_day,'day_shift')) {
       $table->string('day_shift')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_day,'denomination_id')) {
    $table->bigInteger('denomination_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_day,'day_qty')) {
        $table->bigInteger('day_qty')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_day,'day_amount')) {
        $table->bigInteger('day_amount')->nullable();
    }

    if (!Schema::hasColumn($tbl_sale_day,'day_payment_handover_received')) {
    $table->bigInteger('day_payment_handover_received')->nullable();
    }
   if (!Schema::hasColumn($tbl_sale_day,'day_payment_way_type')) {
       $table->bigInteger('day_payment_way_type')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_day,'day_reference_no')) {
     $table->string('day_reference_no')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_day,'day_notes')) {
         $table->string('day_notes')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_day,'day_code')) {
         $table->string('day_code')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_day,'day_code_type')) {
     $table->string('day_code_type')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_day,'voucher_id')) {
     $table->bigInteger('voucher_id')->nullable();
     }  
      if (!Schema::hasColumn($tbl_sale_day,'business_id')) {
          $table->bigInteger('business_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_day,'company_id')) {
          $table->bigInteger('company_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_day,'branch_id')) {
          $table->bigInteger('branch_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_day,'day_user_id')) {
          $table->bigInteger('day_user_id')->nullable();
      }
  });


  // sale Consumer Protection

  $tbl_sale_consumer_protection = 'tbl_sale_consumer_protection';
  if (!Schema::hasTable($tbl_sale_consumer_protection)) {
      Schema::create('tbl_sale_consumer_protection', function (Blueprint $table) {
          $table->bigInteger('protection_id')->primary();
          $table->timestamps();
      });
  }
  Schema::table($tbl_sale_consumer_protection, function (Blueprint $table) use ($tbl_sale_consumer_protection) {
      if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_id')) {
          $table->text('protection_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_entry_status')) {
          $table->bigInteger('protection_entry_status')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_bill_no')) {
          $table->string('protection_bill_no')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_date')) {
          $table->date('protection_date')->nullable();
      }
 
      if (!Schema::hasColumn($tbl_sale_consumer_protection,'customer_id')) {
       $table->bigInteger('customer_id')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_address')) {
    $table->string('protection_address')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_remarks')) {
        $table->string('protection_remarks')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_sales_man')) {
        $table->bigInteger('protection_sales_man')->nullable();
    }

    if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_credit_days')) {
    $table->bigInteger('protection_credit_days')->nullable();
    }
   if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_sales_type')) {
       $table->string('protection_sales_type')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_consumer_protection,'payment_mode_id')) {
     $table->bigInteger('payment_mode_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_consumer_protection,'sales_order_booking_id')) {
         $table->string('sales_order_booking_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_delivery_id')) {
         $table->string('protection_delivery_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_code')) {
     $table->string('protection_code')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_consumer_protection,'currency_id')) {
     $table->bigInteger('currency_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_consumer_protection,'payment_term_id')) {
        $table->bigInteger('payment_term_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_type')) {
        $table->string('protection_type')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_exchange_rate')) {
    $table->bigInteger('protection_exchange_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection,'voucher_id')) {
    $table->bigInteger('voucher_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_mobile_no')) {
        $table->string('protection_mobile_no')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_net_amount')) {
        $table->bigInteger('protection_net_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection,'terminal_id')) {
    $table->bigInteger('terminal_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection,'total_expense')) {
    $table->bigInteger('total_expense')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection,'hold_id')) {
        $table->bigInteger('hold_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection,'cashreceived')) {
        $table->bigInteger('cashreceived')->nullable();
    }

    if (!Schema::hasColumn($tbl_sale_consumer_protection,'change')) {
    $table->bigInteger('change')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection,'ref_id')) {
    $table->bigInteger('ref_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_total')) {
        $table->bigInteger('protection_total')->nullable();
        }
        if (!Schema::hasColumn($tbl_sale_consumer_protection,'mac_address')) {
        $table->string('mac_address')->nullable();
        }
        if (!Schema::hasColumn($tbl_sale_consumer_protection,'sub_total_amount')) {
            $table->bigInteger('sub_total_amount')->nullable();
        }
        if (!Schema::hasColumn($tbl_sale_consumer_protection,'sub_total_qty')) {
            $table->bigInteger('sub_total_qty')->nullable();
        }
    
        if (!Schema::hasColumn($tbl_sale_consumer_protection,'customer_points')) {
        $table->bigInteger('customer_points')->nullable();
        }
        if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_store_id')) {
        $table->bigInteger('protection_store_id')->nullable();
        } 
        if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_loyalty_points')) {
        $table->bigInteger('protection_loyalty_points')->nullable();
        }
        if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_visa_points')) {
        $table->bigInteger('protection_visa_points')->nullable();
        }
        if (!Schema::hasColumn($tbl_sale_consumer_protection,'common_sales_id')) {
            $table->string('common_sales_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_rate_perc')) {
            $table->string('protection_rate_perc')->nullable();
        }
    
        if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_rate_type')) {
        $table->string('protection_rate_type')->nullable();
        }
        if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_contract_id')) {
        $table->bigInteger('protection_contract_id')->nullable();
        }
   
      if (!Schema::hasColumn($tbl_sale_consumer_protection,'business_id')) {
          $table->bigInteger('business_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_consumer_protection,'company_id')) {
          $table->bigInteger('company_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_consumer_protection,'branch_id')) {
          $table->bigInteger('branch_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_consumer_protection,'protection_user_id')) {
          $table->bigInteger('protection_user_id')->nullable();
      }
  });


  // sale Consumer Protection Dtl

  $tbl_sale_consumer_protection_dtl = 'tbl_sale_consumer_protection_dtl';
  if (!Schema::hasTable($tbl_sale_consumer_protection_dtl)) {
      Schema::create('tbl_sale_consumer_protection_dtl', function (Blueprint $table) {
          $table->bigInteger('protection_dtl_id')->primary();
          $table->timestamps();
      });
  }
  Schema::table($tbl_sale_consumer_protection_dtl, function (Blueprint $table) use ($tbl_sale_consumer_protection_dtl) {
      if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'protection_dtl_id')) {
          $table->text('protection_dtl_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'protection_id')) {
          $table->bigInteger('protection_id')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'protection_dtl_barcode')) {
          $table->string('protection_dtl_barcode')->nullable();
      }
      if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'product_id')) {
          $table->bigInteger('product_id')->nullable();
      }
 
      if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'uom_id')) {
       $table->bigInteger('uom_id')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'protection_dtl_packing')) {
    $table->bigInteger('protection_dtl_packing')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'protection_dtl_quantity')) {
        $table->bigInteger('protection_dtl_quantity')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'protection_dtl_foc_qty')) {
        $table->bigInteger('protection_dtl_foc_qty')->nullable();
    }

    if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'protection_dtl_rate')) {
    $table->bigInteger('protection_dtl_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'protection_dtl_amount')) {
       $table->bigInteger('protection_dtl_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'protection_dtl_disc_amount')) {
     $table->bigInteger('protection_dtl_disc_amount')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'protection_dtl_disc_per')) {
         $table->bigInteger('protection_dtl_disc_per')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'protection_dtl_vat_per')) {
         $table->bigInteger('protection_dtl_vat_per')->nullable();
     }
 
     if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'protection_dtl_vat_amount')) {
     $table->bigInteger('protection_dtl_vat_amount')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'protection_dtl_total_amount')) {
     $table->bigInteger('protection_dtl_total_amount')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'protection_dtl_fc_rate')) {
        $table->bigInteger('protection_dtl_fc_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'protection_type')) {
        $table->string('protection_type')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'product_barcode_id')) {
    $table->bigInteger('product_barcode_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'prod_active')) {
    $table->bigInteger('prod_active')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'cost_rate')) {
        $table->bigInteger('cost_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'qty_base_unit')) {
        $table->bigInteger('qty_base_unit')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'common_sale_id')) {
    $table->bigInteger('common_sale_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'protection_dtl_gross_rate')) {
    $table->bigInteger('protection_dtl_gross_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'sr_no')) {
        $table->bigInteger('sr_no')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_consumer_protection_dtl,'protection_dtl_user_id')) {
        $table->bigInteger('protection_dtl_user_id')->nullable();
    }
  });



   // sale Consumer Protection Expense

   $tbl_sale_consumer_protection_expense = 'tbl_sale_consumer_protection_expense';
   if (!Schema::hasTable($tbl_sale_consumer_protection_expense)) {
       Schema::create('tbl_sale_consumer_protection_expense', function (Blueprint $table) {
           $table->bigInteger('protection_expense_id')->primary();
           $table->timestamps();
       });
   }
   Schema::table($tbl_sale_consumer_protection_expense, function (Blueprint $table) use ($tbl_sale_consumer_protection_expense) {
       if (!Schema::hasColumn($tbl_sale_consumer_protection_expense,'protection_expense_id')) {
           $table->text('protection_expense_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_consumer_protection_expense,'protection_expense_amount')) {
           $table->bigInteger('protection_expense_amount')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_consumer_protection_expense,'protection_expense_account_code')) {
           $table->string('protection_expense_account_code')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_consumer_protection_expense,'protection_expense_account_name')) {
           $table->string('protection_expense_account_name')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_consumer_protection_expense,'chart_account_id')) {
        $table->bigInteger('chart_account_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_consumer_protection_expense,'protection_id')) {
        $table->bigInteger('protection_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_consumer_protection_expense,'business_id')) {
           $table->bigInteger('business_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_consumer_protection_expense,'company_id')) {
           $table->bigInteger('company_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_consumer_protection_expense,'branch_id')) {
           $table->bigInteger('branch_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_consumer_protection_expense,'protection_expense_user_id')) {
           $table->bigInteger('protection_expense_user_id')->nullable();
       }
   });


 // sale Bank Distribution

 $tbl_sale_bank_distribution = 'tbl_sale_bank_distribution';
 if (!Schema::hasTable($tbl_sale_bank_distribution)) {
     Schema::create('tbl_sale_bank_distribution', function (Blueprint $table) {
         $table->bigInteger('bd_id')->primary();
         $table->timestamps();
     });
 }
 Schema::table($tbl_sale_bank_distribution, function (Blueprint $table) use ($tbl_sale_bank_distribution) {
     if (!Schema::hasColumn($tbl_sale_bank_distribution,'bd_id')) {
         $table->text('bd_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_bank_distribution,'bd_shift')) {
         $table->string('bd_shift')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_bank_distribution,'bd_date')) {
         $table->date('bd_date')->nullable();
     }
     if (!Schema::hasColumn($tbl_sale_bank_distribution,'bd_case_type')) {
         $table->string('bd_case_type')->nullable();
     }

     if (!Schema::hasColumn($tbl_sale_bank_distribution,'saleman_id')) {
      $table->bigInteger('saleman_id')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_bank_distribution,'bd_payment_handover_received')) {
   $table->bigInteger('bd_payment_handover_received')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_bank_distribution,'bd_payment_way_type')) {
       $table->bigInteger('bd_payment_way_type')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_bank_distribution,'bd_reference_no')) {
       $table->string('bd_reference_no')->nullable();
   }

   if (!Schema::hasColumn($tbl_sale_bank_distribution,'bd_notes')) {
   $table->string('bd_notes')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_bank_distribution,'bd_code')) {
      $table->string('bd_code')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_bank_distribution,'bd_code_type')) {
    $table->string('bd_code_type')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_bank_distribution,'bd_tot_qty')) {
        $table->bigInteger('bd_tot_qty')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_bank_distribution,'bd_tot_amount')) {
        $table->bigInteger('bd_tot_amount')->nullable();
    }

    if (!Schema::hasColumn($tbl_sale_bank_distribution,'voucher_id')) {
    $table->bigInteger('voucher_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_bank_distribution,'document_verified_status')) {
    $table->bigInteger('document_verified_status')->nullable();
    }
   if (!Schema::hasColumn($tbl_sale_bank_distribution,'business_id')) {
       $table->bigInteger('business_id')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_bank_distribution,'company_id')) {
       $table->bigInteger('company_id')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_bank_distribution,'branch_id')) {
       $table->bigInteger('branch_id')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_bank_distribution,'bd_user_id')) {
       $table->bigInteger('bd_user_id')->nullable();
   }
 });

// sale Bank Distribution Dtl

 $tbl_sale_bank_distribution_dtl = 'tbl_sale_bank_distribution_dtl';
   if (!Schema::hasTable($tbl_sale_bank_distribution_dtl)) {
       Schema::create('tbl_sale_bank_distribution_dtl', function (Blueprint $table) {
           $table->bigInteger('bd_dtl_id')->primary();
           $table->timestamps();
       });
   }
   Schema::table($tbl_sale_bank_distribution_dtl, function (Blueprint $table) use ($tbl_sale_bank_distribution_dtl) {
       if (!Schema::hasColumn($tbl_sale_bank_distribution_dtl,'bd_dtl_id')) {
           $table->text('bd_dtl_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_bank_distribution_dtl,'bd_id')) {
           $table->bigInteger('bd_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_bank_distribution_dtl,'bank_id')) {
           $table->bigInteger('bank_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_bank_distribution_dtl,'denomination_id')) {
           $table->bigInteger('denomination_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_bank_distribution_dtl,'bd_dtl_qty')) {
        $table->bigInteger('bd_dtl_qty')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_bank_distribution_dtl,'bd_dtl_amount')) {
        $table->bigInteger('bd_dtl_amount')->nullable();
       }

       if (!Schema::hasColumn($tbl_sale_bank_distribution_dtl,'sr_no')) {
        $table->bigInteger('sr_no')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_bank_distribution_dtl,'business_id')) {
           $table->bigInteger('business_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_bank_distribution_dtl,'company_id')) {
           $table->bigInteger('company_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_bank_distribution_dtl,'branch_id')) {
           $table->bigInteger('branch_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_sale_bank_distribution_dtl,'bd_dtl_user_id')) {
           $table->bigInteger('bd_dtl_user_id')->nullable();
       }
   });

// sale Delivery

$tbl_sale_sales_delivery = 'tbl_sale_sales_delivery';
if (!Schema::hasTable($tbl_sale_sales_delivery)) {
    Schema::create('tbl_sale_sales_delivery', function (Blueprint $table) {
        $table->bigInteger('sales_delivery_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_sale_sales_delivery, function (Blueprint $table) use ($tbl_sale_sales_delivery) {
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_delivery_id')) {
        $table->text('sales_delivery_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_delivery_entry_status')) {
        $table->bigInteger('sales_delivery_entry_status')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_delivery_bill_no')) {
        $table->string('sales_delivery_bill_no')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_delivery_date')) {
        $table->date('sales_delivery_date')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'customer_id')) {
     $table->bigInteger('customer_id')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_delivery_address')) {
  $table->string('sales_delivery_address')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_delivery_remarks')) {
      $table->string('sales_delivery_remarks')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_delivery_sales_man')) {
      $table->bigInteger('sales_delivery_sales_man')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_delivery_credit_days')) {
  $table->bigInteger('sales_delivery_credit_days')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_delivery_sales_type')) {
     $table->string('sales_delivery_sales_type')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_delivery,'payment_mode_id')) {
   $table->bigInteger('payment_mode_id')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_order_booking_id')) {
       $table->string('sales_order_booking_id')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_delivery_code')) {
       $table->string('sales_delivery_code')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_sales_delivery,'currency_id')) {
   $table->bigInteger('currency_id')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_sales_delivery,'payment_term_id')) {
   $table->bigInteger('payment_term_id')->nullable();
   }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_delivery_type')) {
    $table->string('sales_delivery_type')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_delivery_exchange_rate')) {
    $table->bigInteger('sales_delivery_exchange_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'voucher_id')) {
        $table->bigInteger('voucher_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_delivery_mobile_no')) {
    $table->string('sales_delivery_mobile_no')->nullable();
    }   
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_delivery_net_amount')) {
    $table->bigInteger('sales_delivery_net_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'terminal_id')) {
    $table->bigInteger('terminal_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'total_expense')) {
    $table->bigInteger('total_expense')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'hold_id')) {
    $table->bigInteger('hold_id')->nullable();
    }       
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'cashreceived')) {
    $table->bigInteger('cashreceived')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'change')) {
    $table->bigInteger('change')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'ref_id')) {
    $table->bigInteger('ref_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'sale_total')) {
    $table->bigInteger('sale_total')->nullable();
    }   
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'mac_address')) {
    $table->string('mac_address')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'sub_total_amount')) {
    $table->bigInteger('sub_total_amount')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'sub_total_qty')) {
    $table->bigInteger('sub_total_qty')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'customer_points')) {
    $table->bigInteger('customer_points')->nullable();
    } 
    
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_delivery_rate_type')) {
    $table->string('sales_delivery_rate_type')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_delivery_rate_perc')) {
    $table->bigInteger('sales_delivery_rate_perc')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_contract_id')) {
    $table->bigInteger('sales_contract_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_id')) {
    $table->bigInteger('sales_id')->nullable();
    } 
    if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_delivery_no')) {
        $table->string('sales_delivery_no')->nullable();
          }      
  if (!Schema::hasColumn($tbl_sale_sales_delivery,'business_id')) {
      $table->bigInteger('business_id')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_delivery,'company_id')) {
      $table->bigInteger('company_id')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_delivery,'branch_id')) {
      $table->bigInteger('branch_id')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_delivery,'sales_delivery_user_id')) {
      $table->bigInteger('sales_delivery_user_id')->nullable();
  }
});



// sale Delivery DtL

$tbl_sale_sales_delivery_dtl = 'tbl_sale_sales_delivery_dtl';
if (!Schema::hasTable($tbl_sale_sales_delivery_dtl)) {
    Schema::create('tbl_sale_sales_delivery_dtl', function (Blueprint $table) {
        $table->bigInteger('sales_delivery_dtl_id')->primary();
        $table->timestamps();
    });
}
Schema::table($tbl_sale_sales_delivery_dtl, function (Blueprint $table) use ($tbl_sale_sales_delivery_dtl) {
    if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'sales_delivery_dtl_id')) {
        $table->text('sales_delivery_dtl_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'sales_delivery_id')) {
        $table->bigInteger('sales_delivery_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'sales_delivery_dtl_barcode')) {
        $table->string('sales_delivery_dtl_barcode')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'product_id')) {
        $table->bigInteger('product_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'uom_id')) {
     $table->bigInteger('uom_id')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'sales_delivery_dtl_packing')) {
  $table->bigInteger('sales_delivery_dtl_packing')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'sales_delivery_dtl_quantity')) {
      $table->bigInteger('sales_delivery_dtl_quantity')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'sales_delivery_dtl_foc_qty')) {
      $table->bigInteger('sales_delivery_dtl_foc_qty')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'sales_delivery_dtl_rate')) {
  $table->bigInteger('sales_delivery_dtl_rate')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'sales_delivery_dtl_amount')) {
     $table->bigInteger('sales_delivery_dtl_amount')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'sales_delivery_dtl_disc_amount')) {
   $table->bigInteger('sales_delivery_dtl_disc_amount')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'sales_delivery_dtl_disc_per')) {
       $table->bigInteger('sales_delivery_dtl_disc_per')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'sales_delivery_dtl_vat_per')) {
       $table->bigInteger('sales_delivery_dtl_vat_per')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'sales_delivery_dtl_vat_amount')) {
   $table->bigInteger('sales_delivery_dtl_vat_amount')->nullable();
   }
   if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'sales_delivery_dtl_total_amount')) {
   $table->bigInteger('sales_delivery_dtl_total_amount')->nullable();
   }
    if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'sales_delivery_dtl_fc_rate')) {
    $table->bigInteger('sales_delivery_dtl_fc_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'sales_delivery_type')) {
    $table->string('sales_delivery_type')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'product_barcode_id')) {
        $table->bigInteger('product_barcode_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'prod_active')) {
    $table->bigInteger('prod_active')->nullable();
    }   
    if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'sales_delivery_dtl_gross_rate')) {
    $table->bigInteger('sales_delivery_dtl_gross_rate')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'qty_base_unit')) {
    $table->string('qty_base_unit')->nullable();
    }
    if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'sr_no')) {
    $table->bigInteger('sr_no')->nullable();
    }  
  if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'business_id')) {
      $table->bigInteger('business_id')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'company_id')) {
      $table->bigInteger('company_id')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'branch_id')) {
      $table->bigInteger('branch_id')->nullable();
  }
  if (!Schema::hasColumn($tbl_sale_sales_delivery_dtl,'sales_delivery_dtl_user_id')) {
      $table->bigInteger('sales_delivery_dtl_user_id')->nullable();
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
       // Schema::dropIfExists('sales');
    }
}
