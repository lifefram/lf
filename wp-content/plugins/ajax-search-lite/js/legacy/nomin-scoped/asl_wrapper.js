(function(jQuery, $, window){
// INIT CODE
// Use the window to make sure it is in the main scope, I do not trust IE
window.ASL = typeof window.ASL !== 'undefined' ? window.ASL : {};

window.ASL.getScope = function() {
    /**
     * Explanation:
     * If the sript is scoped, the first argument is always passed in a localized jQuery
     * variable, while the actual parameter can be aspjQuery or jQuery (or anything) as well.
     */
    if (typeof jQuery !== "undefined") {
        // Is there more than one jQuery? Let's try to find the one where ajax search pro is added
        if ( typeof jQuery.fn.ajaxsearchlite == 'undefined' ) {
            // Let's try noconflicting through all the versions
            var temp = jQuery;
            var original = jQuery;
            for (var i = 0; i < 10; i++) {
                if (typeof temp.fn.ajaxsearchlite == 'undefined') {
                    temp = jQuery.noConflict(true);
                    console.log('ASL: executed one noconflict');
                } else {
                    // Restore the globals to the initial, original one
                    if ( temp.fn.jquery != original.fn.jquery ) {
                        window.jQuery = window.$ = original;
                    }
                    return temp;
                }
            }
        } else {
            return jQuery;
        }
    }

    // The code should never reach this point, but sometimes magic happens (unloaded or undefined jQuery??)
    // .. I am almost positive at this point this is going to fail anyways, but worth a try.
    if (typeof window[ASL.js_scope] !== "undefined")
        return window[ASL.js_scope];
    else
        return false;
};

window.ASL.instances = {
    instances: [],
    get: function(id, instance) {
        this.clean();
        if ( typeof id === 'undefined' || id == 0) {
            return this.instances;
        } else {
            if ( typeof instance === 'undefined' ) {
                let ret = [];
                for ( let i=0; i<this.instances.length; i++ ) {
                    if ( this.instances[i].o.id == id ) {
                        ret.push(this.instances[i]);
                    }
                }
                return ret.length > 0 ? ret : false;
            } else {
                for ( let i=0; i<this.instances.length; i++ ) {
                    if ( this.instances[i].o.id == id && this.instances[i].o.iid == instance ) {
                        return this.instances[i];
                    }
                }
            }
        }
        return false;
    },
    set: function(obj) {
        if ( !this.exist(obj.o.id, obj.o.iid) ) {
            this.instances.push(obj);
            return true;
        } else {
            return false;
        }
    },
    exist: function(id, instance) {
        this.clean();
        for ( let i=0; i<this.instances.length; i++ ) {
            if ( this.instances[i].o.id == id ) {
                if (typeof instance === 'undefined') {
                    return true;
                } else if (this.instances[i].o.iid == instance) {
                    return true;
                }
            }
        }
        return false;
    },
    clean: function() {
        let unset = [], _this = this;
        this.instances.forEach(function(v, k){
            if ( typeof jQuery !== 'undefined' ) {
                if (jQuery('.asl_m_' + v.o.rid).length == 0) {
                    unset.push(k);
                }
            }
        });
        unset.forEach(function(k){
            if ( typeof _this.instances[k] !== 'undefined' ) {
                _this.instances[k].destroy();
                _this.instances.splice(k, 1);
            }
        });
    },
    destroy: function(id, instance) {
        let i = this.get(id, instance);
        if ( i !== false ) {
            if ( Array.isArray(i) ) {
                i.forEach(function (s) {
                    s.destroy();
                });
                this.instances = [];
            } else {
                let u = 0;
                this.instances.forEach(function(v, k){
                    if ( v.o.id == id && v.o.iid == instance) {
                        u = k;
                    }
                });
                i.destroy();
                this.instances.splice(u, 1);
            }
        }
    }
};

window.ASL.initialized = false;

// Call this function if you need to initialize an instance that is printed after an AJAX call
// Calling without an argument initializes all instances found.
window.ASL.initialize = function(id) {
    function b64_utf8_decode(utftext) {
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

    function b64_decode(input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;
        var _keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        while (i < input.length) {

            enc1 = _keyStr.indexOf(input.charAt(i++));
            enc2 = _keyStr.indexOf(input.charAt(i++));
            enc3 = _keyStr.indexOf(input.charAt(i++));
            enc4 = _keyStr.indexOf(input.charAt(i++));

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
        output = b64_utf8_decode(output);
        return output;
    }

    // window.ASL
    var _this = this;

    // Some weird ajax loader problem prevention
    if ( typeof _this.getScope == 'undefined' || typeof _this.version == 'undefined' )
        return false;

    // Yeah I could use $ or jQuery as the scope variable, but I like to avoid magical errors..
    var scope = _this.getScope();
    var selector = ".asl_init_data";

    if ((typeof ASL_INSTANCES != "undefined") && Object.keys(ASL_INSTANCES).length > 0) {
        scope.each(ASL_INSTANCES, function(k, v){
            if ( typeof v == "undefined" ) return false;
            // Return if it is already initialized
            if ( scope("#ajaxsearchlite" + k).hasClass("hasASL") )
                return false;
            else
                scope("#ajaxsearchlite" + k).addClass("hasASL");

            return scope("#ajaxsearchlite" + k).ajaxsearchlite(v);
        });
    } else {
        if (typeof id !== 'undefined')
            selector = "div[id*=asl_init_id_" + id + "]";

        /**
         * Getting around inline script declarations with this solution.
         * So these new, invisible divs contains a JSON object with the parameters.
         * Parse all of them and do the declaration.
         */
        scope(selector).each(function (index, value) {
            var rid = scope(this).attr('id').match(/^asl_init_id_(.*)/)[1];

            var jsonData = scope(this).data("asldata");
            if (typeof jsonData === "undefined") return false;

            jsonData = b64_decode(jsonData);
            if (typeof jsonData === "undefined" || jsonData == "") return false;

            var args = JSON.parse(jsonData);
            scope("#ajaxsearchlite" + rid).addClass('hasASL');

            return scope("#ajaxsearchlite" + rid).ajaxsearchlite(args);
        });
    }

    if ( _this.highlight.enabled ) {
        var data = localStorage.getItem('asl_phrase_highlight');
        localStorage.removeItem('asl_phrase_highlight');
        if ( data != null ) {
            data = JSON.parse(data);
            scope.each(_this.highlight.data, function(i, o){
                var selector = o.selector != '' && scope(o.selector).length > 0 ? o.selector : 'article';
                selector = scope(selector).length > 0 ? selector : 'body';
                scope(selector).highlight(data.phrase, { element: 'span', className: 'asl_single_highlighted', wordsOnly: o.whole, excludeParents : '.asl_w, .asl-try' });
                if ( o.scroll && scope('.asl_single_highlighted').length > 0 ) {
                    var stop = scope('.asl_single_highlighted').offset().top - 120;
                    if (scope("#wpadminbar").length > 0)
                        stop -= scope("#wpadminbar").height();
                    stop = stop + o.scroll_offset;
                    stop = stop < 0 ? 0 : stop;
                    scope('html').animate({
                        "scrollTop": stop
                    }, {
                        duration: 500
                    });
                }
                return false;
            });
        }
    }
    _this.initialized = true;
};

window.ASL.fixClones = function() {
    var _this = this;
    _this.fix_duplicates = _this.fix_duplicates || 0;
    if ( _this.fix_duplicates == 0 )
        return false;

    if ( typeof _this.getScope == 'undefined' )
        return false;
    var scope = _this.getScope();

    var inst = {};
    var selector = ".asl_init_data";

    scope(selector).each(function(){
        var rid =  scope(this).attr('id').match(/^asl_init_id_(.*)/)[1];
        if ( typeof inst[rid] == 'undefined' ) {
            inst[rid] = {
                'rid'  : rid,
                'id'  : rid,
                'count': 1
            };
        } else {
            inst[rid].count++;
        }
    });

    scope.each(inst, function(k, v){
        // Same instance, but more copies
        if ( v.count > 1 ) {
            scope('.asl_m_' + v.rid).each(function(kk, vv){
                if ( kk == 0 ) return true;
                var parent = scope(this).parent();
                var n_rid = v.id;
                while ( scope('#ajaxsearchlite' + n_rid).length != 0 ) {
                    n_rid++;
                }
                // Main box
                scope(this).attr('id', 'ajaxsearchlite' + n_rid);
                scope(this).removeClass('asl_m_' + v.rid).addClass('asl_m_' + n_rid);
                scope(this).removeClass('hasASL');
                // Results box
                // Check if the cloning did make a copy before init, if not, make a results box
                if ( scope('.asl_r_'+v.rid, this).length == 0 ) {
                    scope('.asl_r_'+v.rid).clone().appendTo(scope(this));
                }
                scope('.asl_r_'+v.rid, this).attr('id', 'ajaxsearchliteres'+n_rid);
                scope('.asl_r_'+v.rid, this).attr('data-id', n_rid);
                scope('.asl_r_'+v.rid, this).removeClass('asl_r_'+v.rid).addClass('asl_r_'+n_rid);
                if ( typeof(ASL.resHTML) != 'undefined' ) {
                    scope('#ajaxsearchliteres'+n_rid).html(ASL.resHTML);
                }
                // Settings box
                // Check if the cloning did make a copy before init, if not, make a settings box
                if ( scope('.asl_s_'+v.rid, this).length == 0 && scope('.asl_s_'+v.rid).length != 0 ) {
                    scope('.asl_s_'+v.rid).clone().appendTo(scope(this));
                }
                if ( scope('.asl_sb_'+v.rid, this).length == 0 && scope('.asl_sb_'+v.rid).length != 0 ) {
                    scope('.asl_sb_'+v.rid).clone().appendTo(scope(this));
                }
                scope('.asl_s_'+v.rid, this).attr('id', 'ajaxsearchlitesettings'+n_rid);
                if ( typeof(ASL.setHTML) != 'undefined' ) {
                    scope('#ajaxsearchlitesettings'+n_rid).html(ASL.setHTML);
                }
                scope('.asl_sb_'+v.rid, parent).attr('id', 'ajaxsearchlitebsettings'+n_rid);
                if ( typeof(ASL.setHTML) != 'undefined' ) {
                    scope('#ajaxsearchlitebsettings'+n_rid).html(ASL.setHTML);
                }
                // Other data
                if ( scope('.asl_hidden_data', parent).length > 0 )
                    scope('.asl_hidden_data', parent).attr('id', 'asl_hidden_data_'+n_rid);
                if ( scope('.asl_init_data', parent).length > 0 )
                    scope('.asl_init_data', parent).attr('id', 'asl_init_id_'+n_rid);

                _this.initialize(n_rid);
            });
        }
    });
};

window.ASL.ready = function() {
    var _this = this;
    var scope = _this.getScope();
    var t = null;

    if ( scope === false ) {
        return false;
    }

    scope(function(){

        /**
         * If the inline var ASL = ... is moved to an external file and defered, the other object properties may not exist
         * yes, and the initialize() method will fail.
         * This method will try to get the timing right.
         */
        var interval, tries = 0;
        interval = setInterval(function(){
            ++tries;
            if ( tries > 20 || _this.initialized ) {
                clearInterval(interval);
                return false;
            }

            _this.initialize();

            clearInterval(interval);
        }, 200);

        _this.initialize();

        setTimeout(function(){
            _this.fixClones();
        }, 2500);
    });

    // DOM tree modification detection to re-initialize automatically if enabled
    if (typeof(ASL.detect_ajax) != "undefined" && ASL.detect_ajax == 1) {
        scope("body").bind("DOMSubtreeModified", function() {
            clearTimeout(t);
            t = setTimeout(function(){
                _this.initialize();
            }, 500);
        });
    }

    var tt;
    scope(window).on('resize', function(){
        clearTimeout(tt);
        tt = setTimeout(function(){
            _this.fixClones();
        }, 2000);
    });

    var ttt;
    // Known slide-out and other type of menus to initialize on click
    var triggerSelectors = '#menu-item-search, .fa-search, .fa, .fas';
    // Avada theme
    triggerSelectors = triggerSelectors + ', .fusion-flyout-menu-toggle, .fusion-main-menu-search-open';
    // Be theme
    triggerSelectors = triggerSelectors + ', #search_button';
    // The 7 theme
    triggerSelectors = triggerSelectors + ', .mini-search.popup-search';
    // Flatsome theme
    triggerSelectors = triggerSelectors + ', .icon-search';
    // Enfold theme
    triggerSelectors = triggerSelectors + ', .menu-item-search-dropdown';
    // Uncode theme
    triggerSelectors = triggerSelectors + ', .mobile-menu-button';
    // Newspaper theme
    triggerSelectors = triggerSelectors + ', .td-icon-search, .tdb-search-icon';
    // Bridge theme
    triggerSelectors = triggerSelectors + ', .side_menu_button, .search_button';
    // Jupiter theme
    triggerSelectors = triggerSelectors + ', .raven-search-form-toggle';
    // Elementor trigger lightbox & other elementor stuff
    triggerSelectors = triggerSelectors + ', [data-elementor-open-lightbox], .elementor-button-link, .elementor-button';

    // Attach this to the document ready, as it may not attach if this is loaded early
    scope(function(){
        scope('body').on('click touchend', triggerSelectors, function(){
            clearTimeout(ttt);
            ttt = setTimeout(function(){
                _this.initialize();
            }, 500);
        });
    });
};

window.ASL.loadScriptStack = function(stack) {
    if ( stack.length > 0 ) {
        var scriptTag = document.createElement('script');
        scriptTag.src = stack.splice(0, 1);
        if ( stack.length == 0 ) {
            scriptTag.onload = function () {
                if (typeof jQuery.fn.ajaxsearchlite !== 'undefined') {
                    console.log('ASL: Initializing via onload..');
                    window.ASL.ready();
                }
            }
        } else {
            scriptTag.onload = function () {
                window.ASL.loadScriptStack(stack);
            }
        }
        console.log('ASL adding:', scriptTag.src);
        document.body.appendChild(scriptTag);
    }
}

window.ASL.init = function() {
    // jQuery deferred and other loading issues protection
    if ( typeof jQuery === 'undefined' || typeof jQuery.fn.ajaxsearchlite === 'undefined' ) {
        console.log('ASL: jQuery script is probably deferred or delayed loading, trying to resolve');

        var asl_interval;
        /**
         * The script can reload itself, and get to this point of execution again. We want to keep the number of tries
         * memorized, otherwise it may result in an endless loop.
         * @type {number}
         */
        window.asl_interval_tries = typeof window.asl_interval_tries !== 'undefined' ? window.asl_interval_tries : 0;
        asl_interval = setInterval(function(){
            ++window.asl_interval_tries;

            if ( window.asl_interval_tries > 5 ) {
                console.log('ASL: faliure, init tried', asl_interval_tries, 'times');
                clearInterval(asl_interval);

                // This should execute once, even if the script is loaded again.
                if ( typeof jQuery !== 'undefined' && typeof ASL.min_script_src !== 'undefined' && ASL.min_script_src !== null ) {
                    console.log('ASL: jQuery exists, adding ASL script dynamically & trying to load.')
                    ASL.loadScriptStack(ASL.min_script_src);
                    ASL.min_script_src = null; // prevent duplicate loading
                }
                return false;
            } else {
                if ( typeof jQuery !== 'undefined' && typeof jQuery.fn.ajaxsearchlite !== 'undefined' ) {
                    window.ASL.ready();
                    console.log('ASL: success at try: ', window.asl_interval_tries);
                    clearInterval(asl_interval);
                    return false;
                }
            }
        }, 250);
    } else {
        // Call the ready method
        window.ASL.ready();
    }
};

if ( typeof window.ASL.version == 'undefined' ) {
    var asl_init_interval, asl_init_interval_tries = 0;
    console.log('ASL: global not defined, trying to wait..');
    asl_init_interval = setInterval(function(){
        ++asl_init_interval_tries;
        if ( typeof window.ASL.version != 'undefined' || asl_init_interval_tries > 10 ) {
            if ( asl_init_interval_tries > 10 ) {
                console.log('ASL: global found at try ', asl_init_interval_tries);
            } else {
                console.log('ASL: global not found, initializing anyways at try ', asl_init_interval_tries);
            }
            window.ASL.init();
            clearInterval(asl_init_interval);
            return true;
        }
    }, 100);
} else {
    window.ASL.init();
}
})(asljQuery, asljQuery, window);