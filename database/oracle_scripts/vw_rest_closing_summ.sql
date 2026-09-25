CREATE OR REPLACE VIEW VW_REST_CLOSING_SUMM AS
WITH paid_base AS
(
    SELECT
        s.session_id,
        s.session_no,
        s.verified,
        b.branch_id,
        b.branch_name,
        us.name AS user_name,
        TRUNC(o.order_date) AS order_date,

        CASE
            WHEN o.payment_status = 'paid'
                 AND o.payment_method <> 'credit'
            THEN o.order_amount
            ELSE 0
        END AS order_amount,

        NVL(pd.cash_paid, 0)   AS cash_paid,
        NVL(pd.card_paid, 0)   AS card_paid,
        NVL(pd.credit_paid, 0) AS credit_paid,

        (
            SELECT COUNT(*)
            FROM order_details od
            WHERE od.order_id = o.id
              AND TRIM(od.is_deleted) = 'Y'
        ) AS deleted_count,

        CASE WHEN o.payment_status = 'paid' THEN 1 ELSE 0 END AS paid_order_flag
    FROM SHIFT_SESSIONS s
         JOIN orders o
             ON s.session_id = o.payment_user_session_id
         LEFT JOIN POS_ORDER_ADDITIONAL_DTL pd
             ON o.id = pd.order_id
         LEFT JOIN users us
             ON o.payment_user_id = us.id
         LEFT JOIN TBL_SOFT_BRANCH b
             ON o.restaurant_id = b.branch_id
),
paid_agg AS
(
    SELECT
        session_id,
        session_no,
        verified,
        branch_id,
        branch_name,
        user_name,
        order_date,
        SUM(cash_paid)       AS total_cash_paid,
        SUM(card_paid)       AS total_card_paid,
        SUM(credit_paid)     AS total_credit,
        SUM(order_amount)    AS tot_amount,
        SUM(deleted_count)   AS deleted_count,
        SUM(paid_order_flag) AS paid_orders
    FROM paid_base
    GROUP BY
        session_id,
        session_no,
        verified,
        branch_id,
        branch_name,
        user_name,
        order_date
),
unpaid_agg AS
(
    SELECT
        b.branch_id,
        TRUNC(o.order_date) AS order_date,
        COUNT(*) AS unpaid_orders
    FROM orders o
         LEFT JOIN TBL_SOFT_BRANCH b
             ON o.restaurant_id = b.branch_id
    WHERE o.payment_status = 'unpaid'
    GROUP BY
        b.branch_id,
        TRUNC(o.order_date)
)
SELECT
    p.session_id,
    p.session_no,
    p.verified,
    p.branch_id,
    p.branch_name,
    p.user_name,
    p.order_date,
    p.total_cash_paid,
    NVL(s.closing_cash, 0) AS closing_cash,
    (NVL(s.closing_cash, 0) - p.total_cash_paid) AS cash_diff,
    p.total_card_paid,
    NVL(s.closing_visa, 0) AS closing_visa,
    (NVL(s.closing_visa, 0) - p.total_card_paid) AS visa_diff,
    p.tot_amount,
    (NVL(s.closing_cash, 0) + NVL(s.closing_visa, 0)) - p.tot_amount AS tot_diff,
    ROUND(
        NVL(
            (
                ((NVL(s.closing_cash, 0) + NVL(s.closing_visa, 0)) - p.tot_amount)
                / NULLIF(p.tot_amount, 0)
            ) * 100,
            0
        ),
        2
    ) AS tot_diff_percentage,
    p.total_credit,
    p.deleted_count,
    NVL(u.unpaid_orders, 0) AS unpaid_orders,
    p.paid_orders
FROM paid_agg p
     JOIN SHIFT_SESSIONS s
         ON s.session_id = p.session_id
     LEFT JOIN unpaid_agg u
         ON u.branch_id = p.branch_id
        AND u.order_date = p.order_date
ORDER BY
    p.branch_name,
    p.order_date,
    p.user_name;



old VIEW
/* Formatted on 9/23/2026 6:59:44 PM (QP5 v5.336) */
-- CREATE OR REPLACE FORCE VIEW VW_REST_CLOSING_SUMM
-- (
--     SESSION_ID,
--     SESSION_NO,
--     VERIFIED,
--     BRANCH_ID,
--     BRANCH_NAME,
--     USER_NAME,
--     ORDER_DATE,
--     TOTAL_CASH_PAID,
--     CLOSING_CASH,
--     CASH_DIFF,
--     TOTAL_CARD_PAID,
--     CLOSING_VISA,
--     VISA_DIFF,
--     TOT_AMOUNT,
--     TOT_DIFF,
--     TOT_DIFF_PERCENTAGE,
--     TOTAL_CREDIT,
--     DELETED_COUNT,
--     UNPAID_ORDERS,
--     PAID_ORDERS
-- )
-- BEQUEATH DEFINER
-- AS
--     WITH
--         paid_base
--         AS
--             (SELECT s.session_id,
--                     s.session_no,
--                     s.verified,
--                     b.branch_id,
--                     b.branch_name,
--                     us.name
--                         AS user_name,
--                     TRUNC (o.order_date)
--                         AS order_date,
--                     CASE
--                         WHEN     o.payment_status = 'paid'
--                              AND o.payment_method <> 'credit'
--                         THEN
--                             o.order_amount
--                         ELSE
--                             0
--                     END
--                         AS order_amount,
--                     NVL (pd.cash_paid, 0)
--                         AS cash_paid,
--                     NVL (pd.card_paid, 0)
--                         AS card_paid,
--                     NVL (pd.credit_paid, 0)
--                         AS credit_paid,
--                     (SELECT COUNT (*)
--                        FROM order_details od
--                       WHERE od.order_id = o.id AND TRIM (od.is_deleted) = 'Y')
--                         AS deleted_count,
--                     CASE WHEN o.payment_status = 'paid' THEN 1 ELSE 0 END
--                         AS paid_order_flag
--                FROM SHIFT_SESSIONS  s
--                     JOIN orders o ON s.session_id = o.payment_user_session_id
--                     LEFT JOIN POS_ORDER_ADDITIONAL_DTL pd
--                         ON o.id = pd.order_id
--                     LEFT JOIN users us ON o.payment_user_id = us.id
--                     LEFT JOIN TBL_SOFT_BRANCH b
--                         ON o.restaurant_id = b.branch_id),
--         paid_agg
--         AS
--             (  SELECT session_id,
--                       session_no,
--                       verified,
--                       branch_id,
--                       branch_name,
--                       user_name,
--                       order_date,
--                       SUM (cash_paid)           AS total_cash_paid,
--                       SUM (card_paid)           AS total_card_paid,
--                       SUM (credit_paid)         AS total_credit,
--                       SUM (order_amount)        AS tot_amount,
--                       SUM (deleted_count)       AS deleted_count,
--                       SUM (paid_order_flag)     AS paid_orders
--                  FROM paid_base
--              GROUP BY session_id,
--                       session_no,
--                       verified,
--                       branch_id,
--                       branch_name,
--                       user_name,
--                       order_date),
--         unpaid_agg
--         AS
--             (  SELECT b.branch_id,
--                       TRUNC (o.order_date)     AS order_date,
--                       COUNT (*)                AS unpaid_orders
--                  FROM orders o
--                       LEFT JOIN TBL_SOFT_BRANCH b
--                           ON o.restaurant_id = b.branch_id
--                 WHERE o.payment_status = 'unpaid'
--              GROUP BY b.branch_id, TRUNC (o.order_date))
--       SELECT p.session_id,
--              p.session_no,
--              p.verified,
--              p.branch_id,
--              p.branch_name,
--              p.user_name,
--              p.order_date,
--              p.total_cash_paid,
--              s.closing_cash,
--              (s.closing_cash - p.total_cash_paid)
--                  AS cash_diff,
--              p.total_card_paid,
--              s.closing_visa,
--              (s.closing_visa - p.total_card_paid)
--                  AS visa_diff,
--              p.tot_amount,
--              (s.closing_cash + s.closing_visa) - p.tot_amount
--                  AS tot_diff,
--              ROUND (
--                  NVL (
--                        (  ((s.closing_cash + s.closing_visa) - p.tot_amount)
--                         / NULLIF (p.tot_amount, 0))
--                      * 100,
--                      0),
--                  2)
--                  AS tot_diff_percentage,
--              p.total_credit,
--              p.deleted_count,
--              NVL (u.unpaid_orders, 0)
--                  AS unpaid_orders,
--              p.paid_orders
--         FROM paid_agg p
--              JOIN SHIFT_SESSIONS s ON s.session_id = p.session_id
--              LEFT JOIN unpaid_agg u
--                  ON u.branch_id = p.branch_id AND u.order_date = p.order_date
--     ORDER BY p.branch_name, p.order_date, p.user_name;


/* Formatted on 9/25/2026 12:15:05 PM (QP5 v5.336)
CREATE OR REPLACE FORCE VIEW VW_REST_ORDER_DTL
(
    ID,
    ORDER_ID,
    UPDATED_AT,
    ORDER_SERIAL,
    ORDER_DATE,
    ORDER_STATUS,
    PAYMENT_STATUS,
    ORDER_TYPE,
    BRANCH_ID,
    BRANCH_NAME,
    PARTNER_NAME,
    CATEGORY_ID,
    CATEGORY_NAME,
    FOOD_ID,
    FOOD_NAME,
    VARIATION,
    ADD_ONS,
    FOOD_PAYMENT_STATUS,
    FOOD_CREATE_TIME,
    IS_DELETED,
    FOOD_CANCEL_REASON,
    CANCEL_TEXT,
    FOOD_COOKING_STATUS,
    PRICE,
    QUANTITY,
    ITEM_AMOUNT,
    ITEM_DISCOUNT,
    ITEM_DISCOUNT_TYPE_ID,
    ITEM_DISCOUNT_TYPE,
    ITEM_AMOUNT_AFTER_DISCOUNT,
    TOTAL_ADD_ON_PRICE,
    ITEM_NET_AMOUNT,
    NOTES
)
BEQUEATH DEFINER
AS
    SELECT od.id,
           od.order_id,
           od.updated_at,
           o.ORDER_SERIAL,
           o.ORDER_DATE,
           o.ORDER_STATUS,
           o.PAYMENT_STATUS,
           o.ORDER_TYPE,
           o.RESTAURANT_ID
               branch_id,
           b.branch_name,
           p.PARTNER_NAME,
           f.category_id,
           ct.name
               category_name,
           od.food_id,
           f.name
               food_name,
           od.variation,
           od.add_ons,
           od.payment_Status
               food_payment_status,
           NVL (od.food_create_time, od.created_at)
               food_create_time,
           NVL (TRIM (od.IS_DELETED), 'N')
               IS_DELETED,
           cr.reason
               food_cancel_reason,
           od.cancel_text,
           CASE
               WHEN    od.cooking_status = '1'
                    OR od.cooking_status = 'unprepared'
               THEN
                   'unprepared'
               WHEN od.cooking_status = '2' OR od.cooking_status = 'prepared'
               THEN
                   'resold'
               WHEN od.cooking_status = '3' OR od.cooking_status = 'wasted'
               THEN
                   'wasted'
               ELSE
                   'NA'
           END
               AS food_cooking_status,
           od.price,
           od.quantity,
           (NVL (od.price, 0) * NVL (od.quantity, 0))
               item_amount,
           (NVL (od.discount_on_food, 0) * NVL (od.quantity, 0))
               item_discount,
           od.pos_discount_type
               ITEM_DISCOUNT_TYPE_ID,
           distype.NAME
               ITEM_DISCOUNT_TYPE,
             (NVL (od.price, 0) * NVL (od.quantity, 0))
           - (NVL (od.discount_on_food, 0) * NVL (od.quantity, 0))
               item_amount_after_discount,
           NVL (od.TOTAL_ADD_ON_PRICE, 0)
               TOTAL_ADD_ON_PRICE,
           (  (NVL (od.price, 0) * NVL (od.quantity, 0))
            - (NVL (od.discount_on_food, 0) * NVL (od.quantity, 0))
            + NVL (od.TOTAL_ADD_ON_PRICE, 0))
               item_net_amount,
           od.notes
      FROM order_details  od
           JOIN orders o ON o.id = od.order_id
           JOIN food f ON f.id = od.food_id
           JOIN CATEGORIES ct ON ct.id = f.category_id
           JOIN TBL_SOFT_BRANCH b ON b.branch_id = o.RESTAURANT_ID
           LEFT JOIN ORDER_CANCEL_REASONS cr ON cr.id = od.cancel_reason
           LEFT JOIN tbl_sale_order_partners p ON o.PARTNER_ID = p.PARTNER_ID
           LEFT JOIN tbl_pos_discount_types distype
               ON distype.id = od.pos_discount_type;



///////////////////////////////


/* Formatted on 9/25/2026 12:23:28 PM (QP5 v5.336)
CREATE OR REPLACE FORCE VIEW VW_REST_SUMMARY_DATE_WISE
(
    ORDER_DATE,
    BRANCH_ID,
    BRANCH_NAME,
    ITEMS_AMOUNT,
    DISCOUNT_ON_ITEMS,
    TOTAL_ADD_ON_PRICE,
    GROSS_SALES,
    PAID_CANCEL_AMOUNT,
    UNPAID_CANCEL_AMOUNT,
    DISCOUNT_BY_RESTAURANT,
    COUPON_DISCOUNT,
    TOTAL_DISCOUNTS,
    DELIVERY_CHARGES,
    VAT,
    NET_SALES,
    NO_OF_BILLS,
    AVERAGE_BILL,
    CASH_SALES,
    CARD_SALES,
    CREDIT_SALES,
    DELIVERY_PARTNER_SALES,
    DELIVERY_SALES,
    DINE_IN_SALES,
    TAKEAWAY_SALES,
    RETURNS,
    CANCELED_AMOUNT,
    UNPAID_BILLS,
    TODAY_NET_SALE
)
BEQUEATH DEFINER
AS
    WITH
        item_agg
        AS
            (                   /* One row per order: item-level aggregates */
               SELECT ad.ORDER_ID,
                      SUM (ad.ITEM_AMOUNT)                 AS ITEMS_AMOUNT,
                      SUM (NVL (ad.ITEM_DISCOUNT, 0))      AS ITEM_DISCOUNT,
                      SUM (ad.TOTAL_ADD_ON_PRICE)          AS TOTAL_ADD_ON_PRICE,
                      SUM (NVL (ad.ITEM_NET_AMOUNT, 0))    AS NET_ITEM_AMOUNT
                 FROM VW_REST_ORDER_DTL ad
                WHERE     ad.PAYMENT_STATUS = 'paid'
                      AND ad.ORDER_STATUS != 'canceled'
                      AND TRIM (IS_DELETED) = 'N'
             GROUP BY ad.ORDER_ID),
        item_p
        AS
            (                   /* One row per order: item-level aggregates */
               SELECT ad.ORDER_ID,
                      SUM (
                          CASE
                              WHEN ad.FOOD_PAYMENT_STATUS = 'paid'
                              THEN
                                  NVL (ad.ITEM_AMOUNT, 0)
                              ELSE
                                  0
                          END)    AS PAID_CANCEL_AMOUNT,
                      SUM (
                          CASE
                              WHEN ad.FOOD_PAYMENT_STATUS = 'unpaid'
                              THEN
                                  NVL (ad.ITEM_AMOUNT, 0)
                              ELSE
                                  0
                          END)    AS UNPAID_CANCEL_AMOUNT
                 FROM VW_REST_ORDER_DTL ad
                WHERE TRIM (IS_DELETED) = 'Y'
             GROUP BY ad.ORDER_ID),
        pay_agg
        AS
            (            /* One row per order: payment breakdown aggregates */
               SELECT addtl.ORDER_ID,
                      SUM (NVL (addtl.CASH_PAID, 0))       AS CASH_PAID,
                      SUM (NVL (addtl.CARD_PAID, 0))       AS CARD_PAID,
                      SUM (NVL (addtl.CREDIT_PAID, 0))     AS CREDIT_PAID
                 FROM POS_ORDER_ADDITIONAL_DTL addtl
             GROUP BY addtl.ORDER_ID)
      SELECT TRUNC (o.ORDER_DATE)
                 AS "ORDER_DATE",
             b.BRANCH_ID,
             b.BRANCH_NAME
                 AS BRANCH_NAME,
             /* Item and discount amounts */
             SUM (NVL (ia.ITEMS_AMOUNT, 0))
                 AS ITEMS_AMOUNT,
             SUM (NVL (ia.ITEM_DISCOUNT, 0))
                 AS DISCOUNT_ON_ITEMS,
             SUM (NVL (ia.TOTAL_ADD_ON_PRICE, 0))
                 AS TOTAL_ADD_ON_PRICE,
             SUM (NVL (ia.NET_ITEM_AMOUNT, 0))
                 AS GROSS_SALES,
             /* PAID AND UNPAID AMOUNT FOOD WISE */
             SUM (NVL (ip.PAID_CANCEL_AMOUNT, 0))
                 AS PAID_CANCEL_AMOUNT,
             SUM (NVL (ip.UNPAID_CANCEL_AMOUNT, 0))
                 AS UNPAID_CANCEL_AMOUNT,
             /* Order-level discounts */
             SUM (NVL (o.RESTAURANT_DISCOUNT_AMOUNT, 0))
                 AS DISCOUNT_BY_RESTAURANT,
             SUM (NVL (o.COUPON_DISCOUNT_AMOUNT, 0))
                 AS COUPON_DISCOUNT,
               SUM (NVL (ia.ITEM_DISCOUNT, 0))
             + SUM (NVL (o.RESTAURANT_DISCOUNT_AMOUNT, 0))
             + SUM (NVL (o.COUPON_DISCOUNT_AMOUNT, 0))
                 AS TOTAL_DISCOUNTS,
             /* Charges and tax */
             SUM (
                 CASE
                     WHEN     o.PAYMENT_STATUS = 'paid'
                          AND LOWER (o.ORDER_STATUS) <> 'canceled'
                     THEN
                         NVL (o.DELIVERY_CHARGE, 0)
                     ELSE
                         0
                 END)
                 AS DELIVERY_CHARGES,
             SUM (NVL (o.TOTAL_TAX_AMOUNT, 0))
                 AS VAT,
             /* Net sales (same logic as your original, but using aggregated item data)
             SUM (
                   NVL (ia.NET_ITEM_AMOUNT, 0)
                 - (  NVL (o.RESTAURANT_DISCOUNT_AMOUNT, 0)
                    + NVL (o.COUPON_DISCOUNT_AMOUNT, 0))
                 + NVL (o.TOTAL_TAX_AMOUNT, 0)
                 + NVL (o.DELIVERY_CHARGE, 0))
                 AS NET_SALES,
             */
             SUM (
                 CASE
                     WHEN     o.PAYMENT_STATUS = 'paid'
                          AND o.ORDER_STATUS <> 'caneled'
                     THEN
                         NVL (o.ORDER_AMOUNT, 0)
                     ELSE
                         0
                 END)
                 AS NET_SALES,
             /* Bill counts and averages */
             COUNT (
                 CASE
                     WHEN     o.PAYMENT_STATUS = 'paid'
                          AND LOWER (o.ORDER_STATUS) <> 'canceled'
                     THEN
                         1
                 END)
                 AS NO_OF_BILLS,
             ROUND (
                   SUM (
                       CASE
                           WHEN     o.PAYMENT_STATUS = 'paid'
                                AND LOWER (o.ORDER_STATUS) <> 'canceled'
                           THEN
                               NVL (o.ORDER_AMOUNT, 0)
                           ELSE
                               0
                       END)
                 / NULLIF (
                       SUM (
                           CASE
                               WHEN     o.PAYMENT_STATUS = 'paid'
                                    AND LOWER (o.ORDER_STATUS) <> 'canceled'
                               THEN
                                   1
                               ELSE
                                   0
                           END),
                       0),
                 3)
                 AS AVERAGE_BILL,
             /* Payment breakdown (using pay_agg + payment status/method) */
             SUM (NVL (pa.CASH_PAID, 0))
                 AS CASH_SALES,
             SUM (NVL (pa.CARD_PAID, 0))
                 AS CARD_SALES,
             /* SUM (NVL (pa.CREDIT_PAID, 0))                  AS CREDIT_SALES,
               /* Credit customers type splits (kept same logic as original) */
             SUM (
                 CASE
                     WHEN o.PARTNER_ID IS NULL OR o.PARTNER_ID = ''
                     THEN
                         NVL (pa.CREDIT_PAID, 0)
                     ELSE
                         0
                 END)
                 AS CREDIT_SALES,
             /* Delivery partner type splits (kept same logic as original) */
             SUM (
                 CASE
                     WHEN o.PARTNER_ID IS NOT NULL THEN NVL (pa.CREDIT_PAID, 0)
                     ELSE 0
                 END)
                 AS DELIVERY_PARTNER_SALES,
             SUM (
                 CASE
                     WHEN     o.ORDER_TYPE = 'delivery'
                          AND o.PAYMENT_STATUS = 'paid'
                          AND o.ORDER_STATUS <> 'canceled'
                     THEN
                         NVL (o.ORDER_AMOUNT, 0)
                     ELSE
                         0
                 END)
                 AS DELIVERY_SALES,
             SUM (
                 CASE
                     WHEN     o.ORDER_TYPE = 'dine_in'
                          AND o.PAYMENT_STATUS = 'paid'
                          AND o.ORDER_STATUS <> 'canceled'
                     THEN
                         NVL (o.ORDER_AMOUNT, 0)
                     ELSE
                         0
                 END)
                 AS DINE_IN_SALES,
             SUM (
                 CASE
                     WHEN     o.ORDER_TYPE = 'take_away'
                          AND o.PAYMENT_STATUS = 'paid'
                          AND o.ORDER_STATUS <> 'canceled'
                     THEN
                         NVL (o.ORDER_AMOUNT, 0)
                     ELSE
                         0
                 END)
                 AS TAKEAWAY_SALES,
             /* Returns and unpaid bills */
             SUM (
                 CASE
                     WHEN     o.PAYMENT_STATUS = 'paid'
                          AND LOWER (o.ORDER_STATUS) = 'canceled'
                     THEN
                         NVL (o.ORDER_AMOUNT, 0)
                     ELSE
                         0
                 END)
                 AS RETURNS,
             /* Returns and unpaid bills */
             SUM (
                 CASE
                     WHEN     o.PAYMENT_STATUS <> 'paid'
                          AND LOWER (o.ORDER_STATUS) = 'canceled'
                     THEN
                         NVL (o.ORDER_AMOUNT, 0)
                     ELSE
                         0
                 END)
                 AS CANCELED_AMOUNT,
             COUNT (
                 CASE
                     WHEN     o.PAYMENT_STATUS != 'paid'
                          AND o.ORDER_STATUS <> 'canceled'
                     THEN
                         o.ID
                 END)
                 AS UNPAID_BILLS,
               /* Today net sale (same logic as your original) */
               SUM (
                   CASE
                       WHEN     o.PAYMENT_STATUS = 'paid'
                            AND LOWER (o.ORDER_STATUS) != 'canceled'
                       THEN
                           NVL (o.ORDER_AMOUNT, 0)
                       ELSE
                           0
                   END)
             - SUM (
                   CASE
                       WHEN     o.PAYMENT_STATUS = 'paid'
                            AND LOWER (o.ORDER_STATUS) = 'canceled'
                       THEN
                           NVL (o.ORDER_AMOUNT, 0)
                       ELSE
                           0
                   END)
                 AS TODAY_NET_SALE
        FROM ORDERS o
             JOIN TBL_SOFT_BRANCH b ON o.RESTAURANT_ID = b.BRANCH_ID
             LEFT JOIN item_agg ia ON o.ID = ia.ORDER_ID
             LEFT JOIN item_p ip ON o.ID = ip.ORDER_ID
             LEFT JOIN pay_agg pa ON o.ID = pa.ORDER_ID
    GROUP BY TRUNC (o.ORDER_DATE), b.BRANCH_ID, b.BRANCH_NAME
    ORDER BY TRUNC (o.ORDER_DATE), b.BRANCH_ID, b.BRANCH_NAME;





////////////////////////////////



/* Formatted on 9/25/2026 12:33:43 PM (QP5 v5.336) */
CREATE OR REPLACE FORCE VIEW VW_REST_SUMMARY_ORDER_WISE
(
    ORDER_DATE,
    BRANCH_ID,
    BRANCH_NAME,
    COMPANY_ID,
    BUSINESS_ID,
    ORDER_ID,
    ORDER_SERIAL,
    ITEMS_AMOUNT,
    DISCOUNT_ON_ITEMS,
    TOTAL_ADD_ON_PRICE,
    GROSS_SALES,
    DISCOUNT_BY_RESTAURANT,
    COUPON_DISCOUNT,
    ORDER_TOTAL_DISCOUNTS,
    TOTAL_DISCOUNTS,
    DELIVERY_CHARGES,
    VAT,
    NET_SALES,
    PAYMENT_METHOD,
    ORDER_PREPERATION_TIME,
    BANK_ACCOUNT,
    PAYMENT_STATUS,
    ORDER_STATUS,
    SALES_TYPE,
    NO_OF_BILLS,
    AVERAGE_BILL,
    CASH_SALES,
    CARD_SALES,
    CREDIT_SALES,
    DELIVERY_PARTNER_SALES,
    DELIVERY_SALES,
    DINE_IN_SALES,
    TAKEAWAY_SALES,
    RETURNS,
    CANCELED_AMOUNT,
    UNPAID_BILLS,
    TODAY_NET_SALE,
    CUSTOMER_ID,
    CUSTOMER_NAME,
    CUSTOMER_ACCOUNT_ID,
    PARTNER_ID,
    PARTNER_NAME,
    PARTNER_ACCOUNT_ID,
    PAYMENT_USER_ID,
    ORDER_TYPE,
    CREATED_AT,
    UPDATED_AT,
    WALK_CUSTOMER_NAME,
    CAR_NUMBER,
    PHONE,
    ORDER_NOTES,
    CREATED_USER,
    PAYMENT_USER
)
BEQUEATH DEFINER
AS
    WITH
        item_agg
        AS
            (  SELECT ad.ORDER_ID,
                      SUM (ad.ITEM_AMOUNT)                 AS ITEMS_AMOUNT,
                      SUM (NVL (ad.ITEM_DISCOUNT, 0))      AS ITEM_DISCOUNT,
                      SUM (ad.TOTAL_ADD_ON_PRICE)          AS TOTAL_ADD_ON_PRICE,
                      SUM (NVL (ad.ITEM_NET_AMOUNT, 0))    AS NET_ITEM_AMOUNT
                 FROM VW_REST_ORDER_DTL ad
                WHERE     ad.PAYMENT_STATUS = 'paid'
                      AND LOWER (ad.ORDER_STATUS) <> 'canceled'
                      AND TRIM (ad.IS_DELETED) = 'N'
             GROUP BY ad.ORDER_ID),
        pay_agg
        AS
            (  SELECT addtl.ORDER_ID,
                      addtl.BANK_ACCOUNT,
                      SUM (NVL (addtl.CASH_PAID, 0))
                          AS CASH_PAID,
                      SUM (NVL (addtl.CARD_PAID, 0))
                          AS CARD_PAID,
                      SUM (NVL (addtl.CREDIT_PAID, 0))
                          AS CREDIT_PAID,
                      CUSTOMER_NAME
                          AS WALK_CUSTOMER_NAME,
                      CAR_NUMBER,
                      PHONE,
                      ORDER_NOTES
                 FROM POS_ORDER_ADDITIONAL_DTL addtl
             GROUP BY addtl.ORDER_ID,
                      addtl.BANK_ACCOUNT,
                      CUSTOMER_NAME,
                      CAR_NUMBER,
                      PHONE,
                      ORDER_NOTES)
      SELECT TRUNC (o.ORDER_DATE)
                 AS "ORDER_DATE",
             b.BRANCH_ID,
             b.BRANCH_NAME
                 AS BRANCH_NAME,
             1
                 AS COMPANY_ID,
             1
                 AS BUSINESS_ID,
             o.ID
                 AS Order_ID,
             o.ORDER_SERIAL,
             SUM (NVL (ia.ITEMS_AMOUNT, 0))
                 AS ITEMS_AMOUNT,
             SUM (NVL (ia.ITEM_DISCOUNT, 0))
                 AS DISCOUNT_ON_ITEMS,
             SUM (NVL (ia.TOTAL_ADD_ON_PRICE, 0))
                 AS TOTAL_ADD_ON_PRICE,
             SUM (NVL (ia.NET_ITEM_AMOUNT, 0))
                 AS GROSS_SALES,
             SUM (NVL (o.RESTAURANT_DISCOUNT_AMOUNT, 0))
                 AS DISCOUNT_BY_RESTAURANT,
             SUM (NVL (o.COUPON_DISCOUNT_AMOUNT, 0))
                 AS COUPON_DISCOUNT,
               SUM (NVL (o.RESTAURANT_DISCOUNT_AMOUNT, 0))
             + SUM (NVL (o.COUPON_DISCOUNT_AMOUNT, 0))
                 AS ORDER_TOTAL_DISCOUNTS,
               SUM (NVL (ia.ITEM_DISCOUNT, 0))
             + SUM (NVL (o.RESTAURANT_DISCOUNT_AMOUNT, 0))
             + SUM (NVL (o.COUPON_DISCOUNT_AMOUNT, 0))
                 AS TOTAL_DISCOUNTS,
             SUM (
                 CASE
                     WHEN     o.PAYMENT_STATUS = 'paid'
                          AND LOWER (o.ORDER_STATUS) <> 'canceled'
                     THEN
                         NVL (o.DELIVERY_CHARGE, 0)
                     ELSE
                         0
                 END)
                 AS DELIVERY_CHARGES,
             SUM (NVL (o.TOTAL_TAX_AMOUNT, 0))
                 AS VAT,
             SUM (
                 CASE
                     WHEN     o.PAYMENT_STATUS = 'paid'
                          AND LOWER (o.ORDER_STATUS) <> 'canceled'
                     THEN
                         NVL (o.ORDER_AMOUNT, 0)
                     ELSE
                         0
                 END)
                 AS NET_SALES,
             o.PAYMENT_METHOD,
             CASE
                 WHEN o.ORDER_PREPERATION_TIME IS NULL OR o.CREATED_AT IS NULL
                 THEN
                     NULL
                 ELSE
                        ROUND (
                              (  CAST (o.ORDER_PREPERATION_TIME AS DATE)
                               - CAST (o.CREATED_AT AS DATE))
                            * 1440)
                     || ' min'
             END
                 AS ORDER_PREPERATION_TIME,
             pa.BANK_ACCOUNT,
             o.PAYMENT_STATUS,
             o.ORDER_STATUS,
             CASE
                 WHEN     o.PAYMENT_STATUS = 'paid'
                      AND LOWER (o.ORDER_STATUS) = 'canceled'
                 THEN
                     'RPOS'
                 WHEN     o.PAYMENT_STATUS = 'paid'
                      AND LOWER (o.ORDER_STATUS) <> 'canceled'
                 THEN
                     'POS'
                 ELSE
                     'UNPAID'
             END
                 AS SALES_TYPE,
             COUNT (
                 CASE
                     WHEN     o.PAYMENT_STATUS = 'paid'
                          AND LOWER (o.ORDER_STATUS) <> 'canceled'
                     THEN
                         1
                 END)
                 AS NO_OF_BILLS,
             ROUND (
                   SUM (
                       CASE
                           WHEN     o.PAYMENT_STATUS = 'paid'
                                AND LOWER (o.ORDER_STATUS) <> 'canceled'
                           THEN
                               NVL (o.ORDER_AMOUNT, 0)
                           ELSE
                               0
                       END)
                 / NULLIF (
                       SUM (
                           CASE
                               WHEN     o.PAYMENT_STATUS = 'paid'
                                    AND LOWER (o.ORDER_STATUS) <> 'canceled'
                               THEN
                                   1
                               ELSE
                                   0
                           END),
                       0),
                 3)
                 AS AVERAGE_BILL,
             SUM (NVL (pa.CASH_PAID, 0))
                 AS CASH_SALES,
             SUM (NVL (pa.CARD_PAID, 0))
                 AS CARD_SALES,
             SUM (
                 CASE
                     WHEN o.PARTNER_ID IS NULL OR o.PARTNER_ID = ''
                     THEN
                         NVL (pa.CREDIT_PAID, 0)
                     ELSE
                         0
                 END)
                 AS CREDIT_SALES,
             SUM (
                 CASE
                     WHEN o.PARTNER_ID IS NOT NULL THEN NVL (pa.CREDIT_PAID, 0)
                     ELSE 0
                 END)
                 AS DELIVERY_PARTNER_SALES,
             SUM (
                 CASE
                     WHEN     o.ORDER_TYPE = 'delivery'
                          AND o.PAYMENT_STATUS = 'paid'
                          AND LOWER (o.ORDER_STATUS) <> 'canceled'
                     THEN
                         NVL (o.ORDER_AMOUNT, 0)
                     ELSE
                         0
                 END)
                 AS DELIVERY_SALES,
             SUM (
                 CASE
                     WHEN     o.ORDER_TYPE = 'dine_in'
                          AND o.PAYMENT_STATUS = 'paid'
                          AND LOWER (o.ORDER_STATUS) <> 'canceled'
                     THEN
                         NVL (o.ORDER_AMOUNT, 0)
                     ELSE
                         0
                 END)
                 AS DINE_IN_SALES,
             SUM (
                 CASE
                     WHEN     o.ORDER_TYPE = 'take_away'
                          AND o.PAYMENT_STATUS = 'paid'
                          AND LOWER (o.ORDER_STATUS) <> 'canceled'
                     THEN
                         NVL (o.ORDER_AMOUNT, 0)
                     ELSE
                         0
                 END)
                 AS TAKEAWAY_SALES,
             SUM (
                 CASE
                     WHEN     o.PAYMENT_STATUS = 'paid'
                          AND LOWER (o.ORDER_STATUS) = 'canceled'
                     THEN
                         NVL (o.ORDER_AMOUNT, 0)
                     ELSE
                         0
                 END)
                 AS RETURNS,
             SUM (
                 CASE
                     WHEN     o.PAYMENT_STATUS <> 'paid'
                          AND LOWER (o.ORDER_STATUS) = 'canceled'
                     THEN
                         NVL (o.ORDER_AMOUNT, 0)
                     ELSE
                         0
                 END)
                 AS CANCELED_AMOUNT,
             COUNT (
                 CASE
                     WHEN     o.PAYMENT_STATUS <> 'paid'
                          AND LOWER (o.ORDER_STATUS) <> 'canceled'
                     THEN
                         o.ID
                 END)
                 AS UNPAID_BILLS,
               SUM (
                   CASE
                       WHEN     o.PAYMENT_STATUS = 'paid'
                            AND LOWER (o.ORDER_STATUS) <> 'canceled'
                       THEN
                           NVL (o.ORDER_AMOUNT, 0)
                       ELSE
                           0
                   END)
             - SUM (
                   CASE
                       WHEN     o.PAYMENT_STATUS = 'paid'
                            AND LOWER (o.ORDER_STATUS) = 'canceled'
                       THEN
                           NVL (o.ORDER_AMOUNT, 0)
                       ELSE
                           0
                   END)
                 AS TODAY_NET_SALE,
             o.CUSTOMER_ID,
             c.CUSTOMER_NAME,
             c.CUSTOMER_ACCOUNT_ID,
             o.PARTNER_ID,
             p.PARTNER_NAME,
             p.PARTNER_ACCOUNT_ID,
             o.PAYMENT_USER_ID,
             o.ORDER_TYPE,                                 /* 2. Added here */
             o.created_at,
             o.updated_at,
             pa.WALK_CUSTOMER_NAME,
             pa.CAR_NUMBER,
             pa.PHONE,
             pa.ORDER_NOTES,
             us.NAME
                 AS created_user,
             usp.NAME
                 AS payment_user
        FROM ORDERS o
             JOIN TBL_SOFT_BRANCH b ON o.RESTAURANT_ID = b.BRANCH_ID
             LEFT JOIN item_agg ia ON o.ID = ia.ORDER_ID
             LEFT JOIN pay_agg pa ON o.ID = pa.ORDER_ID
             LEFT JOIN tbl_sale_customer c ON o.CUSTOMER_ID = c.CUSTOMER_ID
             LEFT JOIN tbl_sale_order_partners p ON o.PARTNER_ID = p.PARTNER_ID
             LEFT JOIN users us ON us.ID = o.ORDER_TAKEN_BY
             LEFT JOIN users usp ON usp.ID = o.PAYMENT_USER_ID
    GROUP BY TRUNC (o.ORDER_DATE),
             o.ID,
             o.ORDER_SERIAL,
             b.BRANCH_NAME,
             b.BRANCH_ID,
             o.PAYMENT_STATUS,
             o.PAYMENT_METHOD,
             o.ORDER_PREPERATION_TIME,
             pa.BANK_ACCOUNT,
             o.ORDER_STATUS,
             o.CUSTOMER_ID,
             c.CUSTOMER_NAME,
             c.CUSTOMER_ACCOUNT_ID,
             o.PARTNER_ID,
             p.PARTNER_NAME,
             p.PARTNER_ACCOUNT_ID,
             o.PAYMENT_USER_ID,
             o.ORDER_TYPE,                  /* 3. Added here to fix the bug */
             o.created_at,
             o.updated_at,
             pa.WALK_CUSTOMER_NAME,
             pa.CAR_NUMBER,
             pa.PHONE,
             pa.ORDER_NOTES,
             us.name,
             usp.name
    ORDER BY TRUNC (o.ORDER_DATE), o.id, b.BRANCH_NAME;



////////////////////////


/* Formatted on 9/25/2026 12:38:13 PM (QP5 v5.336) */
CREATE OR REPLACE FORCE VIEW VW_SALE_SALES_INVOICE
(
    SALES_ID,
    SALES_CODE,
    SALES_BILL_NO,
    SALES_DATE,
    CUSTOMER_ID,
    CUSTOMER_NAME,
    SALES_ADDRESS,
    SALES_REMARKS,
    SALES_SALES_MAN,
    SALES_SALES_MAN_NAME,
    SALES_CREDIT_DAYS,
    SALES_SALES_TYPE,
    PAYMENT_MODE_ID,
    SALES_ORDER_BOOKING_ID,
    SALES_DELIVERY_ID,
    CREATED_AT,
    UPDATED_AT,
    CURRENCY_ID,
    PAYMENT_TERM_ID,
    SALES_TYPE,
    SALES_EXCHANGE_RATE,
    SALES_USER_ID,
    SALES_ENTRY_STATUS,
    BUSINESS_ID,
    BUSINESS_NAME,
    COMPANY_ID,
    COMPANY_NAME,
    BRANCH_ID,
    BRANCH_NAME,
    SALES_STORE_ID,
    SALES_STORE_NAME,
    PRODUCT_ID,
    PRODUCT_NAME,
    PRODUCT_BARCODE_ID,
    PRODUCT_BARCODE_BARCODE,
    GROUP_ITEM_ID,
    GROUP_ITEM_NAME,
    GROUP_ITEM_PARENT_ID,
    GROUP_ITEM_PARENT_NAME,
    SUPPLIER_ID,
    SUPPLIER_CODE,
    SUPPLIER_NAME,
    UOM_ID,
    UOM_NAME,
    SALES_DTL_BARCODE,
    PRODUCT_HS_CODE,
    SALES_DTL_FC_RATE,
    SALES_DTL_FOC_QTY,
    SALES_DTL_ID,
    SALES_DTL_PACKING,
    SALES_DTL_QUANTITY,
    SALES_DTL_RATE,
    SALES_DTL_AMOUNT,
    SALES_DTL_VAT_PER,
    SALES_DTL_VAT_AMOUNT,
    SALES_DTL_DISC_PER,
    SALES_DTL_DISC_AMOUNT,
    SALES_DTL_TOTAL_AMOUNT,
    EXT_DISC_PER,
    EXT_DISC_AMOUNT,
    SALES_DTL_NET_AMOUNT,
    QTY_BASE_UNIT,
    FBR_CHARGES,
    DISC_PER,
    DISC_AMOUNT,
    SALES_NET_AMOUNT,
    CASH_AMOUNT,
    VISA_AMOUNT,
    LOYALTY_AMOUNT,
    MERCHANT_ID,
    CUSTOMER_CREDIT_CARD_NO,
    FBR_INVOICE_NO,
    FBR_POSTED,
    COST_RATE,
    COST_AMOUNT,
    SALES_DTL_USER_ID,
    TERMINAL_ID,
    PRODUCT_ARABIC_NAME,
    TERMINAL_NAME,
    SALES_VISA_POINTS,
    SALES_LOYALTY_POINTS,
    LOYALTY_EARNED,
    COMMON_SALES_ID,
    CASHRECEIVED,
    CHANGE,
    SALES_TYPE_NAME,
    HOLD_ID,
    SALES_RETURN_REF_NO,
    CUSTOMER_ACCOUNT_ID,
    CARD_NUMBER,
    EXPIRY_DATE,
    ISSUE_DATE,
    MEMBERSHIP_TYPE_ID,
    CUSTOMER_MOBILE_NO,
    SHIFT_ID,
    HS_CODE,
    POSTED
)
BEQUEATH DEFINER
AS
    SELECT DISTINCT C.SALES_ID,
                    C.SALES_CODE,
                    C.SALES_BILL_NO,
                    C.SALES_DATE,
                    C.CUSTOMER_ID,
                    CT.CUSTOMER_NAME,
                    C.SALES_ADDRESS,
                    C.SALES_REMARKS,
                    C.SALES_SALES_MAN,
                    UUU.NAME                  AS SALES_SALES_MAN_NAME,
                    C.SALES_CREDIT_DAYS,
                    C.SALES_SALES_TYPE,
                    C.PAYMENT_MODE_ID,
                    C.SALES_ORDER_BOOKING_ID,
                    C.SALES_DELIVERY_ID,
                    C.CREATED_AT,
                    C.UPDATED_AT,
                    C.CURRENCY_ID,
                    C.PAYMENT_TERM_ID,
                    C.SALES_TYPE,
                    C.SALES_EXCHANGE_RATE,
                    C.SALES_USER_ID,
                    C.SALES_ENTRY_STATUS,
                    C.BUSINESS_ID,
                    SB.BUSINESS_NAME,
                    C.COMPANY_ID,
                    SC.COMPANY_NAME,
                    C.BRANCH_ID,
                    SBR.BRANCH_NAME,
                    C.SALES_STORE_ID,
                    STR.STORE_NAME            SALES_STORE_NAME,
                    P.PRODUCT_ID,
                    P.PRODUCT_NAME,
                    PB.PRODUCT_BARCODE_ID,
                    PB.PRODUCT_BARCODE_BARCODE,
                    P.GROUP_ITEM_ID,
                    GI.GROUP_ITEM_NAME,
                    P.GROUP_ITEM_PARENT_ID,
                    GIP.GROUP_ITEM_NAME       GROUP_ITEM_PARENT_NAME,
                    SUP.SUPPLIER_ID,
                    SUP.SUPPLIER_CODE,
                    SUP.SUPPLIER_NAME,
                    UOM.UOM_ID,
                    UOM.UOM_NAME,
                    CD.SALES_DTL_BARCODE,
                    CD.PRODUCT_HS_CODE,
                    CD.SALES_DTL_FC_RATE,
                    CD.SALES_DTL_FOC_QTY,
                    CD.SALES_DTL_ID,
                    CD.SALES_DTL_PACKING,
                    CD.SALES_DTL_QUANTITY,
                    CD.SALES_DTL_RATE,
                    CD.SALES_DTL_AMOUNT,
                    CD.SALES_DTL_VAT_PER,
                    CD.SALES_DTL_VAT_AMOUNT,
                    CD.SALES_DTL_DISC_PER,
                    CD.SALES_DTL_DISC_AMOUNT,
                    CD.SALES_DTL_TOTAL_AMOUNT,
                    CD.EXT_DISC_PER,
                    CD.EXT_DISC_AMOUNT,
                    CD.SALES_DTL_NET_AMOUNT,
                    CD.QTY_BASE_UNIT,
                    C.FBR_CHARGES,
                    C.DISC_PER,
                    C.DISC_AMOUNT,
                    C.SALES_NET_AMOUNT,
                    C.CASH_AMOUNT,
                    C.VISA_AMOUNT,
                    C.LOYALTY_AMOUNT,
                    C.MERCHANT_ID,
                    C.CUSTOMER_CREDIT_CARD_NO,
                    C.FBR_INVOICE_NO,
                    C.FBR_POSTED,
                    CD.COST_RATE,
                    CD.COST_AMOUNT,
                    CD.SALES_DTL_USER_ID,
                    C.TERMINAL_ID,
                    P.PRODUCT_ARABIC_NAME,
                    TR.TERMINAL_NAME,
                    C.SALES_VISA_POINTS,
                    C.SALES_LOYALTY_POINTS,
                    C.LOYALTY_EARNED,
                    C.COMMON_SALES_ID,
                    C.CASHRECEIVED,
                    C.CHANGE,
                    PAY.PAYMENT_TYPE_NAME     SALES_TYPE_NAME,
                    C.HOLD_ID,
                    CD.COMMON_SALE_ID         SALES_RETURN_REF_NO,
                    CT.CUSTOMER_ACCOUNT_ID,
                    CT.CARD_NUMBER,
                    CT.EXPIRY_DATE,
                    CT.ISSUE_DATE,
                    CT.MEMBERSHIP_TYPE_ID,
                    CT.CUSTOMER_MOBILE_NO,
                    C.SHIFT_ID,
                    CD.HS_CODE,
                    C.POSTED
      FROM TBL_SALE_SALES            C,
           TBL_SALE_SALES_DTL        CD,
           TBL_SALE_CUSTOMER         CT,
           TBL_DEFI_CURRENCY         DC,
           TBL_SOFT_company          SC,
           TBL_SOFT_BUSINESS         SB,
           TBL_SOFT_BRANCH           SBR,
           --  VW_PURC_PRODUCT_BARCODE  P,
           USERS                     UUU,
           TBL_SOFT_POS_TERMINAL     TR,
           TBL_DEFI_PAYMENT_TYPE     PAY,
           TBL_DEFI_STORE            STR,
           TBL_PURC_PRODUCT          P,
           TBL_PURC_PRODUCT_BARCODE  PB,
           TBL_PURC_GROUP_ITEM       GI,
           TBL_PURC_GROUP_ITEM       GIP,
           TBL_DEFI_UOM              UOM,
           TBL_PURC_SUPPLIER         SUP
     WHERE     C.SALES_ID = CD.SALES_ID
           AND C.CUSTOMER_ID = CT.CUSTOMER_ID
           AND C.CURRENCY_ID = DC.CURRENCY_ID(+)
           AND SC.COMPANY_ID = C.COMPANY_ID
           AND SB.BUSINESS_ID = C.BUSINESS_ID
           AND SBR.BRANCH_ID = C.BRANCH_ID
           AND CD.PRODUCT_BARCODE_ID = PB.PRODUCT_BARCODE_ID
           AND CD.PRODUCT_ID = PB.PRODUCT_ID
           AND UUU.ID = C.SALES_SALES_MAN
           AND C.SALES_STORE_ID = STR.STORE_ID(+)
           AND C.TERMINAL_ID = TR.TERMINAL_ID(+)
           AND C.SALES_SALES_TYPE = PAY.PAYMENT_TYPE_ID(+)
           AND P.PRODUCT_ID = PB.PRODUCT_ID
           AND PB.UOM_ID = UOM.UOM_ID(+)
           AND P.GROUP_ITEM_ID = GI.GROUP_ITEM_ID
           AND P.GROUP_ITEM_PARENT_ID = GIP.GROUP_ITEM_ID(+)
           AND P.SUPPLIER_ID = SUP.SUPPLIER_ID(+);
*/
