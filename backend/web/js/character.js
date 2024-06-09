$('.update-external-data-link').click(function () {
    $.get(
        '../external-data/update',
        {
            id: $(this).closest('tr').data('key')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#update-parameter-modal').modal();
        }
    );
});

$('#button-copy-key').click(function () {
    var data = $('#key-value').data('key');

    $('#button-copy-key').prop('disable', true);

    navigator.clipboard.writeText(data);

    $('#button-copy-key').text($('#button-message-progress').text());

    setTimeout(function () {
        $('#button-copy-key').text($('#button-message-base').text());
        $('#button-copy-key').prop('disable', false);
    }, 1024);
});
