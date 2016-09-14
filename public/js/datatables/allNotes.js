function log(text) {
    console.log(text);
}

$('#notes_table').dataTable({
    "sPaginationType": "bootstrap",
    "ajax": {
        "url": "notes/data"
    },
    "lengthMenu": [25, 50, 100],
    "deferRender": true,
    "aoColumnDefs": [{
        'bSortable': false, 'aTargets': [  0,1,2 ]
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
            return "<span class='archive table-action-button round-button full-round-button'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span>Archive</span><span class='edit table-action-button round-button full-round-button'><span class='glyphicon glyphicon-edit' aria-hidden='true'></span>Edit</span>"+"<span class='note-body'>"+data+"</span>";
        }
    }],
    "order": [[ 0, "desc" ]],
    "fnInitComplete": function() {
        add_all_notes_events();
    }
});

var table = $('#notes_table').DataTable();
$('input[type=search]').on( 'keyup', function () {
    table.search( this.value ).draw();
});

function add_all_notes_events() {
    log("add all notes events");

    // View/Edit single note
    $('#notes_table').on('click', '.note-body', function () {
        // HARD CODED "PUBLIC"
        ga('send', 'event', 'Note', 'View', 'Old');
        if(!$(this).attr("contentEditable")) {
	        log($(this).attr("contentEditable"));
	        var id = $(this).parent().parent().children("td:nth-child(2)").children(".hidden").text();
	        window.location.href = "../?note="+id;
        }
    });

    $('#notes_table').on('click', '.edit', function () {
        ga('send', 'event', 'Note', 'Edit', 'Old');
        var id = $(this).parent().parent().children("td:nth-child(2)").children(".hidden").text();
        window.location.href = "/?note="+id+"&edit=1";
    });

   $('#notes_table').on('click', '.archive', function () {
        var row = $(this).parent().parent();
        var id = row.children("td:nth-child(2)").children(".hidden").text();
        if(confirm("Are you sure you want to archive this note?")) {
            $.ajax({
                url:"notes/archive",
                method:"DELETE",
                data: {
                    "id": id
                },
                success:function(data) {
                    log(data);
                    table.row(row).remove().draw( false );
                },
                error:function() {
                    log(data);
                }
            })
        } else {
            log("whatever");
        };
    });

/*
    $("#notes_table td:nth-child(4)").click(function() {
        var table = $("#notes_table").DataTable();
        log( table.row( $(this).parent() ).data().note_raw );
        var id = $(this).parent().children("td:nth-child(2)").children(".hidden").text();
        $(this).parent().children("td:nth-child(3)").html(table.row( $(this).parent() ).data().note_raw);
        $(this).parent().children("td:nth-child(3)").attr("contentEditable",true);
        $(this).parent().children("td:nth-child(3)").focus();
        $($(this).parent().children("td:nth-child(3)")).keydown(function(e) {
            log(e);
            if(e.metaKey == true && e.keyCode === 83) {
                e.preventDefault();
                var note = $(this).parent().children("td:nth-child(3)").html();
                log(note);
                $.ajax({
                    url:"notes/edit",
                    method:"POST",
                    data: {
                        "id": id,
                        "note_text": note
                    },
                    success:function(data) {
                        log(data);
                        var table = $('#notes_table').DataTable();
                        table.ajax.reload();
                    },
                    error:function(data) {
                        log(data);
                    }
                })
            }
        })
    });
*/

}
