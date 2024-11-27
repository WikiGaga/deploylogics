// Class definition
var KTFormRepeater = function() {

    // Private functions
    var demo6 = function() {
        $('#kt_repeater_6').repeater({
            initEmpty: false,
            isFirstItemUndeletable: true,
            defaultValues: {
                'text-input': 'foo'
            },

            show: function() {
               // $(this).find('.kt_dropzone_4').dropzone({ url: "http://127.0.0.1:8000/employee/form" });
                $(this).slideDown();
            },
            ready: function(setIndexes){
                //demo2();
            },
            hide: function(deleteElement) {
                if(confirm('Are you sure you want to delete this element?')) {
                    $(this).slideUp(deleteElement);
                }
            }
        });
    }
    return {
        // public functions
        init: function() {
            demo6();
        }
    };
}();
jQuery(document).ready(function() {
    KTFormRepeater.init();
});
function demo2() {
    // set the dropzone container id
    var id = '.kt_dropzone_4';

    // set the preview element template
    var previewNode = $(id + " .dropzone-item");
    previewNode.id = "";
    var previewTemplate = previewNode.parent('.dropzone-items').html();
    previewNode.remove();

    var myDropzone4 = new Dropzone(id, { // Make the whole body a dropzone
        url: "http://127.0.0.1:8000/employee/form", // Set the url for your upload script location
        parallelUploads: 20,
        previewTemplate: previewTemplate,
        maxFilesize: 1, // Max filesize in MB
        acceptedFiles: "image/*,application/pdf",
        autoQueue: false, // Make sure the files aren't queued until manually added
        previewsContainer: id + " .dropzone-items", // Define the container to display the previews
        clickable: id + " .dropzone-select" // Define the element that should be used as click trigger to select files.
    });

    myDropzone4.on("addedfile", function(file) {
        // Hookup the start button
        // file.previewElement.querySelector(id + " .dropzone-start").onclick = function() { myDropzone4.enqueueFile(file); };
        $(document).find( id + ' .dropzone-item').css('display', '');
        $( id + " .dropzone-remove-all").css('display', 'inline-block');
    });

    // Update the total progress bar
    myDropzone4.on("totaluploadprogress", function(progress) {
        // $(this).find( id + " .progress-bar").css('width', progress + "%");
    });

    myDropzone4.on("sending", function(file) {
        // Show the total progress bar when upload starts
        // $( id + " .progress-bar").css('opacity', '1');
        // And disable the start button
        //  file.previewElement.querySelector(id + " .dropzone-start").setAttribute("disabled", "disabled");
    });

    // Hide the total progress bar when nothing's uploading anymore
    /*myDropzone4.on("complete", function(progress) {
        var thisProgressBar = id + " .dz-complete";
        setTimeout(function(){
            $( thisProgressBar + " .progress-bar, " + thisProgressBar + " .progress, " + thisProgressBar + " .dropzone-start").css('opacity', '0');
        }, 300);

    });*/

    // Setup the buttons for all transfers
    /*document.querySelector( id + " .dropzone-upload").onclick = function() {
        myDropzone4.enqueueFiles(myDropzone4.getFilesWithStatus(Dropzone.ADDED));
    };*/

    // Setup the button for remove all files
    document.querySelector(id + " .dropzone-remove-all").onclick = function() {
        $( id + " .dropzone-remove-all").css('display', 'none');
        myDropzone4.removeAllFiles(true);
    };

    // On all files completed upload
    /*myDropzone4.on("queuecomplete", function(progress){
        $( id + " .dropzone-upload").css('display', 'none');
    });*/

    // On all files removed
    myDropzone4.on("removedfile", function(file){
        if(myDropzone4.files.length < 1){
            $( id + " .dropzone-remove-all").css('display', 'none');
        }
    });
}
