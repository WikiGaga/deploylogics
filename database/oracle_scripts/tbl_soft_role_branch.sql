
CREATE TABLE tbl_soft_role_branch (
    role_id    VARCHAR2(255) NOT NULL,
    branch_id  NUMBER NOT NULL,
    CONSTRAINT pk_tbl_soft_role_branch PRIMARY KEY (role_id, branch_id)
);

COMMENT ON TABLE tbl_soft_role_branch IS 'Optional branches per role. Role updates replace only optional branches on users with that role; user default branch is unchanged.';
