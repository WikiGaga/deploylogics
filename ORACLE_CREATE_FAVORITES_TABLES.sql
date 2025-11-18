-- =====================================================
-- Oracle SQL Script to Create Product Favorites Tables
-- =====================================================
-- This script creates the generic favorites tables that can be used
-- across different modules (GRN, Purchase Orders, Sales Orders, etc.)
-- =====================================================

-- =====================================================
-- Table 1: tbl_purchase_favorites (Header Table)
-- =====================================================
CREATE TABLE tbl_purchase_favorites (
    favorite_id VARCHAR2(255) NOT NULL,
    favorite_name VARCHAR2(255) NOT NULL,
    favorite_description CLOB NULL,
    module_type VARCHAR2(50) NULL,
    user_id NUMBER NOT NULL,
    business_id NUMBER NULL,
    company_id NUMBER NULL,
    branch_id NUMBER NULL,
    is_active NUMBER(1) DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    created_by VARCHAR2(255) NULL,
    updated_by VARCHAR2(255) NULL,
    CONSTRAINT pk_purchase_favorites PRIMARY KEY (favorite_id)
);

-- Add comments to table
COMMENT ON TABLE tbl_purchase_favorites IS 'Product Favorites Lists Header - Generic';
COMMENT ON COLUMN tbl_purchase_favorites.favorite_id IS 'UUID for favorite list';
COMMENT ON COLUMN tbl_purchase_favorites.favorite_name IS 'User-defined name for the favorite list';
COMMENT ON COLUMN tbl_purchase_favorites.favorite_description IS 'Optional description';
COMMENT ON COLUMN tbl_purchase_favorites.module_type IS 'Module identifier (e.g., GRN, PO, SO) - for future module-specific filtering';
COMMENT ON COLUMN tbl_purchase_favorites.user_id IS 'User who created this favorite';
COMMENT ON COLUMN tbl_purchase_favorites.business_id IS 'Business context';
COMMENT ON COLUMN tbl_purchase_favorites.company_id IS 'Company context';
COMMENT ON COLUMN tbl_purchase_favorites.branch_id IS 'Branch context';
COMMENT ON COLUMN tbl_purchase_favorites.is_active IS '1=Active, 0=Deleted/Inactive';

-- Create indexes
CREATE INDEX idx_purchase_favorites_user_id ON tbl_purchase_favorites(user_id);
CREATE INDEX idx_purchase_favorites_module_type ON tbl_purchase_favorites(module_type);
CREATE INDEX idx_purchase_favorites_business_branch ON tbl_purchase_favorites(business_id, branch_id);
CREATE INDEX idx_purchase_favorites_is_active ON tbl_purchase_favorites(is_active);

-- =====================================================
-- Table 2: tbl_purchase_favorite_items (Detail Table)
-- =====================================================
CREATE TABLE tbl_purchase_favorite_items (
    favorite_item_id VARCHAR2(255) NOT NULL,
    favorite_id VARCHAR2(255) NOT NULL,
    sr_no NUMBER NOT NULL,
    product_id NUMBER NOT NULL,
    product_barcode_id NUMBER NOT NULL,
    uom_id NUMBER NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT pk_purchase_favorite_items PRIMARY KEY (favorite_item_id),
    CONSTRAINT fk_favorite_items_favorite FOREIGN KEY (favorite_id)
        REFERENCES tbl_purchase_favorites(favorite_id) ON DELETE CASCADE
);

-- Add comments to table
COMMENT ON TABLE tbl_purchase_favorite_items IS 'Product Favorites Items Detail - Generic';
COMMENT ON COLUMN tbl_purchase_favorite_items.favorite_item_id IS 'UUID for favorite item';
COMMENT ON COLUMN tbl_purchase_favorite_items.favorite_id IS 'Reference to tbl_purchase_favorites';
COMMENT ON COLUMN tbl_purchase_favorite_items.sr_no IS 'Sequence number for ordering products';
COMMENT ON COLUMN tbl_purchase_favorite_items.product_id IS 'Product ID';
COMMENT ON COLUMN tbl_purchase_favorite_items.product_barcode_id IS 'Product Barcode ID';
COMMENT ON COLUMN tbl_purchase_favorite_items.uom_id IS 'Unit of Measure ID (optional, can be fetched from barcode)';

-- Create indexes
CREATE INDEX idx_favorite_items_favorite_id ON tbl_purchase_favorite_items(favorite_id);
CREATE INDEX idx_favorite_items_product_id ON tbl_purchase_favorite_items(product_id);
CREATE INDEX idx_favorite_items_product_barcode_id ON tbl_purchase_favorite_items(product_barcode_id);

-- =====================================================
-- Verification Queries (Optional - Run after creation)
-- =====================================================
-- Check if tables were created successfully
-- SELECT table_name FROM user_tables WHERE table_name IN ('TBL_PURCHASE_FAVORITES', 'TBL_PURCHASE_FAVORITE_ITEMS');

-- Check indexes
-- SELECT index_name, table_name FROM user_indexes WHERE table_name IN ('TBL_PURCHASE_FAVORITES', 'TBL_PURCHASE_FAVORITE_ITEMS');

-- Check constraints
-- SELECT constraint_name, constraint_type, table_name FROM user_constraints WHERE table_name IN ('TBL_PURCHASE_FAVORITES', 'TBL_PURCHASE_FAVORITE_ITEMS');

-- =====================================================
-- Notes:
-- =====================================================
-- 1. This script uses VARCHAR2 for strings (Oracle standard)
-- 2. NUMBER is used instead of BIGINT/DECIMAL (Oracle standard)
-- 3. CLOB is used for TEXT fields (for large descriptions)
-- 4. Foreign key has ON DELETE CASCADE to automatically delete items when favorite is deleted
-- 5. Indexes are created for performance optimization
-- 6. All timestamps are nullable (no defaults set - handled by application)
-- 7. is_active uses NUMBER(1) which is equivalent to TINYINT(1) in MySQL
-- =====================================================

