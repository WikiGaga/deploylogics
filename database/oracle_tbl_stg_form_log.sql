-- Oracle: Create/replace staging form log table to match app (HasStaging / TblStgFormLog)
-- Run as schema owner. Drop existing table first if recreating.

BEGIN
   EXECUTE IMMEDIATE 'DROP TABLE tbl_stg_form_log';
EXCEPTION
   WHEN OTHERS THEN
      IF SQLCODE != -942 THEN
         RAISE;
      END IF;
END;
/

CREATE TABLE tbl_stg_form_log (
    stg_form_log_id          VARCHAR2(36)   NOT NULL,
    menu_dtl_id              NUMBER         NOT NULL,
    document_id              VARCHAR2(36)   NOT NULL,
    stg_form_cases_id        VARCHAR2(36)   NOT NULL,
    user_id                  NUMBER         NOT NULL,
    stg_flows_id             NUMBER         NOT NULL,
    stg_actions_id           VARCHAR2(36)   NOT NULL,
    remarks                  VARCHAR2(4000),
    posted                   NUMBER(1)      DEFAULT 0 NOT NULL,
    stg_form_log_entry_status NUMBER(1)     DEFAULT 1 NOT NULL,
    business_id              NUMBER         NOT NULL,
    company_id               NUMBER         NOT NULL,
    branch_id                NUMBER         NOT NULL,
    created_at               TIMESTAMP,
    updated_at               TIMESTAMP,
    CONSTRAINT pk_tbl_stg_form_log PRIMARY KEY (stg_form_log_id)
);

CREATE INDEX idx_stg_form_log_menu_doc ON tbl_stg_form_log (menu_dtl_id, document_id);
CREATE INDEX idx_stg_form_log_bcb ON tbl_stg_form_log (business_id, company_id, branch_id);
CREATE INDEX idx_stg_form_log_created ON tbl_stg_form_log (created_at DESC);

COMMENT ON TABLE tbl_stg_form_log IS 'Staging activity log per document and flow';
COMMENT ON COLUMN tbl_stg_form_log.stg_form_log_id IS 'PK, UUID';
COMMENT ON COLUMN tbl_stg_form_log.document_id IS 'Document PK (e.g. purchase_order_id)';
COMMENT ON COLUMN tbl_stg_form_log.posted IS '0=in progress, 1=posted/final';
COMMENT ON COLUMN tbl_stg_form_log.stg_form_log_entry_status IS '1=valid, 0=invalid';
