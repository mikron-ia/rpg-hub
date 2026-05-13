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

const setActors = function (storyKeyFieldId, objects, rank, visibility) {
    $.ajax(
        '../character-assignment-story/set-character-stories',
        {
            method: "PUT",
            data: {
                characterKey: $(storyKeyFieldId).data('character-key'),
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

$('#form-character-story-assignment-public-vital').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#character-characterstoryassignmentchoicespublicvital',
        $(this).find('[name="Character[characterStoryAssignmentChoicesPublicVital][]"]').val(),
        'vital',
        'full'
    );
})

$('#form-character-story-assignment-public-major').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#character-characterstoryassignmentchoicespublicmajor',
        $(this).find('[name="Character[characterStoryAssignmentChoicesPublicMajor][]"]').val(),
        'major',
        'full'
    );
})

$('#form-character-story-assignment-public-minor').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#character-characterstoryassignmentchoicespublicminor',
        $(this).find('[name="Character[characterStoryAssignmentChoicesPublicMinor][]"]').val(),
        'minor',
        'full'
    );
})

$('#form-character-story-assignment-public-other').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#character-characterstoryassignmentchoicespublicother',
        $(this).find('[name="Character[characterStoryAssignmentChoicesPublicOther][]"]').val(),
        'other',
        'full'
    );
})

$('#form-character-story-assignment-private-vital').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#character-characterstoryassignmentchoicesprivatevital',
        $(this).find('[name="Character[characterStoryAssignmentChoicesPrivateVital][]"]').val(),
        'vital',
        'gm'
    );
})

$('#form-character-story-assignment-private-major').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#character-characterstoryassignmentchoicesprivatemajor',
        $(this).find('[name="Character[characterStoryAssignmentChoicesPrivateMajor][]"]').val(),
        'major',
        'gm'
    );
})

$('#form-character-story-assignment-private-minor').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#character-characterstoryassignmentchoicesprivateminor',
        $(this).find('[name="Character[characterStoryAssignmentChoicesPrivateMinor][]"]').val(),
        'minor',
        'gm'
    );
})

$('#form-character-story-assignment-private-other').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        '#character-characterstoryassignmentchoicesprivateother',
        $(this).find('[name="Character[characterStoryAssignmentChoicesPrivateOther][]"]').val(),
        'other',
        'gm'
    );
})

$(document).ready(function () {
    fillList();
})
