$(".scribble-button").on('click', function () {
    const key = $(this).data('group-key');
    const header = $(this).data('group-name');

    $.get(
        '../group/open-scribble-modal',
        {key: key},
        function (data) {
            $('.modal-title').html(header);
            $('.modal-body').html(data);
            $('#scribble-modal').modal();
        }
    );
});
