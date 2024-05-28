$(".scribble-button").on('click', function () {
    $.get(
        '../character/open-scribble-modal',
        {key: $(this).data('character-key')},
        function (data) {
            $('.modal-body').html(data);
            $('#scribble-modal').modal();
        }
    );
});
