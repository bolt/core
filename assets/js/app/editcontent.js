/**
 * Set up editcontent stuff
 *
 * @mixin
 * @namespace Bolt.editcontent
 *
 * @param {Object} bolt - The Bolt module.
 * @param {Object} $ - jQuery.
 * @param {Object} window - Global window object.
 * @param {Object} moment - Global moment object.
 * @param {Object} bootbox - Global bootbox object.
 * @param {Object|undefined} ckeditor - CKEDITOR global or undefined.
 */
(function(bolt, $, window, moment, bootbox, ckeditor) {
    'use strict';

    /**
     * Bind data.
     *
     * @typedef {Object} BindData
     * @memberof Bolt.editcontent
     *
     * @property {string} bind - Always 'editcontent'.
     * @property {boolean} hasGroups - Has groups.
     * @property {string} msgNotSaved - Message when entry could not be saved.
     * @property {boolean} newRecord - Is new Record?
     * @property {string} savedOn - "Saved on" template.
     * @property {string} singularSlug - Contenttype slug.
     * @property {string} previewUrl - The preview url.
     */

    /**
     * Bolt.editcontent mixin container.
     *
     * @private
     * @type {Object}
     */
    const editcontent = {};

    /**
     * Gets the current value of an input element processed to be comparable
     *
     * @static
     * @function getComparable
     * @memberof Bolt.editcontent
     *
     * @param {Object} item - Input element
     *
     * @returns {string|undefined}
     */
    function getComparable(item) {
        let val;

        if (item.name) {
            val = $(item).val();
            if (item.type === 'select-multiple') {
                val = JSON.stringify(val);
            }
        }

        return val;
    }

    /**
     * Detect if changes were made to the content.
     *
     * @static
     * @function hasChanged
     * @memberof Bolt.editcontent
     *
     * @returns {boolean}
     */
    function hasChanged() {
        let changes = 0;

        $('form[name="content_edit"]')
            .find('input, textarea, select')
            .each(function() {
                if ($(this).attr('name') !== 'content_edit[save]') {
                    if (this.type === 'textarea' && $(this).hasClass('ckeditor')) {
                        if (ckeditor.instances[this.id].checkDirty()) {
                            changes++;
                        }
                    } else {
                        let val = getComparable(this);
                        if (val !== undefined && $(this).data('watch') !== val) {
                            changes++;
                        }
                    }
                }
            });

        return changes > 0;
    }

    /**
     * Remember current state of content for change detection.
     *
     * @static
     * @function watchChanges
     * @memberof Bolt.editcontent
     */
    function watchChanges() {
        $('form[name="content_edit"]')
            .find('input, textarea, select')
            .each(function() {
                let val = getComparable(this);
                if (val !== undefined) {
                    $(this).data('watch', val);
                }
            });

        // Initialize handler for 'closing window'
        window.onbeforeunload = function() {
            if (hasChanged()) {
                return bolt.data('editcontent.msg.change_quit');
            }
        };
    }

    /**
     * Disable the "save" buttons, to indicate stuff is being done in the background.
     *
     * @static
     * @function indicateSavingAction
     * @memberof Bolt.editcontent
     */
    function indicateSavingAction() {
        $('button[name="save"]')
            .addClass('disabled')
            .text(bolt.data('editcontent.msg.saving'));
        $('button[name="save"] i, button[name="save"] i').addClass('fa-spin fa-spinner');
    }

    /**
     * Initialize "save" button handlers.
     *
     * Clicking the button either triggers an "ajaxy" post, or a regular post which returns to this page.
     * The latter happens if the record doesn't exist yet, so it doesn't have an id yet.
     *
     * @static
     * @function initSave
     * @memberof Bolt.editcontent
     *
     * @fires "Bolt.Content.Save.Start"
     * @fires "Bolt.Content.Save.Done"
     * @fires "Bolt.Content.Save.Fail"
     * @fires "Bolt.Content.Save.Always"
     *
     * @param {BindData} data - Editcontent configuration data
     */
    function initSave(data) {
        $('#sidebar_save').bind('click', function() {
            $('#content_edit_save').trigger('click');
        });

        $('#content_edit_save').bind('click', function(e) {
            e.preventDefault();

            let editForm = $('form[name="content_edit"]');

            // Trigger form validation
            editForm.trigger('boltvalidate');
            // Check validation
            if (!editForm.data('valid')) {
                return false;
            }

            let newrecord = data.newRecord,
                savedon = data.savedOn,
                msgNotSaved = data.msgNotSaved;

            indicateSavingAction();

            if (newrecord) {
                watchChanges();

                if (bolt.liveEditor.active) {
                    bolt.liveEditor.stop();
                }

                if (data.duplicate) {
                    window.onbeforeunload = null;
                }

                // New record. Do a regular post, and expect to be redirected back to this page.
                bolt.actions.submit(editForm, this);
            } else {
                watchChanges();

                // Trigger save started event
                bolt.events.fire('Bolt.Content.Save.Start');

                // Existing record. Do an 'ajaxy' post to update the record.
                // Let the controller know we're calling AJAX and expecting to be returned JSON.
                let button = $(e.target);
                let postData =
                    editForm.serialize() + '&' + encodeURI(button.attr('name')) + '=' + encodeURI(button.attr('value'));
                $.post('', postData)
                    .done(function(data) {
                        bolt.events.fire('Bolt.Content.Save.Done', { form: data });

                        // Submit was successful, disable warning.
                        window.onbeforeunload = null;

                        $('p.lastsaved')
                            .removeClass('alert alert-danger')
                            .html(savedon)
                            .find('strong')
                            .text(moment(data.datechanged).format('MMM D, HH:mm'))
                            .end()
                            .find('.buic-moment')
                            .buicMoment()
                            .buicMoment('set', data.datechanged);

                        let elSelected = $('#statusselect').find('option:selected');
                        $('a#lastsavedstatus strong').html(
                            '<i class="fa fa-circle status-' + elSelected.val() + '"></i> ' + elSelected.text(),
                        );
                        // Display the 'save succeeded' icon in the buttons
                        $('#sidebar_save i, #content_edit_save i')
                            .removeClass('fa-flag fa-spin fa-spinner fa-exclamation-triangle')
                            .addClass('fa-check');

                        // Update anything changed by POST_SAVE handlers, as well as the 'view saved version on site' link
                        if ($.type(data) === 'object') {
                            $.each(data, function(index, item) {
                                // Things like images are stored in JSON arrays
                                if ($.type(item) === 'object') {
                                    $.each(item, function(subindex, subitem) {
                                        $(':input[name="' + index + '[' + subindex + ']"]').val(subitem);
                                    });
                                } else if ($.type(item) === 'array') {
                                    // In 2.3 we return filelists, and imagelist
                                    // as an array of "objects"… because JSON…
                                    // and they now fail here because… JavaScript…
                                    // so we're catching arrays and ignoring
                                    // them, someone else can fix this!
                                } else {
                                    let field = $(editForm.name).find('[name=' + index + ']');

                                    if (field.attr('type') === 'checkbox') {
                                        // A checkbox, so set with prop
                                        field.prop('checked', item === '1');
                                    } else {
                                        // Either an input or a textarea, so set with val
                                        field.val(item);
                                    }

                                    // If there is a CKEditor attached to our element, update it
                                    if (ckeditor && ckeditor.instances[index]) {
                                        ckeditor.instances[index].setData(item, {
                                            callback: function() {
                                                this.resetDirty();
                                            },
                                        });
                                    }
                                }
                            });

                            // Update the "View saved version on site" link.
                            $('a[data-href-placeholder]').each(function() {
                                let link = $(this)
                                    .data('href-placeholder')
                                    .replace('__replaceme', data['slug']);
                                $(this).attr('href', link);
                            });
                        }
                        // Update dates and times from new values
                        bolt.datetime.update();

                        watchChanges();
                    })
                    .fail(function(data) {
                        bolt.events.fire('Bolt.Content.Save.Fail');

                        let response = $.parseJSON(data.responseText);
                        let message = '<b>' + msgNotSaved + '</b><br><small>' + response.error.message + '</small>';

                        $('p.lastsaved')
                            .html(message)
                            .addClass('alert alert-danger');

                        // Display the 'save failed' icon in the buttons
                        $('#sidebar_save i, #content_edit_save i')
                            .removeClass('fa-flag fa-spin fa-spinner')
                            .addClass('fa-exclamation-triangle');
                    })
                    .always(function() {
                        bolt.events.fire('Bolt.Content.Save.Always');

                        // Re-enable buttons
                        window.setTimeout(function() {
                            $('#sidebar_save, #content_edit_save, #live_editor_save')
                                .removeClass('disabled')
                                .blur();
                        }, 1000);
                        window.setTimeout(function() {
                            $('#sidebar_save i, #content_edit_save i').addClass('fa-flag');
                        }, 5000);
                    });
            }
        });
    }

    /**
     * Initializes the mixin.
     *
     * @static
     * @function init
     * @memberof Bolt.editcontent
     *
     * @param {BindData} data - Editcontent configuration data
     */
    editcontent.init = function(data) {
        initSave(data);
    };

    // Apply mixin container.
    bolt.editcontent = editcontent;
})(Bolt || {}, jQuery, window, moment, bootbox);
