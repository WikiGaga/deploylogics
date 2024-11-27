<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class VwSaleBankDistribution extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
        CREATE OR REPLACE FORCE VIEW VW_SALE_BANK_DISTRIBUTION
(
    BD_ID,
    bd_date,
    BD_CODE,
    BD_USER_ID,
    BRANCH_ID,
    BUSINESS_ID,
    COMPANY_ID,
    CREATED_AT,
    UPDATED_AT,
    SALEMAN_ID,
    SALEMAN_NAME,
    BD_PAYMENT_WAY_TYPE,
    VOUCHER_ID,
    DOCUMENT_VERIFIED_STATUS,
    BD_DTL_ID,
    BANK_ID,
    BANK_NAME,
    SR_NO,
    DENOMINATION_ID,
    BD_DTL_QTY,
    BD_DTL_AMOUNT
)
BEQUEATH DEFINER
AS
    SELECT BNK_DIST.BD_ID,
           BNK_DIST.bd_date,
           BNK_DIST.BD_CODE,
           BNK_DIST.BD_USER_ID,
           BNK_DIST.BRANCH_ID,
           BNK_DIST.BUSINESS_ID,
           BNK_DIST.COMPANY_ID,
           BNK_DIST.CREATED_AT,
           BNK_DIST.UPDATED_AT,
           BNK_DIST.SALEMAN_ID,
           USERS.NAME   SALEMAN_NAME,
           BNK_DIST.BD_PAYMENT_WAY_TYPE,
           BNK_DIST.VOUCHER_ID,
           BNK_DIST.DOCUMENT_VERIFIED_STATUS,
           BNK_DIST_DTL.BD_DTL_ID,
           BNK_DIST_DTL.BANK_ID,
           CHART.CHART_NAME     BANK_NAME,
           BNK_DIST_DTL.SR_NO,
           BNK_DIST_DTL.DENOMINATION_ID,
           BNK_DIST_DTL.BD_DTL_QTY,
           BD_DTL_AMOUNT
      FROM TBL_SALE_BANK_DISTRIBUTION      BNK_DIST,
           TBL_SALE_BANK_DISTRIBUTION_DTL  BNK_DIST_DTL,
           USERS,
           TBL_ACCO_CHART_ACCOUNT          CHART
     WHERE     BNK_DIST.BD_ID = BNK_DIST_DTL.BD_ID
           AND BNK_DIST.SALEMAN_ID = USERS.ID(+)
           AND BNK_DIST_DTL.BANK_ID = CHART.CHART_ACCOUNT_ID(+)
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
