# Stock Distribution (SD) — Manual Setup

## Menu (already done locally)

| Field | Value |
|-------|--------|
| Menu id | `356` |
| Name | Stock Distribution |
| Link | `/stock/stock-distribution` |
| Form URL | `/stock/stock-distribution/form` |

## Oracle columns (run if not already applied)

```sql
ALTER TABLE tbl_inve_stock_dtl ADD stock_dtl_branch_to_id NUMBER;
ALTER TABLE tbl_inve_stock_dtl ADD stock_dtl_store_to_id NUMBER;
ALTER TABLE tbl_inve_stock_dtl ADD stock_dtl_branch_from_id NUMBER;
ALTER TABLE tbl_inve_stock_dtl ADD stock_dtl_store_from_id NUMBER;
```

## Staging

`config/staging.php` maps menu `356` → `stock_id` (included in codebase).

## Deferred (phase 2)

- Stock qty impact (`vw_purc_stock_dtl` registration for `sd`)
- Stock Receiving workflow for SD
- Accounting vouchers for SD

## Document type

- `stock_code_type`: `sd`
- Code prefix: `SD` (e.g. `SD-0000001`)
