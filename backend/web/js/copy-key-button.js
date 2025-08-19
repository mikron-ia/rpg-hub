$('#button-copy-key').click(function (event) {
    event.preventDefault();
    $('#button-copy-key').prop('disable', true);
    navigator.clipboard.writeText($('#key-value').data('key')).then(function () {
        $('#button-copy-key').text($('#button-message-copy-confirm').text());
    }).catch(function () {
        $('#button-copy-key').text($('#button-message-copy-failure').text());
    }).finally(function () {
        setTimeout(function () {
            $('#button-copy-key').text($('#button-message-copy-base').text());
            $('#button-copy-key').prop('disable', false);
        }, 2000);
    });
});
