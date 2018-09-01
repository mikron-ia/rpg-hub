$('.create-parameter-link').click(function () {
    $.get(
        '../parameter/create',
        {
            pack_id: $(this).data('pack-id')
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
            id: $(this).closest('tr').data('key')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#update-parameter-modal').modal();
        }
    );
});
