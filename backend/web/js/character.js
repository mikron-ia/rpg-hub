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
