const fillList = function (type) {
    const list = $('#story-' + type + '-assignment-list');
    const path = '../story-assignment-' + type + '/get-story-' + type + 's';

    $.ajax(path, {
        method: "GET", data: {
            storyKey: list.data('story-key'),
        }
    }).done(function (xhr) {
        list.html(xhr);
    }).fail(function (xhr) {
        list.html('<div class="loader-broken">' + xhr.status + '<p>' + xhr.responseText + '</p></div>');
    }).always(function (xhr) {
        //@todo Logging
    });
}

const setActors = function (type, storyKeyFieldId, objects, rank, visibility) {
    $.ajax('../story-assignment-' + type + '/set-story-' + type + 's', {
        method: "PUT", data: {
            storyKey: $(storyKeyFieldId).data('story-key'),
            keys: objects,
            rank: rank,
            visibility: visibility,
        }
    }).done(function (xhr) {
        fillList(type);
    }).fail(function (xhr) {
        console.log('Failed: ' + xhr.status + ': ' + xhr.responseText);
    }).always(function (xhr) {
        //@todo Logging
    });
}

$('#form-story-character-assignment-public-vital').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'character',
        '#story-storycharacterassignmentchoicespublicvital',
        $(this).find('[name="Story[storyCharacterAssignmentChoicesPublicVital][]"]').val(),
        'vital',
        'full'
    );
})

$('#form-story-character-assignment-public-major').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'character',
        '#story-storycharacterassignmentchoicespublicmajor',
        $(this).find('[name="Story[storyCharacterAssignmentChoicesPublicMajor][]"]').val(),
        'major',
        'full'
    );
})

$('#form-story-character-assignment-public-minor').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'character',
        '#story-storycharacterassignmentchoicespublicminor',
        $(this).find('[name="Story[storyCharacterAssignmentChoicesPublicMinor][]"]').val(),
        'minor',
        'full'
    )
})

$('#form-story-character-assignment-public-other').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'character',
        '#story-storycharacterassignmentchoicespublicother',
        $(this).find('[name="Story[storyCharacterAssignmentChoicesPublicOther][]"]').val(),
        'other',
        'full'
    )
})

$('#form-story-character-assignment-private-vital').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'character',
        '#story-storycharacterassignmentchoicesprivatevital',
        $(this).find('[name="Story[storyCharacterAssignmentChoicesPrivateVital][]"]').val(),
        'vital',
        'gm'
    );
})

$('#form-story-character-assignment-private-major').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'character',
        '#story-storycharacterassignmentchoicesprivatemajor',
        $(this).find('[name="Story[storyCharacterAssignmentChoicesPrivateMajor][]"]').val(),
        'major',
        'gm'
    );
})

$('#form-story-character-assignment-private-minor').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'character',
        '#story-storycharacterassignmentchoicesprivateminor',
        $(this).find('[name="Story[storyCharacterAssignmentChoicesPrivateMinor][]"]').val(),
        'minor',
        'gm'
    )
})

$('#form-story-character-assignment-private-other').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'character',
        '#story-storycharacterassignmentchoicesprivateother',
        $(this).find('[name="Story[storyCharacterAssignmentChoicesPrivateOther][]"]').val(),
        'other',
        'gm'
    )
})

$('#form-story-group-assignment-public-vital').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'group',
        '#story-storygroupassignmentchoicespublicvital',
        $(this).find('[name="Story[storyGroupAssignmentChoicesPublicVital][]"]').val(),
        'vital',
        'full'
    );
})

$('#form-story-group-assignment-public-major').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'group',
        '#story-storygroupassignmentchoicespublicmajor',
        $(this).find('[name="Story[storyGroupAssignmentChoicesPublicMajor][]"]').val(),
        'major',
        'full'
    );
})

$('#form-story-group-assignment-public-minor').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'group',
        '#story-storygroupassignmentchoicespublicminor',
        $(this).find('[name="Story[storyGroupAssignmentChoicesPublicMinor][]"]').val(),
        'minor',
        'full'
    )
})

$('#form-story-group-assignment-public-other').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'group',
        '#story-storygroupassignmentchoicespublicother',
        $(this).find('[name="Story[storyGroupAssignmentChoicesPublicOther][]"]').val(),
        'other',
        'full'
    )
})

$('#form-story-group-assignment-private-vital').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'group',
        '#story-storygroupassignmentchoicesprivatevital',
        $(this).find('[name="Story[storyGroupAssignmentChoicesPrivateVital][]"]').val(),
        'vital',
        'gm'
    );
})

$('#form-story-group-assignment-private-major').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'group',
        '#story-storygroupassignmentchoicesprivatemajor',
        $(this).find('[name="Story[storyGroupAssignmentChoicesPrivateMajor][]"]').val(),
        'major',
        'gm'
    );
})

$('#form-story-group-assignment-private-minor').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'group',
        '#story-storygroupassignmentchoicesprivateminor',
        $(this).find('[name="Story[storyGroupAssignmentChoicesPrivateMinor][]"]').val(),
        'minor',
        'gm'
    )
})

$('#form-story-group-assignment-private-other').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'group',
        '#story-storygroupassignmentchoicesprivateother',
        $(this).find('[name="Story[storyGroupAssignmentChoicesPrivateOther][]"]').val(),
        'other',
        'gm'
    )
})

$(document).ready(function () {
    fillList('character');
    fillList('group');
})
