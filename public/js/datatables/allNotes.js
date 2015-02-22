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
            return "<span class='note-body'>"+data+"</span>"+"<span class='archive table-action-button'><span class='glyphicon glyphicon-remove' aria-hidden='true'></span>Archive</span><span class='edit table-action-button'><span class='glyphicon glyphicon-edit' aria-hidden='true'></span>Edit</span>";
        }
    }],
    "order": [[ 0, "desc" ]],
    "fnDrawCallback": function() {
        add_all_notes_events();
    }
});
var table = $('#notes_table').DataTable();
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
// migrate temporary user to permanent user UI
$(".create-permanent-user a").click(function() {
    $(".menu-slide-out").removeClass("showing");	
    $("#login-screen").fadeIn("fast");
	$(".signup-form").show();	
});

// if signup form is submitted, block it and submit via AJAX
$("form.signup-form").submit(function(e) {
    e.preventDefault();
    var form = $(this).serialize(); 
    $.ajax({
        url: "/users/create",
        type: "POST",
        dataType:"json",
        data: form,
        success:function(data) {
            console.log(data);
	        if(data.success) {
    	        // hide screens we don't need and set href of "view note"
                alert("User successfully migrated to a permanent user!");
                $("#login-screen").fadeOut("fast");
	        }
        },
        error:function(xhr, status, error) {
             console.log(xhr.responseText);
        }
    }); 
});

function add_all_notes_events() {
    // View/Edit single note 
    $("#notes_table .note-body").click(function() {
        // HARD CODED "PUBLIC"
        if(!$(this).attr("contentEditable")) {
	        console.log($(this).attr("contentEditable"));
	        var id = $(this).parent().parent().children("td:nth-child(2)").children(".hidden").text();
	        window.location.href = "../?note="+id;
        }
    });	
    
    $(".edit").click(function() {
        var id = $(this).parent().parent().children("td:nth-child(2)").children(".hidden").text();
        window.location.href = "/?note="+id+"&edit=1";
    });
    $(".archive").click(function() {
        var id = $(this).parent().parent().children("td:nth-child(2)").children(".hidden").text();
        if(confirm("Are you sure you want to archive this note?")) {
            $.ajax({
                url:"notes/archive",
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
        } else {
            console.log("whatever");   
        };
    });
/*
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
*/

}

