(function(window, $) {
    'use strict';

    function erpParseAjaxJson(xhr) {
        if (!xhr) {
            return null;
        }
        if (xhr.responseJSON && typeof xhr.responseJSON === 'object') {
            return xhr.responseJSON;
        }
        if (xhr.responseText) {
            try {
                return JSON.parse(xhr.responseText);
            } catch (e) {
                return null;
            }
        }
        return null;
    }

    function erpDocumentAjaxMessage(response, xhr) {
        if (response && response.message) {
            return response.message;
        }
        var parsed = erpParseAjaxJson(xhr);
        if (parsed && parsed.message) {
            return parsed.message;
        }
        return null;
    }

    function erpDocumentAjaxIsSuccess(response) {
        return !!(response && (response.status === 'success' || response.success === true));
    }

    function erpDocumentAjaxDone(response, xhr, options) {
        options = options || {};
        var parsed = response;
        if (!parsed || typeof parsed !== 'object') {
            parsed = erpParseAjaxJson(xhr);
        }
        var msg = erpDocumentAjaxMessage(parsed, xhr);
        var ok = erpDocumentAjaxIsSuccess(parsed);

        if (typeof toastr === 'undefined') {
            if (!ok && msg) {
                alert(msg);
            }
            return ok;
        }

        if (ok) {
            var toastOpts = {};
            if (options.reload !== false) {
                toastOpts.onHidden = function() {
                    if (options.redirect) {
                        window.location.href = options.redirect;
                    } else {
                        location.reload();
                    }
                };
                if (options.reloadDelay != null && options.reloadDelay >= 0) {
                    toastOpts.timeOut = options.reloadDelay;
                    toastOpts.extendedTimeOut = Math.min(1000, options.reloadDelay);
                }
            }
            toastr.success(msg || options.successMsg || 'Success', '', toastOpts);
            if (typeof options.onSuccess === 'function') {
                options.onSuccess(parsed, msg);
            }
            return true;
        }

        toastr.error(msg || options.errorMsg || 'Request failed');
        if (typeof options.onError === 'function') {
            options.onError(parsed, msg);
        }
        return false;
    }

    function erpFormAjaxDone(response, xhr, options) {
        options = options || {};
        var opts = {
            errorMsg: options.errorMsg || 'Request failed',
            successMsg: options.successMsg
        };
        if (options.reload === false) {
            opts.reload = false;
        }
        var data = response && response.data;
        if (data && data.redirect) {
            opts.redirect = data.redirect;
        } else if (data && data.form === 'new') {
            if (options.newFormUrl) {
                opts.redirect = data.redirect || (
                    typeof options.newFormUrl === 'function'
                        ? options.newFormUrl(response)
                        : options.newFormUrl
                );
            }
        } else if (options.reloadOnSuccess) {
            opts.reload = true;
        } else if (opts.reload !== false && !opts.redirect) {
            opts.reload = false;
        }
        return erpDocumentAjaxDone(response, xhr, opts);
    }

    function erpDocumentAjax(options) {
        var opts = options || {};
        var ajaxOpts = {
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: opts.type || 'POST',
            url: opts.url,
            data: opts.data,
            dataType: 'json',
            success: function(response, textStatus, xhr) {
                erpDocumentAjaxDone(response, xhr, opts);
            },
            error: function(xhr) {
                erpDocumentAjaxDone(erpParseAjaxJson(xhr), xhr, opts);
            }
        };
        return $.ajax(ajaxOpts);
    }

    /**
     * UM Post: optionally save the form first (when user can update), then post.
     * options: {
     *   documentId, idMissingMsg, postUrl, postData,
     *   form (selector|element), canUpdate (bool),
     *   successMsg, errorMsg, updateErrorMsg
     * }
     */
    function erpVoucherPostedUpdateThenPost(options) {
        var opts = options || {};
        var documentId = opts.documentId;
        if (!documentId) {
            if (typeof toastr !== 'undefined') {
                toastr.error(opts.idMissingMsg || 'Document id not found');
            }
            return;
        }

        function doPost() {
            erpDocumentAjax({
                url: opts.postUrl,
                data: opts.postData || {},
                successMsg: opts.successMsg || 'Successfully Posted.',
                errorMsg: opts.errorMsg || 'Unable to post.'
            });
        }

        var canUpdate = !!opts.canUpdate;
        var form = null;
        if (opts.form) {
            form = (opts.form && opts.form.nodeType === 1)
                ? opts.form
                : $(opts.form).get(0);
        }

        if (canUpdate && form && $('#btn-update-entry').length) {
            if (typeof $(form).valid === 'function' && !$(form).valid()) {
                return;
            }
            var formData = new FormData(form);
            $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                url: form.action,
                type: form.method || 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response, textStatus, xhr) {
                    if (response && response.status === 'error') {
                        erpDocumentAjaxDone(response, xhr, {
                            errorMsg: opts.updateErrorMsg || 'Unable to update before post.',
                            reload: false
                        });
                        return;
                    }
                    if (!erpDocumentAjaxIsSuccess(response) && !(xhr.status >= 200 && xhr.status < 300 && !response)) {
                        erpDocumentAjaxDone(response, xhr, {
                            errorMsg: opts.updateErrorMsg || 'Unable to update before post.',
                            reload: false
                        });
                        return;
                    }
                    doPost();
                },
                error: function(xhr) {
                    erpDocumentAjaxDone(erpParseAjaxJson(xhr), xhr, {
                        errorMsg: opts.updateErrorMsg || 'Unable to update before post.',
                        reload: false
                    });
                }
            });
            return;
        }

        doPost();
    }

    window.erpDocumentAjaxMessage = erpDocumentAjaxMessage;
    window.erpDocumentAjaxDone = erpDocumentAjaxDone;
    window.erpFormAjaxDone = erpFormAjaxDone;
    window.erpDocumentAjax = erpDocumentAjax;
    window.erpVoucherPostedUpdateThenPost = erpVoucherPostedUpdateThenPost;
})(window, jQuery);
