$('.add-membership-link').click(function () {
    $.get(
        '../group-membership/create',
        {
            groupKey: $(this).data('group-key')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#add-membership-modal').modal();
        }
    );
});

$('.view-membership-link').click(function () {
    $.get(
        '../group-membership/view',
        {
            key: $(this).data('key')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#view-membership-modal').modal();
        }
    );
});

$('.update-membership-link').click(function () {
    $.get(
        '../group-membership/update',
        {
            key: $(this).data('key')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#update-membership-modal').modal();
        }
    );
});

$('.membership-history-link').click(function () {
    $.get(
        '../group-membership/history',
        {
            key: $(this).data('key')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#membership-history-modal').modal();
        }
    );
});