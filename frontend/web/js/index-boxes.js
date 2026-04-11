$(".favorite-button").on('click', function () {
    const button = $(this);

    var favorite = button.hasClass('glyphicon-star');

    button.prop('disabled', true);

    $.ajax(
        '../scribble/reverse-favorite',
        {
            method: 'GET',
            data: {
                id: $(this).data('scribble-id')
            }
        }
    ).done(function () {
        favorite = !favorite;
    }).fail(function () {
    }).always(function () {
        button.prop('disabled', false);
        button.first().removeClass('glyphicon-star');
        button.first().removeClass('glyphicon-star-empty');
        button.first().addClass(favorite ? 'glyphicon-star' : 'glyphicon-star-empty');
    });
});
