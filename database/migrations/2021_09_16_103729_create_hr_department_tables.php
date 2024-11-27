<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrDepartmentTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //loan_configration
        $tbl_payr_loan_configuration = 'tbl_payr_loan_configuration';
        if (!Schema::hasTable($tbl_payr_loan_configuration)) {
            Schema::create('tbl_payr_loan_configuration', function (Blueprint $table) {
                $table->bigInteger('loan_configuration_id')->primary();
                $table->timestamps();
            });
        }
        Schema::table($tbl_payr_loan_configuration, function (Blueprint $table) use ($tbl_payr_loan_configuration) {
            if (!Schema::hasColumn($tbl_payr_loan_configuration,'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_configuration,'loan_type')) {
                $table->bigInteger('loan_type')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_configuration,'occurence_type')) {
                $table->string('occurence_type')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_configuration,'minimum_installment')) {
                $table->string('minimum_installment')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_configuration,'maximum_installment')) {
                $table->string('maximum_installment')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_configuration,'allowance')) {
                $table->bigInteger('allowance')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_configuration,'rate_type')) {
                $table->string('rate_type')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_configuration,'rate_value')) {
                $table->string('rate_value')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_configuration,'minimum_value')) {
                $table->string('minimum_value')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_configuration,'maximum_value')) {
                $table->string('maximum_value')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_configuration,'employee_contribution')) {
                $table->bigInteger('employee_contribution')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_configuration,'employee_rate_type')) {
                $table->string('employee_rate_type')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_configuration,'employee_rate_value')) {
                $table->string('employee_rate_value')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_configuration,'apply_on_loan')) {
                $table->string('apply_on_loan')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_configuration,'business_id')) {
                $table->bigInteger('business_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_configuration,'company_id')) {
                $table->bigInteger('company_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_configuration,'branch_id')) {
                $table->bigInteger('branch_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_configuration,'loan_configuration_user_id')) {
                $table->bigInteger('loan_configuration_user_id')->nullable();
            }
        });


        //loan 

        $tbl_payr_loan = 'tbl_payr_loan';
        if (!Schema::hasTable($tbl_payr_loan)) {
            Schema::create('tbl_payr_loan', function (Blueprint $table) {
                $table->bigInteger('loan_id')->primary();
                $table->timestamps();
            });
        }
        Schema::table($tbl_payr_loan, function (Blueprint $table) use ($tbl_payr_loan) {
            if (!Schema::hasColumn($tbl_payr_loan,'employee_id')) {
                $table->bigInteger('employee_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan,'loan_type')) {
                $table->bigInteger('loan_type')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan,'description')) {
                $table->string('description')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan,'loan_date')) {
                $table->timestamp('loan_date')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan,'department')) {
                $table->string('department')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan,'designation')) {
                $table->string('designation')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan,'loan_start_date')) {
                $table->timestamp('loan_start_date')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan,'loan_end_date')) {
                $table->timestamp('loan_end_date')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan,'loan_amount')) {
                $table->string('loan_amount')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan,'installment_amount')) {
                $table->bigInteger('installment_amount')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan,'installment_no')) {
                $table->bigInteger('installment_no')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan,'loan_deduction')) {
                $table->bigInteger('loan_deduction')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan,'loan_paid')) {
                $table->bigInteger('loan_paid')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan,'balance_loan')) {
                $table->string('balance_loan')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan,'remarks')) {
                $table->string('remarks')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan,'business_id')) {
                $table->bigInteger('business_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan,'company_id')) {
                $table->bigInteger('company_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan,'branch_id')) {
                $table->bigInteger('branch_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan,'loan_user_id')) {
                $table->bigInteger('loan_user_id')->nullable();
            }
        });


        //allowance deduction type

        $tbl_payr_allowance_deduction = 'tbl_payr_allowance_deduction_type';
        if (!Schema::hasTable($tbl_payr_allowance_deduction)) {
            Schema::create('tbl_payr_allowance_deduction_type', function (Blueprint $table) {
                $table->bigInteger('allowance_deduction_id')->primary();
                $table->timestamps();
            });
        }
        Schema::table($tbl_payr_allowance_deduction, function (Blueprint $table) use ($tbl_payr_allowance_deduction) {
            if (!Schema::hasColumn($tbl_payr_allowance_deduction,'allowance_deduction_name')) {
                $table->string('allowance_deduction_name')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_allowance_deduction,'allowance_deduction_short_name')) {
                $table->string('allowance_deduction_short_name')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_allowance_deduction,'allowance_deduction_allowance_status')) {
                $table->bigInteger('allowance_deduction_allowance_status')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_allowance_deduction,'allowance_deduction_adjust_attendance')) {
                $table->bigInteger('allowance_deduction_adjust_attendance')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_allowance_deduction,'allowance_deduction_entry_status')) {
                $table->bigInteger('allowance_deduction_entry_status')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_allowance_deduction,'allowance_deduction_tag_name')) {
                $table->string('allowance_deduction_tag_name')->nullable();
            }
            
            if (!Schema::hasColumn($tbl_payr_allowance_deduction,'business_id')) {
                $table->bigInteger('business_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_allowance_deduction,'company_id')) {
                $table->bigInteger('company_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_allowance_deduction,'branch_id')) {
                $table->bigInteger('branch_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_allowance_deduction,'allowance_deduction_user_id')) {
                $table->bigInteger('allowance_deduction_user_id')->nullable();
            }
        });

        //loan installment details 

        $tbl_payr_loan_installment_dtl = 'tbl_payr_loan_installment_dtl';
        if (!Schema::hasTable($tbl_payr_loan_installment_dtl)) {
            Schema::create('tbl_payr_loan_installment_dtl', function (Blueprint $table) {
                $table->bigInteger('loan_installment_id')->primary();
                $table->timestamps();
            });
        }

        Schema::table($tbl_payr_loan_installment_dtl, function (Blueprint $table) use ($tbl_payr_loan_installment_dtl) {
            if (!Schema::hasColumn($tbl_payr_loan_installment_dtl,'loan_id')) {
                $table->bigInteger('loan_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_installment_dtl,'date')) {
                $table->timestamp('date')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_installment_dtl,'per_installment_amount')) {
                $table->bigInteger('per_installment_amount')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_installment_dtl,'paid_amount')) {
                $table->bigInteger('paid_amount')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_installment_dtl,'balance_amount')) {
                $table->bigInteger('balance_amount')->nullable();
            }

            if (!Schema::hasColumn($tbl_payr_loan_installment_dtl,'business_id')) {
                $table->bigInteger('business_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_installment_dtl,'company_id')) {
                $table->bigInteger('company_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_installment_dtl,'branch_id')) {
                $table->bigInteger('branch_id')->nullable();
            }
            if (!Schema::hasColumn($tbl_payr_loan_installment_dtl,'loan_installment_user_id')) {
                $table->bigInteger('loan_installment_user_id')->nullable();
            }
        });


    //payrol computation 

    $tbl_payr_payroll_computation = 'tbl_payr_payroll_computation';
    if (!Schema::hasTable($tbl_payr_payroll_computation)) {
        Schema::create('tbl_payr_payroll_computation', function (Blueprint $table) {
            $table->bigInteger('payroll_computation_id')->primary();
            $table->timestamps();
        });
    }

    Schema::table($tbl_payr_payroll_computation, function (Blueprint $table) use ($tbl_payr_payroll_computation) {
        if (!Schema::hasColumn($tbl_payr_payroll_computation,'payroll_computation_name')) {
            $table->string('payroll_computation_name')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation,'payroll_computation_date')) {
            $table->timestamp('payroll_computation_date')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation,'payroll_computation_entry_status')) {
            $table->bigInteger('payroll_computation_entry_status')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation,'business_id')) {
            $table->bigInteger('business_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation,'company_id')) {
            $table->bigInteger('company_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation,'branch_id')) {
            $table->bigInteger('branch_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation,'payroll_computation_user_id')) {
            $table->bigInteger('payroll_computation_user_id')->nullable();
        }
    });


    //payroll computation allowance 
    $tbl_payr_payroll_computation_allowance = 'tbl_payr_payroll_computation_allowance';
    if (!Schema::hasTable($tbl_payr_payroll_computation_allowance)) {
        Schema::create('tbl_payr_payroll_computation_allowance', function (Blueprint $table) {
            $table->bigInteger('allowance_id')->primary();
            $table->timestamps();
        });
    }

    Schema::table($tbl_payr_payroll_computation_allowance, function (Blueprint $table) use ($tbl_payr_payroll_computation_allowance) {
        if (!Schema::hasColumn($tbl_payr_payroll_computation_allowance,'payroll_computation_id')) {
            $table->string('payroll_computation_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation_allowance,'allowance_salary_head')) {
            $table->bigInteger('allowance_salary_head')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation_allowance,'allowance_salary_type')) {
            $table->string('allowance_salary_type')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation_allowance,'allowance_value')) {
            $table->string('allowance_value')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation_allowance,'business_id')) {
            $table->bigInteger('business_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation_allowance,'company_id')) {
            $table->bigInteger('company_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation_allowance,'branch_id')) {
            $table->bigInteger('branch_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation_allowance,'allowance_user_id')) {
            $table->bigInteger('allowance_user_id')->nullable();
        }
    });

    //payroll computation deductions

   $tbl_payr_payroll_computation_deduction = 'tbl_payr_payroll_computation_deduction';
    if (!Schema::hasTable($tbl_payr_payroll_computation_deduction)) {
        Schema::create('tbl_payr_payroll_computation_deduction', function (Blueprint $table) {
            $table->bigInteger('deduction_id')->primary();
            $table->timestamps();
        });
    }

    Schema::table($tbl_payr_payroll_computation_deduction, function (Blueprint $table) use ($tbl_payr_payroll_computation_deduction) {
        if (!Schema::hasColumn($tbl_payr_payroll_computation_deduction,'payroll_computation_id')) {
            $table->string('payroll_computation_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation_deduction,'deduction_salary_head')) {
            $table->bigInteger('deduction_salary_head')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation_deduction,'deduction_salary_type')) {
            $table->string('deduction_salary_type')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation_deduction,'deduction_value')) {
            $table->string('deduction_value')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation_deduction,'business_id')) {
            $table->bigInteger('business_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation_deduction,'company_id')) {
            $table->bigInteger('company_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation_deduction,'branch_id')) {
            $table->bigInteger('branch_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_payroll_computation_deduction,'deduction_user_id')) {
            $table->bigInteger('deduction_user_id')->nullable();
        }
    });

//grade

$tbl_payr_grade = 'tbl_payr_grade';
    if (!Schema::hasTable($tbl_payr_grade)) {
        Schema::create('tbl_payr_grade', function (Blueprint $table) {
            $table->bigInteger('grade_id')->primary();
            $table->timestamps();
        });
    }

    Schema::table($tbl_payr_grade, function (Blueprint $table) use ($tbl_payr_grade) {
        if (!Schema::hasColumn($tbl_payr_grade,'grade_id')) {
            $table->bigInteger('grade_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_grade,'grade_name')) {
            $table->string('grade_name')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_grade,'grade_entry_status')) {
            $table->bigInteger('grade_entry_status')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_grade,'GRADE_SHORT_NAME')) {
            $table->string('GRADE_SHORT_NAME')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_grade,'grade_upper_grade')) {
            $table->string('grade_upper_grade')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_grade,'grade_max_range')) {
            $table->bigInteger('grade_max_range')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_grade,'grade_min_range')) {
            $table->bigInteger('grade_min_range')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_grade,'business_id')) {
            $table->bigInteger('business_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_grade,'company_id')) {
            $table->bigInteger('company_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_grade,'branch_id')) {
            $table->bigInteger('branch_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_grade,'GRADE_USER_ID')) {
            $table->bigInteger('GRADE_USER_ID')->nullable();
        }
    });


    //Section
    $tbl_payr_section = 'tbl_payr_section';
    if (!Schema::hasTable($tbl_payr_section)) {
        Schema::create('tbl_payr_section', function (Blueprint $table) {
            $table->bigInteger('section_id')->primary();
            $table->timestamps();
        });
    }

    Schema::table($tbl_payr_section, function (Blueprint $table) use ($tbl_payr_section) {
        if (!Schema::hasColumn($tbl_payr_section,'section_id')) {
            $table->bigInteger('section_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_section,'section_name')) {
            $table->string('section_name')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_section,'section_entry_status')) {
            $table->bigInteger('section_entry_status')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_section,'department_id')) {
            $table->bigInteger('department_id')->nullable();
        }
        
        if (!Schema::hasColumn($tbl_payr_section,'business_id')) {
            $table->bigInteger('business_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_section,'company_id')) {
            $table->bigInteger('company_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_section,'branch_id')) {
            $table->bigInteger('branch_id')->nullable();
        }
        if (!Schema::hasColumn($tbl_payr_section,'section_user_id')) {
            $table->bigInteger('section_user_id')->nullable();
        }
    });
     //religion
     $tbl_payr_religion = 'tbl_payr_religion';
     if (!Schema::hasTable($tbl_payr_religion)) {
         Schema::create('tbl_payr_religion', function (Blueprint $table) {
             $table->bigInteger('religion_id')->primary();
             $table->timestamps();
         });
     }
 
     Schema::table($tbl_payr_religion, function (Blueprint $table) use ($tbl_payr_religion) {
         if (!Schema::hasColumn($tbl_payr_religion,'religion_id')) {
             $table->bigInteger('religion_id')->nullable();
         }
         if (!Schema::hasColumn($tbl_payr_religion,'religion_name')) {
             $table->string('religion_name')->nullable();
         }
         if (!Schema::hasColumn($tbl_payr_religion,'religion_entry_status')) {
             $table->bigInteger('religion_entry_status')->nullable();
         }        
         if (!Schema::hasColumn($tbl_payr_religion,'business_id')) {
             $table->bigInteger('business_id')->nullable();
         }
         if (!Schema::hasColumn($tbl_payr_religion,'company_id')) {
             $table->bigInteger('company_id')->nullable();
         }
         if (!Schema::hasColumn($tbl_payr_religion,'branch_id')) {
             $table->bigInteger('branch_id')->nullable();
         }
         if (!Schema::hasColumn($tbl_payr_religion,'religion_user_id')) {
             $table->bigInteger('religion_user_id')->nullable();
         }
     }); 
     
     
     //hr department qualification


     $tbl_payr_job_type = 'tbl_payr_job_type';
     if (!Schema::hasTable($tbl_payr_job_type)) {
         Schema::create('tbl_payr_job_type', function (Blueprint $table) {
             $table->bigInteger('qualification_id')->primary();
             $table->timestamps();
         });
     }
 
     Schema::table($tbl_payr_job_type, function (Blueprint $table) use ($tbl_payr_job_type) {
         if (!Schema::hasColumn($tbl_payr_job_type,'qualification_id')) {
             $table->bigInteger('qualification_id')->nullable();
         }
         if (!Schema::hasColumn($tbl_payr_job_type,'qualification_name')) {
             $table->string('qualification_name')->nullable();
         }
         if (!Schema::hasColumn($tbl_payr_job_type,'qualification_entry_status')) {
             $table->bigInteger('qualification_entry_status')->nullable();
         }        
         if (!Schema::hasColumn($tbl_payr_job_type,'business_id')) {
             $table->bigInteger('business_id')->nullable();
         }
         if (!Schema::hasColumn($tbl_payr_job_type,'company_id')) {
             $table->bigInteger('company_id')->nullable();
         }
         if (!Schema::hasColumn($tbl_payr_job_type,'branch_id')) {
             $table->bigInteger('branch_id')->nullable();
         }
         if (!Schema::hasColumn($tbl_payr_job_type,'qualification_user_id')) {
             $table->bigInteger('qualification_user_id')->nullable();
         }
     });



     //hr department job_type


     $tbl_payr_job_type = 'tbl_payr_job_type';
     if (!Schema::hasTable($tbl_payr_job_type)) {
         Schema::create('tbl_payr_job_type', function (Blueprint $table) {
             $table->bigInteger('job_type_id')->primary();
             $table->timestamps();
         });
     }
 
     Schema::table($tbl_payr_job_type, function (Blueprint $table) use ($tbl_payr_job_type) {
         if (!Schema::hasColumn($tbl_payr_job_type,'job_type_id')) {
             $table->bigInteger('job_type_id')->nullable();
         }
         if (!Schema::hasColumn($tbl_payr_job_type,'job_type_name')) {
             $table->string('job_type_name')->nullable();
         }
         if (!Schema::hasColumn($tbl_payr_job_type,'job_type_entry_status')) {
             $table->bigInteger('job_type_entry_status')->nullable();
         }        
         if (!Schema::hasColumn($tbl_payr_job_type,'business_id')) {
             $table->bigInteger('business_id')->nullable();
         }
         if (!Schema::hasColumn($tbl_payr_job_type,'company_id')) {
             $table->bigInteger('company_id')->nullable();
         }
         if (!Schema::hasColumn($tbl_payr_job_type,'branch_id')) {
             $table->bigInteger('branch_id')->nullable();
         }
         if (!Schema::hasColumn($tbl_payr_job_type,'job_type_user_id')) {
             $table->bigInteger('job_type_user_id')->nullable();
         }
     });

   //hr department visa_types


   $tbl_payr_visa_types = 'tbl_payr_visa_types';
   if (!Schema::hasTable($tbl_payr_visa_types)) {
       Schema::create('tbl_payr_visa_types', function (Blueprint $table) {
           $table->bigInteger('visa_types_id')->primary();
           $table->timestamps();
       });
   }

   Schema::table($tbl_payr_visa_types, function (Blueprint $table) use ($tbl_payr_visa_types) {
       if (!Schema::hasColumn($tbl_payr_visa_types,'visa_types_id')) {
           $table->bigInteger('visa_types_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_payr_visa_types,'visa_types_name')) {
           $table->string('visa_types_name')->nullable();
       }
       if (!Schema::hasColumn($tbl_payr_visa_types,'visa_types_entry_status')) {
           $table->bigInteger('visa_types_entry_status')->nullable();
       }        
       if (!Schema::hasColumn($tbl_payr_visa_types,'business_id')) {
           $table->bigInteger('business_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_payr_visa_types,'company_id')) {
           $table->bigInteger('company_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_payr_visa_types,'branch_id')) {
           $table->bigInteger('branch_id')->nullable();
       }
       if (!Schema::hasColumn($tbl_payr_visa_types,'visa_types_user_id')) {
           $table->bigInteger('visa_types_user_id')->nullable();
       }
   });

 //hr department designation


 $tbl_payr_designation = 'tbl_payr_designation';
 if (!Schema::hasTable($tbl_payr_designation)) {
     Schema::create('tbl_payr_designation', function (Blueprint $table) {
         $table->bigInteger('designation_id')->primary();
         $table->timestamps();
     });
 }

 Schema::table($tbl_payr_designation, function (Blueprint $table) use ($tbl_payr_designation) {
     if (!Schema::hasColumn($tbl_payr_designation,'designation_id')) {
         $table->bigInteger('designation_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_payr_designation,'designation_name')) {
         $table->string('designation_name')->nullable();
     }
     if (!Schema::hasColumn($tbl_payr_designation,'designation_short_name')) {
        $table->string('designation_short_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_designation,'designation_code')) {
        $table->bigInteger('designation_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_designation,'designation_notes')) {
        $table->string('designation_notes')->nullable();
    }
     if (!Schema::hasColumn($tbl_payr_designation,'designation_entry_status')) {
         $table->bigInteger('designation_entry_status')->nullable();
     }        
     if (!Schema::hasColumn($tbl_payr_designation,'business_id')) {
         $table->bigInteger('business_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_payr_designation,'company_id')) {
         $table->bigInteger('company_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_payr_designation,'branch_id')) {
         $table->bigInteger('branch_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_payr_designation,'designation_user_id')) {
         $table->bigInteger('designation_user_id')->nullable();
     }
 });

//Department

 $tbl_payr_department = 'tbl_payr_department';
 if (!Schema::hasTable($tbl_payr_department)) {
     Schema::create('tbl_payr_department', function (Blueprint $table) {
         $table->bigInteger('department_id')->primary();
         $table->timestamps();
     });
 }

 Schema::table($tbl_payr_department, function (Blueprint $table) use ($tbl_payr_department) {
     if (!Schema::hasColumn($tbl_payr_department,'department_id')) {
         $table->bigInteger('department_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_payr_department,'department_name')) {
         $table->string('department_name')->nullable();
     }
     if (!Schema::hasColumn($tbl_payr_department,'department_short_name')) {
        $table->string('department_short_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_department,'department_code')) {
        $table->bigInteger('department_code')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_department,'department_notes')) {
        $table->string('department_notes')->nullable();
    }
     if (!Schema::hasColumn($tbl_payr_department,'department_entry_status')) {
         $table->bigInteger('department_entry_status')->nullable();
     }        
     if (!Schema::hasColumn($tbl_payr_department,'business_id')) {
         $table->bigInteger('business_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_payr_department,'company_id')) {
         $table->bigInteger('company_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_payr_department,'branch_id')) {
         $table->bigInteger('branch_id')->nullable();
     }
     if (!Schema::hasColumn($tbl_payr_department,'department_user_id')) {
         $table->bigInteger('department_user_id')->nullable();
     }
 });

//employee type

$tbl_payr_employee_type = 'tbl_payr_employee_type';
if (!Schema::hasTable($tbl_payr_employee_type)) {
    Schema::create('tbl_payr_employee_type', function (Blueprint $table) {
        $table->bigInteger('employee_type_id')->primary();
        $table->timestamps();
    });
}

Schema::table($tbl_payr_employee_type, function (Blueprint $table) use ($tbl_payr_employee_type) {
    if (!Schema::hasColumn($tbl_payr_employee_type,'department_id')) {
        $table->bigInteger('department_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_employee_type,'employee_type_name')) {
        $table->string('employee_type_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_employee_type,'employee_type_entry_status')) {
        $table->bigInteger('employee_type_entry_status')->nullable();
    }        
    if (!Schema::hasColumn($tbl_payr_employee_type,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_employee_type,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_employee_type,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_employee_type,'employee_type_user_id')) {
        $table->bigInteger('employee_type_user_id')->nullable();
    }
});

//leave type

$tbl_payr_leave_type = 'tbl_payr_leave_type';
if (!Schema::hasTable($tbl_payr_leave_type)) {
    Schema::create('tbl_payr_leave_type', function (Blueprint $table) {
        $table->bigInteger('leave_type_id')->primary();
        $table->timestamps();
    });
}

Schema::table($tbl_payr_leave_type, function (Blueprint $table) use ($tbl_payr_leave_type) {
    if (!Schema::hasColumn($tbl_payr_leave_type,'leave_type_id')) {
        $table->bigInteger('leave_type_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_leave_type,'leave_type_name')) {
        $table->string('leave_type_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_leave_type,'leave_type_short_name')) {
        $table->string('leave_type_short_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_leave_type,'leave_type_notes')) {
        $table->string('leave_type_notes')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_leave_type,'leave_type_entry_status')) {
        $table->bigInteger('leave_type_entry_status')->nullable();
    }        
    if (!Schema::hasColumn($tbl_payr_leave_type,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_leave_type,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_leave_type,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_leave_type,'leave_type_user_id')) {
        $table->bigInteger('leave_type_user_id')->nullable();
    }
});

//leave type

$tbl_payr_leave_type = 'tbl_payr_leave_type';
if (!Schema::hasTable($tbl_payr_leave_type)) {
    Schema::create('tbl_payr_leave_type', function (Blueprint $table) {
        $table->bigInteger('leave_type_id')->primary();
        $table->timestamps();
    });
}

Schema::table($tbl_payr_leave_type, function (Blueprint $table) use ($tbl_payr_leave_type) {
    if (!Schema::hasColumn($tbl_payr_leave_type,'leave_type_id')) {
        $table->bigInteger('leave_type_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_leave_type,'leave_type_name')) {
        $table->string('leave_type_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_leave_type,'leave_type_short_name')) {
        $table->string('leave_type_short_name')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_leave_type,'leave_type_notes')) {
        $table->string('leave_type_notes')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_leave_type,'leave_type_entry_status')) {
        $table->bigInteger('leave_type_entry_status')->nullable();
    }        
    if (!Schema::hasColumn($tbl_payr_leave_type,'business_id')) {
        $table->bigInteger('business_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_leave_type,'company_id')) {
        $table->bigInteger('company_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_leave_type,'branch_id')) {
        $table->bigInteger('branch_id')->nullable();
    }
    if (!Schema::hasColumn($tbl_payr_leave_type,'leave_type_user_id')) {
        $table->bigInteger('leave_type_user_id')->nullable();
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
       // Schema::dropIfExists('hr_department_tables');
    }
}
