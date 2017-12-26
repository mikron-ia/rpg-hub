var descriptionBoxesLoad = function ($descriptionContainer) {
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
    var $descriptions = $('#description-container');

    descriptionBoxesLoad($descriptions);

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
            descriptionBoxesLoad($descriptions);
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
            descriptionBoxesLoad($descriptions);
        });
    });

    $descriptions.on('click', '.description-history-link', function () {
        $.get(
            '../description/history',
            {
                id: $(this).data('id')
            },
            function (data) {
                $('.modal-body').html(data);
                $('#description-history-modal').modal();
            }
        );
    });

    $descriptions.on('click', '.update-description-link', function () {
        $.get(
            '../description/update',
            {
                id: $(this).data('id')
            },
            function (data) {
                $('.modal-body').html(data);
                $('#update-description-modal').modal();
            }
        );
    });

    $descriptions.on('click', '.create-description-link', function () {
        $.get(
            '../description/create',
            {
                pack_id: $(this).data('pack-id')
            },
            function (data) {
                $('.modal-body').html(data);
                $('#create-description-modal').modal();
            }
        );
    });
});
