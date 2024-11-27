"use strict";
// Class definition

var KTDropzoneDemo = function () {
    var cd = console.log;
    var demo1 = function () {
        // set the dropzone container id
        var id = '#kt_dropzone_4';

        // set the preview element template
        var previewNode = $(id + " .dropzone-item");
        previewNode.id = "";
        var previewTemplate = previewNode.parent('.dropzone-items').html();
        previewNode.remove();
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        var myDropzone4 = new Dropzone(id, { // Make the whole body a dropzone
            url: "http://127.0.0.1:8000/upload-document-files", // Set the url for your upload script location
            parallelUploads: 20,
            previewTemplate: previewTemplate,
            maxFilesize: 1, // Max filesize in MB
            uploadMultiple:true,
            paramName: 'document_list[0][files]',
            // autoQueue: false, // Make sure the files aren't queued until manually added
            previewsContainer: id + " .dropzone-items", // Define the container to display the previews
            clickable: id + " .dropzone-select", // Define the element that should be used as click trigger to select files.
            headers: {
                'x-csrf-token': CSRF_TOKEN,
            },
            autoProcessQueue: false
        });

        myDropzone4.on("addedfile", function(file) {
            // Hookup the start button
            file.previewElement.querySelector(id + " .dropzone-start").onclick = function() { myDropzone4.enqueueFile(file); };
            $(document).find( id + ' .dropzone-item').css('display', '');
            $( id + " .dropzone-upload, " + id + " .dropzone-remove-all").css('display', 'inline-block');
        });

        // Update the total progress bar
        myDropzone4.on("totaluploadprogress", function(progress) {
            $(this).find( id + " .progress-bar").css('width', progress + "%");
        });

        myDropzone4.on("sending", function(file,formData) {
            // Show the total progress bar when upload starts
            // $( id + " .progress-bar").css('opacity', '1');
            // And disable the start button
            //  file.previewElement.querySelector(id + " .dropzone-start").setAttribute("disabled", "disabled");
        });

        // Hide the total progress bar when nothing's uploading anymore
        myDropzone4.on("complete", function(progress) {
            var thisProgressBar = id + " .dz-complete";
            setTimeout(function(){
                $( thisProgressBar + " .progress-bar, " + thisProgressBar + " .progress, " + thisProgressBar + " .dropzone-start").css('opacity', '0');
            }, 300);

        });

        // Setup the buttons for all transfers
        document.querySelector( id + " .dropzone-upload").onclick = function() {
            myDropzone4.enqueueFiles(myDropzone4.getFilesWithStatus(Dropzone.ADDED));
        };

        // Setup the button for remove all files
        document.querySelector(id + " .dropzone-remove-all").onclick = function() {
            $( id + " .dropzone-upload, " + id + " .dropzone-remove-all").css('display', 'none');
            myDropzone4.removeAllFiles(true);
        };

        // On all files completed upload
        myDropzone4.on("queuecomplete", function(progress){
            $( id + " .dropzone-upload").css('display', 'none');
        });

        // On all files removed
        myDropzone4.on("removedfile", function(file){
            if(myDropzone4.files.length < 1){
                $( id + " .dropzone-upload, " + id + " .dropzone-remove-all").css('display', 'none');
            }
        });
        myDropzone4.on("sendingmultiple", function(data, xhr, formData) {
            var data = $('#employee_form').serializeArray();
            $.each(data, function(key, el) {
                formData.append(el.name, el.value);
            });
        });
        $(".btn-success").click(function (e) {
            myDropzone4.processQueue();
        });
    }
    var demo2 = function () {
        // set the dropzone container id
        var id = '#kt_dropzone_4';

        // set the preview element template
        var previewNode = $(id + " .dropzone-item");
        previewNode.id = "";
        var previewTemplate = previewNode.parent('.dropzone-items').html();
        previewNode.remove();
        var myDropzone4 = new Dropzone(id, { // Make the whole body a dropzone
            url: "/upload-document-files", // Set the url for your upload script location
            parallelUploads: 20,
            previewTemplate: previewTemplate,
            maxFilesize: 1, // Max filesize in MB
            autoQueue: false, // Make sure the files aren't queued until manually added
            previewsContainer: id + " .dropzone-items", // Define the container to display the previews
            clickable: id + " .dropzone-select", // Define the element that should be used as click trigger to select files.
            headers: {
                'x-csrf-token': $('meta[name="csrf-token"]').attr('content'),
            },
        });

        myDropzone4.on("addedfile", function(file) {
            // Hookup the start button
            file.previewElement.querySelector(id + " .dropzone-start").onclick = function() { myDropzone4.enqueueFile(file); };
            $(document).find( id + ' .dropzone-item').css('display', '');
            $( id + " .dropzone-upload, " + id + " .dropzone-remove-all").css('display', 'inline-block');
        });

        // Update the total progress bar
        myDropzone4.on("totaluploadprogress", function(progress) {
            $(this).find( id + " .progress-bar").css('width', progress + "%");
        });

        myDropzone4.on("sending", function(file) {
            // Show the total progress bar when upload starts
            $( id + " .progress-bar").css('opacity', '1');
            // And disable the start button
            file.previewElement.querySelector(id + " .dropzone-start").setAttribute("disabled", "disabled");
        });

        // Hide the total progress bar when nothing's uploading anymore
        myDropzone4.on("complete", function(progress) {
            var thisProgressBar = id + " .dz-complete";
            setTimeout(function(){
                $( thisProgressBar + " .progress-bar, " + thisProgressBar + " .progress, " + thisProgressBar + " .dropzone-start").css('opacity', '0');
            }, 300);

        });

        // Setup the buttons for all transfers
        document.querySelector( id + " .dropzone-upload").onclick = function() {
            myDropzone4.enqueueFiles(myDropzone4.getFilesWithStatus(Dropzone.ADDED));
        };

        // Setup the button for remove all files
        document.querySelector(id + " .dropzone-remove-all").onclick = function() {
            $( id + " .dropzone-upload, " + id + " .dropzone-remove-all").css('display', 'none');
            myDropzone4.removeAllFiles(true);
        };

        // On all files completed upload
        myDropzone4.on("queuecomplete", function(progress){
            $( id + " .dropzone-upload").css('display', 'none');
        });

        // On all files removed
        myDropzone4.on("removedfile", function(file){
            if(myDropzone4.files.length < 1){
                $( id + " .dropzone-upload, " + id + " .dropzone-remove-all").css('display', 'none');
            }
        });
    }

    var demo3 = function (){
        // multiple file upload
        $('.kt_dropzone').each(function(){
            cd($(this));
            $(this).dropzone({
                url: "/form-upload-document-files", // Set the url for your upload script location
                paramName: "file", // The name that will be used to transfer the file
                maxFiles: 10,
                maxFilesize: 10, // MB
                addRemoveLinks: true,
                autoProcessQueue: false,
                uploadMultiple: true,
                parallelUploads: 5,
                headers: {
                    'x-csrf-token': $('meta[name="csrf-token"]').attr('content'),
                },
                accept: function(file, done) {
                    cd('done');
                    if (file.name == "justinbieber.jpg") {
                        done("Naha, you don't.");
                    } else {
                        done();
                    }
                },
                init: function () {
                    cd(this);
                    var myDropzone = this;
                    $("button").click(function (e) {
                        e.preventDefault();
                        myDropzone.processQueue(); // process dropzone instance 1
                    });
                    myDropzone.on('sendingmultiple', function(file, xhr, formData) {
                        var form_upload_doc = $('#upload_doc').serializeArray();
                        $.each(form_upload_doc, function(key, el) {
                            formData.append(el.name, el.value);
                        });
                    });
                    myDropzone.on('successmultiple', function(files, response) {
                        //window.location.replace("/home");
                    });
                    myDropzone.on('errormultiple', function(files, response) {
                        cd(response);
                    });
                }
            });
        });

        /*$('.kt_dropzone_1w').dropzone({
            url: "/form-upload-document-files", // Set the url for your upload script location
            paramName: "file", // The name that will be used to transfer the file
            maxFiles: 10,
            maxFilesize: 10, // MB
            addRemoveLinks: true,
            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 5,
            headers: {
                'x-csrf-token': $('meta[name="csrf-token"]').attr('content'),
            },
            accept: function(file, done) {
                cd('done');
                if (file.name == "justinbieber.jpg") {
                    done("Naha, you don't.");
                } else {
                    done();
                }
            },
            init: function () {
                myDropzone1 = this;
                myDropzone1.on('sendingmultiple', function(file, xhr, formData) {
                    var form_upload_doc = $('#upload_doc').serializeArray();
                    $.each(form_upload_doc, function(key, el) {
                        formData.append(el.name, el.value);
                    });
                });
                myDropzone1.on('successmultiple', function(files, response) {
                    //window.location.replace("/home");
                });
                myDropzone1.on('errormultiple', function(files, response) {
                    cd(response);
                });
            }
        });
        $('.kt_dropzone_2w').dropzone({
            url: "/form-upload-document-files", // Set the url for your upload script location
            paramName: "file", // The name that will be used to transfer the file
            maxFiles: 10,
            maxFilesize: 10, // MB
            addRemoveLinks: true,
            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: 5,
            headers: {
                'x-csrf-token': $('meta[name="csrf-token"]').attr('content'),
            },
            accept: function(file, done) {
                cd('done');
                if (file.name == "justinbieber.jpg") {
                    done("Naha, you don't.");
                } else {
                    done();
                }
            },
            init: function () {
                myDropzone2 = this;
                myDropzone2.on('sendingmultiple', function(file, xhr, formData) {
                    var form_upload_doc = $('#upload_doc').serializeArray();
                    $.each(form_upload_doc, function(key, el) {
                        formData.append(el.name, el.value);
                    });
                });
                myDropzone2.on('successmultiple', function(files, response) {
                    //window.location.replace("/home");
                });
                myDropzone2.on('errormultiple', function(files, response) {
                    cd(response);
                });
            }
        });
        $("button").click(function (e) {
            e.preventDefault();
            myDropzone1.processQueue(); // process dropzone instance 1
            myDropzone2.processQueue(); // process dropzone instance 2
        });*/
    }
    return {
        // public functions
        init: function() {
            demo3();
        }
    };
}();

KTUtil.ready(function() {
    KTDropzoneDemo.init();
});
