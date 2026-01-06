CREATE TABLE tbl_system_configuration (
    system_configuration_id VARCHAR2(255) PRIMARY KEY,
    config_key VARCHAR2(255) NOT NULL,
    config_value CLOB,
    config_type VARCHAR2(50) DEFAULT 'text',
    config_group VARCHAR2(100),
    config_description VARCHAR2(500),

    business_id NUMBER(19),
    company_id NUMBER(19),
    branch_id NUMBER(19),
    system_configuration_user_id NUMBER(19),

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

ALTER TABLE tbl_system_configuration
ADD CONSTRAINT uq_branch_config_key
UNIQUE (branch_id, config_key);

CREATE INDEX idx_branch_id
ON tbl_system_configuration (branch_id);

CREATE INDEX idx_business_id
ON tbl_system_configuration (business_id);

CREATE INDEX idx_company_id
ON tbl_system_configuration (company_id);

CREATE INDEX idx_config_key
ON tbl_system_configuration (config_key);

CREATE INDEX idx_config_group
ON tbl_system_configuration (config_group);
