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
