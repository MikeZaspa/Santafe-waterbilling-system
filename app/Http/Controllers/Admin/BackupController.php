<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    /**
     * Create and download database backup without shell commands
     */
    public function backupDatabase(): StreamedResponse
    {
        try {
            // Create backup filename with timestamp
            $filename = 'database_backup_' . date('Y-m-d_H-i-s') . '.sql';
            
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $databaseName = DB::getDatabaseName();
            
            // Start the backup
            $backup = "-- Database Backup\n";
            $backup .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
            $backup .= "-- Database: " . $databaseName . "\n\n";
            
            // Disable foreign key checks
            $backup .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
            
            // Process each table
            foreach ($tables as $table) {
                $tableName = $table->{'Tables_in_' . $databaseName};
                
                // Get CREATE TABLE statement
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                if (isset($createTable[0]->{'Create Table'})) {
                    $backup .= "-- Table structure for `{$tableName}`\n";
                    $backup .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                    $backup .= $createTable[0]->{'Create Table'} . ";\n\n";
                    
                    // Get table data
                    $rows = DB::select("SELECT * FROM `{$tableName}`");
                    
                    if (!empty($rows)) {
                        $backup .= "-- Data for table `{$tableName}`\n";
                        
                        foreach ($rows as $row) {
                            $row = (array) $row;
                            $columns = array_keys($row);
                            $values = array_values($row);
                            
                            // Escape values
                            $escapedValues = array_map(function($value) {
                                if ($value === null) {
                                    return 'NULL';
                                } elseif (is_numeric($value) && !is_string($value)) {
                                    return $value;
                                } else {
                                    return "'" . addslashes($value) . "'";
                                }
                            }, $values);
                            
                            $backup .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $escapedValues) . ");\n";
                        }
                        
                        $backup .= "\n";
                    }
                }
            }
            
            // Re-enable foreign key checks
            $backup .= "SET FOREIGN_KEY_CHECKS=1;\n";
            
            // Create the response
            $response = new StreamedResponse(function() use ($backup) {
                echo $backup;
            });
            
            // Set headers for download
            $response->headers->set('Content-Type', 'application/octet-stream');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
            $response->headers->set('Content-Transfer-Encoding', 'binary');
            $response->headers->set('Cache-Control', 'no-cache, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Content-Length', strlen($backup));
            
            return $response;
            
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Database backup failed: ' . $e->getMessage());
            
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Failed to create database backup: ' . $e->getMessage()
            ], 500);
        }
    }
}