# Product Favorites Feature - Implementation Guide

## Overview
This guide provides a detailed implementation plan for adding a generic favorites feature that can be used across different modules (GRN, Purchase Orders, Sales Orders, etc.). Users will be able to save frequently used product combinations as favorites and quickly populate grids with them. Product details are fetched dynamically using existing product search mechanisms, ensuring data consistency.

## Database Design

### Table 1: `tbl_purchase_favorites` (Header Table)
Stores the favorite list header information. Generic table that can be used across different modules.

```sql
CREATE TABLE `tbl_purchase_favorites` (
  `favorite_id` VARCHAR(255) NOT NULL PRIMARY KEY COMMENT 'UUID for favorite list',
  `favorite_name` VARCHAR(255) NOT NULL COMMENT 'User-defined name for the favorite list',
  `favorite_description` TEXT NULL COMMENT 'Optional description',
  `module_type` VARCHAR(50) NULL COMMENT 'Module identifier (e.g., GRN, PO, SO) - for future module-specific filtering',
  `user_id` BIGINT NOT NULL COMMENT 'User who created this favorite (auth()->user()->id)',
  `business_id` BIGINT NULL COMMENT 'Business context',
  `company_id` BIGINT NULL COMMENT 'Company context',
  `branch_id` BIGINT NULL COMMENT 'Branch context',
  `is_active` TINYINT(1) DEFAULT 1 COMMENT '1=Active, 0=Deleted/Inactive',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `created_by` VARCHAR(255) NULL COMMENT 'User who created',
  `updated_by` VARCHAR(255) NULL COMMENT 'User who last updated',
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_module_type` (`module_type`),
  INDEX `idx_business_branch` (`business_id`, `branch_id`),
  INDEX `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Product Favorites Lists Header - Generic';
```

### Table 2: `tbl_purchase_favorite_items` (Detail Table)
Stores individual products within each favorite list. Only stores essential IDs - all product details are fetched dynamically when loading.

```sql
CREATE TABLE `tbl_purchase_favorite_items` (
  `favorite_item_id` VARCHAR(255) NOT NULL PRIMARY KEY COMMENT 'UUID for favorite item',
  `favorite_id` VARCHAR(255) NOT NULL COMMENT 'Reference to tbl_purchase_favorites',
  `sr_no` INT NOT NULL COMMENT 'Sequence number for ordering products',
  `product_id` BIGINT NOT NULL COMMENT 'Product ID',
  `product_barcode_id` BIGINT NOT NULL COMMENT 'Product Barcode ID',
  `uom_id` BIGINT NULL COMMENT 'Unit of Measure ID (optional, can be fetched from barcode)',
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX `idx_favorite_id` (`favorite_id`),
  INDEX `idx_product_id` (`product_id`),
  INDEX `idx_product_barcode_id` (`product_barcode_id`),
  FOREIGN KEY (`favorite_id`) REFERENCES `tbl_purchase_favorites`(`favorite_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Product Favorites Items Detail - Generic';
```

**Key Design Decision:** Only essential IDs are stored. When loading a favorite, the system uses the existing product search/help mechanism (same as F2 barcode search) to fetch all current product details including:
- Product name, barcode
- UOM, packing
- Current rates, stock
- All other dynamic product information

This ensures data consistency and eliminates the need to maintain duplicate product information.

## Feature Flow

### 1. Creating a Favorite

**User Flow:**
1. User fills the grid with products (using F2 barcode search as usual)
2. User clicks a "Save as Favorite" button (new button to be added)
3. A modal opens asking for:
   - Favorite Name (required)
   - Description (optional)
   - Module Type (optional, for filtering - e.g., "GRN", "PO", "SO")
4. System saves:
   - Only essential IDs: `product_id`, `product_barcode_id`, `uom_id` (if available)
   - Sequence: `sr_no` (maintains order)
   - User ID, business_id, branch_id for filtering
   - Module type (if specified)

**Data Saved:**
- **Only IDs are stored:** `product_id`, `product_barcode_id`, `uom_id`
- **Sequence:** `sr_no` (maintains order)
- **No product details** (name, barcode string, packing, rates, quantities) - these are fetched dynamically

**Rationale:** Storing only IDs ensures:
- Data consistency (always get current product information)
- Smaller database footprint
- No stale data issues
- Works across different modules with different field requirements

### 2. Loading a Favorite

**User Flow:**
1. User clicks a "Load Favorite" button (new button near barcode field or in toolbar)
2. A dropdown/modal shows list of user's favorites (optionally filtered by module type)
3. User selects a favorite
4. System:
   - Clears current grid (or appends, based on preference)
   - Fetches favorite items (only IDs)
   - For each item:
     - Creates a new row in the grid
     - Uses `product_barcode_id` to trigger the **existing product detail fetch mechanism** (same as F2 search)
     - This automatically populates all product fields (name, barcode, UOM, packing, rates, stock, etc.)
     - User can then adjust quantities/prices as needed

**Implementation Note:** When loading, the system:
- Uses the saved `product_barcode_id` to call the existing product help/barcode fetch function
- This is the **same mechanism** used when pressing F2 in the barcode field
- Ensures current rates, stock, and all dynamic data are loaded
- No need to manually populate fields - the existing system handles it

### 3. Managing Favorites

**User Flow:**
1. User clicks "Manage Favorites" button
2. Modal/Page shows:
   - List of all user's favorites
   - Actions: Edit Name, Delete, View Items
   - Option to edit items within a favorite

## UI/UX Recommendations

### Button Placement Options:

**Option 1: Near Barcode Field (Recommended)**
- Add a "⭐ Favorites" button next to the barcode help button (line 403-406)
- Dropdown shows: "Load Favorite", "Save Current as Favorite", "Manage Favorites"

**Option 2: In Toolbar**
- Add buttons in the toolbar area (around line 366-388) where other action buttons are

**Option 3: Both**
- Quick access button near barcode field
- Full management in toolbar

### Modal Design:
- Use existing modal system (`#kt_modal_xl` or similar)
- Follow existing UI patterns in the codebase
- Include search/filter for favorites list if user has many

## Technical Implementation Details

### 1. Controller Methods Needed

**File:** `app/Http/Controllers/Common/FavoriteController.php` (Generic controller for reuse)

```php
// Get user's favorites list (optionally filtered by module_type)
public function getFavorites(Request $request)

// Get favorite items (returns only IDs)
public function getFavoriteItems($favorite_id)

// Save current grid as favorite
public function saveFavorite(Request $request)

// Update favorite name/description
public function updateFavorite(Request $request, $favorite_id)

// Delete favorite
public function deleteFavorite($favorite_id)
```

**Note:** No `applyFavorite` method needed - JavaScript handles applying favorites by calling the existing product fetch mechanism.

### 2. Model Classes

**File:** `app/Models/TblPurchaseFavorite.php`
```php
class TblPurchaseFavorite extends Model
{
    protected $table = 'tbl_purchase_favorites';
    protected $primaryKey = 'favorite_id';
    
    public function items()
    {
        return $this->hasMany(TblPurchaseFavoriteItem::class, 'favorite_id')
            ->orderBy('sr_no', 'asc');
    }
}
```

**File:** `app/Models/TblPurchaseFavoriteItem.php`
```php
class TblPurchaseFavoriteItem extends Model
{
    protected $table = 'tbl_purchase_favorite_items';
    protected $primaryKey = 'favorite_item_id';
    
    public function favorite()
    {
        return $this->belongsTo(TblPurchaseFavorite::class, 'favorite_id');
    }
    
    public function product()
    {
        return $this->belongsTo(TblPurcProduct::class, 'product_id');
    }
    
    public function barcode()
    {
        return $this->belongsTo(TblPurcProductBarcode::class, 'product_barcode_id');
    }
}
```

### 3. JavaScript Functions Needed

**Location:** Add to `resources/views/purchase/grn/form.blade.php` or separate JS file

```javascript
// Show favorites dropdown/modal
function showFavoritesMenu()

// Load favorite items into grid
function loadFavorite(favoriteId)

// Save current grid as favorite
function saveCurrentAsFavorite()

// Show save favorite modal
function showSaveFavoriteModal()

// Process save favorite form
function processSaveFavorite(formData)

// Show manage favorites modal
function showManageFavorites()
```

### 4. Routes Needed

```php
// In routes/web.php - Generic routes for all modules
Route::get('/common/favorites', 'Common\FavoriteController@getFavorites');
Route::get('/common/favorites/{id}/items', 'Common\FavoriteController@getFavoriteItems');
Route::post('/common/favorites', 'Common\FavoriteController@saveFavorite');
Route::put('/common/favorites/{id}', 'Common\FavoriteController@updateFavorite');
Route::delete('/common/favorites/{id}', 'Common\FavoriteController@deleteFavorite');
```

**Note:** These are generic routes that can be used by GRN, Purchase Orders, Sales Orders, or any other module.

## Data Flow Example

### Saving a Favorite:
1. JavaScript collects all grid rows: `$('.erp_form__grid_body tr')`
2. Extracts only essential IDs from each row:
   - `product_id`
   - `product_barcode_id`
   - `uom_id` (if available)
   - `sr_no` (sequence)
3. Sends AJAX POST to `/common/favorites` with:
   ```json
   {
     "favorite_name": "Weekly Order - Supplier ABC",
     "favorite_description": "Regular weekly items",
     "module_type": "GRN",
     "items": [
       {
         "sr_no": 1,
         "product_id": 123,
         "product_barcode_id": 456,
         "uom_id": 1
       },
       {
         "sr_no": 2,
         "product_id": 124,
         "product_barcode_id": 457,
         "uom_id": 1
       }
       // ... more items (only IDs)
     ]
   }
   ```
4. Controller validates and saves to database (only IDs)
5. Returns success response

### Loading a Favorite:
1. User selects favorite from dropdown
2. JavaScript sends GET to `/common/favorites/{id}/items`
3. Controller returns JSON with items (only IDs):
   ```json
   {
     "favorite_name": "Weekly Order - Supplier ABC",
     "items": [
       {
         "sr_no": 1,
         "product_id": 123,
         "product_barcode_id": 456,
         "uom_id": 1
       },
       // ... more items
     ]
   }
   ```
4. JavaScript loops through items:
   - For each item, creates a new row in grid
   - Sets the `product_barcode_id` in the barcode field
   - **Triggers the existing product fetch mechanism** (same as F2 search)
   - This automatically populates all product details (name, barcode, UOM, packing, rates, stock, etc.)
5. Grid is populated with current product data and user can adjust quantities/prices

**Key Point:** The existing product fetch mechanism handles all the heavy lifting - we just need to trigger it with the `product_barcode_id`.

## Important Considerations

### 1. Data Consistency
- ✅ **Always fetch fresh product data** - Since we only store IDs, product details are always current
- ✅ **No stale data** - Rates, stock, and other dynamic fields are fetched in real-time
- ✅ **Product changes handled** - If product/barcode is deleted, the existing product fetch will handle the error gracefully

### 2. User Context
- Favorites are user-specific (`user_id`)
- Consider business/branch context if products vary by location
- Filter favorites by current `business_id` and `branch_id`
- Optional `module_type` allows filtering favorites by module (GRN, PO, SO, etc.)

### 3. Validation
- Validate that products still exist when loading (handled by existing product fetch)
- Handle deleted/inactive products gracefully (existing mechanism handles this)
- Check if barcode is still valid (existing product fetch validates this)

### 4. Performance
- Index `user_id`, `favorite_id`, `product_id`, `product_barcode_id` for fast queries
- Minimal data storage (only IDs) = faster queries and smaller database
- Consider caching favorite lists if user has many
- Limit number of items per favorite (e.g., max 100)

### 5. Existing Functionality
- ✅ **DO NOT** modify existing barcode search (F2) functionality
- ✅ **DO NOT** modify existing grid add/delete row functions
- ✅ **Reuse existing product detail fetch mechanisms** - This is the key advantage
- Follow existing code patterns and conventions
- The favorite system is a thin layer on top of existing functionality

## Database Table Creation

**Note:** You mentioned you will create the tables yourself. The SQL CREATE statements are provided in the "Database Design" section above. Use those to create the tables manually.

**Key Points:**
- Table names are generic: `tbl_purchase_favorites` and `tbl_purchase_favorite_items`
- Only essential IDs are stored in the items table
- Foreign key with CASCADE DELETE ensures data integrity
- Indexes are included for performance

## Testing Checklist

- [ ] Create favorite with multiple products
- [ ] Load favorite and verify products populate correctly
- [ ] Verify current product data is fetched dynamically (rates, stock, etc.)
- [ ] Test with deleted/inactive products (should handle gracefully)
- [ ] Test user-specific filtering (User A can't see User B's favorites)
- [ ] Test business/branch context filtering
- [ ] Test module_type filtering (if implemented)
- [ ] Test favorite deletion (cascade to items)
- [ ] Test updating favorite name/description
- [ ] Verify existing form functionality still works (F2 search, grid operations)
- [ ] Test with empty grid (should not allow saving)
- [ ] Test with very large favorites (performance - should be fast with minimal data)
- [ ] Verify product fetch mechanism works correctly for all items

## Next Steps

1. **Review and approve** this design
2. **Create database tables** manually using the SQL provided
3. **Create models** (TblPurchaseFavorite, TblPurchaseFavoriteItem)
4. **Create controller** (Common\FavoriteController) with CRUD operations
5. **Add UI buttons** to the form (GRN form initially, then other modules)
6. **Implement JavaScript** functions to save/load favorites
7. **Test thoroughly** before deployment

## Questions to Consider

1. Should favorites be shareable between users? (Currently user-specific)
2. Should there be a limit on number of favorites per user?
3. Should there be a limit on items per favorite?
4. Should we use `module_type` field for filtering? (e.g., separate favorites for GRN vs PO)
5. Should favorites be editable after creation? (Currently yes - name/description)
6. Should we show a preview of items when hovering over favorite name?
7. Should we allow reordering items within a favorite?

## Advantages of This Design

1. **Generic & Reusable:** Can be used in GRN, Purchase Orders, Sales Orders, etc.
2. **Minimal Storage:** Only stores IDs, not duplicate product information
3. **Always Current:** Product details fetched dynamically, no stale data
4. **Leverages Existing Code:** Uses existing product fetch mechanisms
5. **Simple Implementation:** Thin layer on top of existing functionality
6. **Easy Maintenance:** No need to sync product details in favorites table

---

**Note:** This guide provides the foundation. Actual implementation should follow existing codebase patterns and conventions. Always test in a development environment first.

