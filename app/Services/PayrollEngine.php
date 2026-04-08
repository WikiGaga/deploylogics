<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PayrollEngine
{

    public function processEmployee($employeeId,$payrollRunId,$month,$year)
    {

        $employee = DB::table('employees')
            ->where('id',$employeeId)
            ->first();

        $basicSalary = $employee->basic_salary;

        $gross = 0;
        $deductions = 0;

        $payrollId = DB::table('payrolls')->insertGetId([
            'payroll_run_id'=>$payrollRunId,
            'employee_id'=>$employeeId,
            'basic_salary'=>$basicSalary,
            'created_at'=>now()
        ]);

        $components = DB::table('employee_components')
            ->join(
                'payroll_components',
                'payroll_components.id',
                '=',
                'employee_components.component_id'
            )
            ->where('employee_components.employee_id',$employeeId)
            ->select(
                'payroll_components.id',
                'payroll_components.name',
                'payroll_components.type',
                'payroll_components.calculation_type',
                'payroll_components.value',
                'payroll_components.formula',
                'employee_components.amount'
            )
            ->get();

        foreach($components as $component){

            $amount = 0;

            if($component->calculation_type == 'fixed'){

                $amount = $component->amount ?? $component->value;

            }

            if($component->calculation_type == 'percentage'){

                $percent = $component->amount ?? $component->value;

                $amount = ($basicSalary * $percent)/100;

            }

            if($component->calculation_type == 'formula'){

                $amount = $this->calculateFormula(
                    $component->formula,
                    $employeeId,
                    $month,
                    $year,
                    $basicSalary
                );

            }

            DB::table('payroll_lines')->insert([
                'payroll_id'=>$payrollId,
                'component_id'=>$component->id,
                'title'=>$component->name,
                'amount'=>$amount,
                'created_at'=>now()
            ]);

            if($component->type == 'earning'){
                $gross += $amount;
            }else{
                $deductions += $amount;
            }

        }

        $net = $gross - $deductions;

        DB::table('payrolls')
            ->where('id',$payrollId)
            ->update([
                'gross_salary'=>$gross,
                'total_deductions'=>$deductions,
                'net_salary'=>$net
            ]);

    }

    private function calculateFormula(
        $formula,
        $employeeId,
        $month,
        $year,
        $basicSalary
    ){

        $workingDays = DB::table('attendances')
            ->where('employee_id',$employeeId)
            ->whereMonth('attendance_date',$month)
            ->whereYear('attendance_date',$year)
            ->where('status','present')
            ->count();

        $overtime = DB::table('attendances')
            ->where('employee_id',$employeeId)
            ->whereMonth('attendance_date',$month)
            ->whereYear('attendance_date',$year)
            ->sum('overtime_hours');

        $dailyRate = $basicSalary / 30;

        $formula = str_replace('BASIC',$basicSalary,$formula);
        $formula = str_replace('WORKING_DAYS',$workingDays,$formula);
        $formula = str_replace('DAILY_RATE',$dailyRate,$formula);
        $formula = str_replace('OVERTIME_HOURS',$overtime,$formula);

        return eval("return $formula;");
    }

}