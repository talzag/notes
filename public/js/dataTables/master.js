$('document').ready(function() {
    // Payments table stuff for MASTER or payment ops table
    if (!$.fn.dataTable.isDataTable('#master_table')) {
        $('#master_table').dataTable({
            "sPaginationType": "bootstrap",
            "ajax": {
                "url": "master_data"
            },
            "lengthMenu": [500, 25, 50, 75],
            "aoColumns": [{
                "mData": null,
                "mRender": function() {
                    return '<input class="transaction_id checkbox" type="checkbox"/><toggle class="dash-expand"></toggle>';
                }
            }, {
                "mData": "payment_key"
            }, {
                "mData": "total"
            }, {
                "mData": "status"
            }, {
                "mData": "credit_status"
            }, {
                "mData": "debit_status"
            }, {
                "mData": "payee_name"
            }, {
                "mData": "bank"
            }, {
                "mData": "payor_name"
            }, {
                "mData": "payor_routing"
            }, {
                "mData": "payor_account_number"
            }, {
                "mData": "date"
            }, {
                "mData": "lyons"
            }],
            "order": [
                [12, "desc"]
            ],
            "aoColumnDefs": [{
                'bSortable': false,
                'aTargets': [0]
            }],
            "deferRender": true,
            "fnDrawCallback": function() {
                add_master_events();
            },
            "rowCallback": function(row, data) {
                //console.log(data.notes);
                if (data.notes != null) {
                    //$(data.notes, row).html( 'has_notes' );
                    $(row).addClass('has_notes');
                }
            }
        });
    }
});

function add_master_events() {

    // make row red if missing
    $("td span").each(function() {
        if ($(this).html().length <= 0) {
            $(this).parent().parent().data("error", true);
            $(this).parent().parent().addClass("error");
        }
    });

    //| Multiple transaction selection logic
    // Checkbox value is transaction id
    $("input[type='checkbox'].transaction_id").each(function() {
        var table = $("#master_table").DataTable();
        var tr = $(this).parents("tr");
        var row = table.row(tr);
        $(this).attr("value", row.data().transaction_id);
    });

    // Checked checkboxes represent selected rows
    $(".checkbox").change(function() {
        var row = $(this).parents("tr");
        if (this.checked) {
            row.addClass("selected");
        } else {
            row.removeClass("selected");
        }
    });

    $.ajax({
        url: "count_credit_queue",
        success: function(data) {
            $("span.credit_money").text("$" + data["money"]);
            $("span.credit_transactions").text(data["count"]);
        }
    });

    $.ajax({
        url: "count_debit_queue",
        success: function(data) {
            $("span.debit_money").text("$" + data["money"]);
            $("span.debit_transactions").text(data["count"]);
        }
    });

    // add child row and show more information in accordion when clicked
    $(".dash-expand,.dash-collapse").click(function() {
        var table = $('#master_table').DataTable();
        var tr = $(this).parent().closest('tr');
        var row = table.row(tr);
        console.log(row.data().total);
        if ($(this).hasClass("dash-expand")) {
            row.child("<span class='transaction_id hidden'>" + row.data().transaction_id +
                "</span><div class='childrow_container'> Notes: <p class='notes'>" + row.data().notes +
                "</p></div><p>Payment Key: " + row.data().full_payment_key + "<br>" +
                "Business Email: " + row.data().payee_email + "<br>" +
                "Customer Email: " + row.data().payor_email + "</p>").show();
            $(".notes").dblclick(function() {
                var html = $(this).html();
                var textarea = "<textarea name='notes'>" + html + "</textarea><input type='submit' class='notes-submit'>";
                $(this).parent(".childrow_container").html(textarea);
                // update notes when form submitted
                $("input.notes-submit").click(function(event) {
                    event.preventDefault();
                    var transaction_id = $(this).parent().siblings(".transaction_id").text();
                    console.log(transaction_id);
                    var data = $(this).prev().serialize() + "&transaction_id=" + transaction_id;
                    $.ajax({
                        data: data,
                        method: "POST",
                        url: "master_update_notes",
                        success: function(data, textStatus, jqXHR) {
                            if (textStatus == "success") {
                                var table = $("#master_table").DataTable();
                                table.ajax.reload();
                                alert("SUCCESS");
                            }
                        },
                        error: function(arg) {
                            console.log(arg);
                        }
                    });
                });
            });
        } else {
            // clear out the child rows when they're gone because we don't want events added twice. Since these are created as one offs from data that requires no IO, there should be no performance loss from doing this. However, if you think of a way to avoid this, do it.
            row.child("").hide();
        }
        $(this).toggleClass("dash-expand");
        $(this).toggleClass("dash-collapse");
    });

    // Edit Amount
    $("td:not(.editing)").dblclick(function() {
        var editable = ["Amount", "Account Number", "Routing Number", "Customer Name"];
        if (!$(this).hasClass("editing") && editable.indexOf($(this).closest('table').find('th').eq(this.cellIndex).text()) > -1 && $(".editing").length === 0) {
            var html = "";
            if ($("span", this).length > 0) {
                $("span", this).addClass("hidden");
                html = $("span:first-child", this).html();
                var input = $('<input class="account" type="text" />');
                input.val(html);
                $(this).append(input);
            } else {
                html = $(this).html();
                var input = $('<input type="text" />');
                input.val(html);
                $(this).html(input);
            }
            $(this).addClass("editing");
        }
    });

    // Command or Control click to get backend calls
    $("#master_table td:nth-child(2)").click(function(e) {
        if (e.metaKey === true || e.ctrlKey === true) {
            var table = $("#master_table").DataTable();
            var row = table.row($(this).parents("tr"));
            var data = row.data().full_payment_key;
            $('#infoModal').modal("show");
            $.ajax({
                url: "backend_api_calls",
                method: "get",
                type: "json",
                data: {
                    payment_key: data,
                    session_secret: user_cookie
                },
                success: function(response_raw) {
                    console.log(response_raw);
                    var response = response_raw.reverse();
                    var modal_body_html = "";
                    for (var i = 0; i < response.length; i++) {
                        if (i + 1 === 1 || i % 3 === 0) {
                            modal_body_html += "<div class='backend_api_container'>"
                        }
                        modal_body_html += "<div class='backend_api_column'>";
                        console.log(response[i].route);
                        console.log(response[i].response);
                        modal_body_html += "<h3 class='route_title'>Route: " + response[i].route + "</h3> <h4>Response Time:</h4><p class='backend_api_calls'>" + response[i].response_time / 1000 + "s</p><h4>Parameters :</h4>";
                        $.each(response[i].parameters, function(k, v) {
                            modal_body_html += "<p class='backend_api_calls'>" + k + ": " + v + "</p>"
                        });
                        modal_body_html += "<h4>Response :</h4>";
                        $.each(response[i].response, function(k, v) {
                            modal_body_html += "<p class='backend_api_calls'>" + k + ": " + v + "</p>"
                        });
                        modal_body_html += "</div>";
                        if ((i + 1) % 3 === 0 || i === response.length - 1) {
                            modal_body_html += "</div>"
                        }
                    };
                    $("#infoModal .modal-body").html(modal_body_html);
                },
                error: function(response) {
                    console.dir(response);
                }
            });
        }
    });

    // Command click to get business details when business is clicked
    $(".business_name").click(function(e) {
        if (e.metaKey === true || e.ctrlKey === true) {
            console.log($(this).text());
            console.log($(this).siblings(".credit_id").text());
            $('#infoModal').modal("show");
            var load_string = "admin_business_info?credit_id=" + $(this).siblings(".credit_id").text();
            $("#infoModal .modal-body").load(load_string);
        }
    });

    // Command click to get account holder details for payor
    $(".account_holder").click(function(e) {
        if (e.metaKey === true || e.ctrlKey === true) {
            var user = $(this);
            // make sure nothing is being edited
            if ($("editing").length <= 0) {
                console.log(user.text());
                console.log(user.siblings(".debit_id").text());
                $('#infoModal').modal("show");
                var load_string = "admin_user_info?credit_id=" + user.siblings(".debit_id").text();
                $("#infoModal .modal-body").load(load_string);
            }
        }
    });

    // Command click to get bank account details
    $("td:has(.account_number)").click(function(e) {
        if (e.metaKey === true || e.ctrlKey === true) {
            var user = $(this);
            // make sure nothing is being edited
            if ($("editing").length <= 0) {
                console.log(user.text());
                console.log(user.children(".debit_id").text());
                $('#infoModal').modal("show");
                var load_string = "admin_bank_info?credit_id=" + user.children(".debit_id").text();
                console.log(load_string);
                $("#infoModal .modal-body").load(load_string);
            }
        }
    });

    $("select.credit-select").change(function() {
        var table = $("#master_table").DataTable();
        var selected = $(this).find(":selected").val();
        var row = table.row($(this).parents("tr"));
        $.ajax({
            url: "admin_transaction_update",
            type: "POST",
            data: {
                transaction_id: row.data().transaction_id,
                credit_status: selected
            },
            success: function(data, textStatus, jqXHR) {
                var table = $("#master_table").DataTable();
                table.ajax.reload();
                if (textStatus == "success") {
                    openNotification("The transaction was updated");
                } else if (textStatus == "fail") {
                    openNotification("Oops! Something went wrong, better contact the developers");
                }
            }
        });
    });

    $("select.debit-select").change(function() {
        var table = $("#master_table").DataTable();
        var selected = $(this).find(":selected").val();
        var row = table.row($(this).parents("tr"));
        $.ajax({
            url: "admin_transaction_update",
            type: "POST",
            data: {
                transaction_id: row.data().transaction_id,
                debit_status: selected
            },
            success: function(data, textStatus, jqXHR) {
                var table = $("#master_table").DataTable();
                table.ajax.reload();
                if (textStatus == "success") {
                    openNotification("The transaction was updated");
                } else {
                    openNotification("Oops! Something went wrong, better contact the developers");
                }
            }
        });
    });
}
