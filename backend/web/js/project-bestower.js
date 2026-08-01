$('#form-bestow-access').on('submit', function (ev) {
    ev.preventDefault();
    setBestowed('#project-bestowedaccessids', $(this).find('[name="Project[bestowedAccessIds][]"]').val());
})
