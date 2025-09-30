<?php


echo "deneme";

include '../pqgrid/';

?>

<div id="rapor_53"></div><br/>
<div id="rapor_53_2"></div><br/>
<div id="rapor_53_3"></div>
<?php
$yil=date("Y");

$tableName = getTableName('ERP_SATIS_ANALIZ_319_20XX');
$sqlstring = "select MARKA,
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
from $tableName
WHERE Yil=$yil
group by MARKA";
$stmt = $conn->prepare($sqlstring);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<script>
    var grid;
    $(function () {
        var colM = [
            {
                title: "MARKA", minWidth: 110, dataIndx: "MARKA"
            },
            <?PHP
            $aylar = array("Ocak", "Subat", "Mart", "Nisan", "Mayis", "Haziran", "Temmuz", "Agustos", "Eylul", "Ekim", "Kasim", "Aralik");
            foreach ($aylar as $ay) {
            ?>
            {
                title: "<?PHP echo $ay; ?>", exportRender: true, dataType: "float",
                summary: {type: "sum", edit: true},
                align: "right", format: "#.###",
                editable: false, minWidth: 70, sortable: true, dataIndx: "<?PHP echo $ay; ?>"
            },
            <?PHP
            }
            ?>
            {
                title: "Toplam", exportRender: true, dataType: "float",
                summary: {type: "sum", edit: true},
                align: "right", format: "#.###",
                editable: false, minWidth: 70, sortable: true, dataIndx: "TOPLAM"
            },
        ];
        var data = <?php echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>;
        var obj = {
            toolbar: {
                items: [
                    {
                        type: 'button',
                        label: "Excele At",
                        icon: 'ui-icon-arrowthickstop-1-s',
                        listener: function () {
                            ExcelKaydet('satis_marka_komisyonlu');
                        }
                    }
                ]
            },
            showHeader: true,
            groupModel: {
                on: true,
                merge: true,
                showSummary: [true],
                grandSummary: true,
                collapsed: [false],
                title: [
                    "{0} ({1})",
                    "{0} - {1}"
                ]
            },
            showTitle: true,
            selectionModel: {type: 'cell'},
            editable: false,
            //groupModel: {on: true, dataIndx: ["MARKA"] },
            showToolbar: true,
            summaryTitle: "",
            showTop: true,
            width: "1100",
            height: 'flex',
            colModel: colM,
            resizable: true,
            title: "<span style='font-size: 150%;'><b><?php echo __('Satış Marka Komisyonlu','komtera'); ?></b></span>",
            showBottom: false,
            scrollModel: {autoFit: false},
            dataModel: {data: data}
        };
        grid = pq.grid("div#rapor_53", obj);
    });
</script>
<?PHP
$tableName1 = getTableName('ERP_SATIS_ANALIZ_319_20XX');
$tableName2 = getTableName('aaaa_erp_kt_komisyon_raporu_ham');
$sqlstring = "select s1.MARKA,
       s1.Ocak - COALESCE(s2.Ocak, 0) AS Ocak,
       s1.Subat - COALESCE(s2.Subat, 0) AS Subat,
       s1.Mart - COALESCE(s2.Mart, 0) AS Mart,
       s1.Nisan - COALESCE(s2.Nisan, 0) AS Nisan,
       s1.Mayis - COALESCE(s2.Mayis, 0) AS Mayis,
       s1.Haziran - COALESCE(s2.Haziran, 0) AS Haziran,
       s1.Temmuz - COALESCE(s2.Temmuz, 0) AS Temmuz,
       s1.Agustos - COALESCE(s2.Agustos, 0) AS Agustos,
       s1.Eylul - COALESCE(s2.Eylul, 0) AS Eylul,
       s1.Ekim - COALESCE(s2.Ekim, 0) AS Ekim,
       s1.Kasim - COALESCE(s2.Kasim, 0) AS Kasim,
       s1.Aralik - COALESCE(s2.Aralik, 0) AS Aralik,
       s1.TOPLAM - COALESCE(s2.TOPLAM, 0) AS TOPLAM
FROM
    (SELECT
         MARKA,
         SUM(CASE WHEN [AYRAKAM] = '01' THEN [USD_TUTAR] ELSE 0 END) AS Ocak,
         SUM(CASE WHEN [AYRAKAM] = '02' THEN [USD_TUTAR] ELSE 0 END) AS Subat,
         SUM(CASE WHEN [AYRAKAM] = '03' THEN [USD_TUTAR] ELSE 0 END) AS Mart,
         SUM(CASE WHEN [AYRAKAM] = '04' THEN [USD_TUTAR] ELSE 0 END) AS Nisan,
         SUM(CASE WHEN [AYRAKAM] = '05' THEN [USD_TUTAR] ELSE 0 END) AS Mayis,
         SUM(CASE WHEN [AYRAKAM] = '06' THEN [USD_TUTAR] ELSE 0 END) AS Haziran,
         SUM(CASE WHEN [AYRAKAM] = '07' THEN [USD_TUTAR] ELSE 0 END) AS Temmuz,
         SUM(CASE WHEN [AYRAKAM] = '08' THEN [USD_TUTAR] ELSE 0 END) AS Agustos,
         SUM(CASE WHEN [AYRAKAM] = '09' THEN [USD_TUTAR] ELSE 0 END) AS Eylul,
         SUM(CASE WHEN [AYRAKAM] = '10' THEN [USD_TUTAR] ELSE 0 END) AS Ekim,
         SUM(CASE WHEN [AYRAKAM] = '11' THEN [USD_TUTAR] ELSE 0 END) AS Kasim,
         SUM(CASE WHEN [AYRAKAM] = '12' THEN [USD_TUTAR] ELSE 0 END) AS Aralik,
         SUM([USD_TUTAR]) AS TOPLAM
     FROM $tableName1
     WHERE Yil = $yil
     GROUP BY MARKA) AS s1
        LEFT JOIN
    (SELECT
         MARKA,
         SUM(CASE WHEN MONTH(FATTAR) = 1 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Ocak,
         SUM(CASE WHEN MONTH(FATTAR) = 2 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Subat,
         SUM(CASE WHEN MONTH(FATTAR) = 3 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Mart,
         SUM(CASE WHEN MONTH(FATTAR) = 4 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Nisan,
         SUM(CASE WHEN MONTH(FATTAR) = 5 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Mayis,
         SUM(CASE WHEN MONTH(FATTAR) = 6 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Haziran,
         SUM(CASE WHEN MONTH(FATTAR) = 7 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Temmuz,
         SUM(CASE WHEN MONTH(FATTAR) = 8 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Agustos,
         SUM(CASE WHEN MONTH(FATTAR) = 9 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Eylul,
         SUM(CASE WHEN MONTH(FATTAR) = 10 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Ekim,
         SUM(CASE WHEN MONTH(FATTAR) = 11 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Kasim,
         SUM(CASE WHEN MONTH(FATTAR) = 12 THEN COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) ELSE 0 END) AS Aralik,
         SUM(COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0)) AS TOPLAM
     FROM $tableName2
     WHERE YEAR(FATTAR) = 2025 AND FATTAR IS NOT NULL
     GROUP BY MARKA) AS s2
    ON s1.MARKA = s2.MARKA";

$stmt = $conn->prepare($sqlstring);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<script>
    var grid;
    $(function () {
        var colM = [
            {
                title: "MARKA", minWidth: 110, dataIndx: "MARKA"
            },
            <?PHP
            $aylar = array("Ocak", "Subat", "Mart", "Nisan", "Mayis", "Haziran", "Temmuz", "Agustos", "Eylul", "Ekim", "Kasim", "Aralik");
            foreach ($aylar as $ay) {
            ?>
            {
                title: "<?PHP echo $ay; ?>", exportRender: true, dataType: "float",
                summary: {type: "sum", edit: true},
                align: "right", format: "#.###",
                editable: false, minWidth: 70, sortable: true, dataIndx: "<?PHP echo $ay; ?>"
            },
            <?PHP
            }
            ?>
            {
                title: "Toplam", exportRender: true, dataType: "float",
                summary: {type: "sum", edit: true},
                align: "right", format: "#.###",
                editable: false, minWidth: 70, sortable: true, dataIndx: "TOPLAM"
            },
        ];
        var data = <?php echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>;
        var obj = {
            toolbar: {
                items: [
                    {
                        type: 'button',
                        label: "Excele At",
                        icon: 'ui-icon-arrowthickstop-1-s',
                        listener: function () {
                            ExcelKaydet('satis_marka_komisyonsuz');
                        }
                    }
                ]
            },
            showHeader: true,
            groupModel: {
                on: true,
                merge: true,
                showSummary: [true],
                grandSummary: true,
                collapsed: [false],
                title: [
                    "{0} ({1})",
                    "{0} - {1}"
                ]
            },
            showTitle: true,
            selectionModel: {type: 'cell'},
            editable: false,
            //groupModel: {on: true, dataIndx: ["MARKA"] },
            showToolbar: true,
            summaryTitle: "",
            showTop: true,
            width: "1100",
            height: 'flex',
            colModel: colM,
            resizable: true,
            title: "<span style='font-size: 150%;'><b><?php echo __('Satış Marka Komisyonsuz','komtera'); ?></b></span>",
            showBottom: false,
            scrollModel: {autoFit: false},
            dataModel: {data: data}
        };
        grid = pq.grid("div#rapor_53_2", obj);
    });
</script>
<?PHP
$tableName3 = getTableName('aaaa_erp_kt_komisyon_raporu_ham');
$sqlstring = "select
    MARKA,
    sum(case when month(FATTAR) = 1 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Ocak',
     sum(case when month(FATTAR) = 2 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Subat',
     sum(case when month(FATTAR) = 3 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Mart',
     sum(case when month(FATTAR) = 4 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Nisan',
     sum(case when month(FATTAR) = 5 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Mayis',
     sum(case when month(FATTAR) = 6 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Haziran',
     sum(case when month(FATTAR) = 7 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Temmuz',
     sum(case when month(FATTAR) = 8 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Agustos',
     sum(case when month(FATTAR) = 9 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Eylul',
     sum(case when month(FATTAR) = 10 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Ekim',
     sum(case when month(FATTAR) = 11 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Kasim',
     sum(case when month(FATTAR) = 12 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Aralik',
     sum(COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0)) 'TOPLAM'
from $tableName3
where year(FATTAR)=2025
group by MARKA
UNION ALL
select
    'TOPLAM',
    sum(case when month(FATTAR) = 1 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Ocak',
     sum(case when month(FATTAR) = 2 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Subat',
     sum(case when month(FATTAR) = 3 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Mart',
     sum(case when month(FATTAR) = 4 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Nisan',
     sum(case when month(FATTAR) = 5 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Mayis',
     sum(case when month(FATTAR) = 6 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Haziran',
     sum(case when month(FATTAR) = 7 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Temmuz',
     sum(case when month(FATTAR) = 8 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Agustos',
     sum(case when month(FATTAR) = 9 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Eylul',
     sum(case when month(FATTAR) = 10 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Ekim',
     sum(case when month(FATTAR) = 11 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Kasim',
     sum(case when month(FATTAR) = 12 then COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0) else 0 end) 'Aralik',
     sum(COALESCE(KOMISYON_F1, 0) + COALESCE(KOMISYON_F2, 0) + COALESCE(KOMISYON_H, 0)) 'TOPLAM'
from $tableName3
where year(FATTAR)=2025";

$stmt = $conn->prepare($sqlstring);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<script>
    var grid;
    $(function () {
        var colM = [
            {
                title: "MARKA", minWidth: 110, dataIndx: "MARKA"
            },
            <?PHP
            $aylar = array("Ocak", "Subat", "Mart", "Nisan", "Mayis", "Haziran", "Temmuz", "Agustos", "Eylul", "Ekim", "Kasim", "Aralik");
            foreach ($aylar as $ay) {
            ?>
            {
                title: "<?PHP echo $ay; ?>", exportRender: true, dataType: "float",
                summary: {type: "sum", edit: true},
                align: "right", format: "#.###",
                editable: false, minWidth: 70, sortable: true, dataIndx: "<?PHP echo $ay; ?>"
            },
            <?PHP
            }
            ?>
            {
                title: "Toplam", exportRender: true, dataType: "float",
                summary: {type: "sum", edit: true},
                align: "right", format: "#.###",
                editable: false, minWidth: 70, sortable: true, dataIndx: "TOPLAM"
            },
        ];
        var data = <?php echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>;
        var obj = {
            toolbar: {
                items: [
                    {
                        type: 'button',
                        label: "Excele At",
                        icon: 'ui-icon-arrowthickstop-1-s',
                        listener: function () {
                            ExcelKaydet('satis_marka_komisyonsuz');
                        }
                    }
                ]
            },
            showHeader: true,
            groupModel: {
                on: true,
                merge: true,
                showSummary: [true],
                grandSummary: true,
                collapsed: [false],
                title: [
                    "{0} ({1})",
                    "{0} - {1}"
                ]
            },
            showTitle: true,
            selectionModel: {type: 'cell'},
            editable: false,
            //groupModel: {on: true, dataIndx: ["MARKA"] },
            showToolbar: true,
            summaryTitle: "",
            showTop: true,
            width: "1100",
            height: 'flex',
            colModel: colM,
            resizable: true,
            title: "<span style='font-size: 150%;'><b><?php echo __('Satış Marka Komisyon','komtera'); ?></b></span>",
            showBottom: false,
            scrollModel: {autoFit: false},
            dataModel: {data: data}
        };
        grid = pq.grid("div#rapor_53_3", obj);
    });
</script>