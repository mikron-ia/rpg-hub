var descriptionBoxesLoad = function () {
    var $descriptionContainer = $('#description-container');
    $.ajax(
        '../description/display',
        {
            method: "GET",
            data: {
                id: $descriptionContainer.data('pack-id')
            }
        }
    ).done(function (xhr) {
        $descriptionContainer.html(xhr);
    }).fail(function (xhr) {
        $descriptionContainer.html('<div class="loader-broken">' + xhr.status + '<p>' + xhr.responseText + '</p></div>');
    }).always(function (xhr) {
        //@todo Logging
    });
};

$(document).ready(function () {
    descriptionBoxesLoad();

    var $descriptions = $('#description-container');

    $descriptions.on('click', '.move-up', function () {
        var $descriptionId = $(this).data('description-id');
        $.ajax(
            '../description/move-up',
            {
                method: "GET",
                data: {
                    id: $descriptionId
                }
            }
        ).always(function () {
            descriptionBoxesLoad();
        });
    });

    $descriptions.on('click', '.move-down', function () {
        var $descriptionId = $(this).data('description-id');
        $.ajax(
            '../description/move-down',
            {
                method: "GET",
                data: {
                    id: $descriptionId
                }
            }
        ).always(function () {
            descriptionBoxesLoad();
        });
    });
});
