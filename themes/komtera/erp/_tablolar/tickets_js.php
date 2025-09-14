<?PHP
if (!isset($_SESSION)) { session_start(); }
$user=$_SESSION['user'];
$company=$user["company"];
?>
<script>var grid;

$(function () {
   
     var interval;

        function saveChanges() {
            /**
            1. if there is no active ajax request.
            2. there is no ongoing editing in the grid.
            3. grid is dirty.
            4. all changes are valid.
            */
            if (!$.active && !grid.getEditCell().$cell && grid.isDirty() && grid.isValidChange({ allowInvalid: true }).valid) {

                var gridChanges = grid.getChanges({ format: 'byVal' });
                $.ajax({
                    //url: '_tablolar/tickets_edit.php', //for ASP.NET, java
                    url: '_tablolar/tickets_edit.php?pq_batch=1',
                    data: {
                        //JSON.stringify not required for PHP
                        list: JSON.stringify( gridChanges )
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
                        grid.commit({ type: 'add', rows: changes.addList });
                        grid.commit({ type: 'update', rows: changes.updateList });
                        grid.commit({ type: 'delete', rows: changes.deleteList });
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
        {title: "id", hidden: false, width: 70, dataIndx: "id", align: "right",
            filter: { 
                        crules: [{condition: 'begin'}]
                    }
            },
        {title: "<?php echo __('edit','komtera'); ?>", align: "center", editable: false, minWidth: 50, sortable: true,
            render: function (ui) {
                return "<a href='#' class='edit_btn'>" + ui.rowData.id + "</a>";
                //return "<button type='button' class='delete_btn' style='height: 23px;'>Edit</button>";
            },
            postRender: function (ui) {
                var grid = this,
                    $cell = grid.getCell(ui);
                $cell.find(".edit_btn")
                    //.button({ icons: { primary: 'ui-icon-zoomin'} })
                    .bind("click", function (evt) {
                        FileMaker.PerformScriptWithOption ( "Ticket", "Ac" + "\n" + ui.rowData.id );
                    });
            }
        },
        {title: "<?php echo __('status','komtera'); ?>",editor: {
                    options: ['<?php echo __('open','komtera'); ?>', '<?php echo __('in_progress','komtera'); ?>', '<?php echo __('close','komtera'); ?>'],
                    type: function (ui) {
                        //debugger;
                        var options = ui.column.editor.options,
                            str = options.map(function (option) {
                                var checked = (option == ui.cellData)? 'checked = checked': '';
                                return "<input type='radio' " + checked + " name='" + ui.dataIndx + "' style='margin-left:5px;' value='" + option + "'>  " + option + "<br />";
                            }).join("");

                        ui.$cell.append("<div class='pq-editor-focus' tabindex='0' style='padding:5px;margin-top:1px;'>" + str + "</div>");
                    },
                    getData: function (ui) {
                        return ui.$cell.find('input:checked').val();
                    }
                }, editable: true, minWidth: 90, sortable: true, dataIndx: "status",  filter: { 
                        crules: [{condition: 'range',value: ['<?php echo __('in_progress','komtera'); ?>','<?php echo __('open','komtera'); ?>']}]
                    }},
        {title: "<?php echo __('type','komtera'); ?>", width: 90, dataIndx: "type",
            editor: {
                    options: ['<?php echo __('task','komtera'); ?>', '<?php echo __('bug','komtera'); ?>', '<?php echo __('info','komtera'); ?>', '<?php echo __('new_feature','komtera'); ?>'],
                    type: function (ui) {
                        //debugger;
                        var options = ui.column.editor.options,
                            str = options.map(function (option) {
                                var checked = (option == ui.cellData)? 'checked = checked': '';
                                return "<input type='radio' " + checked + " name='" + ui.dataIndx + "' style='margin-left:5px;' value='" + option + "'>  " + option + "<br />";
                            }).join("");

                        ui.$cell.append("<div class='pq-editor-focus' tabindex='0' style='padding:5px;margin-top:1px;'>" + str + "</div>");
                    },
                    getData: function (ui) {
                        return ui.$cell.find('input:checked').val();
                    }
                },
            render: function (ui) {
                if (ui.cellData == 'Bug') {
                    return { style: { "background": "red" } };
                }
                if (ui.cellData == 'Task') {
                    return { style: { "background": "lightgray" } };
                }
                if (ui.cellData == 'Info') {
                    return { style: { "background": "yellow" } };
                }
                if (ui.cellData == 'New Feature') {
                    return { style: { "background": "lightgreen" } };
                }
            },
            filter: { 
                        crules: [{condition: 'range'}]
                    },
            },
            
            {title: "<?php echo __('priority','komtera'); ?>", width: 85, dataIndx: "priority",
            editor: {
                    options: ['<?php echo __('critical','komtera'); ?>', '<?php echo __('high','komtera'); ?>', '<?php echo __('normal','komtera'); ?>', '<?php echo __('low','komtera'); ?>'],
                    type: function (ui) {
                        //debugger;
                        var options = ui.column.editor.options,
                            str = options.map(function (option) {
                                var checked = (option == ui.cellData)? 'checked = checked': '';
                                return "<input type='radio' " + checked + " name='" + ui.dataIndx + "' style='margin-left:5px;' value='" + option + "'>  " + option + "<br />";
                            }).join("");

                        ui.$cell.append("<div class='pq-editor-focus' tabindex='0' style='padding:5px;margin-top:1px;'>" + str + "</div>");
                    },
                    getData: function (ui) {
                        return ui.$cell.find('input:checked').val();
                    }
                },
            filter: {
                        crules: [{condition: 'range'}]
                    },
                                render: function (ui) {
                if (ui.cellData == 'Critical') {
                    return { style: { "background": "Violet" ,  "color": "white" } };
                }
                if (ui.cellData == 'High') {
                    return { style: { "background": "Tomato" ,  "color": "white" } };
                }
                if (ui.cellData == 'Normal') {
                    return { style: { "background": "WhiteSmoke" } };
                }
                if (ui.cellData == 'Low') {
                    return { style: { "background": "lightgreen" } };
                }
            },
                     cls: 'pq-dropdown pq-side-icon',
            },
            
            {title: "<?php echo __('company','komtera'); ?>", width: 120,
             editor: {
                    options: ['Komtera', 'Ulke_Endustriyel', '4SON', 'Ulke_Enerji', 'Veri_Kurtarma' , 'Lidyum' , 'Utopic_Games'],
                    type: function (ui) {
                        //debugger;
                        var options = ui.column.editor.options,
                            str = options.map(function (option) {
                                var checked = (option == ui.cellData)? 'checked = checked': '';
                                return "<input type='radio' " + checked + " name='" + ui.dataIndx + "' style='margin-left:5px;' value='" + option + "'>  " + option + "<br />";
                            }).join("");

                        ui.$cell.append("<div class='pq-editor-focus' tabindex='0' style='padding:5px;margin-top:1px;'>" + str + "</div>");
                    },
                    getData: function (ui) {
                        return ui.$cell.find('input:checked').val();
                    }
                },
                    dataIndx: "company",validations: [{ type: 'nonEmpty', msg: "Required"}],
            filter: { 
                        crules: [{condition: 'range',value: ['Komtera']}]
                    }
            },
//        {title: "CDate", editable: false, minWidth: 80, sortable: true, dataIndx: "cd"},
        {title: "<?php echo __('acan','komtera'); ?>", editable: false, minWidth: 80, sortable: true, dataIndx: "cn"},
        {title: "<?php echo __('module','komtera'); ?>",editable: false, minWidth: 130, sortable: true, dataIndx: "modul",
            filter: { 
                        crules: [{condition: 'range'}]
                    }
                    },
        {title: "<?php echo __('title','komtera'); ?>",editable: false, width: 230, dataIndx: "title",
            filter: { 
                        crules: [{condition: 'contain'}]
                    }
            },
        {title: "<?php echo __('description','komtera'); ?>",editable: false, width: 410, dataIndx: "description",
            filter: {
                        crules: [{condition: 'contain'}]
                    }
            },
       
        {title: "Version",editor: {
                    options: ['22.1', '22.2', '22.3', '22.4'],
                    type: function (ui) {
                        //debugger;
                        var options = ui.column.editor.options,
                            str = options.map(function (option) {
                                var checked = (option == ui.cellData)? 'checked = checked': '';
                                return "<input type='radio' " + checked + " name='" + ui.dataIndx + "' style='margin-left:5px;' value='" + option + "'>  " + option + "<br />";
                            }).join("");

                        ui.$cell.append("<div class='pq-editor-focus' tabindex='0' style='padding:5px;margin-top:1px;'>" + str + "</div>");
                    },
                    getData: function (ui) {
                        return ui.$cell.find('input:checked').val();
                    }
                }, width: 83, dataIndx: "version",
            filter: { 
                        crules: [{condition: 'range'}]
                    }
            }
        // {title: "Release Note",editable: true, width: 160, dataIndx: "release_note",
        //     filter: {
        //         crules: [{condition: 'contain'}]
        //     }
        // },
        // {title: "Sure",editable: true, width: 60, dataIndx: "sure",
        //     filter: {
        //         crules: [{condition: 'contain'}]
        //     }
        // },

//        {title: "Module", width: 120, dataIndx: "modul",
//            filter: { 
//                        crules: [{condition: 'range'}]
//                    }
//            },
                    
        // {title: "DeadLine", width: 230, dataIndx: "dead_line", dataType: 'date', format: 'yy-mm-dd',
		//         cls: 'pq-calendar pq-side-icon',
		//         editor: {
		//             type: 'textbox',
		//             init: dateEditor,
		//             getData: function (ui) {
		//                 //convert from column format to native js date format "mm/dd/yy"
		//                 var dt = $.datepicker.parseDate(ui.column.format, ui.$cell.find("input").val());
		//                 return $.datepicker.formatDate("yy-mm-dd", dt);
		//             }
		//         },
		//
        // filter: {
        //             crules: [{condition: 'between'}]
        //         }
        // }
//                { title: "Delete", editable: false, minWidth: 85, sortable: false,
//                    render: function (ui) {
//                        return "<button type='button' class='delete_btn' style='height: 23px;'>Delete</button>";
//                    },
//                    postRender: function (ui) {
//                        var grid = this,
//                            $cell = grid.getCell(ui);
//                        $cell.find(".delete_btn")
//                            .button({ icons: { primary: 'ui-icon-scissors'} })
//                            .bind("click", function (evt) {
//                                grid.deleteRow({ rowIndx: ui.rowIndx });
//                            });
//                    }
//                }
    ];
    var dataModelSS = {
        location: "remote",
        dataType: "JSON",
        method: "GET",
        recIndx: "id",
        url: "_tablolar/tickets.php",
        getData: function (response) {
                    return { data: response.data };
        }
    };

    var obj = {
        menuIcon: false,
        trackModel: { on: true },
        collapsible: {on: false, toggle: false},
        reactive: true,
        scrollModel: { autoFit: true },            
        editor: { select: true },
        // sortModel: {
        //         type: 'local',
        //         single: true,
        //         sorter: [{ dataIndx: 'id', dir: 'down' }],
        //         space: true,
        //         multiKey: false
        //     },
             toolbar: {
                items: [{
                    type: 'button',
                    icon: 'ui-icon-plus',
                    label: '<?php echo __('new_ticket','komtera'); ?>',
                    listener: function () {                        
                        FileMaker.PerformScriptWithOption ( "Ticket", "Yeni" , 1 );
                    }
                },
                { type: 'separator' },
                {
                    type: 'button',
                    icon: 'ui-icon-arrowreturn-1-s',
                    label: '<?php echo __('undo','komtera'); ?>',                    
                    options: { disabled: true },
                    listener: function () {
                        grid.history({ method: 'undo' });
                    }
                },
                {
                    type: 'button',
                    icon: 'ui-icon-arrowrefresh-1-s',
                    label: '<?php echo __('redo','komtera'); ?>',
                    options: { disabled: true },
                    listener: function () {
                        grid.history({ method: 'redo' });
                    }
                }
            ,
                    {
                        type: 'checkbox',
                        value: false,
                        label: '<?php echo __('satir_kaydir','komtera'); ?>',
                        listener: function (evt) {                            
                            this.option('wrap', evt.target.checked);
                            this.option('autoRow', evt.target.checked);
                            this.refreshDataAndView();
                        }
                    }            
            ]
            },
            history: function (evt, ui) {
                var $tb = this.toolbar(), 
                    $undo = $tb.find("button:contains('Undo')"), 
                    $redo = $tb.find("button:contains('Redo')");

                if (ui.canUndo != null) {
                    $undo.button("option", { disabled: !ui.canUndo });
                }
                if (ui.canRedo != null) {
                    $redo.button("option", "disabled", !ui.canRedo);
                }
                $undo.button("option", { label: 'Undo (' + ui.num_undo + ')' });
                $redo.button("option", { label: 'Redo (' + ui.num_redo + ')' });
            },
        roundCorners: true,
        rowBorders: true,
        //selectionModel: { type: 'cell' },
        stripeRows: true,
        scrollModel: {autoFit: false},            
        showHeader: true,
        showTitle: false,
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
                    style: { "background": "#FF9999" } //can also return attr (for attributes) and cls (for css classes) properties.
                };
            }
            if (ui.rowData.status == 'In progress') {
                return { 
                    style: { "background": "#DDDDDD" } //can also return attr (for attributes) and cls (for css classes) properties.
                };
            }
        },
        load: function (evt, ui) {
                var grid = this,
                    data = grid.option('dataModel').data;

                grid.widget().pqTooltip(); //attach a tooltip.

                //validate the whole data.
                grid.isValid({ data: data });
            },
        freezeCols: 2,
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
            rPPOptions: [1000,10000]
        },
        sortable: true,
        wrap: false, hwrap: false,
        numberCell: {show: false, resizable: true, width: 30, title: "#"},
        title: '<?php echo __('development_roadmap','komtera'); ?>',
        autoRow: false,
        resizable: true,
        rowHt: 21,
       create: function () {
           //this.loadState({refresh: false});
       },
    };
    grid = pq.grid("div#grid_tickets", obj);
    grid.toggle();
    $(window).on('unload', function () {
        //grid.saveState();
    });
    grid.on("destroy", function () {
        //this.saveState();
    })    
});
</script>
