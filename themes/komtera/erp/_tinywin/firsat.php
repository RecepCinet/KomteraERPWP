<style>
body,table {
    font-family: Arial;
    color: #333333;
    font-size: 13px;
    border-collapse: collapse;
    margin: 2px;
}
input {
    font-size: 15px;
}
.tt tr {
    cursor: pointer;
}
tr.ss-button {
    background-color: #FFFFFF;
}
tr.ss-button:hover {
    background-color: #e0e0e0;
}
tr.ss-button:nth-of-type(odd):hover {
    background: #e0e0e0;
}
tr.ss-button:nth-of-type(odd) {
/*    background: #e6f4ff;*/
    background: #f7f7f7;
}
td {
/*    border-color: #a8c4dc;*/
    border-color: #d6d6d6;
}
</style>
<?php
error_reporting(E_ALL);
ini_set('display_errors',true);
include '../_check.php';
include '../../_conn.php';
include '../../_conn_fm.php';
include '../_user.php';
$user=$_SESSION['user'];
$gelen = $_GET['data'];
$string="select 
*
    from aa_erp_kt_firsatlar";
$stmt = $conn->query($string);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
?>
<table style='width: 100%;' class='tt' border="1">
<?PHP
foreach ($data as $key => $value) {
?>
    <tr class='ss-button'>
        <td width="140"><b><?PHP echo $key; ?></b></td>
        <td><?PHP echo $value; ?></td>
    </tr>
<?PHP    
}
?>
</table>