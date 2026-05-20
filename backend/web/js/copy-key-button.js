$('#button-copy-key').click(function (event) {
    event.preventDefault();

    $('#button-copy-key').prop('disabled', true);

    navigator.clipboard.writeText($('#key-value').data('key')).then(function () {
        $('#button-copy-key').text($('#button-message-copy-confirm').text());
    }).catch(function () {
        $('#button-copy-key').text($('#button-message-copy-failure').text());
    }).finally(function () {
        setTimeout(function () {
            const button = $('#button-copy-key');

            button.text($('#button-message-copy-base').text());
            button.prop('disabled', false);
        }, 2000);
    });
});

$(document).ready(function () {
    if (navigator.clipboard === undefined) {
        $('#key-div').show()
    } else {
        $('#button-copy-key').show();
    }
});
