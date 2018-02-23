<?php


namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\App;
use App\Controller\LoggingController;
use Cake\Core\Exception\Exception;
use ZipArchive;
use Cake\Routing\Router;


class UsersSalesforceIntegrationController extends AppController
{

  private $log;
  private $logfilename;

   public 	function getLog() {
      return $this->log;
    }
    public  function setLog($log) {
      $this->log= $log;
    }

    public 	function getLogfilename() {
      return $this->logfilename;
    }
    public  function setLogfilename($logfilename) {
      $this->logfilename= $logfilename;
    }

    function beforeFilter(Event $event)
    {
      parent::beforeFilter($event);
      $this->Auth->allow('confirmUser');
    }
    function prepareLogs(){
        $this->autoRender=false;
        $logfilename="custom_Log";          // set default name for log file
        $baseUrl =Router::fullBaseUrl();
        $appUrl =Router::url('/');
        $logfilename= $_SERVER['DOCUMENT_ROOT'].$appUrl."logs/".$logfilename.'.txt';  // set path to save log file
        if(file_exists($logfilename)){                                                // checks if filename already exists
          $fileSizeInMB= number_format(filesize($logfilename)/ 1048576, 2); // ' MB';  // Get file Size and convert to MB
          if($fileSizeInMB>=2){                                                         /// checks if file size exceeds 2MB/
            $zip = new ZipArchive();                                                    /// Using ZipArchiver 
            $filename = $_SERVER['DOCUMENT_ROOT'].$appUrl."logs/Archive/LogsZip.zip";   /// Set path to create zip file
            if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {                     /// Create and check if zip is created
              exit("cannot open <$filename>\n");                                        // return error in case of not created
            }
            try{
              $datetime=str_replace('-','_',str_replace(':','_',str_replace(' ','__',date("Y-m-d h:i:s"))));  // get current datetime
              $zip->addFile($logfilename,$datetime.'_custom_Log.txt');                                       // rename old file by appending timstamp and zip
              $zip->close();                                                                                // close zip directory
              unlink($logfilename);                                                                         // Delete old Log File
            }Catch(Esception $e){
              print_r($e);
            }

          }
        }

        $log = new LoggingController();                                                         // Create LoggingController  Object                                                      
        $log->lfile($logfilename);                                                              // Set new log file name

      }
	
}
