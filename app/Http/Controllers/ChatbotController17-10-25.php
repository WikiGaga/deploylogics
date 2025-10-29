<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ChatbotController extends Controller
{
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
        $this->baseUrl = 'https://api.openai.com/v1';
    }

    public function processMessage(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'message' => 'required|string|max:500',
                'conversation_id' => 'nullable|string',
                'category' => 'nullable|string|in:sales,purchases,inventory,accounts'
            ]);

            $message = $request->input('message');
            $category = $request->input('category', 'sales');
            $conversationId = $request->input('conversation_id', uniqid());
            $user = Auth::user();

            $this->logConversation($user, $message, $conversationId, 'user', $category);

            $response = $this->generateAIResponse($message, $user, $category, $conversationId);

            $this->logConversation($user, $response, $conversationId, 'bot', $category);

            return response()->json([
                'success' => true,
                'response' => $response,
                'conversation_id' => $conversationId,
                'category' => $category,
                'timestamp' => now()->format('H:i')
            ]);

        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'response' => 'Sorry, I encountered an error while processing your request. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    private function generateAIResponse(string $message, $user, string $category = 'sales', string $conversationId = null): string
    {
        try {
            $systemPrompt = $this->getSystemPromptForCategory($category);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $message],
                ],
                'max_tokens' => 1000,
                'temperature' => 0.3,
            ])->json();

            $aiResponse = $response['choices'][0]['message']['content'] ?? 'Sorry, I could not process your request.';

            if (strpos($aiResponse, 'SIMPLE_QUERY:') !== false) {
                return $this->handleSimpleQuery($aiResponse);
            }

            if (strpos($aiResponse, 'GENERATE_REPORT:') !== false) {
                return $this->handleExcelReportGeneration($message, $aiResponse, $user, $category, $conversationId);
            }

            return $this->cleanResponse($aiResponse);

        } catch (\Exception $e) {
            Log::error('OpenAI API error: ' . $e->getMessage());
            return 'Sorry, I encountered an error. Please try again.';
        }
    }

    private function handleSimpleQuery(string $aiResponse): string
    {
        try {
            $parts = explode('SIMPLE_QUERY:', $aiResponse);
            if (!isset($parts[1])) {
                return $this->cleanResponse($aiResponse);
            }

            $query = $this->cleanSQLQuery(trim($parts[1]));
            $naturalText = trim($parts[0]);

            $this->validateQuery($query);
            $result = DB::select($query);

            if (empty($result)) {
                return "No data found.";
            }

            $firstRow = (array) $result[0];
            $value = reset($firstRow);

            if ($naturalText) {
                return $naturalText . ": " . $value;
            }

            return "Result: " . $value;

        } catch (\Exception $e) {
            Log::error('Simple query error: ' . $e->getMessage());
            return "I couldn't fetch that information. Please try again.";
        }
    }

    private function cleanResponse(string $response): string
    {
        $response = preg_replace('/GENERATE_REPORT:.*$/s', '', $response);
        $response = preg_replace('/SIMPLE_QUERY:.*$/s', '', $response);
        $response = preg_replace('/```sql.*?```/s', '', $response);
        $response = preg_replace('/SELECT.*?FROM.*?;/si', '', $response);
        return trim($response);
    }

    private function getCategorySchemas(): array
    {
        return [
            'sales' => [
                'name' => 'Sales',
                'description' => 'Sales orders report with comprehensive order information',
                'tables' => ['ORDER_REPORT_VIEW'],
            ],
            'purchases' => [
                'name' => 'Purchases',
                'description' => 'Purchase orders report with comprehensive purchase information',
                'tables' => ['VW_PURC_PURCHASE_ORDER'],
            ],
            'inventory' => [
                'name' => 'Inventory',
                'description' => 'Stock inventory report with product quantities',
                'tables' => ['VW_PURC_GRN'],
            ],
            'accounts' => [
                'name' => 'Accounts',
                'description' => 'Accounting reports with financial information',
                'tables' => ['VW_ACC_VOUCHER'],
            ]
        ];
    }

    private function getSystemPromptForCategory(string $category): string
    {
        $cacheKey = "chatbot_schema_{$category}";

        $schema = Cache::remember($cacheKey, now()->addDays(30), function() use ($category) {
            return $this->buildSchemaForCategory($category);
        });

        $categoryInfo = $this->getCategorySchemas()[$category] ?? ['name' => 'General', 'description' => 'General queries'];

        $additionalInstructions = '';
        if ($category === 'sales') {
            $additionalInstructions = "\n\nSALES REPORT COLUMNS (ORDER_REPORT_VIEW):
Available columns:
- ORDER_SERIAL (Order ID)
- CREATED_AT (Order Date) - Use for date filtering
- CUSTOMER_NAME, CAR_NUMBER, PHONE (Customer Info)
- ORDER_TYPE, ORDER_STATUS, PAYMENT_STATUS
- GROSS_AMOUNT, RESTAURANT_DISCOUNT_AMOUNT (Discount)
- DELIVERY_CHARGE, TOTAL_TAX_AMOUNT (VAT)
- ORDER_AMOUNT (Net Amount)
- CASH_PAID (Cash Amount), CARD_PAID (Visa Amount)

Date Filtering:
- For date ranges, use WHERE CREATED_AT >= TO_DATE('start_date', 'YYYY-MM-DD') AND CREATED_AT <= TO_DATE('end_date', 'YYYY-MM-DD')
- For relative dates: 'last 7 days' = WHERE CREATED_AT >= SYSDATE - 7
- For today: WHERE TRUNC(CREATED_AT) = TRUNC(SYSDATE)

Column Selection:
- If user specifies columns, SELECT only those columns
- If user doesn't specify columns, SELECT * to get all columns
- Match user's natural language to exact column names
- Use Headings for the columns instead of field names";
        }

        if ($category === 'purchases') {
            $additionalInstructions = "\n\nPURCHASE REPORT COLUMNS (VW_PURC_PURCHASE_ORDER):
Available columns:
- PURCHASE_ORDER_ENTRY_DATE (Purchase Date) - Use for date filtering
- PURCHASE_ORDER_CODE (Purchase Order Code)
- SUPPLIER_NAME (Supplier Name)
- PRODUCT_BARCODE_BARCODE (Barcode)
- PRODUCT_NAME (Product Name)
- UOM_NAME (Unit of Measure)
- PURCHASE_ORDER_DTLPACKING (Packing)
- PURCHASE_ORDER_DTLQUANTITY (Quantity)
- PURCHASE_ORDER_DTLRATE (Rate)
- PURCHASE_ORDER_DTLAMOUNT (Amount)
- PURCHASE_ORDER_DTLDISC_AMOUNT (Discount Amount)
- PURCHASE_ORDER_DTLVAT_AMOUNT (VAT Amount)
- PURCHASE_ORDER_DTLTOTAL_AMOUNT (Net Amount)

Date Filtering:
- For date ranges, use WHERE PURCHASE_ORDER_ENTRY_DATE >= TO_DATE('start_date', 'YYYY-MM-DD') AND PURCHASE_ORDER_ENTRY_DATE <= TO_DATE('end_date', 'YYYY-MM-DD')
- For relative dates: 'last 7 days' = WHERE PURCHASE_ORDER_ENTRY_DATE >= SYSDATE - 7
- For today: WHERE TRUNC(PURCHASE_ORDER_ENTRY_DATE) = TRUNC(SYSDATE)

Column Selection:
- If user specifies columns, SELECT only those columns
- If user doesn't specify columns, SELECT * to get all columns
- Match user's natural language to exact column names
- Use Headings for the columns instead of field names";
        }

        if ($category === 'inventory') {
            $additionalInstructions = "\n\nINVENTORY REPORT COLUMNS (VW_PURC_GRN):
Available columns:
- PRODUCT_ID (Product ID)
- PRODUCT_NAME (Product Name)
- PRODUCT_BARCODE_BARCODE (Barcode)
- TBL_PURC_GRN_DTL_QUANTITY (Quantity)
- TBL_PURC_GRN_DTL_RATE (Rate)
- PO_DATE (Purchase Order Date) - Use for date filtering

Date Filtering:
- For date ranges, use WHERE PO_DATE >= TO_DATE('start_date', 'YYYY-MM-DD') AND PO_DATE <= TO_DATE('end_date', 'YYYY-MM-DD')
- For relative dates: 'last 7 days' = WHERE PO_DATE >= SYSDATE - 7
- For today: WHERE TRUNC(PO_DATE) = TRUNC(SYSDATE)

Column Selection:
- If user specifies columns, SELECT only those columns
- If user doesn't specify columns, SELECT * to get all columns
- Match user's natural language to exact column names
- Use Headings for the columns instead of field names";
        }

        return "You are a business data assistant for {$categoryInfo['name']}.

DATABASE TABLES:
{$schema}
{$additionalInstructions}

RESPONSE FORMAT:

1. Simple questions (total, count, sum):
   Format: Natural text SIMPLE_QUERY: SELECT query
   Example: 'Total sales today SIMPLE_QUERY: SELECT NVL(SUM(NET_AMOUNT), 0) FROM ORDER_REPORT_VIEW WHERE TRUNC(ORDER_DATE) = TRUNC(SYSDATE)'

2. Detailed lists/reports:
   Format: GENERATE_REPORT: SELECT query
   Example: 'GENERATE_REPORT: SELECT * FROM ORDER_REPORT_VIEW WHERE ORDER_DATE >= SYSDATE - 30 ORDER BY ORDER_DATE DESC'

RULES:
- Use Oracle syntax (TRUNC, NVL, TO_DATE, SYSDATE)
- For sales, always use ORDER_REPORT_VIEW
- For purchases, always use VW_PURC_PURCHASE_ORDER
- For inventory, always use VW_PURC_GRN
- DATE FILTERING: When users mention time periods, automatically add appropriate WHERE clauses:
  * 'last 7 days' = WHERE date_column >= SYSDATE - 7
  * 'today' = WHERE TRUNC(date_column) = TRUNC(SYSDATE)
  * 'yesterday' = WHERE TRUNC(date_column) = TRUNC(SYSDATE) - 1
  * 'this month' = WHERE date_column >= TRUNC(SYSDATE, 'MM')
  * 'last month' = WHERE date_column >= TRUNC(ADD_MONTHS(SYSDATE, -1), 'MM') AND date_column < TRUNC(SYSDATE, 'MM')
- Never mention SQL or technical terms
- Query executes automatically
- No explanations needed";
    }

    private function buildSchemaForCategory(string $category): string
    {
        $categories = $this->getCategorySchemas();

        if (!isset($categories[$category])) {
            $category = 'sales';
        }

        $categoryData = $categories[$category];

        if ($category === 'sales') {
            return $this->buildFullDatabaseSchema();
        }

        return $this->buildCompactSchema($categoryData['tables']);
    }

    /**
     * Build compact schema format for specified tables
     */
    private function buildCompactSchema(array $tables): string
    {
        $schema = "";

        try {
            foreach ($tables as $tableName) {
                // Get columns for each table
                $columns = DB::select("
                    SELECT column_name, data_type, nullable
                    FROM user_tab_columns
                    WHERE table_name = ?
                    ORDER BY column_id
                ", [strtoupper($tableName)]);

                if (empty($columns)) {
                    continue;
                }

                $schema .= strtoupper($tableName) . "(\n";

                $columnDefs = [];
                foreach ($columns as $col) {
                    $nullable = $col->nullable === 'N' ? ' NOT NULL' : '';
                    $columnDefs[] = "  {$col->column_name} {$col->data_type}{$nullable}";
                }

                $schema .= implode(",\n", $columnDefs);
                $schema .= "\n)\n\n";
            }

            // Get foreign key relationships
            $schema .= "RELATIONSHIPS:\n";
            $tableList = "'" . implode("','", array_map('strtoupper', $tables)) . "'";

            $constraints = DB::select("
                SELECT
                    a.table_name child_table,
                    a.column_name child_column,
                    c_pk.table_name parent_table,
                    b.column_name parent_column
                FROM user_cons_columns a
                JOIN user_constraints c ON a.constraint_name = c.constraint_name
                JOIN user_constraints c_pk ON c.r_constraint_name = c_pk.constraint_name
                JOIN user_cons_columns b ON c_pk.constraint_name = b.constraint_name
                WHERE c.constraint_type = 'R'
                AND a.table_name IN ({$tableList})
            ");

            foreach ($constraints as $fk) {
                $schema .= "- {$fk->child_table}.{$fk->child_column} -> {$fk->parent_table}.{$fk->parent_column}\n";
            }

            if (empty($constraints)) {
                $schema .= "- No foreign key relationships defined\n";
            }

        } catch (\Exception $e) {
            Log::error('Error building schema: ' . $e->getMessage());
            $schema = "Error fetching schema. Using basic structure.\n";
            $schema .= implode(", ", $tables);
        }

        return $schema;
    }

    private function buildFullDatabaseSchema(): string
    {
        $commonTables = ['ORDER_REPORT_VIEW'];
        return $this->buildCompactSchema($commonTables);
    }

    private function handleExcelReportGeneration(string $message, string $aiResponse, $user, string $category, string $conversationId = null): string
    {
        try {
            $reportData = $this->extractReportData($message, $aiResponse, $category);
            $query = $reportData['query'];

            $this->validateQuery($query);
            $data = DB::select($query);

            if (empty($data)) {
                return "No data found for your request.";
            }

            $filename = 'report_' . time() . '.xlsx';
            $filePath = storage_path('app/public/reports/' . $filename);

            if (!file_exists(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }


            if ($category === 'purchases') {
                $this->generatePurchaseOrderExcelFile($data, $filePath);
            } elseif ($category === 'inventory') {
                $this->generateInventoryExcelFile($data, $filePath);
            } else {
                $this->generateExcelFile($data, $filePath);
            }

            $this->storeReportMetadata($filename, $conversationId, $user->id);

            $downloadUrl = url('storage/reports/' . $filename);

            return "📊 Your report is ready!\n[DOWNLOAD_EXCEL:" . $downloadUrl . "]";

        } catch (\Exception $e) {
            Log::error('Report generation error: ' . $e->getMessage());
            return "I encountered an error while generating the report. Please try again.";
        }
    }

    private function generateExcelFile($data, $filePath): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $dataArray = array_map(function($row) {
            return (array) $row;
        }, $data);

        if (empty($dataArray)) {
            return;
        }

        $headers = array_keys($dataArray[0]);
        $sheet->fromArray($headers, NULL, 'A1');

        $rowNum = 2;
        foreach ($dataArray as $row) {
            $sheet->fromArray(array_values($row), NULL, 'A' . $rowNum);
            $rowNum++;
        }

        $totalsRow = $this->calculateTotals($dataArray, $headers);
        if (!empty($totalsRow)) {
            $sheet->fromArray($totalsRow, NULL, 'A' . $rowNum);
            $totalStyle = $sheet->getStyle('A' . $rowNum . ':' . $sheet->getHighestColumn() . $rowNum);
            $totalStyle->getFont()->setBold(true);
            $totalStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                       ->getStartColor()->setRGB('F3F6F9');
        }

        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $headerStyle = $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('667EEA');
        $headerStyle->getFont()->getColor()->setRGB('FFFFFF');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filePath);
    }

    private function generatePurchaseOrderExcelFile($data, $filePath): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Convert data to array format
        $dataArray = array_map(function($row) {
            return (array) $row;
        }, $data);

        if (empty($dataArray)) {
            return;
        }

        $groupedData = [];
        foreach ($dataArray as $row) {
            $date = date('d-m-Y', strtotime($row['PURCHASE_ORDER_ENTRY_DATE'] ?? $row['purchase_order_entry_date']));
            $poCode = $row['PURCHASE_ORDER_CODE'] ?? $row['purchase_order_code'];
            $supplierName = $row['SUPPLIER_NAME'] ?? $row['supplier_name'];

            $groupedData[$date][$poCode . ' ' . $supplierName][] = $row;
        }

        $headers = ['Barcode', 'Product Name', 'UOM', 'Packing', 'Quantity', 'Rate', 'Amount', 'Disc Amount', 'Vat Amount'];
        $sheet->fromArray($headers, NULL, 'A1');

        $headerStyle = $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('D3D3D3');
        $headerStyle->getFont()->getColor()->setRGB('000000');

        $currentRow = 2;

        foreach ($groupedData as $date => $purchaseOrders) {

            $sheet->setCellValue('A' . $currentRow, $date);
            $sheet->mergeCells('A' . $currentRow . ':I' . $currentRow);
            $dateStyle = $sheet->getStyle('A' . $currentRow);
            $dateStyle->getFont()->setBold(true);
            $dateStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                      ->getStartColor()->setRGB('F0F0F0');
            $currentRow++;

            foreach ($purchaseOrders as $poHeader => $items) {

                $sheet->setCellValue('A' . $currentRow, $poHeader);
                $sheet->mergeCells('A' . $currentRow . ':I' . $currentRow);
                $poStyle = $sheet->getStyle('A' . $currentRow);
                $poStyle->getFont()->setBold(true);
                $poStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('FFFACD'); // Light yellow/gold
                $currentRow++;

                $poTotals = [
                    'quantity' => 0,
                    'amount' => 0,
                    'disc_amount' => 0,
                    'vat_amount' => 0
                ];

                foreach ($items as $item) {
                    $rowData = [
                        $item['PRODUCT_BARCODE_BARCODE'] ?? $item['product_barcode_barcode'] ?? '',
                        $item['PRODUCT_NAME'] ?? $item['product_name'] ?? '',
                        $item['UOM_NAME'] ?? $item['uom_name'] ?? '',
                        $item['PURCHASE_ORDER_DTLPACKING'] ?? $item['purchase_order_dtlpacking'] ?? '',
                        $item['PURCHASE_ORDER_DTLQUANTITY'] ?? $item['purchase_order_dtlquantity'] ?? '',
                        number_format($item['PURCHASE_ORDER_DTLRATE'] ?? $item['purchase_order_dtlrate'] ?? 0, 3),
                        number_format($item['PURCHASE_ORDER_DTLAMOUNT'] ?? $item['purchase_order_dtlamount'] ?? 0, 3),
                        number_format($item['PURCHASE_ORDER_DTLDISC_AMOUNT'] ?? $item['purchase_order_dtldisc_amount'] ?? 0, 3),
                        number_format($item['PURCHASE_ORDER_DTLVAT_AMOUNT'] ?? $item['purchase_order_dtlvat_amount'] ?? 0, 3)
                    ];

                    $sheet->fromArray($rowData, NULL, 'A' . $currentRow);

                    $itemStyle = $sheet->getStyle('A' . $currentRow . ':I' . $currentRow);
                    $itemStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $poTotals['quantity'] += (float)($item['PURCHASE_ORDER_DTLQUANTITY'] ?? $item['purchase_order_dtlquantity'] ?? 0);
                    $poTotals['amount'] += (float)($item['PURCHASE_ORDER_DTLAMOUNT'] ?? $item['purchase_order_dtlamount'] ?? 0);
                    $poTotals['disc_amount'] += (float)($item['PURCHASE_ORDER_DTLDISC_AMOUNT'] ?? $item['purchase_order_dtldisc_amount'] ?? 0);
                    $poTotals['vat_amount'] += (float)($item['PURCHASE_ORDER_DTLVAT_AMOUNT'] ?? $item['purchase_order_dtlvat_amount'] ?? 0);

                    $currentRow++;
                }

                $totalRowData = [
                    'Total:',
                    '',
                    '',
                    '',
                    number_format($poTotals['quantity'], 0),
                    '',
                    number_format($poTotals['amount'], 3),
                    number_format($poTotals['disc_amount'], 3),
                    number_format($poTotals['vat_amount'], 3)
                ];

                $sheet->fromArray($totalRowData, NULL, 'A' . $currentRow);
                $totalStyle = $sheet->getStyle('A' . $currentRow . ':I' . $currentRow);
                $totalStyle->getFont()->setBold(true);
                $totalStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                           ->getStartColor()->setRGB('F3F6F9');
                $totalStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $currentRow++;
            }
        }

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filePath);
    }

    private function generateInventoryExcelFile($data, $filePath): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $dataArray = array_map(function($row) {
            return (array) $row;
        }, $data);

        if (empty($dataArray)) {
            return;
        }

        $headers = ['Sr. No', 'BarCode', 'Product Name', 'Quantity', 'Rate'];
        $sheet->fromArray($headers, NULL, 'A1');

        $headerStyle = $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('D3D3D3');
        $headerStyle->getFont()->getColor()->setRGB('000000');

        $currentRow = 2;
        $totalQuantity = 0;
        $totalRate = 0;
        $serialNumber = 1;

        foreach ($dataArray as $row) {
            $barcode = $row['PRODUCT_BARCODE_BARCODE'] ?? $row['product_barcode_barcode'] ?? '';
            $productName = $row['PRODUCT_NAME'] ?? $row['product_name'] ?? '';
            $quantity = $row['TBL_PURC_GRN_DTL_QUANTITY'] ?? $row['tbl_purc_grn_dtl_quantity'] ?? 0;
            $rate = $row['TBL_PURC_GRN_DTL_RATE'] ?? $row['tbl_purc_grn_dtl_rate'] ?? 0;

            // Format quantity to 3 decimal places if it's a decimal, otherwise show as integer
            $formattedQuantity = (float)$quantity;
            if ($formattedQuantity == (int)$formattedQuantity) {
                $formattedQuantity = (int)$formattedQuantity;
            } else {
                $formattedQuantity = number_format($formattedQuantity, 3);
            }

            // Format rate to 3 decimal places
            $formattedRate = number_format((float)$rate, 3);

            $rowData = [
                $serialNumber,
                $barcode,
                strtoupper($productName),
                $formattedQuantity,
                $formattedRate
            ];

            $sheet->fromArray($rowData, NULL, 'A' . $currentRow);

            $itemStyle = $sheet->getStyle('A' . $currentRow . ':E' . $currentRow);
            $itemStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            $totalQuantity += (float)$quantity;
            $totalRate += (float)$rate;

            $currentRow++;
            $serialNumber++;
        }

        $totalRowData = [
            '',
            '',
            'Total:',
            number_format($totalQuantity, 3),
            number_format($totalRate, 3)
        ];

        $sheet->fromArray($totalRowData, NULL, 'A' . $currentRow);
        $totalStyle = $sheet->getStyle('A' . $currentRow . ':E' . $currentRow);
        $totalStyle->getFont()->setBold(true);
        $totalStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                   ->getStartColor()->setRGB('F3F6F9');
        $totalStyle->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filePath);
    }

    private function calculateTotals(array $dataArray, array $headers): array
    {
        $numericColumns = [
            'GROSS_AMOUNT', 'RESTAURANT_DISCOUNT_AMOUNT', 'DELIVERY_CHARGE',
            'TOTAL_TAX_AMOUNT', 'ORDER_AMOUNT', 'CASH_PAID', 'CARD_PAID',
            'PURCHASE_ORDER_DTLQUANTITY', 'PURCHASE_ORDER_DTLRATE', 'PURCHASE_ORDER_DTLAMOUNT',
            'PURCHASE_ORDER_DTLDISC_AMOUNT', 'PURCHASE_ORDER_DTLVAT_AMOUNT', 'PURCHASE_ORDER_DTLTOTAL_AMOUNT',
            'STOCK_QTY', 'PRODUCT_QTY', 'TBL_PURC_GRN_DTL_QUANTITY', 'TBL_PURC_GRN_DTL_RATE'
        ];

        $totals = [];
        $hasNumericData = false;

        foreach ($headers as $index => $header) {
            $upperHeader = strtoupper($header);

            if (in_array($upperHeader, $numericColumns)) {
                $sum = 0;
                foreach ($dataArray as $row) {
                    $value = array_values($row)[$index];
                    if (is_numeric($value)) {
                        $sum += $value;
                        $hasNumericData = true;
                    }
                }
                $totals[] = number_format($sum, 3);
            } else {
                $totals[] = ($index === 0) ? 'TOTAL' : '';
            }
        }

        return $hasNumericData ? $totals : [];
    }

    private function extractReportData(string $message, string $aiResponse, string $category): array
    {
        $reportData = [
            'title' => ucfirst($category) . ' Report',
            'description' => $message,
            'query' => '',
            'type' => $category
        ];

        if (strpos($aiResponse, 'GENERATE_REPORT:') !== false) {
            $parts = explode('GENERATE_REPORT:', $aiResponse);
            if (isset($parts[1])) {
                $query = trim($parts[1]);
                $query = $this->cleanSQLQuery($query);
                $reportData['query'] = $query;
            }
        }
        if (empty($reportData['query'])) {
            $reportData['query'] = $this->generateBasicQuery($category);
        }

        return $reportData;
    }

    private function cleanSQLQuery(string $text): string
    {
        $text = preg_replace('/```sql\s*/i', '', $text);
        $text = preg_replace('/```\s*$/i', '', $text);

        if (preg_match('/^(.*?);/s', $text, $matches)) {
            return trim($matches[1]);
        }

        return trim($text);
    }

    private function generateBasicQuery(string $category): string
    {
        switch ($category) {
            case 'sales':
                return "SELECT * FROM ORDER_REPORT_VIEW WHERE CREATED_AT >= SYSDATE - 7 AND ROWNUM <= 500";
            case 'purchases':
                return "SELECT * FROM VW_PURC_PURCHASE_ORDER WHERE PURCHASE_ORDER_ENTRY_DATE >= SYSDATE - 7 AND ROWNUM <= 500";
            case 'inventory':
                return "SELECT PRODUCT_ID, PRODUCT_NAME, PRODUCT_BARCODE_BARCODE, TBL_PURC_GRN_DTL_QUANTITY, TBL_PURC_GRN_DTL_RATE, PO_DATE FROM VW_PURC_GRN WHERE PO_DATE >= SYSDATE - 7 AND ROWNUM <= 500 ORDER BY PRODUCT_NAME";
            default:
                return "SELECT 'Report Generated' as status, SYSDATE as generated_at FROM dual";
        }
    }

    private function createReport(array $reportData, $user): string
    {
        $reportId = 'report_' . time() . '_' . rand(1000, 9999);

        DB::table('generated_reports')->insert([
            'report_id' => $reportId,
            'user_id' => $user->id,
            'title' => $reportData['title'],
            'description' => $reportData['description'],
            'query' => $reportData['query'],
            'type' => $reportData['type'],
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return $reportId;
    }

    public function viewReport(Request $request, $reportId): JsonResponse
    {
        try {
            $user = Auth::user();

            $report = DB::table('generated_reports')
                ->where('report_id', $reportId)
                ->where('user_id', $user->id)
                ->first();

            if (!$report) {
                return response()->json([
                    'success' => false,
                    'message' => 'Report not found'
                ], 404);
            }

            $data = $this->executeQuery((string) $report->query);

            DB::table('generated_reports')
                ->where('report_id', $reportId)
                ->update([
                    'status' => 'completed',
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'report' => [
                    'id' => $report->report_id,
                    'title' => $report->title,
                    'description' => $report->description,
                    'type' => $report->type,
                    'data' => $data,
                    'generated_at' => $report->created_at
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Report viewing error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error loading report: ' . $e->getMessage()
            ], 500);
        }
    }

    private function executeQuery(string $query): array
    {
        try {
            // Validate query for security
            $this->validateQuery($query);

            $results = DB::select($query);
            return array_map(function($row) {
                return (array) $row;
            }, $results);
        } catch (\Exception $e) {
            Log::error('Query execution error: ' . $e->getMessage());
            return [
                ['error' => 'Query execution failed', 'message' => $e->getMessage()]
            ];
        }
    }

    /**
     * Validate SQL query for security
     * Only allows SELECT statements
     */
    private function validateQuery(string $query): void
    {
        $query = strtoupper(trim($query));

        $dangerousKeywords = ['DROP', 'DELETE', 'UPDATE', 'INSERT', 'TRUNCATE', 'ALTER', 'CREATE', 'GRANT', 'REVOKE'];

        foreach ($dangerousKeywords as $keyword) {
            if (preg_match('/\b' . $keyword . '\b/', $query)) {
                throw new \Exception('Query contains dangerous operations');
            }
        }

        if (strpos($query, 'SELECT') !== 0) {
            throw new \Exception('Only SELECT queries allowed');
        }
    }

    private function logConversation($user, string $message, string $conversationId, string $sender = 'user', string $category = 'sales'): void
    {
        try {
            DB::table('chatbot_conversations')->insert([
                'user_id' => $user->id,
                'conversation_id' => $conversationId,
                'message' => $message,
                'sender' => $sender,
                'category' => $category,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::warning('Could not log chatbot conversation: ' . $e->getMessage());
        }
    }

    public function getConversationHistory(Request $request): JsonResponse
    {
        try {
            $conversationId = $request->input('conversation_id');
            $user = Auth::user();

            $conversations = DB::table('chatbot_conversations')
                ->where('user_id', $user->id)
                ->when($conversationId, function ($query, $conversationId) {
                    return $query->where('conversation_id', $conversationId);
                })
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'conversations' => $conversations
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching conversation history: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not fetch conversation history'
            ], 500);
        }
    }

    public function generateReport(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'report_type' => 'required|string',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'filters' => 'nullable|array'
            ]);

            $reportType = $request->input('report_type');
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $filters = $request->input('filters', []);

            $reportData = $this->processReportGeneration($reportType, $dateFrom, $dateTo, $filters);

            return response()->json([
                'success' => true,
                'report_data' => $reportData,
                'message' => 'Report generated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating report: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not generate report: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processReportGeneration(string $reportType, ?string $dateFrom, ?string $dateTo, array $filters): array
    {

        return [
            'report_type' => $reportType,
            'date_range' => [
                'from' => $dateFrom,
                'to' => $dateTo
            ],
            'filters' => $filters,
            'data' => [],
            'summary' => 'Report generation logic will be implemented here',
            'generated_at' => now()->toISOString()
        ];
    }

    public function getAnalytics(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();

            $totalConversations = DB::table('chatbot_conversations')
                ->where('user_id', $user->id)
                ->count();

            $todayConversations = DB::table('chatbot_conversations')
                ->where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->count();

            $popularTopics = DB::table('chatbot_conversations')
                ->where('user_id', $user->id)
                ->where('sender', 'user')
                ->selectRaw('message, COUNT(*) as count')
                ->groupBy('message')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'success' => true,
                'analytics' => [
                    'total_conversations' => $totalConversations,
                    'today_conversations' => $todayConversations,
                    'popular_topics' => $popularTopics
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching chatbot analytics: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not fetch analytics'
            ], 500);
        }
    }

    /**
     * Clear cached database schemas
     * Use this after database structure changes
     */
    public function clearSchemaCache(Request $request): JsonResponse
    {
        try {
            $categories = array_keys($this->getCategorySchemas());
            $clearedCategories = [];

            foreach ($categories as $category) {
                $cacheKey = "chatbot_schema_{$category}";
                Cache::forget($cacheKey);
                $clearedCategories[] = $category;
            }

            return response()->json([
                'success' => true,
                'message' => 'Schema cache cleared successfully',
                'cleared_categories' => $clearedCategories,
                'note' => 'New schema will be fetched on next query'
            ]);

        } catch (\Exception $e) {
            Log::error('Error clearing schema cache: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not clear schema cache'
            ], 500);
        }
    }

    /**
     * Get available categories for chatbot
     */
    public function getCategories(Request $request): JsonResponse
    {
        try {
            $categories = $this->getCategorySchemas();

            $formattedCategories = [];
            foreach ($categories as $key => $data) {
                $formattedCategories[] = [
                    'id' => $key,
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'icon' => $this->getCategoryIcon($key)
                ];
            }

            return response()->json([
                'success' => true,
                'categories' => $formattedCategories
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching categories: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not fetch categories'
            ], 500);
        }
    }

    private function getCategoryIcon(string $category): string
    {
        $icons = [
            'sales' => '💰',
            'purchases' => '🛒',
            'inventory' => '📦',
            'accounts' => '💳'
        ];

        return $icons[$category] ?? '📊';
    }

    private function storeReportMetadata(string $filename, ?string $conversationId, int $userId): void
    {
        try {
            DB::table('CHATBOT_REPORTS')->insert([
                'FILENAME' => $filename,
                'CONVERSATION_ID' => $conversationId,
                'USER_ID' => $userId,
                'CREATED_AT' => now(),
                'UPDATED_AT' => now()
            ]);
            Log::info('Stored report metadata: ' . $filename . ' for conversation: ' . $conversationId);
        } catch (\Exception $e) {
            Log::warning('Could not store report metadata: ' . $e->getMessage());
        }
    }

    public function clearConversationReports(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'conversation_id' => 'nullable|string'
            ]);

            $conversationId = $request->input('conversation_id');
            $user = Auth::user();
            $deletedCount = 0;
            $reports = [];

            try {
                if ($conversationId) {
                    $reports = DB::table('CHATBOT_REPORTS')
                        ->where('CONVERSATION_ID', $conversationId)
                        ->where('USER_ID', $user->id)
                        ->get();
                } else {
                    $reports = DB::table('CHATBOT_REPORTS')
                        ->where('USER_ID', $user->id)
                        ->get();
                }

                Log::info('Found ' . count($reports) . ' report(s) to delete for user ' . $user->id .
                         ($conversationId ? ' and conversation ' . $conversationId : ''));

                foreach ($reports as $report) {
                    $filename = $report->filename ?? $report->FILENAME ?? null;

                    if (!$filename) {
                        Log::warning('No filename found in report object: ' . json_encode($report));
                        continue;
                    }

                    $filePath = storage_path('app/public/reports/' . $filename);
                    Log::info('Attempting to delete report: ' . $filePath . ' (exists: ' . (file_exists($filePath) ? 'yes' : 'no') . ')');

                    if (file_exists($filePath)) {
                        if (unlink($filePath)) {
                            $deletedCount++;
                            Log::info('Successfully deleted: ' . $filePath);
                        } else {
                            Log::warning('Failed to delete (permission denied?): ' . $filePath);
                        }
                    } else {
                        Log::warning('File not found: ' . $filePath);

                        $altPath = public_path('storage/reports/' . $filename);
                        Log::info('Checking alternative path: ' . $altPath);
                        if (file_exists($altPath)) {
                            if (unlink($altPath)) {
                                $deletedCount++;
                                Log::info('Successfully deleted from alt path: ' . $altPath);
                            }
                        }
                    }
                }

                if ($conversationId) {
                    DB::table('CHATBOT_REPORTS')
                        ->where('CONVERSATION_ID', $conversationId)
                        ->where('USER_ID', $user->id)
                        ->delete();
                } else {
                    DB::table('CHATBOT_REPORTS')
                        ->where('USER_ID', $user->id)
                        ->delete();
                }
            } catch (\Exception $e) {
                Log::warning('chatbot_reports table might not exist, scanning directory instead: ' . $e->getMessage());

                $reportsDir = storage_path('app/public/reports');
                if (is_dir($reportsDir)) {
                    $files = glob($reportsDir . '/report_*.xlsx');
                    foreach ($files as $file) {
                        if (file_exists($file) && unlink($file)) {
                            $deletedCount++;
                            Log::info('Deleted report file: ' . $file);
                        }
                    }
                }
            }

            try {
                if ($conversationId) {
                    DB::table('CHATBOT_CONVERSATIONS')
                        ->where('CONVERSATION_ID', $conversationId)
                        ->where('USER_ID', $user->id)
                        ->delete();
                } else {
                    DB::table('CHATBOT_CONVERSATIONS')
                        ->where('USER_ID', $user->id)
                        ->delete();
                }
            } catch (\Exception $e) {
                Log::warning('Could not clear conversation history: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Conversation and reports cleared successfully',
                'deleted_reports' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error clearing conversation reports: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not clear conversation reports: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview schema for a category (for debugging)
     */
    public function previewSchema(Request $request, string $category): JsonResponse
    {
        try {
            if (!array_key_exists($category, $this->getCategorySchemas())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category'
                ], 400);
            }

            $schema = $this->buildSchemaForCategory($category);

            return response()->json([
                'success' => true,
                'category' => $category,
                'schema' => $schema,
                'cached' => Cache::has("chatbot_schema_{$category}")
            ]);

        } catch (\Exception $e) {
            Log::error('Error previewing schema: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Could not preview schema',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
