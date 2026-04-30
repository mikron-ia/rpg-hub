const fillList = function () {
    const list = $('#character-story-assignment-list');
    const path = '../character-assignment-story/get-character-stories';

    $.ajax(
        path,
        {
            method: "GET",
            data: {
                characterKey: list.data('character-key'),
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

const setActors = function (storyKeyFieldId, objects, visibility) {
    $.ajax(
        '../character-assignment-story/set-character-stories',
        {
            method: "PUT",
            data: {
                characterKey: $(storyKeyFieldId).data('character-key'),
                keys: objects,
                visibility: visibility,
            }
        }
    ).done(function (xhr) {
        fillList();
    }).fail(function (xhr) {
        console.log('Failed:'.xhr.status + ': ' + xhr.responseText);
    }).always(function (xhr) {
        //@todo Logging
    });
}

$('#form-character-story-assignment-public').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#character-characterstoryassignmentchoicespublic',
        $(this).find('[name="Character[characterStoryAssignmentChoicesPublic][]"]').val(),
        'full'
    );
})

$('#form-character-story-assignment-private').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#character-characterstoryassignmentchoicesprivate',
        $(this).find('[name="Character[characterStoryAssignmentChoicesPrivate][]"]').val(),
        'gm'
    );
})

$(document).ready(function () {
    fillList();
})
