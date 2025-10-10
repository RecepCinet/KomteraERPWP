<script>var grid;

    $(function () {

        var moduller = [
            "Teklifler",
            "Siparişler"
        ];

        var kurallar = [
            "Karlılık",
            "Bayi Vade Değişimi",
            "Peşin Satış",
            "Satış Toplamı",
            "Kara Liste",
        ];
        
        var parametreler = [
            "=","<=",">=","<>"
        ]
        
        var islemler = [
            "Onay","Bilgi","Dikkat","Not"
        ]
        
        function autoCompleteEditor(source) {
            return function (ui) {
                ui.$cell.addClass('ui-front');//so that dropdown remains with input.            

                //initialize the editor
                ui.$editor.autocomplete({
                    //appendTo: ui.$cell, //for grid in maximized state.
                    source: source,
                    position: {
                        collision: 'flipfit',
                        within: ui.$editor.closest(".pq-grid")
                    },
                    selectItem: {on: true}, //custom option
                    highlightText: {on: true}, //custom option
                    minLength: 0
                }).focus(function () {
                    //open the autocomplete upon focus                
                    $(this).autocomplete("search", "");
                });
            }
        }

        var interval;

        function saveChanges() {
            /**
             1. if there is no active ajax request.
             2. there is no ongoing editing in the grid.
             3. grid is dirty.
             4. all changes are valid.
             */
            if (!$.active && !grid.getEditCell().$cell && grid.isDirty() && grid.isValidChange({allowInvalid: true}).valid) {

                var gridChanges = grid.getChanges({format: 'byVal'});
                $.ajax({
                    //url: '_tablolar/tickets_edit.php', //for ASP.NET, java
                    url: '_tablolar/ayarlar_onaylar_edit.php?pq_batch=1',
                    data: {
                        //JSON.stringify not required for PHP
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
                    }
                });
            }
        }
        //save changes from a timer.
        interval = setInterval(saveChanges, 1000);

        function dateEditor(ui) {
            var $inp = ui.$cell.find("input"),
                    grid = this,
                    format = ui.column.format || "yy-mm-dd",
                    val = $.datepicker.formatDate(format, new Date($inp.val()));

            //initialize the editor
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
                                //to fix the issue of datepicker z-index when grid is in maximized state.
                                $('.ui-datepicker').css('z-index', 999999999999);
                            });
                            return !this.firstOpen;
                        },
                        onClose: function () {
                            this.focus();
                        }
                    });
        }

        var colM = [
            {title: "id", hidden: true, width: 70, dataIndx: "id", align: "right",
                filter: {
                    crules: [{condition: 'begin'}]
                }
            },
            {title: "Modül", align: "left", editable: true, minWidth: 120, sortable: false, dataIndx: "modul",
                cls: 'pq-dropdown pq-side-icon',
                editor: {
                    type: "textbox",
                    init: autoCompleteEditor(moduller)
                },
                filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "Kural", align: "left", editable: true, minWidth: 150, sortable: true, dataIndx: "kural",
        cls: 'pq-dropdown pq-side-icon',
                editor: {
                    type: "textbox",
                    init: autoCompleteEditor(kurallar)
                }
                ,
                filter: {
                    crules: [{condition: 'range'}]
                }
        },
            {title: "Marka", align: "left", editable: true, minWidth: 120, sortable: true, dataIndx: "marka"},
            {title: "Parametre", align: "left", editable: true, minWidth: 120, sortable: true, dataIndx: "parametre",
                editor: {
                    type: "textbox",
                    init: autoCompleteEditor(parametreler)
                }
                ,
                filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "Değer", align: "left", editable: true, minWidth: 120, sortable: true, dataIndx: "deger"},
            {title: "Kişi", align: "left", editable: true, minWidth: 120, sortable: true, dataIndx: "kisi",
                filter: {
                    crules: [{condition: 'range'}]
                }},
            {title: "İşlem", align: "left", editable: true, minWidth: 120, sortable: true, dataIndx: "islem",
            editor: {
                    type: "textbox",
                    init: autoCompleteEditor(islemler)
                } ,
                filter: {
                    crules: [{condition: 'range'}]
                }
            },
            {title: "EPosta", align: "left", editable: true, minWidth: 120, sortable: true, dataIndx: "eposta",
                filter: {
                    crules: [{condition: 'range'}]
                }}
            ,
            {
                title: "", editable: false, minWidth: 83, sortable: false,
                menuInHide: true,
                render: function (ui) {
                    if (!ui.rowData.pq_gtitle && !ui.rowData.pq_grandsummary)
                        return "<button type='button' class='delete_btn'>Sil</button>";
                },
                postRender: function (ui) {
                    var rowIndx = ui.rowIndx,
                        grid = this,
                        $cell = grid.getCell(ui);

                    $cell.find("button").button({ icons: { primary: 'ui-icon-scissors' } })
                    .bind("click", function () {

                        grid.addClass({ rowIndx: ui.rowIndx, cls: 'pq-row-delete' });

                        setTimeout(function () {
                            var ans = window.confirm( (rowIndx + 1) + ". Satırdaki kayıdı silmek istediğinizden emin misiniz?");
                            grid.removeClass({ rowIndx: rowIndx, cls: 'pq-row-delete' });
                            if (ans) {                               
                                var Group = grid.Group();
                                if ( Group.isOn() ) 
                                    Group.deleteNodes([ui.rowData]);                                
                                else 
                                    grid.deleteRow({ rowIndx: rowIndx });                                
                            }
                        })
                    });
                }
            }
            
            
        ];
        var dataModelSS = {
            location: "remote",
            dataType: "JSON",
            method: "GET",
            recIndx: "id",
            url: "_tablolar/ayarlar_onaylar.php",
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
            sortModel: {
                type: 'local',
                single: true,
                sorter: [{dataIndx: 'cd', dir: 'down'}],
                space: true,
                multiKey: false
            },
            toolbar: {
                items: [{
                        type: 'button',
                        icon: 'ui-icon-plus',
                        label: 'Yeni Kural',
                        listener: function () {
                            grid.addRow( { newRow: {} } );
                        }
                    },
                    {type: 'separator'},
                    {
                        type: 'button',
                        icon: 'ui-icon-arrowreturn-1-s',
                        label: 'Undo',
                        options: {disabled: true},
                        listener: function () {
                            grid.history({method: 'undo'});
                        }
                    },
                    {
                        type: 'button',
                        icon: 'ui-icon-arrowrefresh-1-s',
                        label: 'Redo',
                        options: {disabled: true},
                        listener: function () {
                            grid.history({method: 'redo'});
                        }
                    }
                ]
            },
            history: function (evt, ui) {
                var $tb = this.toolbar(),
                        $undo = $tb.find("button:contains('Undo')"),
                        $redo = $tb.find("button:contains('Redo')");

                if (ui.canUndo != null) {
                    $undo.button("option", {disabled: !ui.canUndo});
                }
                if (ui.canRedo != null) {
                    $redo.button("option", "disabled", !ui.canRedo);
                }
                $undo.button("option", {label: 'Undo (' + ui.num_undo + ')'});
                $redo.button("option", {label: 'Redo (' + ui.num_redo + ')'});
            },
            roundCorners: true,
            rowBorders: true,
            //selectionModel: { type: 'cell' },
            stripeRows: true,
            scrollModel: {autoFit: false},
            showHeader: true,
            showTitle: true,
            groupModel: {on: false},
            showToolbar: true,
            showTop: true,
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

            // ROW Komple:
            rowInit: function (ui) {
                if (ui.rowData.type == 'Bug') {
                    return {
                        style: {"background": "#FFEEEE"} //can also return attr (for attributes) and cls (for css classes) properties.
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
                header: true,
                mode: "AND",
                hideRows: false,
                type: 'local',
                menuIcon: true
            },
            editable: true,
            pageModel: {
                format: "#,###",
                type: "local",
                rPP: 1000,
                strRpp: "{0}",
                rPPOptions: [1000, 10000]
            },
            sortable: true,
            wrap: false, hwrap: false,
            numberCell: {show: true, resizable: true, width: 50, title: "#"},
            title: 'Ayarlar: Onaylar',
            autoRow: true,
            resizable: true,
            rowHt: 23,
            editModel: {
                clicksToEdit: 1,
                keyUpDown: false
            },
//        create: function () {                              
//            this.loadState({refresh: false});
//        },
        };
        grid = pq.grid("div#grid_ayarlar_onaylar", obj);
        grid.toggle();
        $(window).on('unload', function () {
            grid.saveState();
        });
        grid.on("destroy", function () {
            this.saveState();
        })
    });
</script>
