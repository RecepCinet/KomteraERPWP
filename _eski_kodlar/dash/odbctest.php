<?PHP
	
$conn=odbc_connect('Logo32','crm','crm2017!?');
if (!$conn){
  die("Connection Failed: " . $conn);
}

$sql = "select * from dbo.DORA_PORTAL_VIEW1";

$rs = odbc_exec($conn, $sql);

while (odbc_fetch_row($rs)) {
	for ($i = 1; $i <= odbc_num_fields($rs); $i ++) {
		echo odbc_result($rs, odbc_field_name($rs, $i));
		
		}

}
	
	?>