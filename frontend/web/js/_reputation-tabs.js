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
