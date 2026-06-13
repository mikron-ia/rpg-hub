const setBestowed = function (keyFieldId, objects) {
    $.ajax('../bestowed/set', {
        method: "PUT", data: {
            listKey: $(keyFieldId).data('list-key'),
            objectClass: $(keyFieldId).data('object-class'),
            keys: objects,
        }
    }).done(function (xhr) {
        fillList();
    }).fail(function (xhr) {
        console.log('Failed:'.xhr.status + ': ' + xhr.responseText);
    }).always(function (xhr) {
        //@todo Logging
    });
}

$('#form-bestow-access').on('submit', function (ev) {
    ev.preventDefault();
    setBestowed('#secret-bestowedaccessids', $(this).find('[name="Secret[bestowedAccessIds][]"]').val());
})
