$(document).ready(function() {
    // show the alert
    setTimeout(function() {
        $(".alert").alert('close');
    }, 2000);
});
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});
// ajax call to delete page
$(document).on('click', 'a.delete-page', function(event) {
    event.preventDefault();
    let href = $(this).attr("href");
    $.ajax({
        url:href,
        dataType:"json",
        }).done(function(data, textStatus, jqXHR){
        console.log(data);
        if (data.href !== undefined) {
            $(location).attr('href', data.href);
        }
        });
});
// This loads the form in the modal to edit a page
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
    //modal.find('.modal-body').load(href);
});
// Begin profile update handling
$('#profileModal').on('show.bs.modal', function (event) {
    let button = $(event.relatedTarget); // Button that triggered the modal
    let href = button.data('href'); // Extract info from data-* attributes
    let section = button.data('section'); // Extract info from data-* attributes
    let modal = $(this);
    let formElement = $(this).find('form');
   // formData = new FormData(formElement);
    //console.log(formElement);
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
    $.ajax({
        url:href,
        dataType:"html",
    }).done(function(response) {
        // load the form in the modal
        $('div#profile-work-space').html(response);
    }).fail(function(jqXHR, textStatus, errorThrown) {
        //console.log(jqXHR);
        //console.log(textStatus);
        //console.log(errorThrown);
    });
    //modal.find('.modal-body').load(href);
});
/**
 * process the modal form submission and handle the response
 * if the form has errors server side, then we will return the form
 * with errors, otherwise we will update the dom with the new content
 */
$('div#profile-work-space').on("submit", function(event) {
    event.preventDefault();
    let f = $(event.target);
    let location = $(f).attr("action");
    let postData = $(f).serialize();
    let formMethod = $(f).attr("method");
    let request = $.ajax({
        // handle all of the form submissions based on form action and the data
        url: location,
        method: formMethod,
        data: postData,
        //dataType: "html",
    });
    request.done(function(response, textStatus, jqXHR) {
        // normalize the string to lowercase
        let contentType = jqXHR.getResponseHeader("Content-Type").toLowerCase();
        //console.log(contentType);
        if (contentType === 'text/html; charset=utf-8') {
            //console.log('html response');
            $('div#profile-work-space').html(response);
        }
        if(contentType === 'application/json; charset=utf-8') {
            console.log(response);
            if (response.target !== 'undefined' && response.success || response.status === 'complete') {
                let domTarget = $('#' + response.target);
                $('#profileModal').modal('hide'); // hide the modal since we no longer need it
                console.log(domTarget); // is the target element
                $.ajax({
                    url: '/user/manage-profile/ajax-template/' + response.userName + '/' + response.target,
                    method: 'GET',
                }).done(function(response) {
                    console.log('fetched new content');
                    domTarget.html(response);
                });
            }
        }
    });
    request.fail(function(){
        alert("Profile update failed!!")
    });
});

$('div#work-space').on("submit", function(event) {
    event.preventDefault();
    let f = $(event.target);
    let location = $(f).attr("action");
    let postData = $(f).serialize();
    let formMethod = $(f).attr("method");
    let request = $.ajax({
        // handle all of the form submissions based on form action and the data
        url: location,
        method: formMethod,
        data: postData,
    });
    request.done(function(response, textStatus, jqXHR) {
        if (jqXHR.getResponseHeader("Content-Type") == "text/html; charset=utf-8") {
            $('div#work-space').html(response);
        }
        else if(jqXHR.getResponseHeader("Content-Type") == "application/json") {
            if (response.href !== undefined) {
                window.location = response.href;
            }
        }
    });
    request.fail(function(){
        alert("Processing failed!!")
    });
});