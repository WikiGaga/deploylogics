-- ================================================================
-- Listing Studio setup for /listing/salary-notifications
-- menu_dtl_id = 363
-- Run AFTER tbl_wa_salary_notification.sql
-- ================================================================

-- Remove previous salary-notifications listing config (safe re-run)
DELETE FROM tbl_soft_listing_studio_dimension
WHERE listing_studio_id IN (
    SELECT listing_studio_id FROM tbl_soft_listing_studio
    WHERE listing_studio_case = 'salary-notifications'
);

DELETE FROM tbl_soft_listing_studio
WHERE listing_studio_case = 'salary-notifications';

COMMIT;

-- Listing studio master record
INSERT INTO tbl_soft_listing_studio (
    listing_studio_id,
    listing_studio_code,
    listing_studio_case,
    listing_studio_title,
    listing_studio_table_name,
    listing_studio_date,
    listing_studio_rows_per_page,
    listing_studio_sort_colum_name_1,
    listing_studio_sort_colum_name_value_1,
    listing_studio_sort_colum_name_2,
    listing_studio_sort_colum_name_value_2,
    listing_studio_view_type,
    listing_studio_type,
    menu_dtl_id,
    listing_studio_parent_menu,
    listing_studio_group_by,
    listing_studio_query,
    listing_studio_entry_status,
    business_id,
    company_id,
    branch_id,
    listing_studio_user_id
) VALUES (
    '363salarynotifications01',
    'LS-SALARY-NOTIF',
    'salary-notifications',
    'Salary Notifications',
    'tbl_wa_salary_notification_batch',
    SYSDATE,
    50,
    'created_at',
    'desc',
    NULL,
    NULL,
    'branch',
    'main_listing',
    363,
    NULL,
    0,
    'O:8:"stdClass":10:{s:12:"columns_name";s:157:"tbl_1.batch_id, tbl_1.pay_period, tbl_1.file_name, tbl_1.total_rows, tbl_1.queued_count, tbl_1.sent_count, tbl_1.failed_count, tbl_1.status, tbl_1.created_at";s:6:"metric";s:0:"";s:12:"metricTitles";s:0:"";s:10:"table_name";s:38:"tbl_wa_salary_notification_batch tbl_1";s:26:"listing_business_or_branch";s:6:"branch";s:5:"where";s:0:"";s:10:"fixedWhere";s:0:"";s:7:"groupBy";s:0:"";s:7:"orderBy";s:30:"ORDER BY tbl_1.created_at desc";s:5:"limit";s:0:"";}',
    1,
    1,
    1,
    1,
    1
);

-- Grid columns (dimensions)
INSERT INTO tbl_soft_listing_studio_dimension (
    listing_studio_dimension_id, listing_studio_id,
    listing_studio_dimension_column_name, listing_studio_dimension_column_title,
    listing_studio_dimension_entry_status, business_id, company_id, branch_id,
    listing_studio_dimension_user_id, sr_no
) VALUES ('363salarydim00000001', '363salarynotifications01', 'pay_period', 'Pay Period', 1, 1, 1, 1, 1, 1);

INSERT INTO tbl_soft_listing_studio_dimension (
    listing_studio_dimension_id, listing_studio_id,
    listing_studio_dimension_column_name, listing_studio_dimension_column_title,
    listing_studio_dimension_entry_status, business_id, company_id, branch_id,
    listing_studio_dimension_user_id, sr_no
) VALUES ('363salarydim00000002', '363salarynotifications01', 'file_name', 'File Name', 1, 1, 1, 1, 1, 2);

INSERT INTO tbl_soft_listing_studio_dimension (
    listing_studio_dimension_id, listing_studio_id,
    listing_studio_dimension_column_name, listing_studio_dimension_column_title,
    listing_studio_dimension_entry_status, business_id, company_id, branch_id,
    listing_studio_dimension_user_id, sr_no
) VALUES ('363salarydim00000003', '363salarynotifications01', 'total_rows', 'Total Rows', 1, 1, 1, 1, 1, 3);

INSERT INTO tbl_soft_listing_studio_dimension (
    listing_studio_dimension_id, listing_studio_id,
    listing_studio_dimension_column_name, listing_studio_dimension_column_title,
    listing_studio_dimension_entry_status, business_id, company_id, branch_id,
    listing_studio_dimension_user_id, sr_no
) VALUES ('363salarydim00000004', '363salarynotifications01', 'queued_count', 'Queued', 1, 1, 1, 1, 1, 4);

INSERT INTO tbl_soft_listing_studio_dimension (
    listing_studio_dimension_id, listing_studio_id,
    listing_studio_dimension_column_name, listing_studio_dimension_column_title,
    listing_studio_dimension_entry_status, business_id, company_id, branch_id,
    listing_studio_dimension_user_id, sr_no
) VALUES ('363salarydim00000005', '363salarynotifications01', 'sent_count', 'Sent', 1, 1, 1, 1, 1, 5);

INSERT INTO tbl_soft_listing_studio_dimension (
    listing_studio_dimension_id, listing_studio_id,
    listing_studio_dimension_column_name, listing_studio_dimension_column_title,
    listing_studio_dimension_entry_status, business_id, company_id, branch_id,
    listing_studio_dimension_user_id, sr_no
) VALUES ('363salarydim00000006', '363salarynotifications01', 'failed_count', 'Failed', 1, 1, 1, 1, 1, 6);

INSERT INTO tbl_soft_listing_studio_dimension (
    listing_studio_dimension_id, listing_studio_id,
    listing_studio_dimension_column_name, listing_studio_dimension_column_title,
    listing_studio_dimension_entry_status, business_id, company_id, branch_id,
    listing_studio_dimension_user_id, sr_no
) VALUES ('363salarydim00000007', '363salarynotifications01', 'status', 'Status', 1, 1, 1, 1, 1, 7);

INSERT INTO tbl_soft_listing_studio_dimension (
    listing_studio_dimension_id, listing_studio_id,
    listing_studio_dimension_column_name, listing_studio_dimension_column_title,
    listing_studio_dimension_entry_status, business_id, company_id, branch_id,
    listing_studio_dimension_user_id, sr_no
) VALUES ('363salarydim00000008', '363salarynotifications01', 'created_at', 'Created At', 1, 1, 1, 1, 1, 8);

-- Ensure menu points to listing + table (menu_dtl_id = 363)
UPDATE tbl_soft_menu_dtl
SET menu_dtl_link = '/listing/salary-notifications',
    menu_dtl_table_name = 'tbl_wa_salary_notification_batch'
WHERE menu_dtl_id = 363;

COMMIT;
