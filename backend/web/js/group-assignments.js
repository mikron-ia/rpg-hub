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

const setActors = function (storyKeyFieldId, objects, rank, visibility) {
    $.ajax(
        '../group-assignment-story/set-group-stories',
        {
            method: "PUT",
            data: {
                groupKey: $(storyKeyFieldId).data('group-key'),
                keys: objects,
                rank: rank,
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

$('#form-group-story-assignment-public-vital').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#group-groupstoryassignmentchoicespublicvital',
        $(this).find('[name="Group[groupStoryAssignmentChoicesPublicVital][]"]').val(),
        'vital',
        'full'
    );
})

$('#form-group-story-assignment-public-major').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#group-groupstoryassignmentchoicespublicmajor',
        $(this).find('[name="Group[groupStoryAssignmentChoicesPublicMajor][]"]').val(),
        'major',
        'full'
    );
})

$('#form-group-story-assignment-public-minor').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#group-groupstoryassignmentchoicespublicminor',
        $(this).find('[name="Group[groupStoryAssignmentChoicesPublicMinor][]"]').val(),
        'minor',
        'full'
    );
})

$('#form-group-story-assignment-public-other').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#group-groupstoryassignmentchoicespublicother',
        $(this).find('[name="Group[groupStoryAssignmentChoicesPublicOther][]"]').val(),
        'other',
        'full'
    );
})

$('#form-group-story-assignment-private-vital').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#group-groupstoryassignmentchoicesprivatevital',
        $(this).find('[name="Group[groupStoryAssignmentChoicesPrivateVital][]"]').val(),
        'vital',
        'gm'
    );
})

$('#form-group-story-assignment-private-major').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#group-groupstoryassignmentchoicesprivatemajor',
        $(this).find('[name="Group[groupStoryAssignmentChoicesPrivateMajor][]"]').val(),
        'major',
        'gm'
    );
})

$('#form-group-story-assignment-private-minor').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#group-groupstoryassignmentchoicesprivateminor',
        $(this).find('[name="Group[groupStoryAssignmentChoicesPrivateMinor][]"]').val(),
        'minor',
        'gm'
    );
})

$('#form-group-story-assignment-private-other').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#group-groupstoryassignmentchoicesprivateother',
        $(this).find('[name="Group[groupStoryAssignmentChoicesPrivateOther][]"]').val(),
        'other',
        'gm'
    );
})

$(document).ready(function () {
    fillList();
})
