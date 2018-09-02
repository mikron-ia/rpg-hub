var tabReputation = $('.tab-reputation');
var tabReputationEvents = $('.tab-reputation-events');

$.get(
    ['../character/external-reputation'],
    {key: tabReputation.data('key')},
    function (data) {
        $('.reputations').html(data);
    }
).done(function () {
    tabReputation.removeClass('hidden');
});

$.get(
    '../character/external-reputation-event',
    {key: tabReputationEvents.data('key')},
    function (data) {
        $('.reputation-events').html(data);
    }
).done(function () {
    tabReputationEvents.removeClass('hidden');
});
