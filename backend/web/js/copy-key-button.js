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

$('.index-copy-key').click(function (event) {
    event.preventDefault();

    let copyKey = $(this);

    copyKey.prop('disabled', true);

    navigator.clipboard.writeText(copyKey.closest("tr").data('copy-key')).then(function () {
        copyKey.removeClass('glyphicon-copy');
        copyKey.addClass('glyphicon-check');
    }).finally(function () {
        setTimeout(function () {
            copyKey.removeClass('glyphicon-check');
            copyKey.addClass('glyphicon-copy');
            copyKey.prop('disabled', false);
        }, 2000);
    });
});

$(document).ready(function () {
    if (navigator.clipboard === undefined) {
        $('#key-div').show();
        $('.index-copy-key').hide();
        $('#copy-key-disabled').show();
    } else {
        $('#button-copy-key').show();
    }
});
