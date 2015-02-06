// Account Types table for payment ops
$('#account_types_table').dataTable({
    "sPaginationType": "bootstrap",
    "ajax": {
        "url": "account_types_data"
    },
    "lengthMenu": [100, 25, 50, 75],
    "aoColumns": [{
        "mData": "account_holder"
    }, {
        "mData": "account_number"
    }, {
        "mData": "routing_number"
    }, {
        "mData": null,
        "mRender": function() {
            return '<form><input type="radio" name="sec_code" value="ppd"><label> PPD </label><input type="radio" name="sec_code" value="ccd"><label> CCD </label></form>';
        }
    }, {
        "mData": "created_on"
    }],
    "order": [
        [4, "desc"]
    ],
    "deferRender": true,
    "fnDrawCallback": function() {
        add_accounts_events();
    }
});

function add_accounts_events() {
    $("#account_types_table input[type='radio']").click(function(arg) {
        /* 		Get the values from the DOM to update SEC codes */
        var sec_code = $(this).val();
        var account_holder = $($(this).parent().parent().parent().children()[0]).text();
        var account_number = $($(this).parent().parent().parent().children()[1]).text();
        console.log(account_holder);
        var data = {
            "sec_code": sec_code,
            "account_holder": account_holder,
            "account_number": account_number
        };
        $.ajax({
            url: "master_update_sec",
            method: "POST",
            data: data,
            success: function(arg) {
                console.log(arg);
            }
        });
    })
}

