$(".scribble-button").on('click', function () {
    $.get(
        '../group/open-scribble-modal',
        {key: $(this).data('group-key')},
        function (data) {
            $('.modal-body').html(data);
            $('#scribble-modal').modal();
        }
    );
});
