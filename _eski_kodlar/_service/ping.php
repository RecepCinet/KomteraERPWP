<meta http-equiv="refresh" content="120">
<?PHP
echo date("H:i:s");
echo "<br />";

for ($i=0;$i<40;$i++) {
    for ($t=0;$t<40;$t++) {
        echo rand();
    }
    echo "<br />";
}

?>