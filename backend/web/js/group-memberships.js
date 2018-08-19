$('.add-membership-link').click(function () {
    $.get(
        '../group-membership/create',
        {
            group_id: $(this).data('group-id')
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
            id: $(this).data('id')
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
            id: $(this).data('id')
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
            id: $(this).data('id')
        },
        function (data) {
            $('.modal-body').html(data);
            $('#membership-history-modal').modal();
        }
    );
});