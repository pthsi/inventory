<div style='float: left;'>
    <input type="button" value="Export to Excel" id='excelExport' />
</div>
<div id="gird" style="float:left; width:100%;">
<script type="text/javascript">
    $(document).ready(function () {
    // prepare the data
    var source ={
        datatype: "json",
        datafields: [
                        { name: 'id_glasir' , type: 'string' },
                        { name: 'nama_glasir' , type: 'string' },
                        { name: 'scrap_supp' , type: 'number' },
                        { name: 'adj_bgps' , type: 'number' },
                        { name: 'adj_sply' , type: 'number' },
                        { name: 'gtot' , type: 'number' },
                        { name: 'turun_bgps' , type: 'number' },
                        { name: 'ditarik_supply' , type: 'number' },
                        { name: 'return_prod' , type: 'number' },
                        { name: 'kirim_prod' , type: 'number' },
                        { name: 'stok_bgps' , type: 'number' },
                        { name: 'stok_supply' , type: 'number' },
                        { name: 'total' , type: 'number' },
        ],
        url: '<?php echo base_url().'index.php/glasir/loadStok'?>'
    };
    
    var cellsrenderer = function (row, columnfield, value, defaulthtml, columnproperties, rowdata) {
                if (value < 20) {
                    return '<span style="margin: 4px; float: ' + columnproperties.cellsalign + '; color: #ff0000;">' + value + '</span>';
                }
                else {
                    return '<span style="margin: 4px; float: ' + columnproperties.cellsalign + '; color: #008000;">' + value + '</span>';
                }
            }
    
    $("#jqxgrid").jqxGrid({
        width: '100%',
        height: 490,
        source: source,
        pagesize: 14,
        pageable: true,
        sortable: true,
        columnsresize: true,
        filterable: true,
        showtoolbar: true,
        showstatusbar: true,
        showaggregates: true,
        statusbarheight: 25,
        pagesizeoptions: ['13', '30', '90', '100'],
        columns: [
                        { text: 'Kode',  pinned: true, align: 'center', datafield: 'id_glasir', width: 50, },
                        { text: 'Nama',  pinned: true, align: 'center', datafield: 'nama_glasir', width: 201 },
                        { text: 'Total',  align: 'center',datafield: 'total', cellsrenderer: cellsrenderer, cellsalign: 'right', columngroup: 'stg',cellsformat: 'd2', width: 80},
                        { text: 'BGPS',  align: 'center',datafield: 'stok_bgps', cellsrenderer: cellsrenderer, cellsalign: 'right', columngroup: 'stg',cellsformat: 'd2', width: 75 },
                        { text: 'Supply',  align: 'center',datafield: 'stok_supply', cellsrenderer: cellsrenderer, cellsalign: 'right', columngroup: 'stg',cellsformat: 'd2', width: 75 },
                        { text: 'Turun BGPS',  align: 'center',columngroup: 'trans',cellsrenderer: cellsrenderer, datafield: 'turun_bgps', cellsalign: 'right', cellsformat: 'd2', width: 120 },
                        { text: 'Ditarik Supply',  align: 'center',columngroup: 'trans',cellsrenderer: cellsrenderer, datafield: 'ditarik_supply', cellsalign: 'right', cellsformat: 'd2', width: 110 },
                        { text: 'Return Glasir',  align: 'center',columngroup: 'trans',cellsrenderer: cellsrenderer, datafield: 'return_prod', cellsalign: 'right', cellsformat: 'd2', width: 110 },
                        { text: 'Kirim Glasir',  align: 'center',columngroup: 'trans', cellsrenderer: cellsrenderer, datafield: 'kirim_prod', cellsalign: 'right', cellsformat: 'd2', width: 110 },
                        { text: 'BGPS',  align: 'center',columngroup: 'stok2', cellsrenderer: cellsrenderer, datafield: 'adj_bgps', cellsalign: 'right', cellsformat: 'd2', width: 70 },
                        { text: 'Supply',  align: 'center',columngroup: 'stok2', cellsrenderer: cellsrenderer, datafield: 'adj_sply', cellsalign: 'right', cellsformat: 'd2', width: 70 },
                        { text: 'Scrap Supply',  align: 'center', cellsrenderer: cellsrenderer, datafield: 'scrap_supp', cellsalign: 'right', cellsformat: 'd2', width: 100 }
        ],
        columngroups: [
                    { text: 'Stok Akhir Glaze (Kg)', align: 'center', name: 'stg' },
                    { text: 'Transaksi Glaze (Kg)', align: 'center', name: 'trans' },
                    { text: 'Penyesuaian (Kg)', align: 'center', name: 'stok2' }
                ]
    });
    $("#excelExport").jqxButton();
    $("#excelExport").click(function () {
                $("#jqxgrid").jqxGrid('exportdata', 'xls', 'Data Stok Inventory Glasir');           
            });
});
</script>
<div id="jqxgrid"></div>
</div>