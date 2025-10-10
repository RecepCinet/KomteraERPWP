<script>
    var say = 0;
    function saveChanges() {
        if (!$.active && !grid.getEditCell().$cell && grid.isDirty() && grid.isValidChange({allowInvalid: true}).valid) {
            var gridChanges = grid.getChanges({format: 'byVal'});
            $.ajax({
                //url: '_tablolar/tickets_edit.php', //for ASP.NET, java
                url: '_tablolar/kt_teklif_urunler.php?pq_batch=1',
                data: {
                    list: JSON.stringify(gridChanges)
                },
                dataType: "json",
                type: "POST",
                async: true,
                beforeSend: function (jqXHR, settings) {
                    grid.option("strLoading", "Saving..");
                    grid.showLoading();
                },
                success: function (changes) {
                    //commit the changes.                
                    grid.commit({type: 'add', rows: changes.addList});
                    grid.commit({type: 'update', rows: changes.updateList});
                    grid.commit({type: 'delete', rows: changes.deleteList});
                },
                complete: function () {
                    grid.hideLoading();
                    grid.option("strLoading", $.paramquery.pqGrid.defaults.strLoading);
                    FileMaker.PerformScriptWithOption("Teklif", "refresh");
                }
            });
        }
    }
    interval = setInterval(saveChanges, 1000);
    function dateEditor(ui) {
        var $inp = ui.$cell.find("input"),
                grid = this,
                format = ui.column.format || "yy-mm-dd",
                val = $.datepicker.formatDate(format, new Date($inp.val()));
        $inp
                .attr('readonly', 'readonly')
                .val(val)
                .datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: format,
                    showAnim: '',
                    onSelect: function () {
                        this.firstOpen = true;
                    },
                    beforeShow: function (input, inst) {
                        setTimeout(function () {
                            $('.ui-datepicker').css('z-index', 999999999999);
                        });
                        return !this.firstOpen;
                    },
                    onClose: function () {
                        this.focus();
                    }
                });
    }

    function repeatString(str, num) {
        out = '';
        for (var i = 0; i < num; i++) {
            out += str;
        }
        return out;
    }

    function dump(v, howDisplay, recursionLevel) {
        howDisplay = (typeof howDisplay === 'undefined') ? "alert" : howDisplay;
        recursionLevel = (typeof recursionLevel !== 'number') ? 0 : recursionLevel;
        var vType = typeof v;
        var out = vType;
        switch (vType) {
            case "number":
                /* there is absolutely no way in JS to distinguish 2 from 2.0
                 so 'number' is the best that you can do. The following doesn't work:
                 var er = /^[0-9]+$/;
                 if (!isNaN(v) && v % 1 === 0 && er.test(3.0)) {
                 out = 'int';
                 }
                 */
                break;
            case "boolean":
                out += ": " + v;
                break;
            case "string":
                out += "(" + v.length + '): "' + v + '"';
                break;
            case "object":
                //check if null
                if (v === null) {
                    out = "null";
                }
                //If using jQuery: if ($.isArray(v))
                //If using IE: if (isArray(v))
                //this should work for all browsers according to the ECMAScript standard:
                else if (Object.prototype.toString.call(v) === '[object Array]') {
                    out = 'array(' + v.length + '): {\n';
                    for (var i = 0; i < v.length; i++) {
                        out += repeatString('   ', recursionLevel) + "   [" + i + "]:  " +
                                dump(v[i], "none", recursionLevel + 1) + "\n";
                    }
                    out += repeatString('   ', recursionLevel) + "}";
                } else {
                    //if object
                    let sContents = "{\n";
                    let cnt = 0;
                    for (var member in v) {
                        //No way to know the original data type of member, since JS
                        //always converts it to a string and no other way to parse objects.
                        sContents += repeatString('   ', recursionLevel) + "   " + member +
                                ":  " + dump(v[member], "none", recursionLevel + 1) + "\n";
                        cnt++;
                    }
                    sContents += repeatString('   ', recursionLevel) + "}";
                    out += "(" + cnt + "): " + sContents;
                }
                break;
            default:
                out = v;
                break;
        }

        if (howDisplay == 'body') {
            var pre = document.createElement('pre');
            pre.innerHTML = out;
            document.body.appendChild(pre);
        } else if (howDisplay == 'alert') {
            alert(out);
        }

        return out;
    }
</script>
<?PHP
$teklif_id = $_GET['teklif_id'];
$izin = "true";
$stmt = $conn->prepare("select top 1 KILIT,SATIS_TIPI from aa_erp_kt_teklifler where TEKLIF_NO=:teklif_no");
$stmt->execute(['teklif_no' => $teklif_id]);
$gelen = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
if ($gelen['KILIT'] === "1") {
    $izin = "false";
}

?>
<script>
    function UrunDuzenle(teklif_no, id) {
        FileMaker.PerformScriptWithOption("Teklif", "urun_duzenle" + "\n" + id + "\n" + teklif_no);
    }
    function SatisTipiDuzenle(id) {
        FileMaker.PerformScriptWithOption("Teklif", "teklif_tipi_duzenle" + "\n" + id + "\n");
    }
    function UrunTasi(yon, teklif_no, sira, id, fuck) {
        //alert(sira + " - " + id);
        //return;
        $.get("_tinywin/urun_tasi.php?yon=" + yon + "&teklif_no=" + teklif_no + "&id=" + id + "&sira=" + sira, function (data) {
            refreshDataAndView();
        });
    }
    function sil(ff) {
        if (confirm('Ürünü silmek istediğiniziden emin misiniz?')) {
            $.get("_tinywin/urun_sil.php?bb=" + ff, function (data) {
                if (data === "NOK,KILIT") {
                    alert('Teklif Kilitli!');
                } else {
                    refreshDataAndView();
                    //FileMaker.PerformScriptWithOption("Teklif", "refresh");
                }
            });
        } else {
            //
        }
    }
    var grid;
    $(function () {
        var colM = [
            {title: "idd", hidden: true, editable: false, minWidth: 50, sortable: false, dataIndx: "id"},
            {title: "s", align: "center", formatter: "number", hidden: false, editable: false, minWidth: 30, sortable: false, dataIndx: "SIRA"},
            {title: "ti", hidden: true, editable: false, minWidth: 50, sortable: false, dataIndx: "X_TEKLIF_NO"},
            {title: "", summary: {type: "", edit: true}, align: "center", hidden: false, editable: false, minWidth: 50, sortable: false, dataIndx: "id",
                render: function (ui) {
                    var out = '';
                    if (ui.rowData.id !== undefined) {
                        if (ui.rowIndx > 0) {
                            out += '<a href="#" onclick="UrunTasi(0,\'' + ui.rowData.X_TEKLIF_NO + '\',' + ui.rowData.SIRA + ',' + ui.rowData.id + ',' + ui.getRowIndx + ');"><span class="ui-icon ui-icon-arrowthick-1-n"></span></a>';
                        }
                        out += '<a href="#" onclick="UrunTasi(1,\'' + ui.rowData.X_TEKLIF_NO + '\',' + ui.rowData.SIRA + ',' + ui.rowData.id + ',' + ui.getRowIndx + ');"><span class="ui-icon ui-icon-arrowthick-1-s"></span></a>';
                        return out;
                    }
                }
            },

            {title: "SLot", hidden: false, editable: true, minWidth: 50, sortable: false, dataIndx: "TRACK_TYPE"},
            {title: "SKU",styleHead: {'height': '150px'}, hidden: false, editable: true, minWidth: 110, sortable: false, dataIndx: "SKU"},
            {title: "Açıklama", hidden: false, editable: true, minWidth: 255, sortable: false, dataIndx: "ACIKLAMA"},
            //{title: "MS", hidden: false, editable: true, minWidth: 55, sortable: false, dataIndx: "MCSURE"},
            {title: "Tip", hidden: false, editable: false, minWidth: 65, sortable: false, dataIndx: "TIP"},
            {title: "STip", style: {"background": "#C2E7D1"}, hidden: true, editable: true, minWidth: 95, sortable: false, dataIndx: "SATIS_TIPI",
            render: function (ui) {
                    var out = '';
                    var yazi='İlk Satış';
                    if (ui.rowData.SATIS_TIPI==1) {
                    	yazi='Yenileme';
                    }
                    
                    if (ui.rowData.id !== undefined) {
                        out += '<a href="#" onclick="SatisTipiDuzenle(' + ui.rowData.id + ');">' + yazi + '</span> </a>';
                    }
                    return out;
                }
            },

            {
                title: "Mevcut Lisans",
                hidden: false,
                editable: true,
                minWidth: 80,
                sortable: false,
                dataIndx: "MEVCUT_LISANS"
                // render: function (ui) {
                //     if (ui.cellData === null || ui.cellData === "") {
                //         return { style: "background-color:red;" };
                //     }
                // }
            },
//                         {title: "STip", style: {"background": "#C2E7D1"}, hidden: true, editable: true, minWidth: 95, sortable: false, dataIndx: "SATIS_TIPI",
//         editor: {
//             options: ['İlk Satış', 'Yenileme'],
//             type: function (ui) {
//                 //debugger;
//                 say = -1;
//                 var options = ui.column.editor.options,
//                         str = options.map(function (option) {
//                             //var checked = (option == ui.cellData) ? 'checked = checked': '';
//                             var checked = "";
//                             if ((option === "İlk Satış" && ui.cellData === 0) || (option === "Yenileme" && ui.cellData === 1)) {
//                                 checked = "checked = checked";
//                             }
//                             say++;
//                             return "<input type='radio' " + checked + " name='" + ui.dataIndx + "' style='margin-left:5px;' value='" + say + "'>  " + option + "<br />";
//                         }).join("");
//                 ui.$cell.append("<div class='pq-editor-focus' tabindex='0' style='padding:5px;margin-top:1px;'>" + str + "</div>");
//             },
//             getData: function (ui) {
//                 return ui.$cell.find('input:checked').val();
//             }
//         }, render: function (ui) {
//             var out = '';
//             if (ui.rowData.SATIS_TIPI === 0) {
//                 out += 'İlk Satış';
//             } else {
//                 out += 'Yenileme';
       
            
            
            {title: "Süre", style: {"background": "#C2E7D1"}, align: "center", hidden: false, editable: true, minWidth: 55, sortable: false, dataIndx: "SURE"},
            {title: "Liste<br />Fiyatı", align: "right", hidden: false, format: "#.###,00", editable: false, minWidth: 80, sortable: false, dataIndx: "B_LISTE_FIYATI"},
            {title: "Standart<br />Maliyet", align: "right", hidden: false, format: "#.###,00", editable: false, minWidth: 80, sortable: false, dataIndx: "O_MALIYET"},
            {title: "Özel Satış<br />İskontosu<br />(%)", style: {"background": "#C2E7D1"}, align: "right", format: "#.###,00", hidden: false, editable: <?PHP echo $izin; ?>, minWidth: 60, sortable: false, dataIndx: "ISKONTO"},
            {title: "Özel Alım<br />Maliyeti", style: {"background": "#C2E7D1"}, align: "right", hidden: false, format: "#.###,00", editable: <?PHP echo $izin; ?>, minWidth: 90, sortable: false, dataIndx: "B_MALIYET"},
            {title: "Adet", style: {"background": "#C2E7D1"}, align: "center", hidden: false, editable: <?PHP echo $izin; ?>, minWidth: 55, sortable: false, dataIndx: "ADET"},
            {title: "Satış<br />Fiyatı(Birim)", style: {"background": "#C2E7D1"}, align: "right", format: "#.###,00", hidden: false, editable: <?PHP echo $izin; ?>, minWidth: 90, sortable: false, dataIndx: "B_SATIS_FIYATI"},
            //{title: "Top Lis Fiy",summary: {type: "sum", edit: true}, align: "right", format: "#,###.00", hidden: false, editable: false, minWidth: 100, sortable: false, dataIndx: "T_LISTE_FIYATI"},
            {title: "Toplam<br />Maliyet", summary: {type: "sum", edit: true}, align: "right", format: "#.###,00", hidden: false, editable: false, minWidth: 100, sortable: false, dataIndx: "T_MALIYET"},
            //{title: "T Öz Mly",style: {"background": "#F2BDBD"}, align: "right", format: "#,###.00", hidden: false, editable: false, minWidth: 80, sortable: false, dataIndx: "B_OZEL_FIYAT"},
            {title: "Toplam<br />Satış Fiyatı", summary: {type: "sum", edit: true}, align: "right", format: "#.###,00", hidden: false, editable: false, minWidth: 95, sortable: false, dataIndx: "T_SATIS_FIYATI"},
            {title: "Kar%", style: {'background': '#dddddd'}, summary: {type: "avg", edit: true}, align: "right", format: "#.###,00", hidden: false, editable: false, minWidth: 50, sortable: false, dataIndx: "KARLILIK"},
            {title: "", align: "center", hidden: false, editable: false, minWidth: 30, sortable: false, dataIndx: "SIRA",
                render: function (ui) {
                    var out = '';
                    if (ui.rowData.id !== undefined) {
                        out += '<a href="#" onclick="sil(' + ui.rowData.id + ');"> <span class="ui-icon ui-icon-close" style="color: rgb(255, 0, 0);"></span> </a>';
                    }
                    return out;
                },
            }
            ];
            var dataModelSS = {
                location: "remote",
                dataType: "JSON",
                method: "GET",
                recIndx: "id",
                url: "_tablolar/kt_teklif_urunler.php?dbname=LKS&teklif_id=<?PHP echo $teklif_id; ?>",
            getData: function (response) {
                return {data: response.data};
            }
        };
        var obj = {
            menuIcon: false,
            trackModel: {on: true},
            collapsible: {on: false, toggle: false},
            reactive: true,
            scrollModel: {autoFit: true},
            editor: {select: true},
            // sortModel: {
            //     type: 'local',
            //     single: true,
            //     sorter: [{dataIndx: 'SIRA', dir: 'up', formatter: "number"}],
            //     space: true,
            //     multiKey: false
            // },
            roundCorners: false,
            rowBorders: true,
            //selectionModel: { type: 'cell' },
            stripeRows: true,
            scrollModel: {autoFit: false},
            showHeader: true,
            showTitle: false,
            // groupModel: {on: true,
            //     showSummary: [true],
            //     grandSummary: true,
            //     collapsed: [false, false],
            //     title: '{0},{0},{0},{0},{0},{0},{0},{0},{0},{0},{0},{0},{0},{0}',
            // }, // , dataIndx: ["DURUM"]
            showToolbar: false,
            showTop: false,
            width: 1200, height: 400,
            dataModel: dataModelSS,
            colModel: colM,
            postRenderInterval: -1,
            change: function (evt, ui) {
                //saveChanges can also be called from change event. 
            },
            destroy: function () {
                //clear the interval upon destroy.
                clearInterval(interval);
            },
            rowInit: function (ui) {
                if (typeof ui.rowData.TIP === 'undefined') {
                    return {
                        style: {"background": "#cccccc"} //can also return attr (for attributes) and cls (for css classes) properties.
                    };
                }
                if (typeof ui.rowData.TIP === 'Komtera') {
                    return {
                        style: {"background": "#eeeeee"} //can also return attr (for attributes) and cls (for css classes) properties.
                    };
                }
                if (ui.rowData.MCSURE === "1") {
                    return {
                        //style: {"background": "#222222"} //can also return attr (for attributes) and cls (for css classes) properties.
                    };
                }
            },
            load: function (evt, ui) {
                var grid = this,
                        data = grid.option('dataModel').data;
                grid.widget().pqTooltip(); //attach a tooltip.
                //validate the whole data.
                grid.isValid({data: data});
            },
            filterModel: {
                on: true,
                header: false,
                mode: "AND",
                hideRows: false,
                type: 'local',
                menuIcon: true
            },
            editable: true,
            summaryTitle: "",
            pageModel: {
                format: "#,###",
                type: "local",
                rPP: 1000,
                strRpp: "{0}",
                rPPOptions: [100, 1000, 10000]
            },
            sortable: false,
            //wrap: false, hwrap: false,
            //numberCell: {show: false, resizable: true, width: 30, title: "#"},
            title: 'Urunler',
            resizable: true,
            //rowHt: 19,
        create: function () {
               // this.loadState({refresh: false});
        },
        };
        grid = pq.grid("div#teklif_urunler", obj);
        grid.toggle();
        $(window).on('unload', function () {
            grid.saveState();
        });
        grid.on("destroy", function () {
            this.saveState();
        });
        
<?PHP
if (strlen($gelen['SATIS_TIPI']) > 2) {
    ?>
    for (let t of colM) {
    if (t.title === "STip") {
    t.hidden = false;
    }
    }
    <?PHP
}
if ($izin === "true") {
    ?>
    for (let t of colM) {
    if (t.title === "Süre") {
    t.hidden = false;
    }
    }
    <?PHP
}
?>      
    });
</script>
