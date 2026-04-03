const descriptionBoxesLoad = function ($descriptionContainer) {
    $.ajax(
        '../description/display',
        {
            method: "GET",
            data: {
                packKey: $descriptionContainer.data('pack-key')
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
    const $descriptions = $('#description-container');

    descriptionBoxesLoad($descriptions);

    $descriptions.on('click', '.move-up', function () {
        const $descriptionKey = $(this).data('description-key');
        $.ajax(
            '../description/move-up',
            {
                method: "GET",
                data: {
                    key: $descriptionKey
                }
            }
        ).always(function () {
            descriptionBoxesLoad($descriptions);
        });
    });

    $descriptions.on('click', '.move-down', function () {
        const $descriptionKey = $(this).data('description-key');
        $.ajax(
            '../description/move-down',
            {
                method: "GET",
                data: {
                    key: $descriptionKey
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
                key: $(this).data('key')
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
                key: $(this).data('key')
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
                packKey: $(this).data('pack-key')
            },
            function (data) {
                $('.modal-body').html(data);
                $('#create-description-modal').modal();
            }
        );
    });

    $(document).on('submit', '#description-form', function (event) {
        event.preventDefault();

        const descriptionForm = $(this);
        $.ajax({
            url: descriptionForm.attr('action'),
            method: (descriptionForm.attr('method') || 'POST'),
            data: descriptionForm.serialize()
        }).done(function (data) {
            $('.modal-body').html(data);
        });
    });
});
