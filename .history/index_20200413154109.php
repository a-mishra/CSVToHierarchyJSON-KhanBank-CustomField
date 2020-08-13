<?php

header('Access-Control-Allow-Origin: *');


/**This Metod will be used for logging purpose  */
function debugLog($message) {
    $logfile = './logs';

        if (!file_exists($logfile)) {
            mkdir($logfile, 0777, true);
        }

        $log_file_data = $logfile.'/logs_'.date('d-M-Y').'.log';
    $now     = "\n[" . date("Y-M-d H:i:s") . "] ";
    $message = $now . $message;
    error_log($message, 3, $log_file_data);
}


/** MasterFunction That will take in fileName and will return JSON for custom field */
function TakeFileReturnJson( $fileContent ) {
    //$DependentDropDownConfig_fileHandle = $fileName; // "References/sample custom dependent fields - Sheet1.csv"
    $DependentDropDownConfig_fileContent= $fileContent;
    $returnObject = array();

    debugLog("TakeFileReturnJson : called : ");
    print_r($fileContent);
    //----converting the csvFile in multiDArray--------------------------------
        $csvArray = array();
        //--------------------------------------------------------------------
        // $fileOpened = fopen($DependentDropDownConfig_fileHandle, "r");
        // while ($fileData = fgetcsv($fileOpened)) {
        //     array_push($csvArray, $fileData);
        // }
        //--------------------------------------------------------------------
        
        // --- if using file handle use above part to initialise csvArray else use below part

        $csvString = $DependentDropDownConfig_fileContent;        
        $csvArray = str_getcsv($csvString, "\n");

        $tempArray = array();
        foreach($csvArray as $Row) {
            $Row = str_getcsv($Row, ",");
            array_push($tempArray, $Row);
        }
        $csvArray = $tempArray;

        //print_r($csvArray);
        
    //-------------------------------------------------------------------------


    //-------- creating labels for final object--------------------------------
        $label = array();
        for($i = 0 ; $i<count($csvArray[0]); $i++) {
            $label[$i+1] = $csvArray[0][$i];
        }
        $returnObject['label']=$label;
    //--------------------------------------------------------------------------


    //-------Label has been created now moving to values-------------------------
        array_shift($csvArray);
        $possibleValueList = array();
        $returnObject['value']= array();
        $returnObject['value']['possibleValueList'] = array();
        $returnObject['value']['possibleValueList'] = possibleValuesList($csvArray);
    //----------------------------------------------------------------------------


    //----------------------- RETUNING THE FINAL OBJECT --------------------------
        $returnString = json_encode($returnObject);    
        return ($returnString);
    //----------------------------------------------------------------------------
}

//----------------------- INLINE FUNCTIONS -------------------------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------------------------------------------------------------------

/** Find distinct options in a column */
function distinctOptionsInColumn($csvArray) {

    $distinctValuesArray = array();

    for($i = 0; $i < count($csvArray) ; $i++) {
        array_push($distinctValuesArray, $csvArray[$i][0]);
    }

    $distinctValuesArray = array_unique($distinctValuesArray);
    $tempArray = array();
    foreach ($distinctValuesArray as $key => $value) {
        array_push($tempArray, $value);
    }

    $distinctValuesArray = $tempArray;

    return $distinctValuesArray;
}



function possibleValuesList($mainArray) {
    $returnArray = array();

    $distinctOptions = distinctOptionsInColumn($mainArray);

    for($i = 0 ; $i < count($distinctOptions) ; $i++) {

        $subArray = array();
        $subReturnArray['name'] = $distinctOptions[$i];

        for($j = 0; $j < count($mainArray); $j++) {
            if($mainArray[$j][0] == $distinctOptions[$i] ) {
                array_push($subArray,  array_slice($mainArray[$j], 1) );
            }
        }

        if(count($subArray[0]) > 0 && $subArray[0][0]!= '' ){
            $subReturnArray['possibleValueList'] =  possibleValuesList($subArray);
        }    
        array_push($returnArray,$subReturnArray);  
    }

    return $returnArray;
}

?>



<?php    
    if(isset($_POST['Submit'])){
        $contents = file_get_contents($_FILES["fileToUpload"]["tmp_name"]);
        $displayString = TakeFileReturnJson($contents);
    }    
?>

<html>
    <body>    
        <form action="#" method="post" enctype="multipart/form-data">
            Select CSV File : 
            <input type="file" name="fileToUpload"/>
            <input type="submit" name="Submit"/>

            <p>
                <?php echo $displayString; ?>
            </p>

        </form>    
    </body>
</html>