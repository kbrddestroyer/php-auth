<?php
    /*
    *   Author:         KeyboardDestroyer
    *   Date:           23.02.2023
    *   Last update:    -
    *   Status:         DEBUG
    *       --- TODO: ---
    * 1. Remove DebugConsole class on Release                               [DONE]
    * 2. Rewrite PHP scripts for AJAX connectivity (using JSON data format) [DONE]
    * 3. JSON IO wrapper                                                    [DONE]
    * 4. Create PHP library for DAOs                                        [DONE]
    * 5. SOLIDify all                                                       [IN PROGRESS]
    * 6. SHA1 password hashing                                              [DONE]
    * 7. Unit testing                                                       [-]
    * 8. Get rid of static methods&variables                                [IN PROGRESS]
    */
    
    // SHA1 or MD5 hash is insecure [!]
    // Bruteforse can be performed
    // Also the idea of one-sided server encryption is not secure with Sniffing and Man-in-the-middle

    require_once("main_classes.php");

    Main::main();
?>