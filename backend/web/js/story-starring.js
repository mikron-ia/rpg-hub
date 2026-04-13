const fillList = function (type) {
    const list = $('#story-' + type + '-assignment-list');
    const path = '../story-assignment-' + type + '/get-story-' + type + 's';

    $.ajax(
        path,
        {
            method: "GET",
            data: {
                storyKey: list.data('story-key'),
            }
        }
    ).done(function (xhr) {
        list.html(xhr);
    }).fail(function (xhr) {
        list.html('<div class="loader-broken">' + xhr.status + '<p>' + xhr.responseText + '</p></div>');
    }).always(function (xhr) {
        //@todo Logging
    });
}

const setActors = function (type, storyKeyFieldId, objects, visibility) {
    $.ajax(
        '../story-assignment-' + type + '/set-story-' + type + 's',
        {
            method: "PUT",
            data: {
                storyKey: $(storyKeyFieldId).data('story-key'),
                keys: objects,
                visibility: visibility,
            }
        }
    ).done(function (xhr) {
        fillList(type);
    }).fail(function (xhr) {
        console.log('Failed:'.xhr.status + ': ' + xhr.responseText);
    }).always(function (xhr) {
        //@todo Logging
    });
}

$('#form-story-character-assignment-public').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'character',
        '#story-storycharacterassignmentchoicespublic',
        $(this).find('[name="Story[storyCharacterAssignmentChoicesPublic][]"]').val(),
        'full'
    );
})

$('#form-story-character-assignment-private').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'character',
        '#story-storycharacterassignmentchoicesprivate',
        $(this).find('[name="Story[storyCharacterAssignmentChoicesPrivate][]"]').val(),
        'gm'
    );
})

$('#form-story-group-assignment-public').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'group',
        '#story-storygroupassignmentchoicespublic',
        $(this).find('[name="Story[storyGroupAssignmentChoicesPublic][]"]').val(),
        'full'
    );
})

$('#form-story-group-assignment-private').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'group',
        '#story-storygroupassignmentchoicesprivate',
        $(this).find('[name="Story[storyGroupAssignmentChoicesPrivate][]"]').val(),
        'gm'
    );
})

$(document).ready(function () {
    fillList('character');
    fillList('group');
})
