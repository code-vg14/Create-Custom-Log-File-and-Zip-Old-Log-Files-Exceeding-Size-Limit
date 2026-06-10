<?php
namespace App\Log\Engine;

use Cake\Log\Engine\BaseLog;
use Exception;
use ZipArchive;

class CustomFileLog extends BaseLog
{
    private string $logPath;
    private int $maxFileSize = 2097152; // 2MB in bytes

    /**
     * Constructor setting up default log file path.
     *
     * @param array $config Configuration options
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        // Uses CakePHP's built-in LOGS constant safely
        $this->logPath = LOGS . 'custom_Log.txt';
    }

    /**
     * Standard compliance method to write messages to log file.
     *
     * @param mixed $level Log severity level
     * @param string $message The message text to append
     * @param array $context Context variables
     * @return void
     */
    public function log($level, $message, array $context = []): void
    {
        try {
            $this->rotateLogsIfNecessary();

            $time = date('[d/M/Y:H:i:s]');
            $formattedMessage = sprintf("%s [%s]: %s%s", $time, strtoupper((string)$level), $message, PHP_EOL);

            // Using atomic operations avoids persistent open dangling file resource pointers
            file_put_contents($this->logPath, $formattedMessage, FILE_APPEND | LOCK_EX);
            
        } catch (Exception $e) {
            // Fallback gracefully without breaking user request flow
            error_log('CustomFileLog failure: ' . $e->getMessage());
        }
    }

    /**
     * Checks log size and automatically archives via ZIP compression.
     *
     * @return void
     */
    private function rotateLogsIfNecessary(): void
    {
        if (!file_exists($this->logPath)) {
            return;
        }

        if (filesize($this->logPath) < $this->maxFileSize) {
            return;
        }

        $archiveDir = LOGS . 'Archive' . DS;
        if (!is_dir($archiveDir)) {
            mkdir($archiveDir, 0755, true);
        }

        $zip = new ZipArchive();
        $timestamp = date('Y_m_d__H_i_s');
        
        // FIX: The zip filename contains a timestamp so old log bundles are never overwritten
        $zipPath = $archiveDir . 'LogsZip_' . $timestamp . '.zip';

        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            error_log("Failed to create log archive zip at: " . $zipPath);
            return;
        }

        $zip->addFile($this->logPath, $timestamp . '_custom_Log.txt');
        $zip->close();

        // Safely wipe out local file to let it cycle cleanly
        if (file_exists($zipPath)) {
            unlink($this->logPath);
        }
    }
}
