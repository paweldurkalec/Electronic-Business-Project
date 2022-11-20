async function getUserData(){
    return new Promise(function(resolve, reject) {
        $.ajax({
            url: AJAX_INFO,
            type: "GET",
            dataType: "json",
            success: function (response) {
                resolve(response)
            },
            error: function(err) {
                reject(err)
            }
        });
    });
}

async function saveUserData(email){
    const fmToken = $(document).find('#fm_token').val();
    const productData = $(document).find('#add-to-cart-or-refresh').serializeArray();

    let data = {
        email: email,
        fm_token: fmToken,
    }

    productData.forEach(function(obj){
        data[obj.name] = obj.value;
    });

    return new Promise(function(resolve, reject) {
        $.ajax({
            url: AJAX_SAVE,
            type: "POST",
            data: data,
            dataType: "json",
            success: function (response) {
                resolve(response)
            },
            error: function(err) {
                reject(err)
            }
        });
    });
}

(function() {
    getUserData().then((response) => {
        if(response.is_logged){
            $(document).find('#fm_email').val(response.email);
            $(document).find('#fm_token').val('');
        }else{
            $(document).find('#fm_email').val('');
            $(document).find('#fm_token').val(response.token);
        }
    });

    $(document).find("#fm_watch_product").submit(function( event ) {
        let userEmail = $(document).find('#fm_email').val();
        saveUserData(userEmail)
            .then((response) => {
                $(document).find("#fm_messages").html('');
                if(response.success){
                    $(document).find("#fm_messages").append('<div class="alert alert-success" role="alert">'+response.message+'</div>');
                }else{
                    $(document).find("#fm_messages").append('<div class="alert alert-warning" role="alert">'+response.message+'</div>');
                }
            });
        event.preventDefault();
    });
})();