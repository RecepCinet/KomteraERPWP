<?php
error_reporting(E_ERROR);
ini_set('display_errors', 1);

include '../FileMaker.php';

$fm = new FileMaker();
$fm->setProperty('database', 'KomteraERP');
$fm->setProperty('hostspec', 'http://172.16.80.214');
$fm->setProperty('username', 'Recep Cinet');
$fm->setProperty('password', 'KlyA2gw1');

$layouts=$fm->listLayouts();


$request = $fm->newFindCommand('T_95_USERS');
//$request->addFindCriterion('kullanici', 'Recep Cinet');
$request->setResultLayout( 'T_95_USERS' );
$results = $request->execute();

$fields = $results->getFields();
$records = $results->getRecords();

foreach ( $records as $record ) {
    foreach ( $fields as $field ) {
        //echo $record->getRecordId();
        echo $record->getField( $field ) . " ### ";
    }
    echo "\n";
}


?>

