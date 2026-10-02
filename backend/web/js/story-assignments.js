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
        '#storycharacterassignmentmodel-storycharacterassignmentchoicespublicvital',
        $(this).find('[name="StoryCharacterAssignmentModel[storyCharacterAssignmentChoicesPublicVital][]"]').val(),
        'vital',
        'full'
    );
})

$('#form-story-character-assignment-public-major').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'character',
        '#storycharacterassignmentmodel-storycharacterassignmentchoicespublicmajor',
        $(this).find('[name="StoryCharacterAssignmentModel[storyCharacterAssignmentChoicesPublicMajor][]"]').val(),
        'major',
        'full'
    );
})

$('#form-story-character-assignment-public-minor').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'character',
        '#storycharacterassignmentmodel-storycharacterassignmentchoicespublicminor',
        $(this).find('[name="StoryCharacterAssignmentModel[storyCharacterAssignmentChoicesPublicMinor][]"]').val(),
        'minor',
        'full'
    )
})

$('#form-story-character-assignment-public-other').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'character',
        '#storycharacterassignmentmodel-storycharacterassignmentchoicespublicother',
        $(this).find('[name="StoryCharacterAssignmentModel[storyCharacterAssignmentChoicesPublicOther][]"]').val(),
        'other',
        'full'
    )
})

$('#form-story-character-assignment-private-vital').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'character',
        '#storycharacterassignmentmodel-storycharacterassignmentchoicesprivatevital',
        $(this).find('[name="StoryCharacterAssignmentModel[storyCharacterAssignmentChoicesPrivateVital][]"]').val(),
        'vital',
        'gm'
    );
})

$('#form-story-character-assignment-private-major').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'character',
        '#storycharacterassignmentmodel-storycharacterassignmentchoicesprivatemajor',
        $(this).find('[name="StoryCharacterAssignmentModel[storyCharacterAssignmentChoicesPrivateMajor][]"]').val(),
        'major',
        'gm'
    );
})

$('#form-story-character-assignment-private-minor').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'character',
        '#storycharacterassignmentmodel-storycharacterassignmentchoicesprivateminor',
        $(this).find('[name="StoryCharacterAssignmentModel[storyCharacterAssignmentChoicesPrivateMinor][]"]').val(),
        'minor',
        'gm'
    )
})

$('#form-story-character-assignment-private-other').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'character',
        '#storycharacterassignmentmodel-storycharacterassignmentchoicesprivateother',
        $(this).find('[name="StoryCharacterAssignmentModel[storyCharacterAssignmentChoicesPrivateOther][]"]').val(),
        'other',
        'gm'
    )
})

$('#form-story-group-assignment-public-vital').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'group',
        '#storygroupassignmentmodel-storygroupassignmentchoicespublicvital',
        $(this).find('[name="StoryGroupAssignmentModel[storyGroupAssignmentChoicesPublicVital][]"]').val(),
        'vital',
        'full'
    );
})

$('#form-story-group-assignment-public-major').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'group',
        '#storygroupassignmentmodel-storygroupassignmentchoicespublicmajor',
        $(this).find('[name="StoryGroupAssignmentModel[storyGroupAssignmentChoicesPublicMajor][]"]').val(),
        'major',
        'full'
    );
})

$('#form-story-group-assignment-public-minor').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'group',
        '#storygroupassignmentmodel-storygroupassignmentchoicespublicminor',
        $(this).find('[name="StoryGroupAssignmentModel[storyGroupAssignmentChoicesPublicMinor][]"]').val(),
        'minor',
        'full'
    )
})

$('#form-story-group-assignment-public-other').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'group',
        '#storygroupassignmentmodel-storygroupassignmentchoicespublicother',
        $(this).find('[name="StoryGroupAssignmentModel[storyGroupAssignmentChoicesPublicOther][]"]').val(),
        'other',
        'full'
    )
})

$('#form-story-group-assignment-private-vital').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'group',
        '#storygroupassignmentmodel-storygroupassignmentchoicesprivatevital',
        $(this).find('[name="StoryGroupAssignmentModel[storyGroupAssignmentChoicesPrivateVital][]"]').val(),
        'vital',
        'gm'
    );
})

$('#form-story-group-assignment-private-major').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'group',
        '#storygroupassignmentmodel-storygroupassignmentchoicesprivatemajor',
        $(this).find('[name="StoryGroupAssignmentModel[storyGroupAssignmentChoicesPrivateMajor][]"]').val(),
        'major',
        'gm'
    );
})

$('#form-story-group-assignment-private-minor').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'group',
        '#storygroupassignmentmodel-storygroupassignmentchoicesprivateminor',
        $(this).find('[name="StoryGroupAssignmentModel[storyGroupAssignmentChoicesPrivateMinor][]"]').val(),
        'minor',
        'gm'
    )
})

$('#form-story-group-assignment-private-other').on('submit', function (ev) {
    ev.preventDefault();
    setActors(
        'group',
        '#storygroupassignmentmodel-storygroupassignmentchoicesprivateother',
        $(this).find('[name="StoryGroupAssignmentModel[storyGroupAssignmentChoicesPrivateOther][]"]').val(),
        'other',
        'gm'
    )
})

$(document).ready(function () {
    fillList('character');
    fillList('group');
})
