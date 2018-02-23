# Create-custom-logfile-and-Zip-old-log-files-exceeding-size-limit
#Create Custom log files and zip existing one if exceeds size limit - Using Zip Archive Utility.

Usage : 
Import these files in your Controller
      
        use App\Controller\LoggingController;
        use App\Controller\CustomLogController;
  
  
Use this code in your function

        $customLog =  new CustomLogController();    // Create Object of CustomLogController
        $customLog-> prepareLogs();                 // create zipfile if logfile size limit exceeds 

        $log = new LoggingController();             // Create Object of LoggingController
        $log->lfile('Custom Log');                  // Set Log file name
        $log->lwrite('message');                    // create or append mmessage in log file.
