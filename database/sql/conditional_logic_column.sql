-- Oracle Query to add support for Conditional Logic in report_styling table
-- Note: The table structure already supports this feature through the existing columns.
-- We will use report_styling_column_type = 'conditional_logic' to store conditional logic rules.
-- No new column is actually needed, but this query documents the approach.

-- If you need to add an index for better performance when querying conditional logic:
-- CREATE INDEX idx_report_styling_conditional_logic 
-- ON tbl_soft_report_styling(report_id, report_styling_column_type) 
-- WHERE report_styling_column_type = 'conditional_logic';

-- The existing table structure (tbl_soft_report_styling) already has:
-- - report_styling_id (PK)
-- - report_id (FK to tbl_soft_report)
-- - report_styling_column_no (used as rule index/sequence)
-- - report_styling_column_type (will use 'conditional_logic' value)
-- - report_styling_key (will store: field_name, condition, value, value_2, logic_operator, background_color, text_color, etc.)
-- - report_styling_value (will store the actual values)

-- Example of how data will be stored:
-- report_styling_column_type = 'conditional_logic'
-- report_styling_column_no = 0 (rule sequence number)
-- report_styling_key = 'field_name', report_styling_value = 'is_deleted'
-- report_styling_key = 'condition', report_styling_value = 'equals'
-- report_styling_key = 'value', report_styling_value = 'yes'
-- report_styling_key = 'background_color', report_styling_value = '#ffebee'
-- report_styling_key = 'text_color', report_styling_value = '#c62828'
-- report_styling_key = 'logic_operator', report_styling_value = 'AND' (or 'OR')
