// why isn't this in the project
$('#archives_table').dataTable({
    "sPaginationType": "bootstrap",
    "ajax": {
        "url": "archives/archives_data"
    },
    "lengthMenu": [25, 50, 100],
    "deferRender": true,
    "aoColumnDefs": [{ 
        'bSortable': false, 'aTargets': [ 0,1,2 ] 
    }],
    "aoColumns": [{
        "mData":"date_updated"
    },{
        "mData": "date_created",
        "mRender": function(data) {
            return "<span>"+data.date+"</span><span class='hidden'>"+data.id+"</span>";
        }
    },{
        "mData":"note",       
        "mRender": function(data) {
            return "<span class='delete table-action-button round-button full-round-button'><span class='glyphicon glyphicon-trash' aria-hidden='true'></span>Delete</span><span class='restore table-action-button round-button full-round-button'><span class='glyphicon glyphicon-refresh' aria-hidden='true'></span>Restore</span>"+"<span class='note-body'>"+data+"</span>";
        }
    }],
    "order": [[ 0, "desc" ]],
    "fnDrawCallback": function() {
        add_all_notes_events();
    }
});

var table = $('#archives_table').DataTable();
$('input[type=search]').on( 'keyup', function () {
    table.search( this.value ).draw();
});
// slideout menu 
$(".hamburger-icon").click(function() {
    if(!$(".menu-slide-out ul").is(":visible")) {
        $("body").addClass("menu-showing");      
    } else {
        $("body").removeClass("menu-showing");
    }
});
function add_all_notes_events() {
    // View/Edit single note 
    $("#archives_table .note-body").click(function() {
        // HARD CODED "PUBLIC"
        if(!$(this).attr("contentEditable")) {
	        console.log($(this).attr("contentEditable"));
	        var id = $(this).parent().parent().children("td:nth-child(2)").children(".hidden").text();
	        window.location.href = "../?note="+id;
        }
    });	
    $("#archives_table .restore").click(function() {
        var id = $(this).parent().parent().children("td:nth-child(2)").children(".hidden").text();
        if(confirm("Are you sure you want to restore this note?")) {    
            $.ajax({
                url:"archives/restore",
                method:"POST",
                data: {
                    "id": id
                },
                success:function(data) {
                    console.log(data);
                    var table = $('#archives_table').DataTable();
                    table.ajax.reload();
                },
                error:function() {
                    console.log(data);
                }
            });
        }
    });

    $("#archives_table .delete").click(function() {
        var id = $(this).parent().parent().children("td:nth-child(2)").children(".hidden").text();
        if(confirm("Are you sure you want to delete this note?")) {
            $.ajax({
                url:"archives/delete",
                method:"DELETE",
                data: {
                    "id": id
                },
                success:function(data) {
                    console.log(data);
                    var table = $('#archives_table').DataTable();
                    table.ajax.reload();
                },
                error:function() {
                    console.log(data);
                }
            })
        };
    });
}

