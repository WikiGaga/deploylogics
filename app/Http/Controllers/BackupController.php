<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use PDO;

class BackupController extends Controller
{
    public function createBackup()
    {
        set_time_limit(0);
        //ini_set('memory_limit', '2048M');
        ini_set('memory_limit', '4G');
        $connection = 'oracle'; // Specify the Oracle database connection name
        // Connect to the Oracle Database using the PDO driver
        $pdo = DB::connection($connection)->getPdo();
        // Set the database connection for Eloquent
        $this->setEloquentConnection($connection);
        //set schema name
        $schema = $this->getOracleSchemaName();
        // Retrieve distinct table names
        /** old query for tables
            $tables = $pdo->
            query("SELECT DISTINCT
            table_name FROM all_tables
            WHERE owner = '$schema'")
            ->fetchAll();
         */



        $tableDependencies = $this->getTableDependencies($pdo, $schema);
        //query golbal var
        $backupQueries = '';
        $backupQueries .= "-- Schema: $schema\n";
        $backupQueries .= "-- Created at: " . date('Y-m-d H:i:s') . "\n";
        $backupQueries .= "-- Created by: " . auth()->user()->name . "\n";
        // Generate the SQL statements for functions
        $functionStatements = $this->getCreateFunctionStatements();
        foreach ($functionStatements as $statementfun) {
                $backupQueries .= "-- Function  Query\n";
                $backupQueries .= $statementfun . "\n";
        }
        //table iteration
        /*
        foreach ($tableDependencies as $table) {
            // dd($table);
            $tableName = $table;
            $backupQueries .= "-- Table Create Query\n";
            $primary_constraint = '';
            $forigen_constraint = '';
            $uniq_constraint = '';
            // Query to retrieve table structure
            $structureQuery = $this->getTableStructureQuery($pdo, $tableName , $primary_constraint ,$forigen_constraint ,$uniq_constraint );
            $backupQueries .= $structureQuery . "\n";
            $backupQueries .= "-- Constraint Query\n";
            $backupQueries .= $primary_constraint . "";
            $backupQueries .= $forigen_constraint . "";
            $backupQueries .= $uniq_constraint . "";

           ** within commnent old logic start here**
          *//* Execute index creation queries
                $indexQuery = "SELECT index_name, index_type
                            FROM all_indexes
                            WHERE owner = :schemaName
                            AND table_name = :tableName";
                $stmt = $pdo->prepare($indexQuery);
                $stmt->bindValue(':schemaName', $schema);
                $stmt->bindValue(':tableName', $tableName);
                $stmt->execute();
                $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // Generate index creation queries
                foreach ($indexes as $index) {
                    $indexName = $index['index_name'];
                    $indexType = $index['index_type'];
                    $indexColumnsQuery = "SELECT DISTINCT  column_name
                        FROM all_ind_columns
                        WHERE  table_name = :tableName
                        AND index_name = :indexName";
                        $stmt = $pdo->prepare($indexColumnsQuery);
                        // $stmt->bindValue(':schemaName', $schema);
                        $stmt->bindValue(':tableName', $tableName);
                        $stmt->bindValue(':indexName', $indexName);
                        $stmt->execute();
                        $indexColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                        // Generate the index creation query with the indexed column(s)
                        $columnsString = implode(', ', $indexColumns);
                        $indexCreationQuery = "CREATE  INDEX $indexName ON $schema.$tableName ($columnsString)";
                        // Add the index creation query to the backup queries
                        if (strpos($backupQueries, $indexCreationQuery) === false) {
                            $backupQueries .= "-- Index Creation Query\n";
                            $backupQueries .= $indexCreationQuery . ";\n\n";
                        }
                }
            *//*within commnent old logic end here
            // Query to insert records
            $backupQueries .= "-- Record Insertion Queries\n";
            $recordsQuery = $this->getInsertRecordsQuery($pdo, $tableName);
            // $backupQueries .= $recordsQuery . ";\n\n";
            if(!empty( $recordsQuery)){
                $backupQueries .= $recordsQuery . "\n;\n";
            }
        }
        */

           // Generate the SQL statements for tables' structure
           foreach ($tableDependencies as $table) {
            $tableName = $table;
            $primary_constraint = '';
            $foreign_constraint = '';
            $unique_constraint = '';
            $backupQueries .= "\n-- Table Create Query $tableName \n\n";
            $structureQuery = $this->getTableStructureQuery($pdo, $tableName, $primary_constraint, $foreign_constraint, $unique_constraint);
            $backupQueries .= $structureQuery . "\n";
            $backupQueries .= "\n-- Constraint Query\n";

            $backupQueries .= $primary_constraint . "";
            $backupQueries .= $foreign_constraint . "";
            $backupQueries .= $unique_constraint . "";
        }

            // Generate the SQL statements for constraints
            // foreach ($tableDependencies as $table) {
            //     $tableName = $table;
            //     $primary_constraint = '';
            //     $foreign_constraint = '';
            //     $unique_constraint = '';

            //     $constraintQueries = $this->getTableStructureQuery($pdo, $tableName, $primary_constraint, $foreign_constraint, $unique_constraint);

            //     if (!empty($constraintQueries)) {
            //         $backupQueries .= "\n-- Constraint Queries for Table $tableName\n";
            //         $backupQueries .= $constraintQueries;
            //     }
            // }

            // Generate the SQL statements for record insertion
            foreach ($tableDependencies as $table) {
                $tableName = $table;
                $recordsQuery = $this->getInsertRecordsQuery($pdo, $tableName);
                if (!empty($recordsQuery)) {
                    $backupQueries .= "\n-- Record Insertion Queries for Table $tableName\n";
                    $backupQueries .= $recordsQuery . ";\n";
                }
            }


            // Generate the SQL statements for views
            $viewStatements = $this->getCreateViewStatements();
            foreach ($viewStatements as $statement) {
                $backupQueries .= "\n-- Views  Query\n";
                $backupQueries .= $statement . "\n;\n";
            }
            // Generate the SQL statements for procedures
            $procedureStatements = $this->getCreateProcedureStatements();
            foreach ($procedureStatements as $statementpro) {
                $backupQueries .= "\n-- Procedure  Query\n";
                $backupQueries .= $statementpro . "\t\t\n\n";
            }

        // Execute backup queries and save results to a file
        $dateTime = date('Y-m-d_H-i-s'); // Get the current date and time
        $backupFileName = strtolower($schema).'_db_backup_' . $dateTime . '.sql'; // Construct the backup file name with date and time
        $backupFilePath = storage_path('app/' . $backupFileName);
        File::put($backupFilePath, $backupQueries);
        $zipFileName = strtolower($schema) . '_db_backup_' . $dateTime . '.zip'; // Construct the zip file name with date and time
        $zipFilePath = storage_path('app/' . $zipFileName); // Append date and time to the zip file name
        // $backupFilePath = storage_path('app/backup.sql');
        $zip = new \ZipArchive();
        if ($zip->open($zipFilePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            $zip->addFile($backupFilePath, $backupFileName);
            $zip->close();
        }
        return response()->download($zipFilePath)->deleteFileAfterSend(true);
    }
    public function getTableDependencies($pdo, $schema)
    {
        $excludedTables = ['PERMISSIONS_15_09_2022', 'PERMISSIONS_2021_01_14','PERMISSIONS_2021_11_08','PERMISSIONS_2022_01_28'];
        $dependenciesQuery = "
            SELECT distinct t.table_name
            FROM all_constraints p, all_constraints c, all_cons_columns cc, all_tables t
            WHERE
                p.constraint_type = 'P'
                AND p.table_name = cc.table_name
                AND p.owner = cc.owner
                AND c.constraint_name = cc.constraint_name
                AND c.owner = cc.owner
                AND c.r_owner = p.owner
                AND c.r_constraint_name = p.constraint_name
                --AND t.table_name = p.table_name
                --AND t.owner = p.owner
                AND t.owner = '$schema'
                --AND t.table_name NOT LIKE '%_SOFT_%'
                --AND CASE WHEN INSTR(t.table_name, '_SOFT_') > 0 THEN 1 ELSE 0 END = 0 --AND CASE WHEN NOT To include both are right approches
            ORDER BY t.table_name ASC";

        //dd($dependenciesQuery);

        $tables = [];
        $result = $pdo->query($dependenciesQuery);
        if ($result !== false) {
            $tables = $result->fetchAll(PDO::FETCH_COLUMN);
        }
        $tables = array_diff($tables, $excludedTables);
        return $tables;
    }
    public function setEloquentConnection($connection)
    {
        $resolver = Model::getConnectionResolver();
        $resolver->setDefaultConnection($connection);
    }
    public function getOracleSchemaName()
    {
        $base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
        $base_url .= "://" . $_SERVER['HTTP_HOST'];
        $url = 'http://127.0.0.1:8000/';

        if($base_url == $url){
            //$ External Live server
            $DB_CONNECTION='oracle';
            $DB_HOST='103.198.155.90';
            $DB_PORT=1521 ;
            $DB_DATABASE='CDB';
            $DB_USERNAME='ROYAL_RISEN_DEV';
            $DB_PASSWORD='RoYalRi$En2KTeN11';
        }
        else{
            //$ External local server
            $DB_CONNECTION='oracle';
            $DB_HOST='103.198.155.90';
            $DB_PORT=1521 ;
            $DB_DATABASE='CDB';
            $DB_USERNAME='ROYAL_RISEN_DEV';
            $DB_PASSWORD='RoYalRi$En2KTeN11';
        }

        return strtoupper($DB_USERNAME);
    }
    /*
        public function generateIndexCreationQuery($pdo, $schemaName)
        {
            $indexCreationQueries = [];
            // Query to retrieve index information
            $query = "SELECT owner, table_name, index_name, index_type FROM all_indexes WHERE owner = :schemaName ORDER BY owner, table_name, index_name";
            $stmt = $pdo->prepare($query);
            $stmt->bindValue(':schemaName', $schemaName);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $owner = $row['owner'];
                $tableName = $row['table_name'];
                $indexName = $row['index_name'];
                $indexType = $row['index_type'];
                // Generate the index creation query
                $indexCreationQuery = "CREATE $indexType INDEX $indexName ON $owner.$tableName;";
                // Add the index creation query to the array
                $indexCreationQueries[] = $indexCreationQuery;
            }
            return $indexCreationQueries;
        }
    */
    public function getTableStructureQuery($pdo, $tableName , &$primary_constraint ,&$forigen_constraint, &$uniq_constraint)
    {
        set_time_limit(0);
        $query = "
            SELECT column_name, data_type, data_length, data_precision, data_scale, nullable
            FROM all_tab_columns
            WHERE table_name = :table_name
            AND owner = :owner
            ORDER BY column_id
        ";
        $statement = $pdo->prepare($query);
        $statement->execute([
            'table_name' => $tableName,
            'owner' => $this->getOracleSchemaName(),
        ]);
        $columnDefinitions = [];
        while ($column = $statement->fetch()) {
            $columnName = $column[strtolower('COLUMN_NAME')];
            $dataType = $column[strtolower('DATA_TYPE')];
            $dataLength = $column[strtolower('DATA_LENGTH')];
            $dataPrecision = $column[strtolower('DATA_PRECISION')];
            $dataScale = $column[strtolower('DATA_SCALE')];
            // Determine the column type and length
            $columnType = $this->getColumnType($dataType, $dataLength, $dataPrecision, $dataScale);
            // Check if the nullable information exists and column does not allow null
            if (isset($column[strtolower('NULLABLE')]) && $column[strtolower('NULLABLE')] === 'N') {
                $columnType .= ' NOT NULL';
            }
            $columnDefinitions[] = "$columnName $columnType";
        }
        $schema = $this->getOracleSchemaName();
        $tableStructureQuery = "CREATE TABLE $schema.$tableName (\n" . implode(",\n", $columnDefinitions) . "\n)";
        // Add tablespace and storage options
        $tableStructureQuery .= "\nTABLESPACE USERS\n";
        $tableStructureQuery .= "PCTUSED    0\n";
        $tableStructureQuery .= "PCTFREE    10\n";
        $tableStructureQuery .= "INITRANS   1\n";
        $tableStructureQuery .= "MAXTRANS   255\n";
        $tableStructureQuery .= "STORAGE    (\n";
        $tableStructureQuery .= "INITIAL          64K\n";
        $tableStructureQuery .= "NEXT             1M\n";
        $tableStructureQuery .= "MINEXTENTS       1\n";
        $tableStructureQuery .= "MAXEXTENTS       UNLIMITED\n";
        $tableStructureQuery .= "PCTINCREASE      0\n";
        $tableStructureQuery .= "BUFFER_POOL      DEFAULT\n";
        $tableStructureQuery .= " )\n";
        $tableStructureQuery .= "LOGGING\n";
        $tableStructureQuery .= "NOCOMPRESS\n";
        $tableStructureQuery .= "NOCACHE\n";
        $tableStructureQuery .= "MONITORING;";

        // Get primary key columns
        $primaryKeyQuery = "SELECT column_name
            FROM all_cons_columns
            WHERE constraint_name = (
                SELECT constraint_name
                FROM all_constraints
                WHERE table_name = :table_name
                AND constraint_type = 'P'
                AND owner = :owner
            )
            AND owner = :owner";
            
            $statement = $pdo->prepare($primaryKeyQuery);
            $statement->execute([
                'table_name' => $tableName,
                'owner' => $this->getOracleSchemaName(),
            ]);
        $primaryKeyColumns = [];
        while ($column = $statement->fetch()) {
            $primaryKeyColumns[] = $column[strtolower('COLUMN_NAME')];
        }
        // Add primary key constraint
        if (!empty($primaryKeyColumns)) {
            $primaryKeyQuery = "ALTER TABLE $schema.$tableName ADD PRIMARY KEY (" . implode(', ', $primaryKeyColumns) . ");";
            $primary_constraint = $primaryKeyQuery . "\n\n";
        }
        // Get foreign key constraints
        $foreignKeyQuery = "SELECT a.constraint_name, a.column_name, a.position, b.owner, b.table_name, b.column_name AS foreign_column
            FROM all_cons_columns a
            INNER JOIN all_constraints c ON a.owner = c.owner
                AND a.table_name = c.table_name
                AND a.constraint_name = c.constraint_name
            INNER JOIN all_constraints d ON c.owner = d.owner
                AND c.r_constraint_name = d.constraint_name
            INNER JOIN all_cons_columns b ON d.owner = b.owner
                AND d.table_name = b.table_name
                AND d.constraint_name = b.constraint_name
            WHERE a.table_name = :table_name
            AND a.owner = :owner
            AND c.constraint_type = 'R'
            AND d.constraint_type = 'P'
            AND b.owner = :owner";

        $statement = $pdo->prepare($foreignKeyQuery);
        $statement->execute([
            'table_name' => $tableName,
            'owner' => $this->getOracleSchemaName(),
        ]);
        $foreignKeyConstraints = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $constraintName = $row['constraint_name'];
            $columnName = $row['column_name'];
            $position = $row['position'];
            $foreignOwner = $row['owner'];
            $foreignTable = $row['table_name'];
            $foreignColumn = $row['foreign_column'];
            if (!isset($foreignKeyConstraints[$constraintName])) {
                $foreignKeyConstraints[$constraintName] = [
                    'columns' => [],
                    'foreign_table' => $foreignTable,
                    'foreign_columns' => [],
                ];
            }
            $foreignKeyConstraints[$constraintName]['columns'][$position] = $columnName;
            $foreignKeyConstraints[$constraintName]['foreign_columns'][$position] = $foreignColumn;
        }
        // Add foreign key constraints
        foreach ($foreignKeyConstraints as $constraintName => $constraint) {
            $columns = $constraint['columns'];
            $foreignTable = $constraint['foreign_table'];
            $foreignColumns = $constraint['foreign_columns'];

            $foreignKeyQuery = "ALTER TABLE $schema.$tableName ADD CONSTRAINT $constraintName FOREIGN KEY (" . implode(', ', $columns) . ") REFERENCES $foreignOwner.$foreignTable (" . implode(', ', $foreignColumns) . ");";
            $forigen_constraint = $foreignKeyQuery . "\n\n";
        }
        // Get unique key constraints
        $uniqueKeyQuery = "SELECT column_name
            FROM all_cons_columns
            WHERE constraint_name = (
                SELECT constraint_name
                FROM all_constraints
                WHERE table_name = :table_name
                AND constraint_type = 'U'
                AND owner = :owner
            )
            AND owner = :owner";
            $statement = $pdo->prepare($uniqueKeyQuery);
            $statement->execute([
                'table_name' => $tableName,
                'owner' => $this->getOracleSchemaName(),
            ]);
            $uniqueKeyColumns = [];
            while ($column = $statement->fetch()) {
                $uniqueKeyColumns[] = $column[strtolower('COLUMN_NAME')];
            }
            // Add unique key constraints
            if (!empty($uniqueKeyColumns)) {
                $uniqueKeyQuery = "ALTER TABLE $schema.$tableName ADD CONSTRAINT uk_$tableName UNIQUE (" . implode(', ', $uniqueKeyColumns) . ");";
                $uniq_constraint = $uniqueKeyQuery . "\n\n";
            }
        return $tableStructureQuery;
    }
    public function getColumnType($dataType, $dataLength, $dataPrecision, $dataScale)
    {
        // Map Oracle data types to Laravel data types
        $columnType = '';
        switch ($dataType) {
            case 'NUMBER':
            $precisionPart = ($dataPrecision !== null) ? "($dataPrecision)" : "";
            $columnType = "NUMBER$precisionPart";
            break;
                // $columnType = "NUMBER($dataPrecision)";
                // break;
            case 'VARCHAR2':
                $columnType = "VARCHAR2($dataLength BYTE)";
                break;
            case 'NVARCHAR2':
                $columnType = ($dataLength !== null) ? "NVARCHAR2($dataLength BYTE)" : "NVARCHAR2 255 BYTE";
                break;
            case 'TIMESTAMP':
                $columnType = 'TIMESTAMP(6)';
                break;
            // Add more cases for other data types as needed
            default:
            // Default to the original data type if not handled explicitly
            $columnType = $dataType;
        }
        return $columnType;
    }
    public function getInsertRecordsQuery($pdo, $tableName)
    {
        /* old logics
            $query = "SELECT * FROM $tableName";
            $statement = $pdo->prepare($query);
            $statement->execute();
            $insertStatements = [];
            $columnNames = [];
            $columns = [];
            // Get column names
            for ($i = 0; $i < $statement->columnCount(); $i++) {
                $column = $statement->getColumnMeta($i);
                $columns[] = $column['name'];
            }
            $valuesList = [];
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $values = [];
                foreach ($row as $columnName => $columnValue) {
                    // $columnNames[] = $columnName;
                    if (is_null($columnValue)) {
                        $values[] = 'NULL';
                    } elseif (is_numeric($columnValue)) {
                        $values[] = $columnValue;
                    } elseif (is_string($columnValue)) {
                        // Check if the value is a valid date string
                        if (strtotime($columnValue) !== false) {
                            $columnValue = date('Y-m-d H:i:s', strtotime($columnValue));
                            $values[] = "TO_DATE('" . $columnValue . "', 'YYYY-MM-DD HH24:MI:SS')";
                        } else {
                            $values[] = $pdo->quote($columnValue);
                        }
                    }
                }
                $valuesList[] = '(' . implode(', ', $values) . ')';
                // $insertStatements[] = 'INSERT INTO ' . $tableName . ' VALUES (' . implode(', ', $values) . ')';
                // $insertStatements[] = 'INSERT INTO ' . $tableName . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ')';
            }
            if (!empty($valuesList)) {
                $insertStatements[] = 'INSERT INTO ' . $tableName . ' (' . implode(', ', $columns) . ') VALUES ' . implode(', ', $valuesList);
            }
            return implode(";\n", $insertStatements);
        */
        /**? insert all query
            $query = "SELECT * FROM $tableName";

            $statement = $pdo->prepare($query);
            $statement->execute();

            $insertStatement = "INSERT ALL\n";

            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $values = [];
                foreach ($row as $columnValue) {
                    if (is_null($columnValue)) {
                        $values[] = 'NULL';
                    } elseif (is_numeric($columnValue)) {
                        $values[] = $columnValue;
                    } elseif (is_string($columnValue)) {
                        // Check if the value is a valid date string
                        if (strtotime($columnValue) !== false) {
                            $columnValue = date('Y-m-d H:i:s', strtotime($columnValue));
                            $values[] = "TO_DATE('" . $columnValue . "', 'YYYY-MM-DD HH24:MI:SS')";
                        } else {
                            $values[] = $pdo->quote($columnValue);
                        }
                    }
                }

                $insertStatement .= "INTO $tableName (" . implode(', ', array_keys($row)) . ")\n";
                $insertStatement .= "VALUES (" . implode(', ', $values) . ")\n";
            }

            $insertStatement .= "SELECT 1 FROM DUAL ";

            return $insertStatement;
        */
        $query = "SELECT * FROM $tableName";
        $statement = $pdo->prepare($query);
        $statement->execute();
        $insertStatements = [];
        $columnNames = [];
        $columns = [];
            // Get column names
            for ($i = 0; $i < $statement->columnCount(); $i++) {
                $column = $statement->getColumnMeta($i);
                $columns[] = $column['name'];
            }
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $values = [];
            foreach ($row as $columnName => $columnValue) {
                if (is_null($columnValue)) {
                    $values[] = 'NULL';
                } elseif (is_numeric($columnValue)) {
                    $values[] = $columnValue;
                } elseif (is_string($columnValue)) {
                    // Check if the value is a valid date string
                    if (strtotime($columnValue) !== false) {
                        $columnValue = date('Y-m-d H:i:s', strtotime($columnValue));
                        $values[] = "TO_DATE('" . $columnValue . "', 'YYYY-MM-DD HH24:MI:SS')";
                    } else {
                        $values[] = $pdo->quote($columnValue);
                    }
                }
            }
            $insertStatements[] = 'INSERT INTO  ' . $tableName . ' '."\n\t".' (' . implode(', ', $columns) .' ) '."\n \t".'VALUES (' . implode(', ', $values) ."\n \t". ')';
        }
        return implode(";\n", $insertStatements);
    }
    public function getCreateViewStatements()
    {
        $schema =  $this->getOracleSchemaName();
        // Get the list of views in the schema
        $views = DB::select("SELECT  view_name, text FROM all_views  WHERE owner = '$schema'");
        $viewStatements = [];
        foreach ($views as $view) {
            $viewName = $view->view_name;
            $viewQuery = $view->text;
            $createViewStatement = "CREATE OR REPLACE VIEW $viewName AS $viewQuery ";
            $viewStatements[] = $createViewStatement;
        }
        return $viewStatements;
    }
    /*
    public function getCreateProcedureStatements()
    {
        $schema = $this->getOracleSchemaName();
        // Get the list of procedures in the schema
        $procedures = DB::select("SELECT name, text FROM user_source WHERE type = 'PROCEDURE'  GROUP BY name , text");
        $procedureStatements = [];
        foreach ($procedures as $procedure) {
            $procedureName = $procedure->name;
            $procedureQuery = $procedure->text;
            $procedureStatements[] = $procedureQuery;
        }
        return $procedureStatements;
    }
    */
        public function getCreateProcedureStatements()
        {
            $schema = $this->getOracleSchemaName();

            $procedures = DB::select("SELECT name, LISTAGG(text, CHR(10)) WITHIN GROUP (ORDER BY line) AS text
                FROM user_source
                WHERE type = 'PROCEDURE'
                GROUP BY name");

            $procedureStatements = [];

            foreach ($procedures as $procedure) {
                $procedureName = $procedure->name;
                $procedureQuery = $procedure->text;
                $procedureStatements[] = "CREATE OR REPLACE " . $procedureQuery;
            }

            return $procedureStatements;
        }
    /*
        public function getCreateFunctionStatements()
        {
            $schema =  $this->getOracleSchemaName();
            // Get the list of functions in the schema
            $functions = DB::select("SELECT name, LISTAGG(text, CHR(10)) WITHIN GROUP (ORDER BY line) AS text
                    FROM user_source WHERE type = 'FUNCTION' GROUP BY name");
            $functionStatements = [];
            foreach ($functions as $function) {
                $functionName = $function->name;
                $functionQuery = "CREATE OR REPLACE FUNCTION $functionName AS " . $function->text;
                $functionStatements[] = $functionQuery;
            }
            return $functionStatements;
        }
    */
    public function getCreateFunctionStatements()
    {
        $schema = $this->getOracleSchemaName();
        // Get the list of functions in the schema
        $functions = DB::select("SELECT name, LISTAGG(text, CHR(10)) WITHIN GROUP (ORDER BY line) AS text
                FROM user_source WHERE type = 'FUNCTION' GROUP BY name");
        $functionStatements = [];
        foreach ($functions as $function) {
            $functionName = $function->name;
            $functionQuery = "CREATE OR REPLACE " . $function->text;
            $functionQuery = preg_replace('/\s+/', ' ', $functionQuery); // Remove extra lines and whitespace
            $functionQuery = str_replace(';', ";\n", $functionQuery); // Add new line after semicolon
            $functionStatements[] = $functionQuery;
        }
        return $functionStatements;
    }
}
