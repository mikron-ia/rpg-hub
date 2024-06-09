$('#button-copy-key').click(function (event) {
    event.preventDefault();
    $('#button-copy-key').prop('disable', true);
    navigator.clipboard.writeText($('#key-value').data('key'));
    $('#button-copy-key').text($('#button-message-copy-confirm').text());
    setTimeout(function () {
        $('#button-copy-key').text($('#button-message-copy-base').text());
        $('#button-copy-key').prop('disable', false);
    }, 1000);
});
