path = {
    env: window.location.href.search("app_dev.php/") > 0 ? "/app_dev.php" : "",
    images: "/bundles/app/images/",
    components: "/bundles/app/components/",
}

/*
 Info Box
 =======================

 The info box will add an info sign with a tooltip for specific elements that have the data-info attribute set
 */
function initInfoBox(){
    $("input[data-info]").each(function (){
        $(this).after('<i class="fa fa-info-circle hasInfoInput" data-tooltip="yes" title="'+$(this).attr("data-info")+'"></i>');
    });
}


/*
 Tooltip
 =======================

 This will be used to add an HTML tooltip to all elements that have data-tooltip="yes"
 */
function initTooltip(){
    $('*[data-tooltip="yes"]').tooltip({html: true});
}

/*
 Dropzone
 =======================

 This will help us make the file upload
 */


var dropzoneInit = "/bundles/app/components/dropzone";

function initDropzone(){
    if ($('div[data-dropzone]').length==0){ return false; } //No need to load the component if there aren't any data-dropzone divs
    if (dropzoneInit!=""){
        $('<link/>', {rel: 'stylesheet', href: dropzoneInit+'/basic.css'}).appendTo('head');
        $('<link/>', {rel: 'stylesheet', href: dropzoneInit+'/dropzone.css'}).appendTo('head');

        $.ajax({ //Load the dropzone component
            url: dropzoneInit+"/dropzone.min.js",
            dataType: "script",
            cache: true,
            success: applyDropzone
        });
    } else {
        applyDropzone();
    }
}
function applyDropzone(){
    dropzoneInit = "";
    $('div[data-dropzone]').each(function (){
        var attr = $(this).attr("data-dropzone");
        Dropzone.options.myAwesomeDropzone = {
            url: "/upload/interest_" + $(this).attr("data-dropzone") ,
            paramName: "uploadfile", // The name that will be used to transfer the file
            maxFilesize: 2, // MB
            autoProcessQueue: true,
            uploadMultiple: false,
            init: function() {
                var thisDropzone = this;
                $.get("/upload-reverse/interest_" + attr, function(data) {
                    $.each(data, function(key,value){
                        var mockFile = { name: value.fullName, size: value.size, type: value.ext };
                        thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile, value.name);
                        mockFile.previewElement.classList.add('dz-success');
                        mockFile.previewElement.classList.add('dz-complete');
                        if (value.ext.indexOf("image") <= -1) {
                            mockFile.previewElement.classList.remove('dz-image-preview');
                            mockFile.previewElement.classList.add('dz-file-preview');

                        }

                    });

                });
            },
            accept: function(file, done) {
                done();
            },
            success: function (file, data){
                $("#interest_picture").val($("#interest_picture").val()+","+data.id);
            }


        };
    });
}


/*
 Common functions
 =======================

 These will be a couple of common functions
 */
function hide(){ $(this).css("display", "none"); }
function remove(){ $(this).remove(); }

/*
 Ready
 =======================

 These will be the functions that initialise when the document loads
 */
$(document).ready(function (){
    initInfoBox();
    initTooltip();
    initDropzone();
});