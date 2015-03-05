$('#stats_table').dataTable({
    "sPaginationType": "bootstrap",
    "ajax": {
        "url": "stats/data"
    },
    "lengthMenu": [25, 50, 100],
    "deferRender": true,
    "fnDrawCallback": function() {
        
    }
});