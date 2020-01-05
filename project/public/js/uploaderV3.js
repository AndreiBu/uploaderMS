var HC = HC || {};

HC.Uploader = new function () {

    this.serialiseData = function (id) {
        return '&' + $(id).serialize();
    };

    this.after_ajax = function (data) {
    };

    this.unblockUI = function () {
        $.unblockUI();
    };

    this.blockUi = function () {
        $('.progressBar').hide().css('width', '0%');
        $.blockUI({
            css: {
                border: 'none',
                padding: '10px',
                backgroundColor: '#c1bfc1',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .9,
            },
            message: "<img src='/img/load.gif' style='height: 100px;'><br><span>Bitte warten...</span><div class=\"progressBar\"></div>"
        });
    };

    this.time = '';
    this.log = function(step)
    {
        if (step == 'start_upload') {
            HC.Uploader.time = new Date();
        }

        var t1 = new Date(),
            diff = t1 - HC.Uploader.time;

        $('.log').prepend("<div class=" + step + "> " + (diff) + " " + step + " </div>");
    }

    this.filesUploadBind = function () {
        var id = '.printDataUploader';
        this.dropBind(id);
        $(id).unbind('click')
            .click(function () {
                HC.Uploader.log('start');
                $('.progressBar').css('width', '0%').show();
                var position = '';
                if ($(this).data('option') != undefined) {
                    position = $(this).data('option');
                }
                var mediaid = '';
                if ($(this).data('mediaid') != undefined) {
                    mediaid = $(this).data('mediaid');
                }
                if (!HC.Uploader.sortable) {
                    $('#fileupload').attr('accept', $(this).data('accept'))
                        .data('position', position)
                        .data('mediaid', mediaid)
                        .click();
                }
                return false;
            });

        $('.upload__container-details').mouseenter(function () {
            $(this).addClass("dragover");
        }).mouseleave(function () {
            $(this).removeClass("dragover");
        });

        $(function () {
            $('#fileupload').fileupload({
                dataType: 'json',
                add: function (e, data) {
                    HC.Uploader.log('start_upload');
                    HC.Uploader.blockUi();
                    var uploadKey = $('#uploadKey').val(),
                        mediaId = $(this).data('mediaid'),
                        position = $(this).data('position');
                    if (mediaId == undefined) {
                        mediaId = '';
                    }
                    data.formData = {uploadKey: uploadKey, mediaId: mediaId, position: position};
                    data.context = $('<p/>').appendTo(document.body);
                    data.submit();
                },
                done: function (e, data) {
                    HC.Uploader.log('uploaded');
                    HC.Uploader.fileUploadCallback(data);
                }, progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('.progressBar').show().css('width', progress + '%');
                }
                , stop: function (e, data) {
                    HC.Uploader.log('stop');
                    $.unblockUI();
                    $('.progressBar').hide().css('width', '0%');
                }, fail: function (e) {
                    console.log(e);
                    $.unblockUI();
                }
            });
        });
    };
    this.unblockTimeout;
    this.fileUploadCallback = function (data) {
        $('.log').prepend(data.result.html + '<a href="'+data.result.file +'" target="blank">file</a><img style="height: 50px;" src="'+data.result.thumbnail + '">');
        if (data.result.html != undefined && data.result.html != '') {
            $('.hc__uploader').html(data.result.html);
            HC.Uploader.binds();
        }
        $('#fileupload').data('mediaid', '');
    };
    this.dropBind = function (id) {
        $(id).on('drop', function (event) {
            var mediaid = '';
            if ($(this).data('mediaid') != undefined) {
                mediaid = $(this).data('mediaid');
            }
            var position = '';
            if ($(this).data('option') != undefined) {
                position = $(this).data('option');
            }
            $('#fileupload').data('mediaid', mediaid);
            $('#fileupload').data('position', position);
        });

        $('.upload__container-details').on('dragover', function () {
            $(this).addClass("dragover");
        }).on('dragleave', function () {
            $(this).removeClass("dragover");
        });
    };

    this.afterDeleteFile = function (data) {
        if (data.html != undefined && data.html != '') {
            $('.hc__uploader').html(data.html);
            HC.Uploader.binds();
        }
    };


    this.imageZoomBind = function (id) {
        $('.bigImage').remove();
        $('.imageZoom').unbind('mouseenter mouseleave').mouseenter(function () {
            var src = $(this).attr('src'),
            imgHtml = '<div class="bigImage"><img src="' + src + '"></div>',
            imageWidth = this.naturalWidth, 
            imageHeight = this.naturalHeight, 
            imageSize = imageWidth + imageHeight,
            body = $('body');

            body.append(imgHtml);

            if (imageSize < 512) {
                body.find('.bigImage img').css('object-fit', 'scale-down');
            }
            
        }).mouseleave(function () {
            $('.bigImage').remove();
        });
    };
    
    this.binds = function () {
        this.filesUploadBind();
        this.imageZoomBind();
    };
};

$(document).ready(function () {
    HC.Uploader.binds();
});

