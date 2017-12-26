$(document).ready(function () {
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
});
