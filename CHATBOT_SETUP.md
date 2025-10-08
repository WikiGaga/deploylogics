# Chatbot Setup Guide

## ✅ What's Done

1. **Category Tabs** - Sales & General tabs added
2. **Excel Report Generation** - Generates .xlsx files instead of HTML
3. **Smart Response** - Bot answers simple queries in chat, generates Excel for detailed reports
4. **Schema Caching** - Stored server-wide for 30 days (not per user)
5. **Clean Code** - Removed unnecessary comments
6. **User-Friendly** - No SQL/Oracle mentions to users

## 🚀 Quick Setup Steps

### 1. Create Storage Directory
```bash
mkdir storage/app/public/reports
chmod 755 storage/app/public/reports
```

### 2. Create Storage Symlink
```bash
php artisan storage:link
```

This creates `public/storage` → `storage/app/public`

### 3. Verify Excel Library
Check if PhpSpreadsheet is installed:
```bash
composer require phpoffice/phpspreadsheet
```

### 4. Test Category Tabs

Open your app and you should see:
```
┌────────────────────────────────┐
│ [💰 Sales] [🔍 General]        │
└────────────────────────────────┘
```

If tabs don't show, check browser console for errors.

### 5. Test Excel Generation

Try these queries:

**Simple Query** (answers in chat):
- "What is total sales today?"
- "How many orders do we have?"

**Report Query** (generates Excel):
- "Show me all orders from last month"
- "List of sales this week"
- "Export order details"

## 🔧 Clear Schema Cache

When you update database structure:

**Option 1 - Via Browser:**
```
Visit: /chatbot/clear-schema-cache (POST request)
```

**Option 2 - Via Code:**
```php
Cache::forget('chatbot_schema_sales');
Cache::forget('chatbot_schema_general');
```

## 📊 How It Works

```
User: "What is total sales?"
Bot: "Total sales today is $5,240" (Direct answer in chat)

User: "Show me all orders from last week"
Bot: "Here's your report!"
     [Download Excel Report] (Green button with Excel icon)
```

## 🎯 Schema Storage

- **Location**: Laravel Cache (server-wide, not per user)
- **Duration**: 30 days
- **Size**: ~5-10 KB per category
- **All users see same schema** (no duplicate storage)

## ⚠️ Troubleshooting

### Category Tabs Not Showing

1. Open browser console (F12)
2. Check for JavaScript errors
3. Verify `/chatbot/categories` endpoint works:
```
Visit: http://your-domain.com/chatbot/categories
Should return JSON with categories
```

### Excel Download Not Working

1. Check `storage/app/public/reports` exists
2. Verify symlink: `ls -la public/storage` (should point to `../../storage/app/public`)
3. Check Laravel logs: `storage/logs/laravel.log`

### Schema Not Updating

Clear cache:
```bash
php artisan cache:clear
```

Or via API:
```bash
curl -X POST http://your-domain.com/chatbot/clear-schema-cache
```

## 📝 Example Queries

### Sales Category

**Simple (Chat Answer):**
- "Total orders today"
- "Sales count this month"
- "How many customers?"

**Report (Excel Download):**
- "Show all orders from January"
- "List top customers"
- "Export sales details"
- "Download order report"

## 🔐 Security

- Only SELECT queries allowed
- DROP, DELETE, UPDATE, INSERT blocked
- Category-based table restrictions
- Query validation before execution

## 📂 File Changes

Modified Files:
- `app/Http/Controllers/ChatbotController.php`
- `routes/web.php`
- `public/js/chatbot.js`
- `public/css/chatbot.css`
- `resources/views/elements/chatbot.blade.php`

## 🎨 Category Tabs Location

Category tabs appear **below the header** and **above the chat messages**:

```
┌─────────────────────────────────┐
│  Report Assistant       [X]      │  ← Header
├─────────────────────────────────┤
│  [💰 Sales] [🔍 General]       │  ← Category Tabs (HERE!)
├─────────────────────────────────┤
│                                  │
│  Chat messages appear here       │
│                                  │
└─────────────────────────────────┘
```

## 📞 Support

Check:
1. `storage/logs/laravel.log` - Laravel errors
2. Browser console (F12) - JavaScript errors
3. `/chatbot/preview-schema/sales` - View schema structure

---

**Version**: 1.0  
**Last Updated**: October 2025
