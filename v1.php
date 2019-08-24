<?php

header('Access-Control-Allow-Origin: *');
$DependentDropDownConfig = "References/sample custom dependent fields - Sheet1.csv";
$returnObject = array();


//----converting the csvFile in multiDArray--------------------------------
    $csvArray = array();
    $fileOpened = fopen($DependentDropDownConfig, "r");
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
    echo (json_encode($returnObject));
//----------------------------------------------------------------------------


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