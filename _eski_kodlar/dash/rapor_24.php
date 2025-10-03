<?PHP
$sql = "select MTLATIN,
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
from ERP_SATIS_ANALIZ_20XX where MTLATIN<>''
AND Yil='2021'
group by MTLATIN
UNION ALL
select '',
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
from ERP_SATIS_ANALIZ_20XX where MTLATIN<>''
AND Yil='2021'
";
?>
