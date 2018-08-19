$('#game-planned_date').on('change', function () {
    $('#game-basics-constructed').val($(this).val());
});

$('#game-basics-transfer').on('click', function () {
    $('#game-basics').val($('#game-basics-constructed').val());
});
