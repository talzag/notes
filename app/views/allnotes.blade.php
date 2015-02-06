<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/bootstrap.css">
		<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>		
		<script src="js/vendor/modernizr-2.6.2.min.js"></script>
    </head>
    <body class="{{ "temp".Auth::user()->is_temporary }}">
        <!--[if lt IE 7]>
            <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
					  <a class="create-permanent-user round-button">Create Permanent User</a>
                      <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped display right-two-blank" id="notes_table" width="100%">
                        <thead>
                            <tr>
                                <th>Last Updated</th>
                                <th>Created</th>
                                <th>Note</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
        <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.1.min.js"><\/script>')</script>
		<script src="js/jquery.dataTables.min.js"></script> <!-- Edited for search input -->
		<script src="js/jquery.dataTables.editable.js"></script>
		<script src="js/bootstrap-datatables.js"></script>
		<script src="js/dataTables/master.js"></script>
		<script src="js/plugins.js"></script>
        <script src="js/main.js"></script>
        <script>
			$('#notes_table').dataTable({
			    "sPaginationType": "bootstrap",
			    "ajax": {
			        "url": "all_notes_data"
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
								url:"note",
								method:"POST",
								data: {
									"id":id,
									"note":note
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
							url:"note",
							method:"DELETE",
							data: {
								"id":id
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
			// create permanent user 
			$(".create-permanent-user").click(function() {
				var email = prompt("New User Email");
				var password = prompt("New User Password");
				$.ajax({
					url:"newtempuserfrompermanentuser",
					method: "POST",
					data: {
						email: email,
						password: password
					},
					success:function(data) {
						console.log(data);
						if(data === "success") {
							alert("Success! You've moved your temporary user to be a permanent user. Now you can retrieve your notes on any device and they'll be store permanently");
						} else {
							alert("Something went wrong - contact tommy@thecityswig.com and he'll fix it");
						}
					},
					error:function(data) {
						console.log(data);
					}
				})
			})       
	    </script>

        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
<!--
        <script>
            (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
            function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
            e=o.createElement(i);r=o.getElementsByTagName(i)[0];
            e.src='//www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
            ga('create','UA-XXXXX-X');ga('send','pageview');
        </script>
-->
    </body>
</html>
