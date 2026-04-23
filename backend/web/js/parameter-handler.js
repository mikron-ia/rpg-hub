$('.create-parameter-link').click(function () {
    $.get(
        '../' + $(this).data('controller') + '/create-parameter',
        {
            key: $(this).data('key')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#create-parameter-modal').modal();
        }
    );
});

$('.update-parameter-link').click(function () {
    $.get(
        '../parameter/update',
        {
            key: $(this).data('key')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#update-parameter-modal').modal();
        }
    );
});
