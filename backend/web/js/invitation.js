$(document).ready(function () {

    $('.open-modal-with-link').click(function (event) {
        event.preventDefault();
        $('#invitation-link-content').val($(this).data('link'));
        $('#invitation-link-display-modal').modal();
    });

    $('#button-copy-link').click(function (event) {
        event.preventDefault();
        $('#button-copy-link').prop('disable', true);
        navigator.clipboard.writeText($('#invitation-link-content').val());
        $('#button-copy-link').text($('#button-message-copy-confirm').text());
        setTimeout(function () {
            $('#button-copy-link').text($('#button-message-copy-base').text());
            $('#button-copy-link').prop('disable', false);
        }, 1000);
    });

});
