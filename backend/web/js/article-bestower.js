$('#form-bestow-access').on('submit', function (ev) {
    ev.preventDefault();
    setBestowed('#article-bestowedaccessids', $(this).find('[name="Article[bestowedAccessIds][]"]').val());
})
