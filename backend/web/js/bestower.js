const setBestowed = function (keyFieldId, objects) {
    $('#form-bestow-access-errors').hide();
    $('#form-bestow-access-success').hide();
    $.ajax('../bestowed/set', {
        method: "PUT", data: {
            listKey: $(keyFieldId).data('list-key'),
            objectClass: $(keyFieldId).data('object-class'),
            keys: objects,
        }
    }).done(function (xhr) {
        $('#form-bestow-access-success').show();
    }).fail(function (xhr) {
        const status = xhr?.status ?? 'status unknown';
        const responseText = xhr?.responseText ?? 'no response';

        console.error('Failed: ' + status + ': ' + responseText);

        $('#form-bestow-access-fail').show();
        $('#form-bestow-access-fail-text').text(responseText);
    }).always(function (xhr) {
        //@todo Logging
    });
}
