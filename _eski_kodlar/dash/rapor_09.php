<?php
$sql = <<<SQL
select f."Marka_Kilidi" AS MT,
sum(CASE (f.Olasilik) WHEN 'Discovery' THEN f.TahminiTutarKUR END) AS "Discovery",
sum(CASE (f.Olasilik) WHEN 'Solution Mapping' THEN f.TahminiTutarKUR END) AS "Solution Mapping",
sum(CASE (f.Olasilik) WHEN 'Demo/POC' THEN f.TahminiTutarKUR END) AS "Demo/POC",
sum(CASE (f.Olasilik) WHEN 'Negotiation' THEN f.TahminiTutarKUR END) AS "Negotiation",
sum(CASE (f.Olasilik) WHEN 'Confirmed/Waiting for End-User PO' THEN f.TahminiTutarKUR END) AS "Confirmed/Waiting for End-User PO",
sum(f.TahminiTutarKUR) AS "TUTAR"
from T_10 f
where f."durumkod"=1 XXXXX       
group by f."Marka_Kilidi"

   UNION ALL
select ' ' AS MT,
sum(CASE (f.Olasilik) WHEN 'Discovery' THEN f.TahminiTutarKUR END) AS "Discovery",
sum(CASE (f.Olasilik) WHEN 'Solution Mapping' THEN f.TahminiTutarKUR END) AS "Solution Mapping",
sum(CASE (f.Olasilik) WHEN 'Demo/POC' THEN f.TahminiTutarKUR END) AS "Demo/POC",
sum(CASE (f.Olasilik) WHEN 'Negotiation' THEN f.TahminiTutarKUR END) AS "Negotiation",
sum(CASE (f.Olasilik) WHEN 'Confirmed/Waiting for End-User PO' THEN f.TahminiTutarKUR END) AS "Confirmed/Waiting for End-User PO",
sum(f.TahminiTutarKUR) AS "TUTAR"
from T_10 f 
where f."durumkod"=1 XXXXX
SQL;
?>
