<script>
    // toaster common function
    function message(action, message) {
        if (action == 'success') {
            toastr.remove();
            toastr.options.closeButton = true;
            toastr.success(message, {
                timeOut: 1500
            });
        } else {
            toastr.remove();
            toastr.options.closeButton = true;
            toastr.error(message, {
                timeOut: 1500
            });
        }
    }

    // common function for buttonLoader on button events
    function showButtonLoader(id, text, action) {
        /*parameters : button id , text on button  , button property (disable/enable)*/
        var icon = `<span class="fa fa-spin fa-spinner" style="display: inline-block;"></span>`
        if (action === 'disable') {
            $('#' + id).html(`${text} &nbsp ${icon}`);
            $('#' + id).prop('disabled', true);
        } else {
            $('#' + id).html(text);
            $('#' + id).prop('disabled', false);
        }
    }
</script>