
BEGIN
   EXECUTE IMMEDIATE 'DROP TABLE tbl_menu_flow_criteria_flow_bypass CASCADE CONSTRAINTS';
EXCEPTION
   WHEN OTHERS THEN NULL;
END;
/

BEGIN
   EXECUTE IMMEDIATE 'DROP TABLE tbl_menu_flow_criteria_flow_designations CASCADE CONSTRAINTS';
EXCEPTION
   WHEN OTHERS THEN NULL;
END;
/

BEGIN
   EXECUTE IMMEDIATE 'DROP TABLE tbl_menu_flow_criteria_flow_users CASCADE CONSTRAINTS';
EXCEPTION
   WHEN OTHERS THEN NULL;
END;
/

BEGIN
   EXECUTE IMMEDIATE 'DROP TABLE tbl_menu_flow_criteria_flow_actions CASCADE CONSTRAINTS';
EXCEPTION
   WHEN OTHERS THEN NULL;
END;
/

BEGIN
   EXECUTE IMMEDIATE 'DROP TABLE tbl_menu_flow_criteria_flows CASCADE CONSTRAINTS';
EXCEPTION
   WHEN OTHERS THEN NULL;
END;
/

BEGIN
   EXECUTE IMMEDIATE 'DROP TABLE tbl_menu_flow_criteria_conditions CASCADE CONSTRAINTS';
EXCEPTION
   WHEN OTHERS THEN NULL;
END;
/

BEGIN
   EXECUTE IMMEDIATE 'DROP TABLE tbl_menu_flow_criteria CASCADE CONSTRAINTS';
EXCEPTION
   WHEN OTHERS THEN NULL;
END;
/

-- ================================================================
-- 1. Main Flow Criteria Table
-- ================================================================
CREATE TABLE tbl_menu_flow_criteria (
    menu_flow_criteria_id VARCHAR2(36) PRIMARY KEY,
    menu_flow_criteria_dtl_id VARCHAR2(100),
    menu_flow_criteria_name VARCHAR2(255) NOT NULL, -- Table name of the form
    menu_flow_criteria_apply_at DATE DEFAULT SYSDATE,
    menu_flow_criteria_status NUMBER(1) DEFAULT 1, -- 1=Active, 0=Inactive
    menu_flow_criteria_entry_status NUMBER(1) DEFAULT 1,
    business_id NUMBER NOT NULL,
    company_id NUMBER NOT NULL,
    branch_id NUMBER NOT NULL,
    created_by NUMBER NOT NULL,
    updated_by NUMBER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_mfc_name ON tbl_menu_flow_criteria(menu_flow_criteria_name);
CREATE INDEX idx_mfc_business ON tbl_menu_flow_criteria(business_id, company_id, branch_id);

COMMENT ON TABLE tbl_menu_flow_criteria IS 'Main table storing flow criteria configurations for different forms';
COMMENT ON COLUMN tbl_menu_flow_criteria.menu_flow_criteria_name IS 'Database table name of the form this criteria applies to';

-- ================================================================
-- 2. Flow Criteria Conditions Table (WHERE clause)
-- ================================================================
CREATE TABLE tbl_menu_flow_criteria_conditions (
    menu_flow_criteria_condition_id VARCHAR2(36) PRIMARY KEY,
    menu_flow_criteria_id VARCHAR2(36) NOT NULL,
    condition_sr_number NUMBER NOT NULL,
    condition_field VARCHAR2(255) NOT NULL, -- Field name from table
    condition_operator VARCHAR2(20) NOT NULL, -- =, !=, >, <, >=, <=, Like, Between
    condition_value VARCHAR2(500), -- Value to compare
    condition_logic_operator VARCHAR2(10), -- AND, OR (for next condition)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mfcc_criteria FOREIGN KEY (menu_flow_criteria_id)
        REFERENCES tbl_menu_flow_criteria(menu_flow_criteria_id) ON DELETE CASCADE
);

CREATE INDEX idx_mfcc_criteria ON tbl_menu_flow_criteria_conditions(menu_flow_criteria_id);

COMMENT ON TABLE tbl_menu_flow_criteria_conditions IS 'Stores conditional rules (WHERE clauses) for when workflow applies';

-- ================================================================
-- 3. Flow Stages Table
-- ================================================================
CREATE TABLE tbl_menu_flow_criteria_flows (
    menu_flow_criteria_flow_id VARCHAR2(36) PRIMARY KEY,
    menu_flow_criteria_id VARCHAR2(36) NOT NULL,
    stg_flows_id NUMBER, -- Reference to tbl_stg_flows
    flow_order NUMBER NOT NULL, -- Sequence of this stage in workflow
    flow_name VARCHAR2(255) NOT NULL, -- Data Entry, Approval, etc.

    -- Timing Configuration
    lead_time_value NUMBER,
    lead_time_unit VARCHAR2(20), -- Minutes, Hours, Days, Weeks, Month
    reminder_time_minutes NUMBER,

    -- User Assignment Logic
    require_all_users NUMBER(1) DEFAULT 0, -- 1=All must approve, 0=Any one can approve

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mfcf_criteria FOREIGN KEY (menu_flow_criteria_id)
        REFERENCES tbl_menu_flow_criteria(menu_flow_criteria_id) ON DELETE CASCADE
);

CREATE INDEX idx_mfcf_criteria ON tbl_menu_flow_criteria_flows(menu_flow_criteria_id);
CREATE INDEX idx_mfcf_order ON tbl_menu_flow_criteria_flows(menu_flow_criteria_id, flow_order);

COMMENT ON TABLE tbl_menu_flow_criteria_flows IS 'Stores individual stages/flows in the workflow';
COMMENT ON COLUMN tbl_menu_flow_criteria_flows.require_all_users IS '1=All assigned users must act, 0=Any one user can act';

-- ================================================================
-- 4. Flow Actions Table
-- ================================================================
CREATE TABLE tbl_menu_flow_criteria_flow_actions (
    menu_flow_criteria_flow_action_id VARCHAR2(36) PRIMARY KEY,
    menu_flow_criteria_flow_id VARCHAR2(36) NOT NULL,
    action_name VARCHAR2(100) NOT NULL, -- Archive, New, Pull Back, Save, etc.
    send_notification NUMBER(1) DEFAULT 0, -- 1=Send notification, 0=Don't send
    notification_config CLOB, -- JSON config for notifications
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mfcfa_flow FOREIGN KEY (menu_flow_criteria_flow_id)
        REFERENCES tbl_menu_flow_criteria_flows(menu_flow_criteria_flow_id) ON DELETE CASCADE
);

CREATE INDEX idx_mfcfa_flow ON tbl_menu_flow_criteria_flow_actions(menu_flow_criteria_flow_id);

COMMENT ON TABLE tbl_menu_flow_criteria_flow_actions IS 'Stores available actions for each flow stage';

-- ================================================================
-- 5. Flow Users Table
-- ================================================================
CREATE TABLE tbl_menu_flow_criteria_flow_users (
    menu_flow_criteria_flow_user_id VARCHAR2(36) PRIMARY KEY,
    menu_flow_criteria_flow_id VARCHAR2(36) NOT NULL,
    user_id NUMBER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mfcfu_flow FOREIGN KEY (menu_flow_criteria_flow_id)
        REFERENCES tbl_menu_flow_criteria_flows(menu_flow_criteria_flow_id) ON DELETE CASCADE
);

CREATE INDEX idx_mfcfu_flow ON tbl_menu_flow_criteria_flow_users(menu_flow_criteria_flow_id);
CREATE INDEX idx_mfcfu_user ON tbl_menu_flow_criteria_flow_users(user_id);

COMMENT ON TABLE tbl_menu_flow_criteria_flow_users IS 'Stores users assigned to each flow stage';

-- ================================================================
-- 6. Flow Designations Table
-- ================================================================
CREATE TABLE tbl_menu_flow_criteria_flow_designations (
    menu_flow_criteria_flow_designation_id VARCHAR2(36) PRIMARY KEY,
    menu_flow_criteria_flow_id VARCHAR2(36) NOT NULL,
    designation_id NUMBER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mfcfd_flow FOREIGN KEY (menu_flow_criteria_flow_id)
        REFERENCES tbl_menu_flow_criteria_flows(menu_flow_criteria_flow_id) ON DELETE CASCADE
);

CREATE INDEX idx_mfcfd_flow ON tbl_menu_flow_criteria_flow_designations(menu_flow_criteria_flow_id);
CREATE INDEX idx_mfcfd_designation ON tbl_menu_flow_criteria_flow_designations(designation_id);

COMMENT ON TABLE tbl_menu_flow_criteria_flow_designations IS 'Stores designations/roles assigned to each flow stage';

-- ================================================================
-- 7. Flow Bypass Table (Users/Designations who can skip this stage)
-- ================================================================
CREATE TABLE tbl_menu_flow_criteria_flow_bypass (
    menu_flow_criteria_flow_bypass_id VARCHAR2(36) PRIMARY KEY,
    menu_flow_criteria_flow_id VARCHAR2(36) NOT NULL,
    bypass_type VARCHAR2(20) NOT NULL, -- 'user' or 'designation'
    bypass_user_id NUMBER, -- If bypass_type = 'user'
    bypass_designation_id NUMBER, -- If bypass_type = 'designation'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_mfcfb_flow FOREIGN KEY (menu_flow_criteria_flow_id)
        REFERENCES tbl_menu_flow_criteria_flows(menu_flow_criteria_flow_id) ON DELETE CASCADE
);

CREATE INDEX idx_mfcfb_flow ON tbl_menu_flow_criteria_flow_bypass(menu_flow_criteria_flow_id);
CREATE INDEX idx_mfcfb_user ON tbl_menu_flow_criteria_flow_bypass(bypass_user_id);

COMMENT ON TABLE tbl_menu_flow_criteria_flow_bypass IS 'Stores users/designations who can bypass this flow stage';


COMMIT;

SELECT 'Flow Criteria Tables Created Successfully!' AS status FROM dual;
