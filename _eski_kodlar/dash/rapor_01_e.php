<?php
$sql = "
select MARKA,
sum(case when [AYRAKAM] = '01' then [USD_TUTAR] else 0 end) 'Ocak',
sum(case when [AYRAKAM] = '02' then [USD_TUTAR] else 0 end) 'Subat',
sum(case when [AYRAKAM] = '03' then [USD_TUTAR] else 0 end) 'Mart',
sum(case when [AYRAKAM] = '04' then [USD_TUTAR] else 0 end) 'Nisan',
sum(case when [AYRAKAM] = '05' then [USD_TUTAR] else 0 end) 'Mayis',
sum(case when [AYRAKAM] = '06' then [USD_TUTAR] else 0 end) 'Haziran',
sum(case when [AYRAKAM] = '07' then [USD_TUTAR] else 0 end) 'Temmuz',
sum(case when [AYRAKAM] = '08' then [USD_TUTAR] else 0 end) 'Agustos',
sum(case when [AYRAKAM] = '09' then [USD_TUTAR] else 0 end) 'Eylul',
sum(case when [AYRAKAM] = '10' then [USD_TUTAR] else 0 end) 'Ekim',
sum(case when [AYRAKAM] = '11' then [USD_TUTAR] else 0 end) 'Kasim',
sum(case when [AYRAKAM] = '12' then [USD_TUTAR] else 0 end) 'Aralik',
sum([USD_TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_2019
WHERE MARKA='MCAFEE'
group by MARKA

UNION ALL
select MARKA,
sum(case when [AYRAKAM] = '01' then [USD_TUTAR] else 0 end) 'Ocak',
sum(case when [AYRAKAM] = '02' then [USD_TUTAR] else 0 end) 'Subat',
sum(case when [AYRAKAM] = '03' then [USD_TUTAR] else 0 end) 'Mart',
sum(case when [AYRAKAM] = '04' then [USD_TUTAR] else 0 end) 'Nisan',
sum(case when [AYRAKAM] = '05' then [USD_TUTAR] else 0 end) 'Mayis',
sum(case when [AYRAKAM] = '06' then [USD_TUTAR] else 0 end) 'Haziran',
sum(case when [AYRAKAM] = '07' then [USD_TUTAR] else 0 end) 'Temmuz',
sum(case when [AYRAKAM] = '08' then [USD_TUTAR] else 0 end) 'Agustos',
sum(case when [AYRAKAM] = '09' then [USD_TUTAR] else 0 end) 'Eylul',
sum(case when [AYRAKAM] = '10' then [USD_TUTAR] else 0 end) 'Ekim',
sum(case when [AYRAKAM] = '11' then [USD_TUTAR] else 0 end) 'Kasim',
sum(case when [AYRAKAM] = '12' then [USD_TUTAR] else 0 end) 'Aralik',
sum([USD_TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_2019
WHERE MARKA='SOPHOS'
group by MARKA

UNION ALL
select MARKA,
sum(case when [AYRAKAM] = '01' then [USD_TUTAR] else 0 end) 'Ocak',
sum(case when [AYRAKAM] = '02' then [USD_TUTAR] else 0 end) 'Subat',
sum(case when [AYRAKAM] = '03' then [USD_TUTAR] else 0 end) 'Mart',
sum(case when [AYRAKAM] = '04' then [USD_TUTAR] else 0 end) 'Nisan',
sum(case when [AYRAKAM] = '05' then [USD_TUTAR] else 0 end) 'Mayis',
sum(case when [AYRAKAM] = '06' then [USD_TUTAR] else 0 end) 'Haziran',
sum(case when [AYRAKAM] = '07' then [USD_TUTAR] else 0 end) 'Temmuz',
sum(case when [AYRAKAM] = '08' then [USD_TUTAR] else 0 end) 'Agustos',
sum(case when [AYRAKAM] = '09' then [USD_TUTAR] else 0 end) 'Eylul',
sum(case when [AYRAKAM] = '10' then [USD_TUTAR] else 0 end) 'Ekim',
sum(case when [AYRAKAM] = '11' then [USD_TUTAR] else 0 end) 'Kasim',
sum(case when [AYRAKAM] = '12' then [USD_TUTAR] else 0 end) 'Aralik',
sum([USD_TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_2019
WHERE MARKA='WATCHGUARD'
group by MARKA

UNION ALL
select MARKA,
sum(case when [AYRAKAM] = '01' then [USD_TUTAR] else 0 end) 'Ocak',
sum(case when [AYRAKAM] = '02' then [USD_TUTAR] else 0 end) 'Subat',
sum(case when [AYRAKAM] = '03' then [USD_TUTAR] else 0 end) 'Mart',
sum(case when [AYRAKAM] = '04' then [USD_TUTAR] else 0 end) 'Nisan',
sum(case when [AYRAKAM] = '05' then [USD_TUTAR] else 0 end) 'Mayis',
sum(case when [AYRAKAM] = '06' then [USD_TUTAR] else 0 end) 'Haziran',
sum(case when [AYRAKAM] = '07' then [USD_TUTAR] else 0 end) 'Temmuz',
sum(case when [AYRAKAM] = '08' then [USD_TUTAR] else 0 end) 'Agustos',
sum(case when [AYRAKAM] = '09' then [USD_TUTAR] else 0 end) 'Eylul',
sum(case when [AYRAKAM] = '10' then [USD_TUTAR] else 0 end) 'Ekim',
sum(case when [AYRAKAM] = '11' then [USD_TUTAR] else 0 end) 'Kasim',
sum(case when [AYRAKAM] = '12' then [USD_TUTAR] else 0 end) 'Aralik',
sum([USD_TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_2019
WHERE MARKA='VASCO'
group by MARKA

UNION ALL
select MARKA,
sum(case when [AYRAKAM] = '01' then [USD_TUTAR] else 0 end) 'Ocak',
sum(case when [AYRAKAM] = '02' then [USD_TUTAR] else 0 end) 'Subat',
sum(case when [AYRAKAM] = '03' then [USD_TUTAR] else 0 end) 'Mart',
sum(case when [AYRAKAM] = '04' then [USD_TUTAR] else 0 end) 'Nisan',
sum(case when [AYRAKAM] = '05' then [USD_TUTAR] else 0 end) 'Mayis',
sum(case when [AYRAKAM] = '06' then [USD_TUTAR] else 0 end) 'Haziran',
sum(case when [AYRAKAM] = '07' then [USD_TUTAR] else 0 end) 'Temmuz',
sum(case when [AYRAKAM] = '08' then [USD_TUTAR] else 0 end) 'Agustos',
sum(case when [AYRAKAM] = '09' then [USD_TUTAR] else 0 end) 'Eylul',
sum(case when [AYRAKAM] = '10' then [USD_TUTAR] else 0 end) 'Ekim',
sum(case when [AYRAKAM] = '11' then [USD_TUTAR] else 0 end) 'Kasim',
sum(case when [AYRAKAM] = '12' then [USD_TUTAR] else 0 end) 'Aralik',
sum([USD_TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_2019
WHERE MARKA='GEMALTO'
group by MARKA

UNION ALL
select MARKA,
sum(case when [AYRAKAM] = '01' then [USD_TUTAR] else 0 end) 'Ocak',
sum(case when [AYRAKAM] = '02' then [USD_TUTAR] else 0 end) 'Subat',
sum(case when [AYRAKAM] = '03' then [USD_TUTAR] else 0 end) 'Mart',
sum(case when [AYRAKAM] = '04' then [USD_TUTAR] else 0 end) 'Nisan',
sum(case when [AYRAKAM] = '05' then [USD_TUTAR] else 0 end) 'Mayis',
sum(case when [AYRAKAM] = '06' then [USD_TUTAR] else 0 end) 'Haziran',
sum(case when [AYRAKAM] = '07' then [USD_TUTAR] else 0 end) 'Temmuz',
sum(case when [AYRAKAM] = '08' then [USD_TUTAR] else 0 end) 'Agustos',
sum(case when [AYRAKAM] = '09' then [USD_TUTAR] else 0 end) 'Eylul',
sum(case when [AYRAKAM] = '10' then [USD_TUTAR] else 0 end) 'Ekim',
sum(case when [AYRAKAM] = '11' then [USD_TUTAR] else 0 end) 'Kasim',
sum(case when [AYRAKAM] = '12' then [USD_TUTAR] else 0 end) 'Aralik',
sum([USD_TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_2019
WHERE MARKA='F-SECURE'
group by MARKA

UNION ALL
select MARKA,
sum(case when [AYRAKAM] = '01' then [USD_TUTAR] else 0 end) 'Ocak',
sum(case when [AYRAKAM] = '02' then [USD_TUTAR] else 0 end) 'Subat',
sum(case when [AYRAKAM] = '03' then [USD_TUTAR] else 0 end) 'Mart',
sum(case when [AYRAKAM] = '04' then [USD_TUTAR] else 0 end) 'Nisan',
sum(case when [AYRAKAM] = '05' then [USD_TUTAR] else 0 end) 'Mayis',
sum(case when [AYRAKAM] = '06' then [USD_TUTAR] else 0 end) 'Haziran',
sum(case when [AYRAKAM] = '07' then [USD_TUTAR] else 0 end) 'Temmuz',
sum(case when [AYRAKAM] = '08' then [USD_TUTAR] else 0 end) 'Agustos',
sum(case when [AYRAKAM] = '09' then [USD_TUTAR] else 0 end) 'Eylul',
sum(case when [AYRAKAM] = '10' then [USD_TUTAR] else 0 end) 'Ekim',
sum(case when [AYRAKAM] = '11' then [USD_TUTAR] else 0 end) 'Kasim',
sum(case when [AYRAKAM] = '12' then [USD_TUTAR] else 0 end) 'Aralik',
sum([USD_TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_2019
WHERE MARKA='OBSERVEIT'
group by MARKA

UNION ALL
select MARKA,
sum(case when [AYRAKAM] = '01' then [USD_TUTAR] else 0 end) 'Ocak',
sum(case when [AYRAKAM] = '02' then [USD_TUTAR] else 0 end) 'Subat',
sum(case when [AYRAKAM] = '03' then [USD_TUTAR] else 0 end) 'Mart',
sum(case when [AYRAKAM] = '04' then [USD_TUTAR] else 0 end) 'Nisan',
sum(case when [AYRAKAM] = '05' then [USD_TUTAR] else 0 end) 'Mayis',
sum(case when [AYRAKAM] = '06' then [USD_TUTAR] else 0 end) 'Haziran',
sum(case when [AYRAKAM] = '07' then [USD_TUTAR] else 0 end) 'Temmuz',
sum(case when [AYRAKAM] = '08' then [USD_TUTAR] else 0 end) 'Agustos',
sum(case when [AYRAKAM] = '09' then [USD_TUTAR] else 0 end) 'Eylul',
sum(case when [AYRAKAM] = '10' then [USD_TUTAR] else 0 end) 'Ekim',
sum(case when [AYRAKAM] = '11' then [USD_TUTAR] else 0 end) 'Kasim',
sum(case when [AYRAKAM] = '12' then [USD_TUTAR] else 0 end) 'Aralik',
sum([USD_TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_2019
WHERE MARKA='TRUSTWAVE'
group by MARKA

UNION ALL
select MARKA,
sum(case when [AYRAKAM] = '01' then [USD_TUTAR] else 0 end) 'Ocak',
sum(case when [AYRAKAM] = '02' then [USD_TUTAR] else 0 end) 'Subat',
sum(case when [AYRAKAM] = '03' then [USD_TUTAR] else 0 end) 'Mart',
sum(case when [AYRAKAM] = '04' then [USD_TUTAR] else 0 end) 'Nisan',
sum(case when [AYRAKAM] = '05' then [USD_TUTAR] else 0 end) 'Mayis',
sum(case when [AYRAKAM] = '06' then [USD_TUTAR] else 0 end) 'Haziran',
sum(case when [AYRAKAM] = '07' then [USD_TUTAR] else 0 end) 'Temmuz',
sum(case when [AYRAKAM] = '08' then [USD_TUTAR] else 0 end) 'Agustos',
sum(case when [AYRAKAM] = '09' then [USD_TUTAR] else 0 end) 'Eylul',
sum(case when [AYRAKAM] = '10' then [USD_TUTAR] else 0 end) 'Ekim',
sum(case when [AYRAKAM] = '11' then [USD_TUTAR] else 0 end) 'Kasim',
sum(case when [AYRAKAM] = '12' then [USD_TUTAR] else 0 end) 'Aralik',
sum([USD_TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_2019
WHERE MARKA='SKYBOX'
group by MARKA

UNION ALL
select MARKA,
sum(case when [AYRAKAM] = '01' then [USD_TUTAR] else 0 end) 'Ocak',
sum(case when [AYRAKAM] = '02' then [USD_TUTAR] else 0 end) 'Subat',
sum(case when [AYRAKAM] = '03' then [USD_TUTAR] else 0 end) 'Mart',
sum(case when [AYRAKAM] = '04' then [USD_TUTAR] else 0 end) 'Nisan',
sum(case when [AYRAKAM] = '05' then [USD_TUTAR] else 0 end) 'Mayis',
sum(case when [AYRAKAM] = '06' then [USD_TUTAR] else 0 end) 'Haziran',
sum(case when [AYRAKAM] = '07' then [USD_TUTAR] else 0 end) 'Temmuz',
sum(case when [AYRAKAM] = '08' then [USD_TUTAR] else 0 end) 'Agustos',
sum(case when [AYRAKAM] = '09' then [USD_TUTAR] else 0 end) 'Eylul',
sum(case when [AYRAKAM] = '10' then [USD_TUTAR] else 0 end) 'Ekim',
sum(case when [AYRAKAM] = '11' then [USD_TUTAR] else 0 end) 'Kasim',
sum(case when [AYRAKAM] = '12' then [USD_TUTAR] else 0 end) 'Aralik',
sum([USD_TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_2019
WHERE MARKA='VEEAM'
group by MARKA

UNION ALL
select MARKA,
sum(case when [AYRAKAM] = '01' then [USD_TUTAR] else 0 end) 'Ocak',
sum(case when [AYRAKAM] = '02' then [USD_TUTAR] else 0 end) 'Subat',
sum(case when [AYRAKAM] = '03' then [USD_TUTAR] else 0 end) 'Mart',
sum(case when [AYRAKAM] = '04' then [USD_TUTAR] else 0 end) 'Nisan',
sum(case when [AYRAKAM] = '05' then [USD_TUTAR] else 0 end) 'Mayis',
sum(case when [AYRAKAM] = '06' then [USD_TUTAR] else 0 end) 'Haziran',
sum(case when [AYRAKAM] = '07' then [USD_TUTAR] else 0 end) 'Temmuz',
sum(case when [AYRAKAM] = '08' then [USD_TUTAR] else 0 end) 'Agustos',
sum(case when [AYRAKAM] = '09' then [USD_TUTAR] else 0 end) 'Eylul',
sum(case when [AYRAKAM] = '10' then [USD_TUTAR] else 0 end) 'Ekim',
sum(case when [AYRAKAM] = '11' then [USD_TUTAR] else 0 end) 'Kasim',
sum(case when [AYRAKAM] = '12' then [USD_TUTAR] else 0 end) 'Aralik',
sum([USD_TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_2019
WHERE MARKA='CHECKMARX'
group by MARKA

UNION ALL
select MARKA,
sum(case when [AYRAKAM] = '01' then [USD_TUTAR] else 0 end) 'Ocak',
sum(case when [AYRAKAM] = '02' then [USD_TUTAR] else 0 end) 'Subat',
sum(case when [AYRAKAM] = '03' then [USD_TUTAR] else 0 end) 'Mart',
sum(case when [AYRAKAM] = '04' then [USD_TUTAR] else 0 end) 'Nisan',
sum(case when [AYRAKAM] = '05' then [USD_TUTAR] else 0 end) 'Mayis',
sum(case when [AYRAKAM] = '06' then [USD_TUTAR] else 0 end) 'Haziran',
sum(case when [AYRAKAM] = '07' then [USD_TUTAR] else 0 end) 'Temmuz',
sum(case when [AYRAKAM] = '08' then [USD_TUTAR] else 0 end) 'Agustos',
sum(case when [AYRAKAM] = '09' then [USD_TUTAR] else 0 end) 'Eylul',
sum(case when [AYRAKAM] = '10' then [USD_TUTAR] else 0 end) 'Ekim',
sum(case when [AYRAKAM] = '11' then [USD_TUTAR] else 0 end) 'Kasim',
sum(case when [AYRAKAM] = '12' then [USD_TUTAR] else 0 end) 'Aralik',
sum([USD_TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_2019
WHERE MARKA='KOMTERA'
group by MARKA

UNION ALL
select MARKA,
sum(case when [AYRAKAM] = '01' then [USD_TUTAR] else 0 end) 'Ocak',
sum(case when [AYRAKAM] = '02' then [USD_TUTAR] else 0 end) 'Subat',
sum(case when [AYRAKAM] = '03' then [USD_TUTAR] else 0 end) 'Mart',
sum(case when [AYRAKAM] = '04' then [USD_TUTAR] else 0 end) 'Nisan',
sum(case when [AYRAKAM] = '05' then [USD_TUTAR] else 0 end) 'Mayis',
sum(case when [AYRAKAM] = '06' then [USD_TUTAR] else 0 end) 'Haziran',
sum(case when [AYRAKAM] = '07' then [USD_TUTAR] else 0 end) 'Temmuz',
sum(case when [AYRAKAM] = '08' then [USD_TUTAR] else 0 end) 'Agustos',
sum(case when [AYRAKAM] = '09' then [USD_TUTAR] else 0 end) 'Eylul',
sum(case when [AYRAKAM] = '10' then [USD_TUTAR] else 0 end) 'Ekim',
sum(case when [AYRAKAM] = '11' then [USD_TUTAR] else 0 end) 'Kasim',
sum(case when [AYRAKAM] = '12' then [USD_TUTAR] else 0 end) 'Aralik',
sum([USD_TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_2019
WHERE MARKA='USEROAM'
group by MARKA

UNION ALL
select MARKA,
sum(case when [AYRAKAM] = '01' then [USD_TUTAR] else 0 end) 'Ocak',
sum(case when [AYRAKAM] = '02' then [USD_TUTAR] else 0 end) 'Subat',
sum(case when [AYRAKAM] = '03' then [USD_TUTAR] else 0 end) 'Mart',
sum(case when [AYRAKAM] = '04' then [USD_TUTAR] else 0 end) 'Nisan',
sum(case when [AYRAKAM] = '05' then [USD_TUTAR] else 0 end) 'Mayis',
sum(case when [AYRAKAM] = '06' then [USD_TUTAR] else 0 end) 'Haziran',
sum(case when [AYRAKAM] = '07' then [USD_TUTAR] else 0 end) 'Temmuz',
sum(case when [AYRAKAM] = '08' then [USD_TUTAR] else 0 end) 'Agustos',
sum(case when [AYRAKAM] = '09' then [USD_TUTAR] else 0 end) 'Eylul',
sum(case when [AYRAKAM] = '10' then [USD_TUTAR] else 0 end) 'Ekim',
sum(case when [AYRAKAM] = '11' then [USD_TUTAR] else 0 end) 'Kasim',
sum(case when [AYRAKAM] = '12' then [USD_TUTAR] else 0 end) 'Aralik',
sum([USD_TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_2019
WHERE MARKA='ACRONIS'
group by MARKA

UNION ALL
select ' ',
  sum(case when [AYRAKAM] = '01' then [USD_TUTAR] else 0 end) 'Ocak',
  sum(case when [AYRAKAM] = '02' then [USD_TUTAR] else 0 end) 'Subat',
  sum(case when [AYRAKAM] = '03' then [USD_TUTAR] else 0 end) 'Mart',
  sum(case when [AYRAKAM] = '04' then [USD_TUTAR] else 0 end) 'Nisan',
  sum(case when [AYRAKAM] = '05' then [USD_TUTAR] else 0 end) 'Mayis',
  sum(case when [AYRAKAM] = '06' then [USD_TUTAR] else 0 end) 'Haziran',
  sum(case when [AYRAKAM] = '07' then [USD_TUTAR] else 0 end) 'Temmuz',
  sum(case when [AYRAKAM] = '08' then [USD_TUTAR] else 0 end) 'Agustos',
  sum(case when [AYRAKAM] = '09' then [USD_TUTAR] else 0 end) 'Eylul',
  sum(case when [AYRAKAM] = '10' then [USD_TUTAR] else 0 end) 'Ekim',
  sum(case when [AYRAKAM] = '11' then [USD_TUTAR] else 0 end) 'Kasim',
  sum(case when [AYRAKAM] = '12' then [USD_TUTAR] else 0 end) 'Aralik',
  sum([USD_TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_2019
SQL;
";
?>
