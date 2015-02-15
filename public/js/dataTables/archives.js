$('#archives_table').dataTable({
    "sPaginationType": "bootstrap",
    "ajax": {
        "url": "archives/archives_data"
    },
    "lengthMenu": [25, 50, 100],
    "deferRender": true,
    "aoColumnDefs": [{ 
        'bSortable': false, 'aTargets': [ 2 ] 
    }],
    "aoColumns": [{
        "mData":"date_updated"
    },{
        "mData": "date_created",
        "mRender": function(data) {
            return "<span>"+data.date+"</span><span class='hidden'>"+data.id+"</span>";
        }
    },{
        "mData":"note"
    },{
        "mData": null,
        "mRender": function() {
            return 'RESTORE';
        }
    },{
        "mData": null,
        "mRender": function() {
            return 'DELETE';
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
        $(".menu-slide-out").addClass("showing");      
    } else {
        $(".menu-slide-out").removeClass("showing");
    }
});
function add_all_notes_events() {
    // View/Edit single note 
    $("#archives_table td:nth-child(3)").click(function() {
        // HARD CODED "PUBLIC"
        if(!$(this).attr("contentEditable")) {
	        console.log($(this).attr("contentEditable"));
	        var id = $(this).parent().children("td:nth-child(2)").children(".hidden").text();
	        window.location.href = "../?note="+id;
        }
    });	
    $("#archives_table td:nth-child(4)").click(function() {
        var id = $(this).parent().children("td:nth-child(2)").children(".hidden").text();
        $.ajax({
            url:"archives/restore",
            method:"POST",
            data: {
                "id": id
            },
            success:function(data) {
                console.log(data);
                var table = $('#notes_table').DataTable();
                table.ajax.reload();
            },
            error:function() {
                console.log(data);
            }
        });
    });

    $("#archieves_table td:nth-child(5)").click(function() {
        var id = $(this).parent().children("td:nth-child(2)").children(".hidden").text();
        if(confirm("Are you sure you want to delete this note?")) {
            $.ajax({
                url:"archives/delete",
                method:"DELETE",
                data: {
                    "id": id
                },
                success:function(data) {
                    console.log(data);
                    var table = $('#notes_table').DataTable();
                    table.ajax.reload();
                },
                error:function() {
                    console.log(data);
                }
            })
        };
    });
}

