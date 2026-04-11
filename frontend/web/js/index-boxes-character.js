$(".scribble-button").on('click', function () {
    const key = $(this).data('character-key');
    const header = $(this).data('character-name');

    $.get(
        '../character/open-scribble-modal',
        {key: key},
        function (data) {
            $('.modal-title').html(header);
            $('.modal-body').html(data);
            $('#scribble-modal').modal();
        }
    );
});
