# Create-and-Zip-oversized-logs-files-in-cakePHP
#Create Custom log files and zip if exceeding size limit Using Zip Archive Utility.

Usage : create new CustomLogController object and call preparelogs() function from any function in your controller you want to create log for
      
        $customLog =  new CustomLogController();    // Create Object of CustomLogController
        $customLog-> prepareLogs();                 // create zipfile if logfile size limit exceeds 

        $log = new LoggingController();             // Create Object of LoggingController
        $log->lfile('Custom Log');                  // Set Log file name
        $log->lwrite('message');                    // create or append mmessage in log file.
