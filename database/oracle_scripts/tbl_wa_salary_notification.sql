-- ================================================================
-- Salary WhatsApp Notification tables (Oracle)
-- Run manually before using /salary-notifications/form
-- ================================================================

BEGIN
   EXECUTE IMMEDIATE 'DROP TABLE tbl_wa_salary_notification_dtl CASCADE CONSTRAINTS';
EXCEPTION
   WHEN OTHERS THEN NULL;
END;
/

BEGIN
   EXECUTE IMMEDIATE 'DROP TABLE tbl_wa_salary_notification_batch CASCADE CONSTRAINTS';
EXCEPTION
   WHEN OTHERS THEN NULL;
END;
/

-- ================================================================
-- Batch: one row per Excel upload / Confirm & Send All
-- ================================================================
CREATE TABLE tbl_wa_salary_notification_batch (
    batch_id              VARCHAR2(36) PRIMARY KEY,
    pay_period            VARCHAR2(50),
    file_name             VARCHAR2(255),
    template_name         VARCHAR2(100),
    template_lang         VARCHAR2(20),
    total_rows            NUMBER(10) DEFAULT 0,
    queued_count          NUMBER(10) DEFAULT 0,
    sent_count            NUMBER(10) DEFAULT 0,
    failed_count          NUMBER(10) DEFAULT 0,
    status                VARCHAR2(20) DEFAULT 'queued',
    user_id               NUMBER,
    business_id           NUMBER,
    company_id            NUMBER,
    branch_id             NUMBER,
    created_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at          TIMESTAMP
);

CREATE INDEX idx_wa_sal_batch_status ON tbl_wa_salary_notification_batch(status);
CREATE INDEX idx_wa_sal_batch_bcb ON tbl_wa_salary_notification_batch(business_id, company_id, branch_id);
CREATE INDEX idx_wa_sal_batch_created ON tbl_wa_salary_notification_batch(created_at);

COMMENT ON TABLE tbl_wa_salary_notification_batch IS 'Salary WhatsApp bulk send batches';
COMMENT ON COLUMN tbl_wa_salary_notification_batch.status IS 'queued, processing, completed, partial';

-- ================================================================
-- Detail: one row per employee message
-- ================================================================
CREATE TABLE tbl_wa_salary_notification_dtl (
    dtl_id                VARCHAR2(36) PRIMARY KEY,
    batch_id              VARCHAR2(36) NOT NULL,
    row_no                NUMBER(10),
    employee_name         VARCHAR2(255),
    phone                 VARCHAR2(30),
    net_payment           NUMBER(18,3),
    template_params       CLOB,
    status                VARCHAR2(20) DEFAULT 'queued',
    meta_message_id       VARCHAR2(100),
    api_response          CLOB,
    message_exception     CLOB,
    sent_at               TIMESTAMP,
    created_at            TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_wa_sal_dtl_batch
        FOREIGN KEY (batch_id)
        REFERENCES tbl_wa_salary_notification_batch(batch_id)
);

CREATE INDEX idx_wa_sal_dtl_batch ON tbl_wa_salary_notification_dtl(batch_id);
CREATE INDEX idx_wa_sal_dtl_status ON tbl_wa_salary_notification_dtl(status);

COMMENT ON TABLE tbl_wa_salary_notification_dtl IS 'Per-employee salary WhatsApp send log';
COMMENT ON COLUMN tbl_wa_salary_notification_dtl.status IS 'queued, sent, failed';

COMMIT;
