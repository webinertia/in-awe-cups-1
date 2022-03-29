// This is for the dashboard ajax in progress bar
$(document).bind("ajaxSend", function(){
    $("#progress").show();
}).bind("ajaxComplete", function(){
    $("#progress").hide();
}).bind("ajaxError", function(){
    alert("The requested operation could not be performed due to an error");
});
// ajax for the member list widget
$(document).on('click', 'div.userWidgetControl a', function(event) {
    event.preventDefault();
    let href = $(this).attr("href");
    $.ajax({
        url:href,
        dataType:"html",
    }).done(function(response){
        $('tbody#list-data').html(response);
    });
});
$('nav#sidebar').on('click', 'a.nav-link', function(event) {
    event.preventDefault();
    let href = $(this).parent().attr("data-href");
    /**
     * Going home or logging out?
     * All other menu entries are handled within the dashboard workspace
     */
    if(href === "/" || href === "/user/logout") {
        $(location).attr('href', href);
    }
    /**
     * if this not undefined then we have work to do as it will
     * update the work area inside the div#workSpace which
     * is basically the work area for the dashboard 
     * */ 
    if( typeof href !== "undefined") {
        /**
         * This ajax call loads the calls from the main sidebar menu
         * the .done function parses that data and attaches listeners 
         * to any forms that are ajaxed into the DOM
         * prevents default submission and handles the posting 
         * via ajax
         */
        $.ajax({
            url:href,
            dataType: "html",
        }).done(function(response) {
            /**
             * this call to .html() updates the DOM by replacing the contents
             * of div#workSpace with the response of the ajax 
             * request
             * */ 
            $('div#workSpace').html(response);
        });
    } 
});
$(document).bind("ajaxComplete", function() {
    tinymce.remove();
    //let basePath = $('[name="uploadPath"]');
    tinymce.init({
        selector: 'textarea',
        height: 250,
        skin: 'oxide-dark',
        plugins: [
            'image autolink lists charmap print preview anchor',
            'searchreplace visualblocks code',
            'insertdatetime media table paste code help'
            ],
        images_upload_handler: imageplugin_upload_handler,
        automatic_uploads: true,
        image_uploadtab: true,
        images_upload_url: '/upload/admin-upload',
        toolbar: 'undo redo | image | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | help'
    });
});
/**
 * // Delegate a listener for any submit coming from a child of the workSpace
 */
$('div#workSpace').on("submit", function(event) {
    event.preventDefault();
    let f = $(event.target);
    let location = $(f).attr("action");
    let postData = $(f).serialize();
    let formMethod = $(f).attr("method");
    console.log(postData);
    let request = $.ajax({
        // handle all of the form submissions based on form action and the data
        url: location,
        method: formMethod,
        data: postData,
    });
    request.done(function(response) {
        $('div#workSpace').html(response);
    });
    request.fail(function(){
        alert("Processing failed!!")
    });
});
function imageplugin_upload_handler (blobInfo, success, failure, progress) {
    var xhr, formData;
    /////
    xhr = new XMLHttpRequest();
    xhr.withCredentials = true;
    xhr.open('POST', '/upload/admin-upload'); // second argument is href to upload to
    xhr.upload.onprogress = function (e) {
      progress(e.loaded / e.total * 100);
    };
    xhr.onload = function() {
      var json;
      if (xhr.status === 403) {
        failure('HTTP Error: ' + xhr.status, { remove: true });
        return;
      }
      if (xhr.status < 200 || xhr.status >= 300) {
        failure('HTTP Error: ' + xhr.status);
        return;
      }
      /////
      json = JSON.parse(xhr.responseText);
      /////
      if (!json || typeof json.location != 'string') {
        failure('Invalid JSON: ' + xhr.responseText);
        return;
      }
      success(json.location);
    };
    xhr.onerror = function () {
      failure('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
    };
    let formElement = document.querySelector("form");
    formData = new FormData(formElement);
    formData.append('file', blobInfo.blob(), blobInfo.filename());
    // send the data, including the file
    xhr.send(formData);
  };
  