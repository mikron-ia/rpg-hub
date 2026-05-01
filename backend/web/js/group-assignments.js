const fillList = function () {
    const list = $('#group-story-assignment-list');
    const path = '../group-assignment-story/get-group-stories';

    $.ajax(
        path,
        {
            method: "GET",
            data: {
                groupKey: list.data('group-key'),
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
        '../group-assignment-story/set-group-stories',
        {
            method: "PUT",
            data: {
                groupKey: $(storyKeyFieldId).data('group-key'),
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

$('#form-group-story-assignment-public').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#group-groupstoryassignmentchoicespublic',
        $(this).find('[name="Group[groupStoryAssignmentChoicesPublic][]"]').val(),
        'full'
    );
})

$('#form-group-story-assignment-private').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#group-groupstoryassignmentchoicesprivate',
        $(this).find('[name="Group[groupStoryAssignmentChoicesPrivate][]"]').val(),
        'gm'
    );
})

$(document).ready(function () {
    fillList();
})
