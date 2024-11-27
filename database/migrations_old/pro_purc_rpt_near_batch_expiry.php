<?php

use App\Library\Utilities;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ProPurcRptNearBatchExpiry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*DB::unprepared("DROP procedure IF EXISTS pro_purc_rpt_near_batch_expiry");
        DB::unprepared('DROP PROCEDURE IF EXISTS pro_purc_rpt_near_batch_expiry');*/
        $procedure = " DROP PROCEDURE IF EXISTS `pro_purc_rpt_near_batch_expiry`
        CREATE PROCEDURE pro_purc_rpt_near_batch_expiry
(

   CRT_DATE   IN     date,
   v_BUSINESS_ID           IN     NUMBER,
   V_COMPANY_ID            IN     NUMBER,
   V_BRANCH_ID             IN     NUMBER
)

AS


  -- DECLARE
  --CRT_DATE DATE  := SYSDATE;
  l_counter NUMBER := 0;
  l_qty_counter NUMBER := 0;
  SR_NO NUMBER := 0;


 BEGIN

    DELETE FROM RPT_IVEN_BATCH_EXPIRY;

    FOR I IN (
        SELECT * FROM
        (
        SELECT BUSINESS_ID,COMPANY_ID ,  BRANCH_ID ,  PRODUCT_ID , PRODUCT_BARCODE_ID , SUM(QTY_BASE_UNIT_VALUE) QTY FROM VW_PURC_STOCK_DTL   GROUP BY
        BUSINESS_ID,COMPANY_ID , BRANCH_ID , PRODUCT_ID   , PRODUCT_BARCODE_ID
        ) ABC WHERE  QTY > 0

-- SELECT * FROM VW_PURC_STOCK_DTL
    )

    LOOP

        l_counter :=  I.QTY;


        FOR J IN (

        select  GRN_ID , GRN_CODE , GRN_DATE  , PRODUCT_ID , PRODUCT_BARCODE_ID , PRODUCT_NAME ,  TBL_PURC_GRN_DTL_BATCH_NO BATCH_NO  , TBL_PURC_GRN_DTL_EXPIRY_DATE  BATCH_EXPIRY_DATE ,  QTY_BASE_UNIT     from VW_PURC_GRN where  PRODUCT_ID =  I.PRODUCT_ID  AND  PRODUCT_BARCODE_ID =  I.PRODUCT_BARCODE_ID

        ORDER BY   PRODUCT_ID , PRODUCT_BARCODE_ID, TBL_PURC_GRN_DTL_BATCH_NO , GRN_DATE DESC,  GRN_CODE DESC

        )
        LOOP

            l_qty_counter := J.QTY_BASE_UNIT;

            IF l_counter <  J.QTY_BASE_UNIT AND l_counter > 0   THEN
                l_qty_counter := l_counter ;
            ELSIF l_counter >  J.QTY_BASE_UNIT then
                l_qty_counter := J.QTY_BASE_UNIT ;
            ELSE
                EXIT WHEN l_counter < 1;
            END IF;
            SR_NO := SR_NO + 1;
            l_counter := l_counter - l_qty_counter;

              -- select * from RPT_IVEN_BATCH_EXPIRY;
               -- select to_date(J.BATCH_EXPIRY_DATE, 'yyyy-mm-dd') - to_date(J.GRN_DATE, 'yyyy-mm-dd') from dual
              --  set serveroutput on size 30000;
              --  =========      INSERT VOUCHER ====================


                insert into  RPT_IVEN_BATCH_EXPIRY (
                BUSINESS_ID,
                COMPANY_ID,
                BRANCH_ID,
                PRODUCT_ID,

                BATCH_NO,
                BATCH_EXPIRY_DATE,
                GRN_ID,
                GRN_CODE,

                STOCK_QTY,
                BALANCE_QTY,
                SR_NO  ,
                QTY,
                GRN_DATE ,
                EXPIRY_DAYS_INVOICE ,
                NEAR_EXPIRY_DAYS,
                PRODUCT_NAME

                )
                VALUES ( I.BUSINESS_ID , I.COMPANY_ID , I.BRANCH_ID ,  I.PRODUCT_ID   , J.BATCH_NO , J.BATCH_EXPIRY_DATE
                , J.GRN_ID ,  J.GRN_CODE  ,   I.QTY   , l_counter  , SR_NO  , l_qty_counter , J.GRN_DATE  , trunc(J.BATCH_EXPIRY_DATE) - TRUNC(J.GRN_DATE) ,
  trunc(CRT_DATE) - TRUNC(J.GRN_DATE)  , J.PRODUCT_NAME
  );

      --

    --  NVL( to_date(J.BATCH_EXPIRY_DATE, 'yyyy-mm-dd') - to_date(J.GRN_DATE, 'yyyy-mm-dd'),0) * 1

            -- =========================================================


          --  l_counter := l_counter - J.QTY_BASE_UNIT;

            dbms_output.put_line( 'Inside loop: ' ||  l_counter || '->'  || l_qty_counter ) ;

     --   EXIT WHEN l_counter < 3;

        END LOOP;

    DBMS_OUTPUT.PUT_LINE( I.QTY );

    END LOOP;

EXCEPTION
    WHEN NO_DATA_FOUND
    THEN
        NULL;
END;
        ";

        DB::unprepared($procedure);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       // DB::unprepared('DROP PROCEDURE IF EXISTS pro_purc_rpt_near_batch_expiry;');
        /*$sql = "begin DROP PROCEDURE IF EXISTS  ".Utilities::getDatabaseUsername().".pro_purc_rpt_near_batch_expiry end;";
        $pdo = DB::getPdo();
        $stmt = $pdo->prepare($sql);
        $stmt->execute();*/

       /* $sql = "DROP PROCEDURE IF EXISTS pro_purc_rpt_near_batch_expiry";
        DB::connection()->getPdo()->exec($sql);*/
    }
}
