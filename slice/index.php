<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/../includes/sessions.php');
$filename = $_POST["stlFile"];
$targetFolder = $_SERVER['DOCUMENT_ROOT'] . "/fileUpload/cart/" . session_id();

if ($filename != "") {
    $sliceCommand = "CuraEngine -v -s layerThickness=150 -s filamentDiameter=1750 -s infillSpeed=50 -s supportAngle=0 -s supportLineDistance=4000 -s printSpeed=40 -o $targetFolder/test.gcode $targetFolder/$filename";
    //echo "executing " . $sliceCommand;
    exec($sliceCommand, $resultArray);
    print_r($resultArray);
} else {
    echo "STL needs to be specified ( " . $filename . " )" ;
}
?>