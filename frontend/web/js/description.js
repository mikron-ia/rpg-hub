/**
 * This is a quick hack intended to indicate older descriptions as outdated
 * It is intended to be replaced by server-side calculations
 * @type {Array}
 */

var descriptions = [];

$descriptionTimestamps = $('.description-timestamp');
$('.description-outdated').hide();

$descriptionTimestamps.each(function (index, element) {
    var $element = $(element);
    var type = $element.data('type');
    var order = $element.data('order');

    if (type && order) {
        if (descriptions[type]) {
            descriptions[type].push(order);
        } else {
            descriptions[type] = [order]
        }
    }
});

$descriptionTimestamps.each(function (index, element) {
    var $element = $(element);
    var type = $element.data('type');

    if (descriptions[type] && descriptions[type].length > 1) {
        var min = Math.min.apply(null, descriptions[type]);
        descriptions[type].splice(descriptions[type].indexOf(min), 1);
        $('[data-type=' + type + '][data-order=' + min + ']').parent().find('.description-outdated').show();
    }
});
