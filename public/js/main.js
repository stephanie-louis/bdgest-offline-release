$(document).ready(function () {

    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
    });

    $('[data-toggle="tooltip"]').tooltip();

    $('.show-modal').on('click', function () {
        var id = $(this).attr('data-id');
        $('#modal-body').innerHTML = '';
        $.get(
            "/album/" + id,
            function(data) {
                $('#modal-body').html(data);
                $('#album-detail').modal('show');
            }
         );
        return false;
    });
});