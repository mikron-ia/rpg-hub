$(".scribble-button").on('click', function () {
    const key = $(this).data('location-key');
    const header = $(this).data('location-name');

    $.get(
        '../location/open-scribble-modal',
        {key: key},
        function (data) {
            $('.modal-title').html(header);
            $('.modal-body').html(data);
            $('#scribble-modal').modal();
        }
    );
});
