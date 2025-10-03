<?PHP
$sql = "select kullanici,
  sum(case when [AYRAKAM] = '01' then [USD TUTAR] else 0 end) 'Ocak',
  sum(case when [AYRAKAM] = '02' then [USD TUTAR] else 0 end) 'Subat',
  sum(case when [AYRAKAM] = '03' then [USD TUTAR] else 0 end) 'Mart',
  sum(case when [AYRAKAM] = '04' then [USD TUTAR] else 0 end) 'Nisan',
  sum(case when [AYRAKAM] = '05' then [USD TUTAR] else 0 end) 'Mayis',
  sum(case when [AYRAKAM] = '06' then [USD TUTAR] else 0 end) 'Haziran',
  sum(case when [AYRAKAM] = '07' then [USD TUTAR] else 0 end) 'Temmuz',
  sum(case when [AYRAKAM] = '08' then [USD TUTAR] else 0 end) 'Agustos',
  sum(case when [AYRAKAM] = '09' then [USD TUTAR] else 0 end) 'Eylul',
  sum(case when [AYRAKAM] = '10' then [USD TUTAR] else 0 end) 'Ekim',
  sum(case when [AYRAKAM] = '11' then [USD TUTAR] else 0 end) 'Kasim',
  sum(case when [AYRAKAM] = '12' then [USD TUTAR] else 0 end) 'Aralik',
  sum([USD TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_2017
group by kullanici
UNION ALL
select '',
  sum(case when [AYRAKAM] = '01' then [USD TUTAR] else 0 end) 'Ocak',
  sum(case when [AYRAKAM] = '02' then [USD TUTAR] else 0 end) 'Subat',
  sum(case when [AYRAKAM] = '03' then [USD TUTAR] else 0 end) 'Mart',
  sum(case when [AYRAKAM] = '04' then [USD TUTAR] else 0 end) 'Nisan',
  sum(case when [AYRAKAM] = '05' then [USD TUTAR] else 0 end) 'Mayis',
  sum(case when [AYRAKAM] = '06' then [USD TUTAR] else 0 end) 'Haziran',
  sum(case when [AYRAKAM] = '07' then [USD TUTAR] else 0 end) 'Temmuz',
  sum(case when [AYRAKAM] = '08' then [USD TUTAR] else 0 end) 'Agustos',
  sum(case when [AYRAKAM] = '09' then [USD TUTAR] else 0 end) 'Eylul',
  sum(case when [AYRAKAM] = '10' then [USD TUTAR] else 0 end) 'Ekim',
  sum(case when [AYRAKAM] = '11' then [USD TUTAR] else 0 end) 'Kasim',
  sum(case when [AYRAKAM] = '12' then [USD TUTAR] else 0 end) 'Aralik',
  sum([USD TUTAR]) 'TOPLAM'
from ERP_SATIS_ANALIZ_2017
";
?>
