$('#notes_table').dataTable({
    "sPaginationType": "bootstrap",
    "ajax": {
        "url": "notes/data"
    },
    "lengthMenu": [25, 50, 100],
    "deferRender": true,
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
            return 'EDIT';
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

function add_all_notes_events() {
    // View/Edit single note 
    $("#notes_table td:nth-child(3)").click(function() {
        // HARD CODED "PUBLIC"
        if(!$(this).attr("contentEditable")) {
	        console.log($(this).attr("contentEditable"));
	        var id = $(this).parent().children("td:nth-child(2)").children(".hidden").text();
	        window.location.href = "../public?note="+id;
        }
    });	
    $("#notes_table td:nth-child(4)").click(function() {
        var table = $("#notes_table").DataTable();
        console.log( table.row( $(this).parent() ).data().note_raw );
        var id = $(this).parent().children("td:nth-child(2)").children(".hidden").text();
        $(this).parent().children("td:nth-child(3)").html(table.row( $(this).parent() ).data().note_raw);
        $(this).parent().children("td:nth-child(3)").attr("contentEditable",true);
        $(this).parent().children("td:nth-child(3)").focus();
        $($(this).parent().children("td:nth-child(3)")).keydown(function(e) {
            console.log(e);
            if(e.metaKey == true && e.keyCode === 83) {
                e.preventDefault();
                var note = $(this).parent().children("td:nth-child(3)").html();
                console.log(note);
                $.ajax({
                    url:"notes/edit",
                    method:"POST",
                    data: {
                        "id": id,
                        "note_text": note
                    },
                    success:function(data) {
                        console.log(data);
                        var table = $('#notes_table').DataTable();
                        table.ajax.reload();
                    },
                    error:function(data) {
                        console.log(data);
                    }
                })
            }
        })
    });

    $("#notes_table td:nth-child(5)").click(function() {
        var id = $(this).parent().children("td:nth-child(2)").children(".hidden").text();
        if(confirm("Are you sure you want to delete this note?")) {
            $.ajax({
                url:"notes/delete",
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

