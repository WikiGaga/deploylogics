<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class VwAccoVoucher extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE OR REPLACE FORCE VIEW VW_ACCO_VOUCHER
(
    VOUCHER_DATE,
    VOUCHER_NO,
    VOUCHER_TYPE,
    VOUCHER_SR_NO,
    BUSINESS_ID,
    BUSINESS_NAME,
    COMPANY_ID,
    COMPANY_NAME,
    BRANCH_ID,
    BRANCH_NAME,
    CHART_CODE,
    VOUCHER_DESCRIP,
    VOUCHER_DEBIT,
    VOUCHER_CREDIT,
    VOUCHER_CONT_ACC_CODE,
    VOUCHER_HEAD_CODE,
    VOUCHER_USER_ID,
    VOUCHER_ENTRY_STATUS,
    VOUCHER_ENTRY_DATE_TIME,
    VOUCHER_TRAN_ID,
    VOUCHER_TAX_GROSS,
    VOUCHER_TAX_TAX,
    VOUCHER_TAX_NET,
    VOUCHER_TAX_RATE,
    VOUCHER_TAX_STATUS,
    VOUCHER_TAX_TYPE,
    VOUCHER_CHQNO,
    VOUCHER_POSTED,
    VOUCHER_INVOICE_ID,
    VOUCHER_INVOICE_CODE,
    VOUCHER_PROD_ID,
    VOUCHER_CHQBOOKBANK,
    VOUCHER_CREATION_DATE_TIME,
    VOUCHER_REC_AMOUNT,
    VOUCHER_TAX_STATUS_NEW,
    VOUCHER_SHORT_CODE,
    VOUCHER_OLD_VOUCH_NO,
    VOUCHER_OLD_VOUCH_DATE,
    VOUCHER_OLD_VOUCH_TYPE,
    VOUCHER_OLD_TRAN_ID,
    VOUCHER_DEP_SLIP_NO,
    VOUCHER_SUB_ACC_CODE,
    VOUCHER_COMP_ACC_CODE,
    VOUCHER_DEPCODE,
    VOUCHER_VERIFY_UNIT_CODE,
    VOUCHER_VERIFY_USER_ID,
    VOUCHER_CANCEL,
    VOUCHER_PAYMENTRECEIPT,
    VOUCHER_PAYEE,
    VOUCHER_PREPAREDBY,
    VOUCHER_FILEPATH,
    CREATED_AT,
    UPDATED_AT,
    VOUCHER_PAYMENT_MODE,
    VOUCHER_MODE_NO,
    VOUCHER_ACC_NAME,
    VOUCHER_ID,
    VOUCHER_FC_DEBIT,
    VOUCHER_FC_CREDIT,
    CURRENCY_ID,
    CURRENCY_NAME,
    VOUCHER_EXCHANGE_RATE,
    SALEMAN_ID,
    VOUCHER_NOTES,
    VOUCHER_MODE_DATE,
    CHART_ACCOUNT_ID,
    CHART_NAME,
    VOUCHER_DOCUMENT_ID,
    BUDGET_ID,
    BUDGET_BRANCH_ID,
    BANK_REC_POSTED,
    bank_rec_cleared_date,
    BANK_REC_CHEQUE_STATUS_ID,
    BANK_REC_VOUCHER_NOTES,
    DOCUMENT_REF_ACCOUNT,
    CHART_NAME_REF_ACCOUNT,
    VAT_AMOUNT
)
BEQUEATH DEFINER
AS
    SELECT V.VOUCHER_DATE,
           V.VOUCHER_NO,
           V.VOUCHER_TYPE,
           V.VOUCHER_SR_NO,
           b.BUSINESS_ID,
           B.BUSINESS_NAME,
           c.COMPANY_ID,
           C.COMPANY_NAME,
           br.BRANCH_ID,
           BR.BRANCH_NAME,
           COA.CHART_CODE,
           V.VOUCHER_DESCRIP,
           V.VOUCHER_DEBIT,
           V.VOUCHER_CREDIT,
           V.VOUCHER_CONT_ACC_CODE,
           V.VOUCHER_HEAD_CODE,
           V.VOUCHER_USER_ID,
           V.VOUCHER_ENTRY_STATUS,
           V.VOUCHER_ENTRY_DATE_TIME,
           V.VOUCHER_TRAN_ID,
           V.VOUCHER_TAX_GROSS,
           V.VOUCHER_TAX_TAX,
           V.VOUCHER_TAX_NET,
           V.VOUCHER_TAX_RATE,
           V.VOUCHER_TAX_STATUS,
           V.VOUCHER_TAX_TYPE,
           V.VOUCHER_CHQNO,
           V.VOUCHER_POSTED,
           V.VOUCHER_INVOICE_ID,
           V.VOUCHER_INVOICE_CODE,
           V.VOUCHER_PROD_ID,
           V.VOUCHER_CHQBOOKBANK,
           V.VOUCHER_CREATION_DATE_TIME,
           V.VOUCHER_REC_AMOUNT,
           V.VOUCHER_TAX_STATUS_NEW,
           V.VOUCHER_SHORT_CODE,
           V.VOUCHER_OLD_VOUCH_NO,
           V.VOUCHER_OLD_VOUCH_DATE,
           V.VOUCHER_OLD_VOUCH_TYPE,
           V.VOUCHER_OLD_TRAN_ID,
           V.VOUCHER_DEP_SLIP_NO,
           V.VOUCHER_SUB_ACC_CODE,
           V.VOUCHER_COMP_ACC_CODE,
           V.VOUCHER_DEPCODE,
           V.VOUCHER_VERIFY_UNIT_CODE,
           V.VOUCHER_VERIFY_USER_ID,
           V.VOUCHER_CANCEL,
           V.VOUCHER_PAYMENTRECEIPT,
           V.VOUCHER_PAYEE,
           V.VOUCHER_PREPAREDBY,
           V.VOUCHER_FILEPATH,
           V.CREATED_AT,
           V.UPDATED_AT,
           V.VOUCHER_PAYMENT_MODE,
           V.VOUCHER_MODE_NO,
           V.VOUCHER_ACC_NAME,
           V.VOUCHER_ID,
           V.VOUCHER_FC_DEBIT,
           V.VOUCHER_FC_CREDIT,
           V.CURRENCY_ID,
           cr.currency_name,
           V.VOUCHER_EXCHANGE_RATE,
           V.SALEMAN_ID,
           V.VOUCHER_NOTES,
           V.VOUCHER_MODE_DATE,
           V.CHART_ACCOUNT_ID,
           COA.CHART_NAME,
           V.VOUCHER_DOCUMENT_ID,
           V.BUDGET_ID,
           V.BUDGET_BRANCH_ID,
           V.BANK_REC_POSTED,
           V.BANK_REC_CLEARED_DATE,
           V.BANK_REC_CHEQUE_STATUS_ID,
           V.BANK_REC_VOUCHER_NOTES,
           V.DOCUMENT_REF_ACCOUNT,
           COA2.CHART_NAME,
           V.VAT_AMOUNT
      FROM TBL_ACCO_VOUCHER        V,
           TBL_ACCO_CHART_ACCOUNT  COA,
           TBL_ACCO_CHART_ACCOUNT  COA2,
           TBL_SOFT_BUSINESS       B,
           TBL_SOFT_COMPANY        C,
           TBL_SOFT_BRANCH         BR,
           tbl_defi_currency       cr
     WHERE     V.BUSINESS_ID = B.BUSINESS_ID
           AND V.COMPANY_ID = C.COMPANY_ID
           AND V.BRANCH_ID = BR.BRANCH_ID
           AND V.CHART_ACCOUNT_ID = COA.CHART_ACCOUNT_ID
           AND V.DOCUMENT_REF_ACCOUNT = COA2.CHART_ACCOUNT_ID(+)
           AND V.CURRENCY_ID = cr.CURRENCY_ID(+)
            ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW vw_acco_voucher");
    }
}
