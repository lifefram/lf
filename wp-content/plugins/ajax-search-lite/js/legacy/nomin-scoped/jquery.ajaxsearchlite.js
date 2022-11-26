(function(jQuery, $, window){
/*! Ajax Search Lite 4.6 js */
;if ( typeof jQuery != 'undefined' ) {(function ($) {
    var prevState;
    var firstIteration = true;
    var methods = {

        init: function (options, elem) {
            var $this = this;

            this.elem = elem;
            this.$elem = $(elem);

            $this.searching = false;
            $this.o = $.extend({
                'blocking': false
            }, options);
            $this.n = {};
            $this.n.search =  $(this.elem);
            $this.n.container = $this.n.search.closest('.asl_w_container');
            $this.o.id = $this.n.search.data('id');
            $this.o.iid = $this.n.search.data('instance');
            $this.o.rid = $this.o.id;
            $this.n.probox = $('.probox', $this.n.search);
            $this.n.proinput = $('.proinput', $this.n.search);
            $this.n.text = $('.proinput input.orig', $this.n.search);
            $this.n.textAutocomplete = $('.proinput input.autocomplete', $this.n.search);
            $this.n.loading = $('.proinput .loading', $this.n.search);
            $this.n.proloading = $('.proloading', $this.n.search);
            $this.n.proclose = $('.proclose', $this.n.search);
            $this.n.promagnifier = $('.promagnifier', $this.n.search);
            $this.n.prosettings = $('.prosettings', $this.n.search);

            $this.n.searchsettings = $('.asl_s', $this.n.container);
            $this.n.resultsDiv = $('.asl_r', $this.n.container);

            // Fix any potential clones and adjust the variables
            $this.fixClonedSelf();

            $this.n.searchsettings = $this.n.searchsettings.clone();
            $('body').append($this.n.searchsettings);
            $this.n.searchsettings.get(0).id = $this.n.searchsettings.get(0).id.replace('__original__', '');

            $this.n.resultsDiv = $this.n.resultsDiv.clone();
            if ($this.o.resultsposition == 'hover') {
                $('body').append($this.n.resultsDiv);
            } else if ($this.n.resultsAppend.length > 0) {
                $this.n.resultsAppend.append($this.n.resultsDiv);
            }
            $this.n.resultsDiv.get(0).id = $this.n.resultsDiv.get(0).id.replace('__original__', '');

            $this.n.hiddenContainer = $('#asl_hidden_data');
            $this.n.aslItemOverlay = $('.asl_item_overlay', $this.n.hiddenContainer);

            $this.resizeTimeout = null;

            $this.n.showmore = $('.showmore', $this.n.resultsDiv);
            $this.n.items = $('.item', $this.n.resultsDiv);
            $this.n.results = $('.results', $this.n.resultsDiv);
            $this.n.resdrg = $('.resdrg', $this.n.resultsDiv);

            $this.post = null;
            $this.postAuto = null;

            $this.n.textAutocomplete.val('');
            $this.scroll = {};
            $this.savedScrollTop = 0;   // Save the window scroll on IOS devices
            $this.savedContainerTop = 0;
            $this.is_scroll =  typeof asp_SimpleBar != "undefined";
            // Force noscroll on minified version
            if ( typeof ASL.scrollbar != "undefined" && ASL.scrollbar == 0 )
                $this.is_scroll = false;
            $this.settScroll = null;
            $this.n.resultsAppend = $('#wpdreams_asl_results_' + $this.o.id);

            $this.o.redirectOnClick = $this.o.trigger.click != 'ajax_search' && $this.o.trigger.click != 'nothing';
            $this.o.redirectOnEnter = $this.o.trigger.return != 'ajax_search' && $this.o.trigger.return != 'nothing';

            $this.lastSuccesfulSearch = ''; // Holding the last phrase that returned results
            $this.lastSearchData = {};      // Store the last search information
            $this.triggerPrevState = false;


            //$this.n.searchsettings.detach().appendTo("body");



            // Memorize settins and results original HTML codes
            if ( typeof(ASL.resHTML) == 'undefined' )
                ASL.resHTML = $this.n.resultsDiv.html();
            if ( typeof(ASL.setHTML) == 'undefined' )
                ASL.setHTML = $this.n.searchsettings.html();

            $('fieldset' ,$this.n.searchsettings).each(function(){
                $('.asl_option:not(.hiddend)', this).last().addClass("asl-o-last");
            });

            $this.monitorTouchMove();

            if (detectOldIE())
                $this.n.search.addClass('asl_msie');

            // Calculates the settings animation attributes
            $this.initSettingsAnimations();

            // Calculates the results animation attributes
            $this.initResultsAnimations();

            $this.initEvents();

            // Auto populate init
            $this.initAutop();

            $this.initEtc();

            return this;
        },

        monitorTouchMove: function() {
            var $this = this, $b = $("body");
            $this.dragging = false;
            $b.on("touchmove", function(){
                $this.dragging = true;
            });
            $b.on("touchstart", function(){
                $this.dragging = false;
            });
        },

        gaPageview: function(term) {
            var $this = this;
            var tracking_id = $this.gaGetTrackingID();

            if ( typeof ASL.analytics == 'undefined' || ASL.analytics.method != 'pageview' )
                return false;

            if ( ASL.analytics.string != '' ) {
                // YOAST uses __gaTracker, if not defined check for ga, if nothing go null, FUN EH??
                var _ga = typeof __gaTracker == "function" ? __gaTracker : (typeof ga == "function" ? ga : false);
                var _gtag = typeof gtag == "function" ? gtag : false;

                if (!window.location.origin) {
                    window.location.origin = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
                }
                // Multisite Subdirectory (if exists)
                var url = $this.o.homeurl.replace(window.location.origin, '');

                // GTAG bypass pageview tracking method
                if ( _gtag !== false ) {
                    if ( tracking_id !== false ) {
                        _gtag('config', tracking_id, {'page_path': url + ASL.analytics.string.replace("{asl_term}", term)});
                    }
                } else if ( _ga !== false ) {
                    if ( tracking_id !== false ) {
                        _ga('create', tracking_id, 'auto');
                    }
                    _ga('send', 'pageview', {
                        'page': url + ASL.analytics.string.replace("{asl_term}", term),
                        'title': 'Ajax Search'
                    });
                }
            }
        },

        gaEvent: function(which, data) {
            var $this = this;
            var tracking_id = $this.gaGetTrackingID();

            if ( typeof ASL.analytics == 'undefined' || ASL.analytics.method != 'event' )
                return false;

            // Get the scope
            var _gtag = typeof gtag == "function" ? gtag : false;
            var _ga = typeof __gaTracker == "function" ? __gaTracker : (typeof ga == "function" ? ga : false);

            if ( _gtag === false && _ga === false )
                return false;

            if (
                typeof (ASL.analytics.event[which]) != 'undefined' &&
                ASL.analytics.event[which].active == 1 &&
                typeof 'gtag' != 'undefined'
            ) {
                var def_data = {
                    "search_id": $this.o.id,
                    "search_name": $this.o.name,
                    "phrase": $this.n.text.val(),
                    "option_name": '',
                    "option_value": '',
                    "result_title": '',
                    "result_url": '',
                    "results_count": ''
                };
                var event = {
                    'event_category': ASL.analytics.event[which].category,
                    'event_label': ASL.analytics.event[which].label,
                    'value': ASL.analytics.event[which].value
                };
                data = $.extend(def_data, data);
                $.each(data, function (k, v) {
                    v = String(v).replace(/[\s\n\r]+/g, " ").trim();
                    $.each(event, function (kk, vv) {
                        var regex = new RegExp('\{' + k + '\}', 'gmi');
                        event[kk] = vv.replace(regex, v);
                    });
                });
                if ( _gtag === false ) {
                    if ( tracking_id !== false ) {
                        _ga('create', tracking_id, 'auto');
                    }
                    _ga('send', 'event',
                        event.event_category,
                        ASL.analytics.event[which].action,
                        event.event_label,
                        event.value
                    );
                } else {
                    if ( tracking_id !== false ) {
                        event.send_to = tracking_id;
                    }
                    _gtag('event', ASL.analytics.event[which].action, event);
                }
            }
        },

        gaGetTrackingID: function() {
            var $this = this;
            var ret = false;

            if ( typeof ASL.analytics == 'undefined' )
                return ret;

            if ( typeof ASL.analytics.tracking_id != 'undefined' && ASL.analytics.tracking_id != '' ) {
                return ASL.analytics.tracking_id;
            } else {
                // GTAG bypass pageview tracking method
                var _gtag = typeof gtag == "function" ? gtag : false;
                if ( _gtag !== false && typeof ga != 'undefined' && typeof ga.getAll != 'undefined' ) {
                    var id = false;
                    ga.getAll().forEach( function(tracker) {
                        id = tracker.get('trackingId');
                    });
                    return id;
                }
            }

            return ret;
        },

        createVerticalScroll: function () {
            var $this = this;
            if ( $this.is_scroll && typeof $this.scroll.recalculate === 'undefined' ) {
                $this.scroll = new asp_SimpleBar($this.n.results.get(0), {
                    direction: $('body').hasClass('rtl') ? 'rtl' : 'ltr',
                    autoHide: true
                });
            }
        },

        initEvents: function () {
            var $this = this;

            if ( isMobile() && detectIOS() ) {
                /**
                 * Memorize the scroll top when the input is focused on IOS
                 * as fixed elements scroll freely, resulting in incorrect scroll value
                 */
                $this.n.text.on('touchstart', function () {
                    $this.savedScrollTop = $(window).scrollTop();
                    $this.savedContainerTop = $this.n.search.offset().top;
                });
            }

            // Some kind of crazy rev-slider fix
            $this.n.text.on('click', function(e){
                $(this).trigger('focus');
                $this.gaEvent('focus');
            });

            $this.n.text.on('focus input', function(e){
                if ( $this.searching ) return;
                if ( $(this).val() != '' ) {
                    $this.n.proclose.css('display', 'block');
                } else {
                    $this.n.proclose.css({
                        display: "none"
                    });
                }
            });

            $($this.n.text.parent()).on('submit', function (e) {
                e.preventDefault();
                if ( isMobile() ) {
                    if ( $this.o.redirectOnEnter ) {
                        var _e = jQuery.Event("keyup");
                        _e.keyCode = _e.which = 13;
                        $this.n.text.trigger(_e);
                    } else if ( $this.o.trigger.return == 'ajax_search' ) {
                        $this.search();
                        document.activeElement.blur();
                    }
                } else {
                    if ( $this.o.trigger.return == 'ajax_search' )
                        $this.search();
                }
            });

            $this.n.resultsDiv.css({
                opacity: 0
            });
            $(document).on("click touchend", function () {
                $this.hideSettings();
                if ($this.opened == false || $this.o.closeOnDocClick != 1) return;
                $this.hideResults();
            });
            $this.n.proclose.on("click touchend", function () {
                $this.n.text.val("");
                $this.n.textAutocomplete.val("");
                $this.hideResults();
                $this.n.text.trigger('focus');
            });
            $($this.elem).on("click touchend", function (e) {
                e.stopImmediatePropagation();
            });
            $this.n.resultsDiv.on("click touchend", function (e) {
                e.stopImmediatePropagation();
            });
            $this.n.searchsettings.on("click touchend", function (e) {
                e.stopImmediatePropagation();
            });

            $this.n.prosettings.on("click", function () {
                if ($this.n.prosettings.data('opened') == 0) {
                    $this.showSettings();
                } else {
                    $this.hideSettings();
                }
            });

            var fixedp = $this.n.search.parents().filter(
                function() {
                    return $(this).css('position') == 'fixed';
                }
            );
            if ( fixedp.length > 0 || $this.n.search.css('position') == 'fixed' ) {
                if ( $this.n.resultsDiv.css('position') == 'absolute' )
                    $this.n.resultsDiv.css('position', 'fixed');
                $this.n.resultsDiv.css('z-index',99999999999);
                if ( !$this.o.blocking )
                    $this.n.searchsettings.css('position', 'fixed');
            }

            if ( isMobile() ) {
                $(window).on("orientationchange", function () {
                    $this.orientationChange();
                    // Fire once more a bit delayed, some mobile browsers need to re-zoom etc..
                    setTimeout(function(){
                        $this.orientationChange();
                    }, 800);
                });
            } else {
                var resizeTimer;
                $(window).on("resize", function () {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(function () {
                        $this.resize();
                    }, 100);
                });
            }

            var scrollTimer;
            $(window).on("scroll", function () {
                clearTimeout(scrollTimer);
                scrollTimer = setTimeout(function() {
                    $this.scrolling(false);
                }, 400);
            });

            // Prevent zoom on IOS
            if ( detectIOS() && isMobile() ) {
                if ( parseInt($this.n.text.css('font-size')) < 16 ) {
                    $this.n.text.data('fontSize', $this.n.text.css('font-size')).css('font-size', '16px');
                    $this.n.textAutocomplete.css('font-size', '16px');
                    $('<style>#ajaxsearchlite'+$this.o.rid+' input.orig::-webkit-input-placeholder{font-size: 16px !important;}</style>').appendTo('head');
                }
            }

            $this.initNavigationEvent();

            $this.initMagnifierEvent();
            $this.initAutocompleteEvent();
            $this.initFacetEvents();
        },

        initAutop: function () {
            var $this = this;

            // Trigger the prevState here, as it is kind of auto-populate
            if ( prevState != null && $this.triggerPrevState ) {
                $this.search();
                prevState = null;
                return false; // Terminate at this point, to prevent auto-populate
            }
        },

        initEtc: function() {
            var $this = this;
            var t = null;

            // Emulate click on checkbox on the whole option
            $('div.asl_option', $this.n.searchsettings).on('mouseup touchend', function(e){
                e.preventDefault(); // Stop firing twice on mouseup and touchend on mobile devices
                e.stopImmediatePropagation();
                if ( $this.dragging ) {
                    return false;
                }
                $('input[type="checkbox"]', this).prop("checked", !$('input[type="checkbox"]', this).prop("checked"));
                // Trigger a custom change event, for max compatibility
                // .. the original change is buggy for some installations.
                clearTimeout(t);
                var _this = this;
                t = setTimeout(function() {
                    $('input[type="checkbox"]', _this).trigger('asl_chbx_change');
                }, 50);
            });
            $('div.asl_option label', $this.n.searchsettings).on('click', function(e){
                e.preventDefault(); // Let the previous handler handle the events, disable this
            });

            // GTAG on results click
            $this.n.resultsDiv.on('click', '.results .item', function() {
                $this.gaEvent('result_click', {
                   'result_title': $(this).find('a.asl_res_url').text(),
                   'result_url': $(this).find('a.asl_res_url').attr('href')
                });

                // Results highlight on results page
                if ( $this.o.singleHighlight == 1 ) {
                    localStorage.removeItem('asl_phrase_highlight');
                    if ( asl_unquote_phrase( $this.n.text.val() ) != '' )
                        localStorage.setItem('asl_phrase_highlight', JSON.stringify({
                            'phrase': asl_unquote_phrase( $this.n.text.val() ),
                            'id': $this.o.id
                        }));
                }
            });

            // Mobile navigation focus
            if ( isMobile() && $this.o.mobile.menu_selector != '' ) {
                $($this.o.mobile.menu_selector).on('touchend', function(){
                    var _this = this;
                    setTimeout(function () {
                        var $input = $(_this).find('input.orig');
                        $input = $input.length == 0 ? $(_this).next().find('input.orig') : $input;
                        $input = $input.length == 0 ? $(_this).parent().find('input.orig') : $input;
                        $input = $input.length == 0 ? $this.n.text : $input;
                        if ( $this.n.search.is(':visible') ) {
                            $input.get(0).focus();
                        }
                    }, 300);
                });
            }
        },

        initNavigationEvent: function () {
            var $this = this;

            $($this.n.resultsDiv).on('mouseenter', '.item',
                function () {
                    $('.item', $this.n.resultsDiv).removeClass('hovered');
                    $(this).addClass('hovered');
                }
            );
            $($this.n.resultsDiv).on('mouseleave', '.item',
                function () {
                    $('.item', $this.n.resultsDiv).removeClass('hovered');
                }
            );

            $(document).on('keydown', function (e) {

                if (window.event) {
                    var keycode = window.event.keyCode;
                    var ktype = window.event.type;
                } else if (e) {
                    var keycode = e.which;
                    var ktype = e.type;
                }

                if ($('.item', $this.n.resultsDiv).length > 0 && $this.n.resultsDiv.css('display') != 'none') {
                    if ( keycode == 40 || keycode == 38 ) {
                        if (keycode == 40) {
                            $this.n.text.blur();
                            if ($('.item.hovered', $this.n.resultsDiv).length == 0) {
                                $('.item', $this.n.resultsDiv).first().addClass('hovered');
                            } else {
                                $('.item.hovered', $this.n.resultsDiv).removeClass('hovered').next('.item').addClass('hovered');
                            }
                        }
                        if (keycode == 38) {
                            $this.n.text.blur();
                            if ($('.item.hovered', $this.n.resultsDiv).length == 0) {
                                $('.item', $this.n.resultsDiv).last().addClass('hovered');
                            } else {
                                $('.item.hovered', $this.n.resultsDiv).removeClass('hovered').prev('.item').addClass('hovered');
                            }
                        }
                        e.stopPropagation();
                        e.preventDefault();
                        var $container = $this.is_scroll ? $( $this.scroll.getScrollElement() ) : $this.n.results;
                        var $scrollTo = $this.n.resultsDiv.find('.resdrg .item.hovered');
                        if ( $scrollTo.length == 0 ) {
                            $scrollTo = $this.n.resultsDiv.children().first();
                        }
                        $container.animate({
                            "scrollTop": $scrollTo.offset().top - $container.offset().top + $container.scrollTop()
                        }, {
                            "duration": 120
                        });
                    }

                    // Trigger click on return key
                    if ( keycode == 13 && $('.item.hovered', $this.n.resultsDiv).length > 0 ) {
                        e.stopPropagation();
                        e.preventDefault();
                        $('.item.hovered a.asl_res_url', $this.n.resultsDiv).get(0).click();
                    }
                }
            });
        },

        initMagnifierEvent: function () {
           var $this = this;

            var t;
            var rt, enterRecentlyPressed = false;

            // The return event has to be dealt with on a keyup event, as it does not trigger the input event
            $this.n.text.on('keyup', function(e) {
                if (window.event) {
                    $this.keycode = window.event.keyCode;
                    $this.ktype = window.event.type;
                } else if (e) {
                    $this.keycode = e.which;
                    $this.ktype = e.type;
                }

                // Prevent rapid enter key pressing
                if ( $this.keycode == 13 ) {
                    clearTimeout(rt);
                    rt = setTimeout(function(){
                        enterRecentlyPressed = false;
                    }, 300);
                    if ( enterRecentlyPressed ) {
                        return false;
                    } else {
                        enterRecentlyPressed = true;
                    }
                }

                var isInput = $(this).hasClass("orig");
                if ( $this.n.text.val().length >= $this.o.charcount && isInput && $this.ktype == 'keyup' && $this.keycode == 13 ) {
                    $this.gaEvent('return');
                    if ( $this.o.redirectOnEnter == 1 ) {
                        if ($this.o.trigger.return != 'first_result') {
                            $this.doRedirectToResults($this.ktype);
                        } else {
                            $this.search();
                        }
                    } else {
                        if ( $this.o.trigger.return == 'nothing' )
                            return false;
                        if (
                            ($('form', $this.n.searchsettings).serialize() + $this.n.text.val().trim()) != $this.lastSuccesfulSearch ||
                            !$this.resultsOpened
                        ) {
                            $this.search();
                        }
                    }
                    clearTimeout(t);
                }
            });

            $this.n.promagnifier.add($this.n.text).on('click input', function (e) {
                if (window.event) {
                    $this.keycode = window.event.keyCode;
                    $this.ktype = window.event.type;
                } else if (e) {
                    $this.keycode = e.which;
                    $this.ktype = e.type;
                }

                var isInput = $(this).hasClass("orig");

                if ($this.n.text.val().length < $this.o.charcount) {
                    $this.n.proloading.css('display', 'none');
                    $this.hideResults();
                    if ($this.post != null) $this.post.abort();
                    clearTimeout(t);
                    return;
                }

                // If redirection is set to the results page, or custom URL
                if (
                    $this.n.text.val().length >= $this.o.charcount &&
                    (!isInput && $this.o.redirectOnClick == 1 && $this.ktype == 'click' && $this.o.trigger.click != 'first_result' )
                ) {
                    $this.doRedirectToResults($this.ktype);
                    clearTimeout(t);
                    return;
                }
                // Ignore arrows, F1-F12
                if (
                    ($this.keycode >= 37 && $this.keycode <= 40) ||
                    ($this.keycode >= 112 && $this.keycode <= 123)
                ) return;
                if ((isInput && $this.ktype == 'click') || $this.keycode == 32) {
                    if (
                        ($('form', $this.n.searchsettings).serialize() + $this.n.text.val().trim()) == $this.lastSuccesfulSearch
                    ) {
                        $this.n.proclose.css('display', 'block');
                        if ( !$this.resultsOpened )
                            $this.showResults();
                    }
                    return;
                }
                if ($(this).hasClass('orig') && $this.ktype == 'click') return;

                if ( !isInput && $this.ktype == 'click' ) {
                    $this.gaEvent('magnifier');
                }

                if (
                    $this.ktype == 'click' &&
                    !( $this.o.trigger.click == 'ajax_search' || $this.o.trigger.click == 'first_result' )
                ) {
                    return false;
                }

                if ($this.o.trigger.type == 0 && $this.ktype == 'input') return;

                // Is the nothing is choosen
                if (
                    (isInput && $this.ktype == 'input' && $this.o.trigger.return == 'nothing') ||
                    (!isInput && $this.ktype == 'click' && $this.o.trigger.click == 'nothing')
                )
                    return;
                
                if ($this.post != null) $this.post.abort();
                clearTimeout(t);
                $this.hideLoader();
                t = setTimeout(function () {
                    // If the user types and deletes, while the last results are open
                    if ( ($('form', $this.n.searchsettings).serialize() + $this.n.text.val().trim()) != $this.lastSuccesfulSearch ) {
                        $this.search();
                    } else {
                        $this.n.proclose.css('display', 'block');
                        if ( $this.isRedirectToFirstResult() ) {
                            $this.doRedirectToFirstResult();
                            return false;
                        } else {
                            if ( !$this.resultsOpened )
                                $this.showResults();
                        }
                    }
                }, 250);
            });
        },

        initFacetEvents: function() {
            var $this = this;
            var t = null;

            if ($this.o.trigger.facet == 1) {
                $('input[type!=checkbox], select', $this.n.searchsettings).on('change slidechange', function(){
                    if ($this.n.text.val().length < $this.o.charcount) return;
                    if ($this.post != null) $this.post.abort();
                    clearTimeout(t);
                    t = setTimeout(function() {
                        $this.search();
                    }, 50);
                });
                $('input[type=checkbox]', $this.n.searchsettings).on('asl_chbx_change', function(){
                    if ($this.n.text.val().length < $this.o.charcount) return;
                    if ($this.post != null) $this.post.abort();
                    $this.gaEvent('facet_change', {
                        'option_label': $(this).closest('fieldset').find('legend').text(),
                        'option_value': $(this).closest('.asl_option').find('.asl_option_label').text() + ($(this).prop('checked') ? '(checked)' : '(unchecked)')
                    });
                    clearTimeout(t);
                    t = setTimeout(function() {
                        $this.search();
                    }, 50);
                });
            }
        },

        isRedirectToFirstResult: function() {
            var $this = this;
            if (
                $('.asl_res_url', $this.n.resultsDiv).length > 0 &&
                (
                    ($this.o.redirectOnClick == 1 && $this.ktype == 'click' && $this.o.trigger.click == 'first_result' ) ||
                    ($this.o.redirectOnEnter == 1 && ($this.ktype == 'input' || $this.ktype == 'keyup') && $this.keycode == 13 && $this.o.trigger.return == 'first_result' )
                )
            ) {
                return true;
            }
            return false;
        },

        doRedirectToFirstResult: function() {
            var $this = this;
            var _loc;

            if ( $this.ktype == 'click' ) {
                _loc = $this.o.trigger.click_location;
            } else {
                _loc = $this.o.trigger.return_location;
            }

            if ( _loc == 'same' )
                location.href = $( $('.asl_res_url', $this.n.resultsDiv).get(0)).attr('href');
            else
                open_in_new_tab( $( $('.asl_res_url', $this.n.resultsDiv).get(0)).attr('href') );

            $this.hideLoader();
            $this.hideResults();
            return false;
        },

        doRedirectToResults: function( ktype ) {
            var $this = this;

            var source = $this.ktype == 'click' ? $this.o.trigger.click : $this.o.trigger.return;
            var _loc = ktype == 'click' ? $this.o.trigger.click_location : $this.o.trigger.return_location;

            if ( source == 'results_page' ) {
                var url = '?s=' + asl_nice_phrase($this.n.text.val());
            } else if ( source == 'woo_results_page' ) {
                var url = '?post_type=product&s=' + asl_nice_phrase($this.n.text.val());
            } else {
                var url = $this.o.trigger.redirect_url.replace('{phrase}', asl_nice_phrase($this.n.text.val()));
            }

            // Is this an URL like xy.com/?x=y
            if ( $this.o.homeurl.indexOf('?') > 1 && url.indexOf('?') === 0 ) {
                url = url.replace('?', '&');
            }

            if ( $this.o.overridewpdefault ) {
                if ( $this.o.override_method == "post") {
                    asl_submit_to_url($this.o.homeurl + url, 'post', {
                        asl_active: 1,
                        p_asl_data: $('form', $this.n.searchsettings).serialize()
                    }, _loc);
                } else {
                    var _url = $this.o.homeurl + url + "&asl_active=1&p_asid=" + $this.o.id + "&p_asl_data=1&" + $('form', $this.n.searchsettings).serialize();
                    if ( _loc == 'same' )
                        location.href = _url;
                    else
                        open_in_new_tab(_url)
                }
            } else {
                asl_submit_to_url($this.o.homeurl + url, 'post', {
                    np_asl_data: $('form', $this.n.searchsettings).serialize()
                }, _loc);
            }

            $this.n.proloading.css('display', 'none');
            $this.hideLoader();
            $this.hideResults();
            if ($this.post != null) $this.post.abort();
        },

        destroy: function () {
            /*return this.each(function () {
                var $this = $.extend({}, this, methods);
                $(window).unbind($this);
            });*/
            var $this = this;
            $this.n.searchsettings.remove();
            $this.n.resultsDiv.remove();
            $this.n.search.remove();
            $this.n.container.remove();
        },
        searchfor: function (phrase) {
            $(".proinput input", this).val(phrase).trigger("keyup");
        },

        initAutocompleteEvent: function () {
            var $this = this;
            var tt;

            if ( $this.o.autocomplete.enabled == 1 && !isMobile() ) {
                $this.n.text.on('keyup', function (e) {
                    if (window.event) {
                        $this.keycode = window.event.keyCode;
                        $this.ktype = window.event.type;
                    } else if (e) {
                        $this.keycode = e.which;
                        $this.ktype = e.type;
                    }

                    var thekey = 39;
                    // Lets change the keykode if the direction is rtl
                    if ($('body').hasClass('rtl'))
                        thekey = 37;
                    if ($this.keycode == thekey && $this.n.textAutocomplete.val() != "") {
                        e.preventDefault();
                        $this.n.text.val($this.n.textAutocomplete.val());
                        if ($this.post != null) $this.post.abort();
                        $this.search();
                    } else {
                        if ($this.postAuto != null) $this.postAuto.abort();
                        $this.autocompleteGoogleOnly();
                    }
                });
            }
        },

        // If only google source is used, this is much faster..
        autocompleteGoogleOnly: function () {
            var $this = this;

            var val = $this.n.text.val();
            if ($this.n.text.val() == '') {
                $this.n.textAutocomplete.val('');
                return;
            }
            var autocompleteVal = $this.n.textAutocomplete.val();
            if (autocompleteVal != '' && autocompleteVal.indexOf(val) == 0) {
                return;
            } else {
                $this.n.textAutocomplete.val('');
            }

            $.ajax({
                url: 'https://clients1.google.com/complete/search',
                dataType: 'jsonp',
                data: {
                    q: val,
                    hl: $this.o.autocomplete.lang,
                    nolabels: 't',
                    client: 'hp',
                    ds: ''
                },
                success: function(data) {
                    if (data[1].length > 0) {
                        response = data[1][0][0].replace(/(<([^>]+)>)/ig,"");
                        response = $('<textarea />').html(response).text();
                        response = response.substr(val.length);
                        $this.n.textAutocomplete.val(val + response);
                    }
                }
            });
        },

        search: function () {
            var $this = this;

            if ($this.searching && 0) return;
            if ($this.n.text.val().length < $this.o.charcount) return;

            $this.searching = true;
            $this.n.proloading.css({
                display: "block"
            });
            $this.n.proclose.css({
                display: "none"
            });

            var data = {
                action: 'ajaxsearchlite_search',
                aslp: $this.n.text.val(),
                asid: $this.o.id,
                options: $('form', $this.n.searchsettings).serialize()
            };

            data = apply_filters('asl_search_data', data);

            if ( JSON.stringify(data) === JSON.stringify($this.lastSearchData) ) {
                if ( !$this.resultsOpened )
                    $this.showResults();
                $this.hideLoader();
                if ( $this.isRedirectToFirstResult() ) {
                    $this.doRedirectToFirstResult();
                    return false;
                }
                return false;
            }

            $this.gaEvent('search_start');

            // New method without JSON
            $this.post = $.post(ASL.ajaxurl, data, function (response) {
                response = response.replace(/^\s*[\r\n]/gm, "");
                response = response.match(/___ASLSTART___(.*[\s\S]*)___ASLEND___/)[1];

                response = apply_filters('asl_search_html', response);

                $this.n.resdrg.html("");
                $this.n.resdrg.html(response);

                $(".asl_keyword", $this.n.resdrg).on('click', function () {
                    $this.n.text.val($(this).html());
                    $('input.orig', $this.n.search).val($(this).html()).trigger('keydown');
                    $('form', $this.n.search).trigger('submit', 'ajax');
                    $this.search();
                });

                $this.n.items = $('.item', $this.n.resultsDiv);

                $this.gaEvent('search_end', {'results_count':$this.n.items.length});

                $this.gaPageview($this.n.text.val());

                if ( $this.isRedirectToFirstResult() ) {
                    $this.doRedirectToFirstResult();
                    return false;
                }

                $this.hideLoader();
                $this.showResults();
                $this.scrollToResults();
                $this.lastSuccesfulSearch = $('form', $this.n.searchsettings).serialize() + $this.n.text.val().trim();
                $this.lastSearchData = data;

                if ($this.n.items.length == 0) {
                    if ($this.n.showmore != null) {
                        $this.n.showmore.css('display', 'none');
                    }
                } else {
                    if ($this.n.showmore != null) {
                        $this.n.showmore.css('display', 'block');

                        $('a', $this.n.showmore).off();
                        $('a', $this.n.showmore).on('click', function(e){
                            var source = $this.o.trigger.click;
                            var url = '?s=' + asl_nice_phrase($this.n.text.val());

                            if ( source == 'results_page' ) {
                                url = '?s=' + asl_nice_phrase($this.n.text.val());
                            } else if ( source == 'woo_results_page' ) {
                                url = '?post_type=product&s=' + asl_nice_phrase($this.n.text.val());
                            } else {
                                url = $this.o.trigger.redirect_url.replace('{phrase}', asl_nice_phrase($this.n.text.val()));
                            }

                            if ( $this.o.overridewpdefault ) {
                                if ( $this.o.override_method == "post") {
                                    asl_submit_to_url($this.o.homeurl + url, 'post', {
                                        asl_active: 1,
                                        p_asl_data: $('form', $this.n.searchsettings).serialize()
                                    });
                                } else {
                                    location.href = $this.o.homeurl + url + "&asl_active=1&p_asid=" + $this.o.id + "&p_asl_data=1&" + $('form', $this.n.searchsettings).serialize()
                                }
                            } else {
                                asl_submit_to_url($this.o.homeurl + url, 'post', {
                                    np_asl_data: $('form', $this.n.searchsettings).serialize()
                                });
                            }
                        });
                    }
                }

            }, "text").fail(function(jqXHR, textStatus, errorThrown){
                if ( jqXHR.aborted || textStatus == 'abort' )
                    return;
                $this.n.resdrg.html("");
                $this.n.resdrg.html('<div class="asl_nores">The request failed. Please check your connection! Status: ' + jqXHR.status + '</div>');
                $this.n.items = $('.item', $this.n.resultsDiv);
                $this.hideLoader();
                $this.showResults();
                $this.scrollToResults();
            });
        },

        showLoader: function( ) {
            var $this = this;
            $this.n.proloading.css({
                display: "block"
            });
        },

        hideLoader: function( ) {
            var $this = this;

            $this.n.proloading.css({
                display: "none"
            });
            $this.n.results.css("display", "");
        },

        showResultsBox: function() {
            var $this = this;

            $this.n.resultsDiv.css({
                display: 'block',
                height: 'auto'
            });
            $this.n.items.addClass($this.animationOpacity);

            $this.fixResultsPosition(true);

            $this.n.resultsDiv.css($this.resAnim.showCSS);
            $this.n.resultsDiv.removeClass($this.resAnim.hideClass).addClass($this.resAnim.showClass);
        },

        showResults: function( ) {
            var $this = this;

            // Create the scrollbars if needed
            $this.createVerticalScroll();
            $this.showVerticalResults();

            $this.hideLoader();

            $this.n.proclose.css({
                display: "block"
            });

            if ($this.n.showmore != null) {
                if ($this.n.items.length > 0) {
                    $this.n.showmore.css({
                        'display': 'block'
                    });
                } else {
                    $this.n.showmore.css({
                        'display': 'none'
                    });
                }
            }

            if ( $this.is_scroll && typeof $this.scroll.recalculate !== 'undefined' ) {
                setTimeout(function(){
                    $this.scroll.recalculate();
                }, 500);
            }

            $this.resultsOpened = true;
        },

        hideResults: function( ) {
            var $this = this;

            if ( !$this.resultsOpened ) return false;

            $this.n.resultsDiv.removeClass($this.resAnim.showClass).addClass($this.resAnim.hideClass);
            setTimeout(function(){
                $this.n.resultsDiv.css($this.resAnim.hideCSS);
            }, $this.resAnim.duration);

            $this.n.proclose.css({
                display: "none"
            });
            if ($this.n.showmore != null) {
                $this.n.showmore.css({
                    'display': 'none'
                });
            }

            if (isMobile())
                document.activeElement.blur();

            $this.resultsOpened = false;
        },

        scrollToResults: function( ) {
            $this = this;
            if ( this.o.scrollToResults.enabled ) {
                if (this.$elem.parent().hasClass("asl_preview_data")) return;
                if ($this.o.resultsposition == "hover")
                    var stop = $this.n.probox.offset().top - 20;
                else
                    var stop = $this.n.resultsDiv.offset().top - 20;
                if ($("#wpadminbar").length > 0)
                    stop -= $("#wpadminbar").height();
                stop = stop < 0 ? 0 : stop;
                $('body, html').animate({
                    "scrollTop": stop
                }, {
                    duration: 500
                });
            }
        },

        createGroup: function (r) {
            return "<div class='group'>" + r + "</div>";
        },

        showVerticalResults: function () {
            var $this = this;

            $this.showResultsBox();

            if ($this.n.items.length > 0) {
                var count = (($this.n.items.length < $this.o.itemscount) ? $this.n.items.length : $this.o.itemscount);
                var groups = $('.group', $this.n.resultsDiv);

                if ($this.n.items.length <= $this.o.itemscount) {
                    $this.n.results.css({
                        height: 'auto'
                    });
                } else {

                    // Set the height to a fictive value to refresh the scrollbar
                    // .. otherwise the height is not calculated correctly, because of the scrollbar width.
                    $this.n.results.css({
                        height: 30
                    });
                    $this.resize();

                    // Here now we have the correct item height values with the scrollbar enabled
                    var i = 0;
                    var h = 0;
                    var final_h = 0;
                    var highest = 0;

                    $this.n.items.each(function () {
                        h += $(this).outerHeight(true);
                        if ($(this).outerHeight(true) > highest)
                            highest = $(this).outerHeight(true);
                        i++;
                    });

                    // Get an initial height based on the highest item x viewport
                    final_h = highest * count;
                    // Reduce the final height to the overall height if exceeds it
                    if (final_h > h)
                        final_h = h;

                    // Count the average height * viewport size
                    i = i < 1 ? 1 : i;
                    h = h / i * count;

                    $this.n.results.css({
                        height: final_h
                    });
                }

                // ..then all the other math stuff from the resize event
                $this.resize();

                // Mark the last item
                $this.n.items.last().addClass('asl_last_item');

                if ($this.o.highlight == 1) {
                    var wholew = (($this.o.highlightwholewords == 1) ? true : false);
                    $("div.item", $this.n.resultsDiv).highlight($this.n.text.val().split(" "), { element: 'span', className: 'highlighted', wordsOnly: wholew });
                }

            }
            $this.resize();
            if ($this.n.items.length == 0) {
                $this.n.results.css({
                    height: 'auto'
                });
            }

            $this.n.results.css({
                'overflowY': 'auto'
            });

            // Scroll to top
            var $container = $this.is_scroll ? $($this.scroll.getScrollElement()) : $this.n.results;
            $container.scrollTop(0);

            $this.fixResultsPosition(true);
            $this.searching = false;
        },

        initSettingsAnimations: function() {
            var $this = this;
            var animDur = 300;

            $this.settAnim = {
                "showClass": "asl_an_fadeInDrop",
                "showCSS": {
                    "visibility": "visible",
                    "display": "block",
                    "opacity": 1,
                    "animation-duration": animDur + 'ms'
                },
                "hideClass": "asl_an_fadeOutDrop",
                "hideCSS": {
                    "visibility": "hidden",
                    "opacity": 0,
                    "display": "none"
                },
                "duration": animDur
            };

            $this.n.searchsettings.css({
                "-webkit-animation-duration": $this.settAnim.duration + "ms",
                "animation-duration": $this.settAnim.duration + "ms"
            });
        },

        initResultsAnimations: function() {
            var $this = this;
            var animDur = 300;

            $this.resAnim = {
                "showClass": "asl_an_fadeInDrop",
                "showCSS": {
                    "visibility": "visible",
                    "display": "block",
                    "opacity": 1,
                    "animation-duration": animDur  + 'ms'
                },
                "hideClass": "asl_an_fadeOutDrop",
                "hideCSS": {
                    "visibility": "hidden",
                    "opacity": 0,
                    "display": "none"
                },
                "duration": animDur
            };

            $this.n.resultsDiv.css({
                "-webkit-animation-duration": animDur + "ms",
                "animation-duration": animDur + "ms"
            });
        },

        showSettings: function () {
            var $this = this;

            $this.n.searchsettings.css($this.settAnim.showCSS);
            $this.n.searchsettings.removeClass($this.settAnim.hideClass).addClass($this.settAnim.showClass);

            if ($this.settScroll == null && ($this.is_scroll) ) {
                $this.settScroll = [];
                $('.asl_sett_scroll', $this.n.searchsettings).each(function(i, o){
                    var _this = this;
                    // Small delay to fix a rendering issue
                    setTimeout(function(){
                        $this.settScroll[i] = new asp_SimpleBar($(_this).get(0), {
                            direction: $('body').hasClass('rtl') ? 'rtl' : 'ltr',
                            autoHide: true
                        });
                    }, 20);
                });
            }
            $this.n.prosettings.data('opened', 1);
            $this.fixSettingsPosition(true);
        },

        hideSettings: function () {
            var $this = this;

            $this.n.searchsettings.removeClass($this.settAnim.showClass).addClass($this.settAnim.hideClass);
            setTimeout(function(){
                $this.n.searchsettings.css($this.settAnim.hideCSS);
            }, $this.settAnim.duration);

            $this.n.prosettings.data('opened', 0);
        },

        fixClonedSelf: function() {
            let $this = this,
                oldInstanceId = $this.o.iid,
                oldRID = $this.o.rid;
            while ( !ASL.instances.set($this) ) {
                ++$this.o.iid;
                if ($this.o.iid > 50) {
                    break;
                }
            }
            // oof, this was cloned
            if ( oldInstanceId != $this.o.iid ) {
                $this.o.rid = $this.o.id + '_' + $this.o.iid;
                $this.n.search.get(0).id = "ajaxsearchlite" + $this.o.rid;
                $this.n.search.removeClass('asl_m_' + oldRID).addClass('asl_m_' + $this.o.rid);
                $this.n.searchsettings.get(0).id = $this.n.searchsettings.get(0).id.replace('settings'+ oldRID, 'settings' + $this.o.rid);
                if ( $this.n.searchsettings.hasClass('asl_s_' + oldRID) ) {
                    $this.n.searchsettings.removeClass('asl_s_' + oldRID)
                        .addClass('asl_s_' + $this.o.rid).data('instance', $this.o.iid);
                } else {
                    $this.n.searchsettings.removeClass('asl_sb_' + oldRID)
                        .addClass('asl_sb_' + $this.o.rid).data('instance', $this.o.iid);
                }
                $this.n.resultsDiv.get(0).id = $this.n.resultsDiv.get(0).id.replace('prores'+ oldRID, 'prores' + $this.o.rid);
                $this.n.resultsDiv.removeClass('asl_r_' + oldRID)
                    .addClass('asl_r_' + $this.o.rid).data('instance', $this.o.iid);
                $this.n.container.find('.asl_init_data').data('instance', $this.o.iid);
                $this.n.container.find('.asl_init_data').get(0).id =
                    $this.n.container.find('.asl_init_data').get(0).id.replace('asl_init_id_'+ oldRID, 'asl_init_id_' + $this.o.rid);

                $this.n.prosettings.data('opened', 0);
            }
        },

        cleanUp: function () {
            var $this = this;

            if ($('.searchsettings', $this.n.search).length > 0) {
                $('body>#ajaxsearchlitesettings' + $this.o.rid).remove();
                $('body>#ajaxsearchliteres' + $this.o.rid).remove();
            }
        },

        orientationChange: function() {
            var $this = this;
            $this.fixSettingsPosition();
            $this.fixResultsPosition();
            $this.fixTryThisPosition();
        },

        resize: function () {
            var $this = this;

            $this.fixSettingsPosition();
            $this.fixResultsPosition();
            $this.fixTryThisPosition();
        },
        scrolling: function (ignoreVisibility) {
            var $this = this;

            $this.fixSettingsPosition(ignoreVisibility);
            $this.fixResultsPosition(ignoreVisibility);
        },

        fixTryThisPosition: function() {
            // Not available in lite version
        },

        fixResultsPosition: function(ignoreVisibility) {
            ignoreVisibility = typeof ignoreVisibility == 'undefined' ? false : ignoreVisibility;
            var $this = this;

            var rpos = $this.n.resultsDiv.css('position');
            if ( rpos != 'fixed' && rpos != 'absolute' )
                return;

            var bodyTop = 0;
            if ( $("body").css("position") != "static" )
                bodyTop = $("body").offset().top;

            if (ignoreVisibility == true || $this.n.resultsDiv.css('visibility') == 'visible') {
                var _roffset_top = 0;
                var _roffset_left = 0;
                var _rposition = $this.n.search.offset();

                if ( rpos == 'fixed' ) {
                    bodyTop = 0;
                    _roffset_top = $(document).scrollTop();
                    _roffset_left = $(document).scrollLeft();
                    if ( isMobile() && detectIOS() && $this.n.text.is(':focus') ) {
                        _roffset_top = $this.savedScrollTop;
                        _rposition.top = $this.savedContainerTop;
                    }
                }

                if ( typeof _rposition != 'undefined' ) {
                    var vwidth;
                    if ( deviceType() == 'phone' ) {
                        vwidth = $this.o.results.width_phone;
                    } else if ( deviceType() == 'tablet' ) {
                        vwidth = $this.o.results.width_tablet;
                    } else {
                        vwidth = $this.o.results.width;
                    }
                    if ( vwidth == 'auto') {
                        vwidth = $this.n.search.outerWidth() < 240 ? 240 : $this.n.search.outerWidth();
                    }
                    //$this.n.resultsDiv.css('width', !isNaN(vwidth) ? vwidth + 'px' : vwidth);
                    //$this.n.resultsDiv.outerWidth(rwidth);
                    $this.n.resultsDiv.css({
                        width: (!isNaN(vwidth) ? vwidth + 'px' : vwidth),
                        top: _rposition.top + $this.n.search.outerHeight(true) - bodyTop - _roffset_top,
                        left: _rposition.left - _roffset_left
                    });
                }
            }
        },

        fixSettingsPosition: function(ignoreVisibility) {
            ignoreVisibility = typeof ignoreVisibility == 'undefined' ? false : ignoreVisibility;
            var $this = this;
            var bodyTop = 0;
            if ( $("body").css("position") != "static" )
                bodyTop = $("body").offset().top;

            if ( ( ignoreVisibility == true || $this.n.prosettings.data('opened') != 0 ) && $this.o.blocking != true ) {
                $this.fixSettingsWidth();

                if ( $this.n.prosettings.css('display') != 'none' ) {
                    var _node = $this.n.prosettings;
                } else {
                    var _node = $this.n.promagnifier;
                }
                var _sposition = _node.offset();
                var _soffset_top = 0;
                var _soffset_left = 0;
                if ( $this.n.searchsettings.css('position') == 'fixed' ) {
                    _soffset_top = $(window).scrollTop();
                    _soffset_left = $(window).scrollLeft();
                    if ( isMobile() && detectIOS() && $this.n.text.is(':focus') ) {
                        _sposition.top = $this.savedContainerTop;
                        _soffset_top = $this.savedScrollTop;
                    }
                }

                if ($this.o.settingsimagepos == 'left') {
                    $this.n.searchsettings.css({
                        display: "block",
                        top: _sposition.top + _node.height() - 2 - bodyTop - _soffset_top,
                        left: _sposition.left - _soffset_left
                    });
                } else {
                    $this.n.searchsettings.css({
                        display: "block",
                        top: _sposition.top + _node.height() - 2 - bodyTop - _soffset_top,
                        left: _sposition.left + _node.width() - $this.n.searchsettings.width() - _soffset_left
                    });
                }
            }
        },

        fixSettingsWidth: function () {
            // There is always only 1 column in lite version
        }
    };

    function apply_filters() {
        if ( typeof wp != 'undefined' && typeof wp.hooks != 'undefined' && typeof wp.hooks.applyFilters != 'undefined' ) {
            return wp.hooks.applyFilters.apply(null, arguments);
        } else {
            return typeof arguments[1] != 'undefined' ? arguments[1] : false;
        }
    }

    function deviceType() {
        var w = $(window).width();
        if ( w <= 640 ) {
            return 'phone';
        } else if ( w <= 1024 ) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    /* Mobile detection - Touch desktop device safe! */
    function isMobile() {
        try{ document.createEvent("TouchEvent"); return true; }
        catch(e){ return false; }
    }

    function formData(form, data) {
        var els = form.find(':input').get();

        if(arguments.length === 1) {
            // return all data
            data = {};

            $.each(els, function() {
                if (this.name && !this.disabled && (this.checked
                    || /select|textarea/i.test(this.nodeName)
                    || /text/i.test(this.type)) &&
                    !$(this).hasClass('asl_datepicker_field') &&
                    !$(this).hasClass('asl_datepicker')
                ) {
                    if(data[this.name] == undefined){
                        data[this.name] = [];
                    }
                    data[this.name].push($(this).val());
                }
            });
            return JSON.stringify(data);
        } else {
            if ( typeof data != "object" )
                data = JSON.parse(data);
            $.each(els, function() {
                if (this.name) {
                    if (data[this.name]) {
                        var names = data[this.name];
                        var $this = $(this);
                        if(Object.prototype.toString.call(names) !== '[object Array]'){
                            names = [names]; //backwards compat to old version of this code
                        }
                        if(this.type == 'checkbox' || this.type == 'radio') {
                            var val = $this.val();
                            var found = false;
                            for(var i = 0; i < names.length; i++){
                                if(names[i] == val){
                                    found = true;
                                    break;
                                }
                            }
                            $this.attr("checked", found);
                        } else {
                            $this.val(names[0]);
                        }
                    }
                }
            });
            return form;
        }
    }

    function asl_nice_phrase(s) {
        return encodeURIComponent(s).replace(/\%20/g, '+');
    }

    function asl_unquote_phrase(s) {
        return s.replace(/"|'/g, '');
    }

    function asl_submit_to_url(action, method, input, target) {
        'use strict';
        var form;
        form = $('<form />', {
            action: action,
            method: method,
            style: 'display: none;'
        });
        if (typeof input !== 'undefined' && input !== null) {
            $.each(input, function (name, value) {
                $('<input />', {
                    type: 'hidden',
                    name: name,
                    value: value
                }).appendTo(form);
            });
        }
        if ( typeof (target) != 'undefined' && target == 'new')
            form.attr('target', '_blank');
        form.appendTo('body').trigger('submit');
    }

    function open_in_new_tab(url) {
        $('<a href="' + url + '" target="_blank">').get(0).click();
    }

    function detectIE() {
        var ua = window.navigator.userAgent;
        var msie = ua.indexOf('MSIE ');         // <10
        var trident = ua.indexOf('Trident/');   // 11
        var edge = ua.indexOf('Edge/');         // EDGE (12)

        if (msie > 0 || trident > 0 || edge > 0)
            return true;

        // other browser
        return false;
    }

    function detectIOS() {
        if (
            typeof window.navigator != "undefined" &&
            typeof window.navigator.userAgent != "undefined"
        )
            return window.navigator.userAgent.match(/(iPod|iPhone|iPad)/) != null;
        return false;
    }

    function detectOldIE() {
        var ua = window.navigator.userAgent;

        var msie = ua.indexOf('MSIE ');
        if (msie > 0) {
            return true;
        }

        return false;
    }

    // Object.create support test, and fallback for browsers without it
    if (typeof Object.create !== 'function') {
        Object.create = function (o) {
            function F() {
            }

            F.prototype = o;
            return new F();
        };
    }


    // Create a plugin based on a defined object
    $.plugin = function (name, object) {
        $.fn[name] = function (options) {
            return this.each(function () {
                if (!$.data(this, name)) {
                    $.data(this, name, Object.create(object).init(
                        options, this));
                }
            });
        };
    };

    $.plugin('ajaxsearchlite', methods);

    /**
     *
     *  Base64 encode / decode
     *  http://www.webtoolkit.info/
     *
     **/
    var Base64 = {

// private property
        _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

// public method for encoding
        encode : function (input) {
            var output = "";
            var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
            var i = 0;

            input = Base64._utf8_encode(input);

            while (i < input.length) {

                chr1 = input.charCodeAt(i++);
                chr2 = input.charCodeAt(i++);
                chr3 = input.charCodeAt(i++);

                enc1 = chr1 >> 2;
                enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                enc4 = chr3 & 63;

                if (isNaN(chr2)) {
                    enc3 = enc4 = 64;
                } else if (isNaN(chr3)) {
                    enc4 = 64;
                }

                output = output +
                    this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) +
                    this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

            }

            return output;
        },

// public method for decoding
        decode : function (input) {
            var output = "";
            var chr1, chr2, chr3;
            var enc1, enc2, enc3, enc4;
            var i = 0;

            input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

            while (i < input.length) {

                enc1 = this._keyStr.indexOf(input.charAt(i++));
                enc2 = this._keyStr.indexOf(input.charAt(i++));
                enc3 = this._keyStr.indexOf(input.charAt(i++));
                enc4 = this._keyStr.indexOf(input.charAt(i++));

                chr1 = (enc1 << 2) | (enc2 >> 4);
                chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                chr3 = ((enc3 & 3) << 6) | enc4;

                output = output + String.fromCharCode(chr1);

                if (enc3 != 64) {
                    output = output + String.fromCharCode(chr2);
                }
                if (enc4 != 64) {
                    output = output + String.fromCharCode(chr3);
                }

            }

            output = Base64._utf8_decode(output);

            return output;

        },

// private method for UTF-8 encoding
        _utf8_encode : function (string) {
            string = string.replace(/\r\n/g,"\n");
            var utftext = "";

            for (var n = 0; n < string.length; n++) {

                var c = string.charCodeAt(n);

                if (c < 128) {
                    utftext += String.fromCharCode(c);
                }
                else if((c > 127) && (c < 2048)) {
                    utftext += String.fromCharCode((c >> 6) | 192);
                    utftext += String.fromCharCode((c & 63) | 128);
                }
                else {
                    utftext += String.fromCharCode((c >> 12) | 224);
                    utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                    utftext += String.fromCharCode((c & 63) | 128);
                }

            }

            return utftext;
        },

// private method for UTF-8 decoding
        _utf8_decode : function (utftext) {
            var string = "";
            var i = 0;
            var c = c1 = c2 = 0;

            while ( i < utftext.length ) {

                c = utftext.charCodeAt(i);

                if (c < 128) {
                    string += String.fromCharCode(c);
                    i++;
                }
                else if((c > 191) && (c < 224)) {
                    c2 = utftext.charCodeAt(i+1);
                    string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                    i += 2;
                }
                else {
                    c2 = utftext.charCodeAt(i+1);
                    c3 = utftext.charCodeAt(i+2);
                    string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                    i += 3;
                }

            }

            return string;
        }

    }
})(jQuery);}
})(asljQuery, asljQuery, window);