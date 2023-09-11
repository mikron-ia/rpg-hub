$('#game-basics-constructed').val($('#game-basics').val());

function setBasicsConstructed() {
    var datetime = $('#game-planned_date').val();
    var location = $('#game-planned_location').val();
    var separator = '';

    if (datetime && location) {
        separator = ', ';
    }

    $('#game-basics-constructed').val(location + separator + datetime);
}

$('#game-planned_date').on('change', setBasicsConstructed);
$('#game-planned_location').on('keyup', setBasicsConstructed);

$('#game-basics-transfer').on('click', function () {
    $('#game-basics').val($('#game-basics-constructed').val());
});
