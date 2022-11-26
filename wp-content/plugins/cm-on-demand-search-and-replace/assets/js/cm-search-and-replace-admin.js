
(function ($) {

    $(document).ready(function () {

        /*
         * CUSTOM REPLACEMENTS
         */

        var datepicker_options = typeof window.cmodsar_data.datepicker_options !== 'undefined' ? window.cmodsar_data.datepicker_options : {};
        $('input.datepicker:visible').datetimepicker(datepicker_options);

        $(document).on('click', '#cmodsar-custom-add-replacement-btn', function () {
            var data, replace_from, replace_to, replace_case, replace_regex, replace_pause, replace_title, replace_content, replace_excerpt, replace_comments, replace_time_from, replace_time_to, valid = true;

            replace_from = $('.cmodsar-custom-replacement-add textarea[name="cmodsar_custom_from_new"]');
            replace_to = $('.cmodsar-custom-replacement-add textarea[name="cmodsar_custom_to_new"]');
            replace_case = $('.cmodsar-custom-replacement-add input[name="cmodsar_custom_case_new"]');
            replace_regex = $('.cmodsar-custom-replacement-add input[name="cmodsar_custom_regex_new"]');

            replace_pause = $('.cmodsar-custom-replacement-add input[name="cmodsar_custom_pause_new"]');

            replace_title = $('.cmodsar-custom-replacement-add input[name="cmodsar_custom_title_new"]');
            replace_content = $('.cmodsar-custom-replacement-add input[name="cmodsar_custom_content_new"]');
            replace_excerpt = $('.cmodsar-custom-replacement-add input[name="cmodsar_custom_excerpt_new"]');
            replace_comments = $('.cmodsar-custom-replacement-add input[name="cmodsar_custom_comments_new"]');

            replace_time_from = $('.cmodsar-custom-replacement-add input[name*="custom_time_from"]:enabled');
            replace_time_to = $('.cmodsar-custom-replacement-add input[name*="custom_time_to"]:enabled');

            if (replace_from.val() === '') {
                replace_from.addClass('invalid');
                valid = false;
            }
            else {
                replace_from.removeClass('invalid');
            }

            /*
			if (replace_to.val() === '') {
                replace_to.addClass('invalid');
                valid = false;
            } else {
                replace_to.removeClass('invalid');
            }
			*/

            if (!valid) {
                return false;
            }

            data = {
                action: 'cmodsar_add_replacement',
                replace_from: replace_from.val(),
                replace_to: replace_to.val(),
                replace_case: replace_case.is(':checked') ? 1 : 0,
                replace_regex: replace_regex.is(':checked') ? 1 : 0,
                replace_pause: replace_pause.is(':checked') ? 1 : 0,
                replace_title: replace_title.is(':checked') ? 1 : 0,
                replace_content: replace_content.is(':checked') ? 1 : 0,
                replace_excerpt: replace_excerpt.is(':checked') ? 1 : 0,
                replace_comments: replace_comments.is(':checked') ? 1 : 0,
                replace_time_from: $(replace_time_from).map(function () {
                    return $(this).val();
                }).get(),
                replace_time_to: $(replace_time_to).map(function () {
                    return $(this).val();
                }).get()
            };

            $('.custom_loading').fadeIn('fast');

            $.post(window.cmodsar_data.ajaxurl, data, function (response) {
                $('.cmodsar_replacements_list').html(response);
                $('.custom_loading').fadeOut('fast');


                replace_from.val('');
                replace_to.val('');
                replace_case.val('');
                replace_regex.val('');
                replace_pause.val('');

                replace_title.val('');
                replace_content.val('');
                replace_excerpt.val('');
                replace_comments.val('');

                replace_time_from.closest('tr').find('.cmodsar-custom-delete-restriction').trigger('click', [{"silent": true}]);

                $('input.datepicker:visible').datetimepicker(datepicker_options);

                $('div.cmodsar_place_disable_wrapper input[type="checkbox"]').trigger('cmodsar_checkCounts');
            });
        });

        $(document).on('click', '.cmodsar-custom-delete-restriction', function (e, attr) {
            if (typeof attr !== 'undefined' && attr.silent)
            {
                var parentRow = $(this).closest('tr').remove();
            }
            else
            {
                if (window.window.confirm('Do you really want to delete this restriction row?')) {
                    var parentRow = $(this).closest('tr').remove();
                }
            }
        });

        $(document).on('click', '.cmodsar-custom-time-restriction-add-new', function () {
            var parentTable = $(this).closest('td.cmodsar_time_input').find('.cmodsar_time_restriction_wrapper table tbody');
            var newRow = parentTable.find('tr.to-copy').clone();
            newRow.removeClass('to-copy').show().find('input').attr('disabled', false);
            parentTable.append(newRow);
            parentTable.find('input.datepicker:visible').datetimepicker(datepicker_options);
        });

        $(document).on('click', '.cmodsar-custom-place-restriction-add-new', function () {
            var exclusionList = $(this).siblings('div.cmodsar_place_disable_wrapper');
            exclusionList.toggle('fast');
        });

        var checkExclusionCounts = function () {
            var button = $(this).closest('div.cmodsar_place_disable_wrapper').siblings('.cmodsar-custom-place-restriction-add-new');
            var checkboxes = $(this).closest('div.cmodsar_place_disable_wrapper').find('input[type="checkbox"]').length;
            var checkboxesChecked = $(this).closest('div.cmodsar_place_disable_wrapper').find('input[type="checkbox"]:checked').length;

            button.val('Add Exclusion ('+checkboxesChecked+'/'+checkboxes+')');
        };

        $(document).on('change', 'div.cmodsar_place_disable_wrapper input[type="checkbox"]', checkExclusionCounts);

        $(document).on('cmodsar_checkCounts', 'div.cmodsar_place_disable_wrapper input[type="checkbox"]', checkExclusionCounts);
        $('div.cmodsar_place_disable_wrapper input[type="checkbox"]').trigger('cmodsar_checkCounts');

        $(document).on('click', '.cmodsar-custom-delete-replacement', function () {
            if (window.window.confirm('Do you really want to delete this replacement?')) {
                var data = {
                    action: 'cmodsar_delete_replacement',
                    id: $(this).data('rid')
                };
                $('.custom_loading').fadeIn('fast');
                $.post(window.cmodsar_data.ajaxurl, data, function (response) {
                    $('.cmodsar_replacements_list').html(response);
                    $('.custom_loading').fadeOut('fast');
                    $('div.cmodsar_place_disable_wrapper input[type="checkbox"]').trigger('cmodsar_checkCounts');
                });
            } else {
                $('.custom_loading').fadeOut('fast');
            }
        });

        $(document).on('click', '.cmodsar-custom-update-replacement', function () {
            if (window.window.confirm('Do you really want to update this replacement?')) {

                var data, id, replace_from, replace_to, replace_case, replace_regex, replace_pause, replace_title, replace_content, replace_excerpt, replace_comments, replace_time_from, replace_time_to, valid = true;

                id = $(this).data('uid');
                replace_from = $('.cmodsar_replacements_list textarea[name="cmodsar_custom_from[' + id + ']"]');
                replace_to = $('.cmodsar_replacements_list textarea[name="cmodsar_custom_to[' + id + ']"]');
                replace_case = $('.cmodsar_replacements_list input[name="cmodsar_custom_case[' + id + ']"]');
                replace_regex = $('.cmodsar_replacements_list input[name="cmodsar_custom_regex[' + id + ']"]');

                replace_pause = $('.cmodsar_replacements_list input[name="cmodsar_custom_pause[' + id + ']"]');

                replace_title = $('.cmodsar_replacements_list input[name="cmodsar_custom_title[' + id + ']"]');
                replace_content = $('.cmodsar_replacements_list input[name="cmodsar_custom_content[' + id + ']"]');
                replace_excerpt = $('.cmodsar_replacements_list input[name="cmodsar_custom_excerpt[' + id + ']"]');
                replace_comments = $('.cmodsar_replacements_list input[name="cmodsar_custom_comments[' + id + ']"]');

                replace_time_from = $('.cmodsar_replacements_list input[name*="custom_time_from[' + id + ']"]:enabled');
                replace_time_to = $('.cmodsar_replacements_list input[name*="custom_time_to[' + id + ']"]:enabled');

                if (replace_from.val() === '') {
                    replace_from.addClass('invalid');
                    valid = false;
                }
                else {
                    replace_from.removeClass('invalid');
                }

                if (replace_to.val() === '') {
                    replace_to.addClass('invalid');
                    valid = false;
                } else {
                    replace_to.removeClass('invalid');
                }

                if (!valid) {
                    return false;
                }

                data = {
                    action: 'cmodsar_update_replacement',
                    replace_id: $(this).data('uid'),
                    replace_from: replace_from.val(),
                    replace_to: replace_to.val(),
                    replace_case: replace_case.is(':checked') ? 1 : 0,
                    replace_regex: replace_regex.is(':checked') ? 1 : 0,
                    replace_pause: replace_pause.is(':checked') ? 1 : 0,
                    replace_title: replace_title.is(':checked') ? 1 : 0,
                    replace_content: replace_content.is(':checked') ? 1 : 0,
                    replace_excerpt: replace_excerpt.is(':checked') ? 1 : 0,
                    replace_comments: replace_comments.is(':checked') ? 1 : 0,
                    replace_time_from: $(replace_time_from).map(function () {
                        return $(this).val();
                    }).get(),
                    replace_time_to: $(replace_time_to).map(function () {
                        return $(this).val();
                    }).get()
                };
                $('.custom_loading').fadeIn('fast');
                $.post(window.cmodsar_data.ajaxurl, data, function (response) {
                    $('.cmodsar_replacements_list').html(response);
                    $('.custom_loading').fadeOut('fast');

                    $('input.datepicker:visible').datetimepicker(datepicker_options);
                    $('div.cmodsar_place_disable_wrapper input[type="checkbox"]').trigger('cmodsar_checkCounts');
                });
            } else {
                $('.custom_loading').fadeOut('fast');
            }
        });

        /*
         * RELATED ARTICLES
         */
        $.fn.add_new_replacement_row = function () {
            var articleRow, articleRowHtml, rowId;

            rowId = $(".custom-related-article").length;
            articleRow = $('<div class="custom-related-article"></div>');
            articleRowHtml = $('<input type="text" name="custom_related_article_name[]" style="width: 40%" id="custom_related_article_name" value="" placeholder="Name"><input type="text" name="custom_related_article_url[]" style="width: 50%" id="custom_related_article_url" value="" placeholder="http://"><a href="#javascript" class="custom_related_article_remove">Remove</a>');
            articleRow.append(articleRowHtml);
            articleRow.attr('id', 'custom-related-article-' + rowId);

            $("#custom-related-article-list").append(articleRow);
            return false;
        };

        $.fn.delete_replacement_row = function (row_id) {
            $("#custom-related-article-" + row_id).remove();
            return false;
        };

        /*
         * Added in 2.7.7 remove replacement_row
         */
        $(document).on('click', 'a.custom_related_article_remove', function () {
            var $this = $(this), $parent;
            $parent = $this.parents('.custom-related-article').remove();
            return false;
        });

        /*
         * Added in 2.4.9 (shows/hides the explanations to the variations/synonyms/abbreviations)
         */
        $(document).on('click showHideInit', '.cm-showhide-handle', function () {
            var $this = $(this), $parent, $content;

            $parent = $this.parent();
            $content = $this.siblings('.cm-showhide-content');

            if (!$parent.hasClass('closed'))
            {
                $content.hide();
                $parent.addClass('closed');
            }
            else
            {
                $content.show();
                $parent.removeClass('closed');
            }
        });

        $('.cm-showhide-handle').trigger('showHideInit');

        /*
         * CUSTOM REPLACEMENTS - END
         */

        if ($.fn.tabs) {
            $('#cmodsar_tabs').tabs({
                activate: function (event, ui) {
                    window.location.hash = ui.newPanel.attr('id').replace(/-/g, '_');
                },
                create: function (event, ui) {
                    var tab = location.hash.replace(/\_/g, '-');
                    var tabContainer = $(ui.panel.context).find('a[href="' + tab + '"]');
                    if (typeof tabContainer !== 'undefined' && tabContainer.length)
                    {
                        var index = tabContainer.parent().index();
                        $(ui.panel.context).tabs('option', 'active', index);
                    }
                }
            });
        }

        $('.cmodsar_field_help_container').each(function () {
            var newElement,
                    element = $(this);

            newElement = $('<div class="cmodsar_field_help"></div>');
            newElement.attr('title', element.html());

            if (element.siblings('th').length)
            {
                element.siblings('th').append(newElement);
            }
            else
            {
                element.siblings('*').append(newElement);
            }
            element.remove();
        });

        $('.cmodsar_field_help').tooltip({
            show: {
                effect: "slideDown",
                delay: 100
            },
            position: {
                my: "left top",
                at: "right top"
            },
            content: function () {
                var element = $(this);
                return element.attr('title');
            },
            close: function (event, ui) {
                ui.tooltip.hover(
                        function () {
                            $(this).stop(true).fadeTo(400, 1);
                        },
                        function () {
                            $(this).fadeOut("400", function () {
                                $(this).remove();
                            });
                        });
            }
        });

    });

})(jQuery);
