<div id="firsatlar" style="margin:auto;"></div>
<?php
include 'pq.php';
include '_conn.php';
$date1= $_GET['date1'];
$date2= $_GET['date2'];
$dates="  BASLANGIC_TARIHI>='$date1' AND BASLANGIC_TARIHI<='$date2'";

echo $dates;
die();

// where SIL<>'1' AND $dates ORDER BY BASLANGIC_TARIHI
$sql = "-- SQL Server 2017+ (ODBC uyumlu, TEXT -> NVARCHAR(MAX) CAST'leri eklendi)
WITH latest_kur AS (
    SELECT TOP (1) USD, EUR, tarih
    FROM aa_erp_kur
    ORDER BY tarih DESC
),
teklif_agg AS (
    SELECT 
        t.X_FIRSAT_NO,

        -- PDF=1 olan kalemlerin toplamı (TEKLIF_TIPI=1 filtreli)
        SUM(CASE WHEN t.PDF = 1 THEN tu.ADET * tu.B_SATIS_FIYATI ELSE 0 END) AS TUTAR_PDF1,

        -- SKU listesi
        STRING_AGG(CAST(tu.SKU AS varchar(10)), ',') AS SKULAR,

        -- Çözümler listesi (TEXT -> NVARCHAR(MAX))
        STRING_AGG(CAST(fl.cozum AS NVARCHAR(MAX)), ',') AS COZUMLER,

        -- Teklif numaraları, önce TEKLIF_TIPI
        STRING_AGG(CAST(t.TEKLIF_NO AS varchar(10)), ',') 
            WITHIN GROUP (ORDER BY t.TEKLIF_TIPI DESC, t.TEKLIF_NO) AS TEKLIFLER,

        -- SATIS_TIPI için min/max (etiketi türetmekte kullanacağız)
        MIN(CASE WHEN t.SATIS_TIPI = '0' THEN 0 WHEN t.SATIS_TIPI = '1' THEN 1 ELSE 2 END) AS MIN_SAT,
        MAX(CASE WHEN t.SATIS_TIPI = '0' THEN 0 WHEN t.SATIS_TIPI = '1' THEN 1 ELSE 2 END) AS MAX_SAT
    FROM aa_erp_kt_teklifler t
    LEFT JOIN aa_erp_kt_teklifler_urunler tu ON tu.X_TEKLIF_NO = t.TEKLIF_NO
    LEFT JOIN aa_erp_kt_fiyat_listesi fl     ON fl.SKU         = tu.SKU
    WHERE t.TEKLIF_TIPI = 1
    GROUP BY t.X_FIRSAT_NO
)

SELECT 
top 5
    f.id,
    -- SATIP etiketi
    CASE 
      WHEN ta.MIN_SAT IS NULL AND ta.MAX_SAT IS NULL THEN NULL
      WHEN ta.MIN_SAT <> ta.MAX_SAT THEN N'İlk Satış ve Yenileme'
      WHEN ta.MAX_SAT = 0 THEN N'İlk Satış'
      WHEN ta.MAX_SAT = 1 THEN N'Yenileme'
      ELSE N'İlk Satış ve Yenileme'
    END AS SATIP,

    f.MARKA_MANAGER,
    f.BAYI_YETKILI_ISIM,
    f.FIRSAT_NO,

    ta.SKULAR,
    ta.COZUMLER,
    ta.TEKLIFLER,

    ta.TUTAR_PDF1 AS TUTAR,
    f.PARA_BIRIMI,

    -- DLR_TUTAR: TRY→/USD, EUR→/(USD/EUR), USD→aynı
    CASE f.PARA_BIRIMI
      WHEN 'USD' THEN ta.TUTAR_PDF1
      WHEN 'TRY' THEN ta.TUTAR_PDF1 / NULLIF(k.USD, 0)
      WHEN 'EUR' THEN ta.TUTAR_PDF1 / NULLIF(k.USD / NULLIF(k.EUR,0), 0)
      ELSE 0
    END AS DLR_TUTAR,

    f.REGISTER,
    f.GELIS_KANALI,
    f.BASLANGIC_TARIHI,
    f.BITIS_TARIHI,
    MONTH(f.BITIS_TARIHI) AS BITIS_AY,
    f.REVIZE_TARIHI,

    CASE f.DURUM
      WHEN '-1' THEN N'Kaybedildi'
      WHEN '1'  THEN N'Kazanıldı'
      WHEN '0'  THEN N'Açık'
      ELSE N''
    END AS DURUM,

    f.KAYIDI_ACAN,
    f.MUSTERI_TEMSILCISI,
    f.MARKA,
    f.ETKINLIK,
    f.BAYI_ADI,
    f.BAYI_YETKILI_ISIM,
    f.MUSTERI_ADI,
    f.OLASILIK,
    f.KAYBEDILME_NEDENI,
    f.KAYBEDILME_NEDENI_DIGER,
    f.PROJE_ADI,
    f.FIRSAT_ACIKLAMA,

    -- NOTLAR (TEXT -> NVARCHAR(MAX)); DISTINCT/pencere fonksiyonu yok
    n.TNOTLAR

FROM LKS.dbo.aa_erp_kt_firsatlar f
LEFT JOIN teklif_agg ta ON ta.X_FIRSAT_NO = f.FIRSAT_NO
LEFT JOIN latest_kur  k ON 1 = 1
OUTER APPLY (
    SELECT TOP (1)
           CAST(t.NOTLAR AS NVARCHAR(MAX)) AS TNOTLAR
    FROM aa_erp_kt_teklifler t
    WHERE t.TEKLIF_TIPI = 1
      AND t.X_FIRSAT_NO = f.FIRSAT_NO
    ORDER BY t.TEKLIF_NO DESC
) n
order by f.id desc
";

//$sql='select top 1 * from aa_erp_kur';
try {
    $stmt = $conn->query($sql);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response = json_encode($data);
} catch (PDOException $e) {
    // SQL hatasını ekrana yazdır
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
?>
<script>

    var grid;
    $(function () {

        var colM = [
            {
                title: "<?php echo __('Durum','komtera'); ?>", editable: false, minWidth: 110, sortable: true, dataIndx: "DURUM", filter: {
                    crules: [{condition: 'range'}]
                }, render: function (ui) {
                    if (ui.cellData === '<?php echo __('Açık','komtera'); ?>') {
                        return {style: {"background": "#ebebeb"}};
                    } else if (ui.cellData === '<?php echo __('Kazanıldı','komtera'); ?>') {
                        return {style: {"background": "#b2f4ac"}};
                    } else if (ui.cellData === '<?php echo __('Kaybedildi','komtera'); ?>') {
                        return {style: {"background": "#f4acb8"}};
                    }
                }
            },
            {
                title: "<?php echo __('Fırsat','komtera'); ?>",
                render: function (ui) {
                    if (ui.rowData.FIRSAT_NO) {
                        return "<a href='#' class='demo_ac' onclick='FirsatAc(\"" + ui.rowData.FIRSAT_NO + "\")'>" + ui.rowData.FIRSAT_NO + "</a>";
                    }
                },
                exportRender: false,
                style: {'text-color': '#dd0000'},
                dataIndx: "FIRSAT_NO",
                align: "center",
                editable: false,
                minWidth: 60,
                sortable: false,
                filter: {
                    crules: [{condition: 'contain'}]
                }
            },

            {
                title: "R",
                render: function (ui) {
                    if (ui.rowData.REGISTER === '1') {
                        return "<span class='ui-icon ui-icon-check'></span>";
                    }
                },
                exportRender: false,
                style: {'text-color': '#dd0000'},
                dataIndx: "REGISTER",
                align: "center",
                editable: false,
                minWidth: 35,
                sortable: false,
                filter: {
                    crules: [{condition: 'range'}]
                }
            },


            {
                title: "<?php echo __('Teklifler','komtera'); ?>",
                exportRender: false,
                style: {'text-color': '#dd0000'},
                align: "left",
                editable: false,
                minWidth: 90,
                sortable: false,
                filter: {
                    crules: [{condition: 'contain'}]
                }
            }, {
                title: "<?php echo __('SKUlar','komtera'); ?>", filter: {
                    crules: [{condition: 'contain'}]
                }, editable: false, minWidth: 90, sortable: true, dataIndx: "skular"
            },
            {
                title: "<?php echo __('Cozumler','komtera'); ?>", filter: {
                    crules: [{condition: 'contain'}]
                }, editable: false, minWidth: 90, sortable: true, dataIndx: "Cozumler"
            },

            {
                title: "<?php echo __('Satis Tipi','komtera'); ?>", sortable: true, minWidth: 120, dataIndx: "SATIP",
                filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {
                title: "<?php echo __('Tarih','komtera'); ?>",
                sortable: true,
                minWidth: 80,
                dataIndx: "BASLANGIC_TARIHI",
                dataType: "date",
                format: 'dd.mm.yy'
            },
            {title: "<?php echo __('Son Değişiklik','komtera'); ?>", minWidth: 80, dataIndx: "REVIZE_TARIHI", dataType: "date", format: 'dd.mm.yy'},
            {title: "<?php echo __('Bitis Tarihi','komtera'); ?>", minWidth: 80, dataIndx: "BITIS_TARIHI", dataType: "date", format: 'dd.mm.yy'},
            {
                title: "<?php echo __('BitisAY','komtera'); ?>",
                hidden: false,
                editable: false,
                minWidth: 70,
                sortable: true,
                dataIndx: "BITIS_AY",
                filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {
                title: "<?php echo __('Marka','komtera'); ?>",
                hidden: false,
                editable: false,
                minWidth: 110,
                sortable: true,
                dataIndx: "MARKA",
                filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {
                title: "<?php echo __('Mar.Man.','komtera'); ?>",
                hidden: false,
                editable: false,
                minWidth: 110,
                sortable: true,
                dataIndx: "MARKA_MANAGER",
                filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {
                title: "ID", hidden: true, editable: false, minWidth: 110, sortable: true, dataIndx: "id", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {
                title: "<?php echo __('Kayıdı Açan','komtera'); ?>",
                render: function (ui) {
                    if (ui.cellData === '') {
                        return {style: {"background": "yellow"}};
                    }
                },
                editable: false, minWidth: 120, sortable: true, dataIndx: "KAYIDI_ACAN", filter: {
                    crules: [{condition: 'range'}],
                }
            },
            {
                title: "<?php echo __('Müşteri Temsilcisi','komtera'); ?>",
                render: function (ui) {
                    if (ui.cellData === '') {
                        return {style: {"background": "yellow"}};
                    }
                },
                editable: false, minWidth: 120, sortable: true, dataIndx: "MUSTERI_TEMSILCISI", filter: {
                    crules: [{condition: 'range'}],
                }
            },
//            {title: "SKU", editable: false, minWidth: 120, sortable: true, dataIndx: "SKU",filter: {
//                        crules: [{condition: 'contain'}],
//                    }
//            },
//            {title: "Seri No", editable: false, minWidth: 140, sortable: true, dataIndx: "SERIAL_NO",filter: {
//                        crules: [{condition: 'contain'}]
//                    }
//            },
//            {title: "Bitiş", align: "center",dataType: 'date', format: 'dd-mm-yy', editable: false, minWidth: 80, sortable: true, dataIndx: "BITIS_TARIHI",
//            },
            {
                title: "Tutar", exportRender: true, dataType: "float", render: function (ui) {
                    if (ui.cellData === null) {
                        return {style: {"background": "#FF8888"}};
                    }
                }, align: "right", format: "#.###,00", editable: false, minWidth: 90, sortable: true, dataIndx: "TUTAR"
            },
            {title: "", editable: false, minWidth: 40, sortable: true, dataIndx: "PARA_BIRIMI"},
            {
                title: "USD",
                exportRender: true,
                dataType: "float",
                render: function (ui) {
                    if (ui.cellData === null) {
                        return {style: {"background": "#FF8888"}};
                    }
                },
                summary: {type: "sum", edit: true},
                align: "right",
                format: "#.###,00",
                editable: false,
                minWidth: 90,
                sortable: true,
                dataIndx: "DLR_TUTAR"
            },
            {
                title: "<?php echo __('Bayi','komtera'); ?>", editable: false, minWidth: 220, sortable: true, dataIndx: "BAYI_ADI", filter: {
                    crules: [{condition: 'contain'}]
                }
            }
            ,
            {
                title: "<?php echo __('Bayi Yetkili','komtera'); ?>",
                hidden: false,
                editable: false,
                minWidth: 110,
                sortable: true,
                dataIndx: "BAYI_YETKILI_ISIM",
                filter: {
                    crules: [{condition: 'range'}]
                }
            },
//     {title: "Bayi Yetkili", editable: false, minWidth: 120, sortable: true, dataIndx: "BAYI_YETKILI_ISIM", filter: {
//         crules: [{condition: 'contain'}]
//     }
// },
            {
                title: "<?php echo __('Müşteri','komtera'); ?>", editable: false, minWidth: 220, sortable: true, dataIndx: "MUSTERI_ADI", filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {
                title: "<?php echo __('Olasılık','komtera'); ?>", filter: {
                    crules: [{condition: 'range'}]
                }, minWidth: 210, dataIndx: "OLASILIK",
                render: function (ui) {
                    if (ui.cellData === '1-Discovery') {
                        return {style: {"background": "#FF2611"}};
                    } else if (ui.cellData === '2-Solution Mapping') {
                        return {style: {"background": "#FD7038"}};
                    } else if (ui.cellData === '3-Demo/POC') {
                        return {style: {"background": "#FFFF33"}};
                    } else if (ui.cellData === '4-Negotiation') {
                        return {style: {"background": "#A3D879"}};
                    } else if (ui.cellData === '5-Confirmed/Waiting for End-User PO') {
                        return {style: {"background": "#548E28"}};
                    } else if (ui.cellData === '6-Run Rate') {
                        return {style: {"background": "#AE00F0"}};
                    }
                }
            },
            {
                title: "<?php echo __('Gelis Kanali','komtera'); ?>",
                editable: false,
                minWidth: 160,
                sortable: true,
                dataIndx: "GELIS_KANALI",
                filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {
                title: "<?php echo __('Etkinlik','komtera'); ?>", editable: false, minWidth: 150, sortable: true, dataIndx: "ETKINLIK", filter: {
                    crules: [{condition: 'contain'}]
                }
            }, {
                title: "<?php echo __('Proje Adı','komtera'); ?>", editable: false, minWidth: 150, sortable: true, dataIndx: "PROJE_ADI", filter: {
                    crules: [{condition: 'contain'}]
                }
            }, {
                title: "<?php echo __('Fırsat Açıklama','komtera'); ?>",
                editable: false,
                minWidth: 250,
                sortable: true,
                dataIndx: "FIRSAT_ACIKLAMA",
                filter: {
                    crules: [{condition: 'contain'}]
                }
            },
            {
                title: "<?php echo __('Notlar','komtera'); ?>", editable: false, minWidth: 150, sortable: true, dataIndx: "TNOTLAR", filter: {
                    crules: [{condition: 'contain'}]
                }
            }
        ];

        var data =<?PHP echo $response;?>;
        var obj = {
            width: "1400px",
            height: 400,
            resizable: true,
            title: "<?php echo __('Firsatlar','komtera'); ?>",
            showBottom: false,
            colModel: colM,
            groupModel: {
                on: true,
                merge: true,
                dataIndx: ['DURUM'],
                showSummary: [true],
                grandSummary: true,
                collapsed: [false],
                title: [
                    "{0} ({1})",
                    "{0} - {1}"
                ]
            },
            dataModel: {
                data: data
            }
        };

        grid = pq.grid("#firsatlar", obj);
        grid.toggle();

    });




</script>
