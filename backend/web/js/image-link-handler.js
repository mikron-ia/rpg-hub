$('.create-image-link').click(function () {
    $.get(
        '../image/add-link',
        {
            imageKey: $(this).data('key')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#create-image-link-modal').modal();
        }
    );
});

$('.update-image-link').click(function () {
    $.get(
        '../image/update-link',
        {
            imageLinkKey: $(this).data('key')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#update-image-link-modal').modal();
        }
    );
});

$('.view-image-link').click(function () {
    $.get(
        '../image/view-link',
        {
            imageLinkKey: $(this).data('key')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#view-image-link-modal').modal();
        }
    );
});
