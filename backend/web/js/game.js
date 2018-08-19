$('#game-planned_date').on('change', function () {
    var basic = $('#game-basics');

    if (basic.val() === '') {
        basic.val($(this).val());
    }
});
