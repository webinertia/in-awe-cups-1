$(document).ready(function() {
    // show the alert
    setTimeout(function() {
        $(".alert").alert('close');
    }, 2000);
});
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
// function to jquery ajax call to activate user account
$(document).on('click', 'a.activate-user', function(event) {
    event.preventDefault();
    let href = $(this).attr("href");
    let parentId = $(this).parent().attr("id");
    $(this).tooltip('hide');
    $.ajax({
        url:href,
        dataType:"html",
    }).done(function(response){
        $('div#' + parentId).html(response);
    });
});
