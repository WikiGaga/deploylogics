<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ProPurcSupBatchInsert extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        CREATE OR REPLACE FORCE PROCEDURE pro_purc_sup_batch_insert
(
   v_SUPPLIER_ID           IN     NUMBER ,
   v_PRODUCT_ID           IN     NUMBER ,
   V_BATCH   IN     VARCHAR2,
   v_BUSINESS_ID           IN     NUMBER,
   V_COMPANY_ID            IN     NUMBER,
   V_BRANCH_ID             IN     NUMBER
)

AS
    l_acc_code   VARCHAR2 (100);
    v_count      NUMBER;
BEGIN

      SELECT COUNT (*)
      INTO v_count
      FROM TBL_PURC_PRODUCT_FOC
      WHERE PRODUCT_ID = v_PRODUCT_ID AND SUPPLIER_ID = v_SUPPLIER_ID;

--      SELECT * FROM TBL_PURC_PRODUCT_FOC WHERE PRODUCT_ID = v_PRODUCT_ID AND SUPPLIER_ID = v_SUPPLIER_ID  ;


    IF v_count < 1

    THEN

  --        SELECT GET_ACCOUNT_CODE (V_LEVEL_NO, V_PARENT_ACCOUNT_CODE), get_uuid         INTO l_acc_code, l_uuid           FROM DUAL;

-- SELECT * FROM TBL_PURC_PRODUCT_FOC;

                        INSERT INTO TBL_PURC_PRODUCT_FOC
                        (
                        SR_NO,
                        PRODUCT_FOC_ID,
                        PRODUCT_ID,
                        SUPPLIER_ID,
                        PRODUCT_FOC_PURC_QTY,
                        PRODUCT_FOC_FOC_QTY,
                        CREATED_AT,
                        UPDATED_AT
                        )
                        VALUES
                        (
                        GET_UUID(),
                        GET_UUID(),
                        v_SUPPLIER_ID,
                        v_PRODUCT_ID,
                        0,
                        0,
                        SYSDATE,
                        SYSDATE
                        );
    END IF;

-- ================ FOR ====================

-- =========================================


EXCEPTION
    WHEN NO_DATA_FOUND
    THEN
        NULL;
END
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       // DB::unprepared('DROP PROCEDURE IF EXISTS pro_purc_sup_batch_insert');
    }
}
