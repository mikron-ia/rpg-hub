var tabReputation = $('.tab-reputation');
var tabReputationEvents = $('.tab-reputation-events');

$.get(
    '../character/external-reputation',
    {key: tabReputation.data('key')},
    function (data, status) {
        if (status === 'success') {
            $('.reputations').html(data);
            tabReputation.removeClass('hidden');
        }
    }
);

$.get(
    '../character/external-reputation-event',
    {key: tabReputationEvents.data('key')},
    function (data, status) {
        if (status === 'success') {
            $('.reputation-events').html(data);
            tabReputationEvents.removeClass('hidden');
        }
    }
);

$(".scribble-button").on('click', function () {
    $.get(
        '../character/open-scribble-modal',
        {key: $(this).data('character-key')},
        function (data) {
            $('.modal-body').html(data);
            $('#scribble-modal').modal();
        }
    );
});
