$('#form-bestow-access').on('submit', function (ev) {
    ev.preventDefault();
    setBestowed('#secret-bestowedaccessids', $(this).find('[name="Secret[bestowedAccessIds][]"]').val());
})
