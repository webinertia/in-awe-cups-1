$(document).ready(function() {
    // show the alert
    setTimeout(function() {
        $(".alert").alert('close');
    }, 2000);
});
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
// function to jquery ajax call to activate/deactivate user account
$(document).on('click', 'div.userlist-toolbar a.manage-user', function(event) {
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
// function to activate a bootstrap modal and load the content from the ajax call
$('#editorModal').on('show.bs.modal', function (event) {
    let button = $(event.relatedTarget); // Button that triggered the modal
    let href = button.data('href'); // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    $.ajax({
        url:href,
        dataType:"html",
    }).done(function(response){
        $('div#work-space').html(response);
    });
    //let modal = $(this);
   // modal.find('.modal-title').text('New message to ' + href);
    //modal.find('.modal-body').load(href);
});