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
    // echo($fileContent);
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
        if($csvArray[$i][0] != NULL && $csvArray[$i][0] != "" && trim($csvArray[$i][0]) != "" && trim($csvArray[$i][0]) != NULL)
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
    // print_r($mainArray);
    for($i = 0 ; $i < count($distinctOptions) ; $i++) {

        $subArray = array();
        $subReturnArray = array();
        $subReturnArray['name'] = $distinctOptions[$i];

        for($j = 0; $j < count($mainArray); $j++) {
            if($mainArray[$j][0] == $distinctOptions[$i] ) {
                array_push($subArray,  array_slice($mainArray[$j], 1) );
            }
        }

        if(count($subArray[0]) > 0 && $subArray[0][0]!= '' && $subArray[0][0]!= NULL ){
            $subReturnArray['possibleValueList'] =  possibleValuesList($subArray);
        } else {
            // $subReturnArray['possibleValueList'] = array();
        }
        array_push($returnArray,$subReturnArray);  
    }

    return $returnArray;
}

?>



<?php
    $displayString = '';
    $fileName = '';
    if(isset($_POST['Submit'])){
        $fileName = $_FILES["fileToUpload"]['name'];
        // echo $fileName ;
        $contents = file_get_contents($_FILES["fileToUpload"]["tmp_name"]);
        $displayString = TakeFileReturnJson($contents);
        // echo "done";
        // exit;
    }    
?>

<html>
<head>
<link rel="stylesheet" href="css/material.min.css">
<script src="js/material.min.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
<link rel="stylesheet" type="text/css" href="css/material-design-iconic-font.css">
<link rel="stylesheet" type="text/css" href="css/material-design-iconic-font.min.css">

<link rel="stylesheet" type="text/css" href="css/custom.css">

<link rel="stylesheet" href="fonts/Material-Design-Iconic-Font.eot">
<link rel="stylesheet" href="fonts/Material-Design-Iconic-Font.svg">
<link rel="stylesheet" href="fonts/Material-Design-Iconic-Font.ttf">
<link rel="stylesheet" href="fonts/Material-Design-Iconic-Font.woff">
<link rel="stylesheet" href="fonts/Material-Design-Iconic-Font.woff2">


</head>
    <body>
        <div class="container mdl-grid mdl-grid--no-spacing centeredGrid mainContainer">
            <div class="mdl-grid centeredGrid">
                <form action="#" method="post" enctype="multipart/form-data">
                    <!-- Select CSV File : 
                    <input type="file" name="fileToUpload" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect"/>
                    <input type="submit" name="Submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect mdl-button--colored"/> -->

                    <div class="mdl-textfield mdl-js-textfield mdl-textfield--file">
                        <input class="mdl-textfield__input" placeholder="File" type="text" id="uploadFile" readonly/>
                        <div class="mdl-button mdl-button--primary mdl-button--icon mdl-button--file">
                            <i class="material-icons">attach_file</i><input type="file" name="fileToUpload" id="uploadBtn">
                        </div>
                    </div>
                    <input type="submit" name="Submit" class="mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect coloredButton"/>
                </form>
            </div>


            <div id="jsonCard">
                <div class="demo-card-wide mdl-card mdl-shadow--2dp">
                    <div class="mdl-card__title">
                        <div class="mdl-grid mdl-grid--no-spacing ">
                            <div class="mdl-cell mdl-cell--12-col">
                                <h2 class="mdl-card__title-text">Generated JSON</h2>
                            </div>
                            <div class="mdl-cell mdl-cell--12-col">
                                <?php echo "(".$fileName.")";?>
                            </div>
                        </div>
                    </div>
                    <div class="mdl-card__supporting-text">
                        <textarea id="generatedJSON" readonly><?php echo trim($displayString); ?></textarea>
                    </div>
                    <div class="mdl-card__actions mdl-card--border">
                        <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect" onclick='copyGeneratedJSON()'>
                        Copy To Clipboard
                        </a>
                    </div>
                    <div class="mdl-card__menu">
                        <!-- <button class="mdl-button mdl-button--icon mdl-js-button mdl-js-ripple-effect">
                        <i class="material-icons">share</i>
                        </button> -->
                    </div>
                </div>
            </div>
            <script>
                var jsonString = "<?php echo $displayString; ?>";
                if(jsonString == '' ||jsonString == null) {
                    document.getElementById('jsonCard').style.display = 'none'
                }
            </script>
        </div>

        <footer class="mdl-mini-footer" style="position:absolute; bottom:0px; right:0px; left:0px;  padding: 8px 16px;">
            <div class="mdl-mini-footer__left-section">
                <div class="mdl-logo"> © - Ashutosh Mishra</div>
                <ul class="mdl-mini-footer__link-list">
                    <li><a href="https://www.linkedin.com/in/a-mishra/"><i class="zmdi zmdi-linkedin-box mdc-text-grey zmdi-hc-lg"></i></a></li>
                    <li><a href="https://github.com/a-mishra"><i class="zmdi zmdi-github mdc-text-grey zmdi-hc-lg"></i></a></li>
                </ul>
            </div>
        </footer>
    </body>
    <script src="js/custom.js"></script>
</html>