<?php

header('Access-Control-Allow-Origin: *');
/** MasterFunction That will take in fileName and will return JSON for custom field */
function TakeFileReturnJson( $file = "References/sample custom dependent fields - Sheet1.csv") {
    //$DependentDropDownConfig = $fileName;
    $DependentDropDownConfig= $file;
    $returnObject = array();
    print_r($DependentDropDownConfig);


    //----converting the csvFile in multiDArray--------------------------------
        $csvArray = array();
        //$fileOpened = fopen($DependentDropDownConfig, "r");
        $fileOpened = $DependentDropDownConfig;
        while ($fileData = fgetcsv($fileOpened)) {
            array_push($csvArray, $fileData);
        }
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

        if(count($subArray[0]) > 0){
            $subReturnArray['possibleValueList'] =  possibleValuesList($subArray);
        }    
        array_push($returnArray,$subReturnArray);  
    }

    return $returnArray;
}

?>



<?php    
if(isset($_POST['Submit'])){
    // $target_dir = "uploads/";
    // $target_file = $target_dir.basename($_FILES["fileToUpload"]["name"]);
    // print_r($_REQUEST);
    //move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
    $file = file_get_contents($_FILES["fileToUpload"]["tmp_name"]);
    print_r($_FILES["fileToUpload"]["name"]);
    print_r($file);
    $displayString = TakeFileReturnJson($file);
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