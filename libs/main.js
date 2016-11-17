/******************************************************************************************
NEW FORM BUTTON
 *******************************************************************************************/


jQuery(document).ready(function($) {
    $(document).on('click', '.insert_button', function(e) {
        e.preventDefault();
        $('#new_form').toggle()

    });


/******************************************************************************************
SAVE FORM
 *******************************************************************************************/


    $(document).on('click', '.save_form', function(e) {
        e.preventDefault();
            user_name = $('input[name=user_name]').val(),
            user_email = $('input[name=user_email]').val(),
            user_pass = $('input[name=user_pass]').val(),
 //           user_status_payment = $('input[name=user_status_payment]').val(),
            mm_user_org = $('input[name=mm_user_org]').val(),
            that = $(this);
        var data = {
            'action': 'insert_user',
            'user_email': user_email,
            'user_name': user_name,
            'user_pass' : user_pass,
//            'user_status_payment': user_status_payment,
            'mm_user_org': mm_user_org
        };
            jQuery.post(ajaxurl, data, function (response) {
                window.location.reload();
        });
    });

/******************************************************************************************
EDIT FORM
 *******************************************************************************************/


    $(document).on('click', '.edit_button', function(e) {
        e.preventDefault();
        user_id = $(this).closest('tr').find('td').each(function(){
            console.log($(this).data());
            if($(this).data('name') == 'id' || $(this).data('name') == 'payment-status'){
                return;
            } else if($(this).data('name') == 'actions') {
                $(this).html('<button class="save_user_changes">Save</button>');
                return;
            }
            var cur_val = $(this).text(),
                name = $(this).data('name');

            $(this).html('<input type="text" name="'+name+'" value="'+cur_val+'" style="width:100%"/>')
        });
    });


/******************************************************************************************
SAVE CHANGES FORM
 *******************************************************************************************/


    $(document).on('click', '.save_user_changes', function(e) {
        e.preventDefault();
        user_id = $(this).closest('tr').data("id");
        user_email = $(this).closest('tr').find('td[data-name="email"]').find('input').val();
        user_name =  $(this).closest('tr').find('td[data-name="name"]').find('input').val();
        mm_user_org = $(this).closest('tr').find('td[data-name="organization"]').find('input').val();
        mm_pass = $(this).closest('tr').find('td[data-name="password"]').find('input').val();

       console.log(user_id,user_email,user_name,mm_pass,mm_user_org);
        var data = {
            'action' : 'update_user',
            'id' : user_id,
            'user_name' : user_name,
            'user_email' : user_email,
            'user_org' : mm_user_org,
            'mm_pass' : mm_pass
        };

        $.post(ajaxurl, data, function(response) {
            window.location.reload();
        })
    });


/******************************************************************************************
DELETE FORM
 *******************************************************************************************/


    $(document).on('click', '.delete_button', function(e) {
        e.preventDefault();
        if (confirm("you delete a user")) {
            var del = $(this).closest('tr').next().find();
            user_id = $(this).closest('tr').data('id');
            that = $(this);
            var data = {
                'action': 'delete_user',
                'user_id': user_id
            };

        jQuery.post(ajaxurl, data, function (response) {
            that.closest('tr').remove();
            });
        };
    });
});
