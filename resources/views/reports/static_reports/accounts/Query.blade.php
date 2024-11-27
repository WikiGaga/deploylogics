select  CALENDAR_YEAR , CALENDAR_MONTH, MONTH_DATE ,  sum(SALE_AMOUNT) SALE_AMOUNT,  sum(GRN_AMOUNT) GRN_AMOUNT,  sum(STOCK_AMOUNT) STOCK_AMOUNT , sum(STOCK_RCV_AMOUNT)  STOCK_RCV_AMOUNT ,   (sum(SALE_AMOUNT) +  sum(STOCK_AMOUNT)) tot_sale, (sum(STOCK_RCV_AMOUNT) + sum(GRN_AMOUNT) ) tot_purchase from
    (

        select  CALENDAR_YEAR , CALENDAR_MONTH,  to_char(SALE.SALES_DATE,'MM/YYYY') MONTH_DATE , 
        SUM(SALE.SALES_DTL_AMOUNT)  SALE_AMOUNT     , 0 GRN_AMOUNT , 0 STOCK_AMOUNT , 0 STOCK_RCV_AMOUNT  
        from VW_SALE_SALES_INVOICE SALE   ,  TBL_SOFT_CALENDAR 
        WHERE   TBL_SOFT_CALENDAR.CALENDAR_DATE =  SALE.SALES_DATE 
        and   (SALE.SALES_DATE BETWEEN  $from_date$  AND $to_date$ ) 
        AND SALE.BRANCH_ID $branch_multiple$ 
        AND lower(SALES_TYPE) IN ('si' , 'pos', 'rpos', 'sr' )
        group by to_char(SALE.SALES_DATE ,'MM/YYYY') , CALENDAR_YEAR , CALENDAR_MONTH   , CALENDAR_MONTH 

        UNION ALL 

        SELECT    CALENDAR_YEAR , CALENDAR_MONTH,  to_char(GRN_DATE,'MM/YYYY') MONTH_DATE ,  0 SALE_AMOUNT ,   
        SUM( 
        (CASE  GRN_TYPE WHEN  'PR'
        THEN -GRN_TOTAL_NET_AMOUNT
        ELSE GRN_TOTAL_NET_AMOUNT
        END)  
        )  GRN_AMOUNT   , 0 STOCK_AMOUNT   , 0 STOCK_RCV_AMOUNT   
        from TBL_PURC_GRN     ,  TBL_SOFT_CALENDAR
        WHERE   TBL_SOFT_CALENDAR.CALENDAR_DATE =  GRN_DATE
        and   (GRN_DATE BETWEEN  $from_date$  AND $to_date$ ) 
        AND  BRANCH_ID $branch_multiple$ 
        AND lower(GRN_TYPE) IN ('GRN' , 'PI' , 'grn', 'pi' )
        group by to_char( GRN_DATE ,'MM/YYYY') , CALENDAR_YEAR , CALENDAR_MONTH   

        UNION ALL

        SELECT  CALENDAR_YEAR , CALENDAR_MONTH,     to_char(STOCK_DATE,'MM/YYYY') MONTH_DATE , 0 SALE_AMOUNT ,   
        0 GRN_AMOUNT   , SUM(STOCK_DTL_TOTAL_AMOUNT)  STOCK_AMOUNT   , 0 STOCK_RCV_AMOUNT   
        from VW_INVE_STOCK     ,  TBL_SOFT_CALENDAR
        WHERE   TBL_SOFT_CALENDAR.CALENDAR_DATE =  STOCK_DATE
        and   (STOCK_DATE BETWEEN  $from_date$  AND $to_date$ )  
        AND  BRANCH_ID $branch_multiple$ 
        AND  lower( STOCK_CODE_TYPE) IN ('st' , 'ST' )
        group by to_char( STOCK_DATE ,'MM/YYYY') , CALENDAR_YEAR , CALENDAR_MONTH 

       UNION ALL 

        SELECT  CALENDAR_YEAR , CALENDAR_MONTH,     to_char(STOCK_DATE,'MM/YYYY') MONTH_DATE , 0 SALE_AMOUNT ,   
        0 GRN_AMOUNT   ,  0 STOCK_AMOUNT,    SUM(STOCK_DTL_TOTAL_AMOUNT)  STOCK_RCV_AMOUNT   
        from VW_INVE_STOCK     ,  TBL_SOFT_CALENDAR 
        WHERE   TBL_SOFT_CALENDAR.CALENDAR_DATE =  STOCK_DATE
        and   (STOCK_DATE BETWEEN  $from_date$  AND $to_date$ )  
        AND  BRANCH_ID $branch_multiple$ 
        AND  lower( STOCK_CODE_TYPE) IN ('str' , 'STR' ) 
        group by to_char( STOCK_DATE ,'MM/YYYY') , CALENDAR_YEAR , CALENDAR_MONTH 
           
    ) kaka 
    group by  MONTH_DATE , CALENDAR_YEAR , CALENDAR_MONTH order by  CALENDAR_YEAR , CALENDAR_MONTH