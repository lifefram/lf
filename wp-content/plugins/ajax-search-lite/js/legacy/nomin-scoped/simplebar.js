(function(jQuery, $, window){
/**
 * asp_SimpleBar.js - v5.3.5
 * Scrollbars, simpler.
 * https://grsmto.github.io/asp_simplebar/
 *
 * Made by Adrien Denat from a fork by Jonathan Nicol
 * Under MIT License
 */

(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory(require('core-js/modules/es.array.for-each'), require('core-js/modules/web.dom-collections.for-each'), require('core-js/modules/es.array.filter'), require('core-js/modules/es.array.iterator'), require('core-js/modules/es.object.assign'), require('core-js/modules/es.object.to-string'), require('core-js/modules/es.parse-int'), require('core-js/modules/es.string.iterator'), require('core-js/modules/es.weak-map'), require('core-js/modules/web.dom-collections.iterator'), require('core-js/modules/es.array.reduce'), require('core-js/modules/es.function.name'), require('core-js/modules/es.regexp.exec'), require('core-js/modules/es.string.match'), require('core-js/modules/es.string.replace')) :
        typeof define === 'function' && define.amd ? define(['core-js/modules/es.array.for-each', 'core-js/modules/web.dom-collections.for-each', 'core-js/modules/es.array.filter', 'core-js/modules/es.array.iterator', 'core-js/modules/es.object.assign', 'core-js/modules/es.object.to-string', 'core-js/modules/es.parse-int', 'core-js/modules/es.string.iterator', 'core-js/modules/es.weak-map', 'core-js/modules/web.dom-collections.iterator', 'core-js/modules/es.array.reduce', 'core-js/modules/es.function.name', 'core-js/modules/es.regexp.exec', 'core-js/modules/es.string.match', 'core-js/modules/es.string.replace'], factory) :
            (global = global || self, global.asp_SimpleBar = factory());
}(this, function () { 'use strict';

    var commonjsGlobal = typeof globalThis !== 'undefined' ? globalThis : typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};

    /**
     * lodash (Custom Build) <https://lodash.com/>
     * Build: `lodash modularize exports="npm" -o ./`
     * Copyright jQuery Foundation and other contributors <https://jquery.org/>
     * Released under MIT license <https://lodash.com/license>
     * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
     * Copyright Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
     */

    /** Used as the `TypeError` message for "Functions" methods. */
    var FUNC_ERROR_TEXT = 'Expected a function';

    /** Used as references for various `Number` constants. */
    var NAN = 0 / 0;

    /** `Object#toString` result references. */
    var symbolTag = '[object Symbol]';

    /** Used to match leading and trailing whitespace. */
    var reTrim = /^\s+|\s+$/g;

    /** Used to detect bad signed hexadecimal string values. */
    var reIsBadHex = /^[-+]0x[0-9a-f]+$/i;

    /** Used to detect binary string values. */
    var reIsBinary = /^0b[01]+$/i;

    /** Used to detect octal string values. */
    var reIsOctal = /^0o[0-7]+$/i;

    /** Built-in method references without a dependency on `root`. */
    var freeParseInt = parseInt;

    /** Detect free variable `global` from Node.js. */
    var freeGlobal = typeof commonjsGlobal == 'object' && commonjsGlobal && commonjsGlobal.Object === Object && commonjsGlobal;

    /** Detect free variable `self`. */
    var freeSelf = typeof self == 'object' && self && self.Object === Object && self;

    /** Used as a reference to the global object. */
    var root = freeGlobal || freeSelf || Function('return this')();

    /** Used for built-in method references. */
    var objectProto = Object.prototype;

    /**
     * Used to resolve the
     * [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
     * of values.
     */
    var objectToString = objectProto.toString;

    /* Built-in method references for those with the same name as other `lodash` methods. */
    var nativeMax = Math.max,
        nativeMin = Math.min;

    /**
     * Gets the timestamp of the number of milliseconds that have elapsed since
     * the Unix epoch (1 January 1970 00:00:00 UTC).
     *
     * @static
     * @memberOf _
     * @since 2.4.0
     * @category Date
     * @returns {number} Returns the timestamp.
     * @example
     *
     * _.defer(function(stamp) {
     *   console.log(_.now() - stamp);
     * }, _.now());
     * // => Logs the number of milliseconds it took for the deferred invocation.
     */
    var now = function() {
        return root.Date.now();
    };

    /**
     * Creates a debounced function that delays invoking `func` until after `wait`
     * milliseconds have elapsed since the last time the debounced function was
     * invoked. The debounced function comes with a `cancel` method to cancel
     * delayed `func` invocations and a `flush` method to immediately invoke them.
     * Provide `options` to indicate whether `func` should be invoked on the
     * leading and/or trailing edge of the `wait` timeout. The `func` is invoked
     * with the last arguments provided to the debounced function. Subsequent
     * calls to the debounced function return the result of the last `func`
     * invocation.
     *
     * **Note:** If `leading` and `trailing` options are `true`, `func` is
     * invoked on the trailing edge of the timeout only if the debounced function
     * is invoked more than once during the `wait` timeout.
     *
     * If `wait` is `0` and `leading` is `false`, `func` invocation is deferred
     * until to the next tick, similar to `setTimeout` with a timeout of `0`.
     *
     * See [David Corbacho's article](https://css-tricks.com/debouncing-throttling-explained-examples/)
     * for details over the differences between `_.debounce` and `_.throttle`.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Function
     * @param {Function} func The function to debounce.
     * @param {number} [wait=0] The number of milliseconds to delay.
     * @param {Object} [options={}] The options object.
     * @param {boolean} [options.leading=false]
     *  Specify invoking on the leading edge of the timeout.
     * @param {number} [options.maxWait]
     *  The maximum time `func` is allowed to be delayed before it's invoked.
     * @param {boolean} [options.trailing=true]
     *  Specify invoking on the trailing edge of the timeout.
     * @returns {Function} Returns the new debounced function.
     * @example
     *
     * // Avoid costly calculations while the window size is in flux.
     * jQuery(window).on('resize', _.debounce(calculateLayout, 150));
     *
     * // Invoke `sendMail` when clicked, debouncing subsequent calls.
     * jQuery(element).on('click', _.debounce(sendMail, 300, {
     *   'leading': true,
     *   'trailing': false
     * }));
     *
     * // Ensure `batchLog` is invoked once after 1 second of debounced calls.
     * var debounced = _.debounce(batchLog, 250, { 'maxWait': 1000 });
     * var source = new EventSource('/stream');
     * jQuery(source).on('message', debounced);
     *
     * // Cancel the trailing debounced invocation.
     * jQuery(window).on('popstate', debounced.cancel);
     */
    function debounce(func, wait, options) {
        var lastArgs,
            lastThis,
            maxWait,
            result,
            timerId,
            lastCallTime,
            lastInvokeTime = 0,
            leading = false,
            maxing = false,
            trailing = true;

        if (typeof func != 'function') {
            throw new TypeError(FUNC_ERROR_TEXT);
        }
        wait = toNumber(wait) || 0;
        if (isObject(options)) {
            leading = !!options.leading;
            maxing = 'maxWait' in options;
            maxWait = maxing ? nativeMax(toNumber(options.maxWait) || 0, wait) : maxWait;
            trailing = 'trailing' in options ? !!options.trailing : trailing;
        }

        function invokeFunc(time) {
            var args = lastArgs,
                thisArg = lastThis;

            lastArgs = lastThis = undefined;
            lastInvokeTime = time;
            result = func.apply(thisArg, args);
            return result;
        }

        function leadingEdge(time) {
            // Reset any `maxWait` timer.
            lastInvokeTime = time;
            // Start the timer for the trailing edge.
            timerId = setTimeout(timerExpired, wait);
            // Invoke the leading edge.
            return leading ? invokeFunc(time) : result;
        }

        function remainingWait(time) {
            var timeSinceLastCall = time - lastCallTime,
                timeSinceLastInvoke = time - lastInvokeTime,
                result = wait - timeSinceLastCall;

            return maxing ? nativeMin(result, maxWait - timeSinceLastInvoke) : result;
        }

        function shouldInvoke(time) {
            var timeSinceLastCall = time - lastCallTime,
                timeSinceLastInvoke = time - lastInvokeTime;

            // Either this is the first call, activity has stopped and we're at the
            // trailing edge, the system time has gone backwards and we're treating
            // it as the trailing edge, or we've hit the `maxWait` limit.
            return (lastCallTime === undefined || (timeSinceLastCall >= wait) ||
                (timeSinceLastCall < 0) || (maxing && timeSinceLastInvoke >= maxWait));
        }

        function timerExpired() {
            var time = now();
            if (shouldInvoke(time)) {
                return trailingEdge(time);
            }
            // Restart the timer.
            timerId = setTimeout(timerExpired, remainingWait(time));
        }

        function trailingEdge(time) {
            timerId = undefined;

            // Only invoke if we have `lastArgs` which means `func` has been
            // debounced at least once.
            if (trailing && lastArgs) {
                return invokeFunc(time);
            }
            lastArgs = lastThis = undefined;
            return result;
        }

        function cancel() {
            if (timerId !== undefined) {
                clearTimeout(timerId);
            }
            lastInvokeTime = 0;
            lastArgs = lastCallTime = lastThis = timerId = undefined;
        }

        function flush() {
            return timerId === undefined ? result : trailingEdge(now());
        }

        function debounced() {
            var time = now(),
                isInvoking = shouldInvoke(time);

            lastArgs = arguments;
            lastThis = this;
            lastCallTime = time;

            if (isInvoking) {
                if (timerId === undefined) {
                    return leadingEdge(lastCallTime);
                }
                if (maxing) {
                    // Handle invocations in a tight loop.
                    timerId = setTimeout(timerExpired, wait);
                    return invokeFunc(lastCallTime);
                }
            }
            if (timerId === undefined) {
                timerId = setTimeout(timerExpired, wait);
            }
            return result;
        }
        debounced.cancel = cancel;
        debounced.flush = flush;
        return debounced;
    }

    /**
     * Creates a throttled function that only invokes `func` at most once per
     * every `wait` milliseconds. The throttled function comes with a `cancel`
     * method to cancel delayed `func` invocations and a `flush` method to
     * immediately invoke them. Provide `options` to indicate whether `func`
     * should be invoked on the leading and/or trailing edge of the `wait`
     * timeout. The `func` is invoked with the last arguments provided to the
     * throttled function. Subsequent calls to the throttled function return the
     * result of the last `func` invocation.
     *
     * **Note:** If `leading` and `trailing` options are `true`, `func` is
     * invoked on the trailing edge of the timeout only if the throttled function
     * is invoked more than once during the `wait` timeout.
     *
     * If `wait` is `0` and `leading` is `false`, `func` invocation is deferred
     * until to the next tick, similar to `setTimeout` with a timeout of `0`.
     *
     * See [David Corbacho's article](https://css-tricks.com/debouncing-throttling-explained-examples/)
     * for details over the differences between `_.throttle` and `_.debounce`.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Function
     * @param {Function} func The function to throttle.
     * @param {number} [wait=0] The number of milliseconds to throttle invocations to.
     * @param {Object} [options={}] The options object.
     * @param {boolean} [options.leading=true]
     *  Specify invoking on the leading edge of the timeout.
     * @param {boolean} [options.trailing=true]
     *  Specify invoking on the trailing edge of the timeout.
     * @returns {Function} Returns the new throttled function.
     * @example
     *
     * // Avoid excessively updating the position while scrolling.
     * jQuery(window).on('scroll', _.throttle(updatePosition, 100));
     *
     * // Invoke `renewToken` when the click event is fired, but not more than once every 5 minutes.
     * var throttled = _.throttle(renewToken, 300000, { 'trailing': false });
     * jQuery(element).on('click', throttled);
     *
     * // Cancel the trailing throttled invocation.
     * jQuery(window).on('popstate', throttled.cancel);
     */
    function throttle(func, wait, options) {
        var leading = true,
            trailing = true;

        if (typeof func != 'function') {
            throw new TypeError(FUNC_ERROR_TEXT);
        }
        if (isObject(options)) {
            leading = 'leading' in options ? !!options.leading : leading;
            trailing = 'trailing' in options ? !!options.trailing : trailing;
        }
        return debounce(func, wait, {
            'leading': leading,
            'maxWait': wait,
            'trailing': trailing
        });
    }

    /**
     * Checks if `value` is the
     * [language type](http://www.ecma-international.org/ecma-262/7.0/#sec-ecmascript-language-types)
     * of `Object`. (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is an object, else `false`.
     * @example
     *
     * _.isObject({});
     * // => true
     *
     * _.isObject([1, 2, 3]);
     * // => true
     *
     * _.isObject(_.noop);
     * // => true
     *
     * _.isObject(null);
     * // => false
     */
    function isObject(value) {
        var type = typeof value;
        return !!value && (type == 'object' || type == 'function');
    }

    /**
     * Checks if `value` is object-like. A value is object-like if it's not `null`
     * and has a `typeof` result of "object".
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
     * @example
     *
     * _.isObjectLike({});
     * // => true
     *
     * _.isObjectLike([1, 2, 3]);
     * // => true
     *
     * _.isObjectLike(_.noop);
     * // => false
     *
     * _.isObjectLike(null);
     * // => false
     */
    function isObjectLike(value) {
        return !!value && typeof value == 'object';
    }

    /**
     * Checks if `value` is classified as a `Symbol` primitive or object.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a symbol, else `false`.
     * @example
     *
     * _.isSymbol(Symbol.iterator);
     * // => true
     *
     * _.isSymbol('abc');
     * // => false
     */
    function isSymbol(value) {
        return typeof value == 'symbol' ||
            (isObjectLike(value) && objectToString.call(value) == symbolTag);
    }

    /**
     * Converts `value` to a number.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to process.
     * @returns {number} Returns the number.
     * @example
     *
     * _.toNumber(3.2);
     * // => 3.2
     *
     * _.toNumber(Number.MIN_VALUE);
     * // => 5e-324
     *
     * _.toNumber(Infinity);
     * // => Infinity
     *
     * _.toNumber('3.2');
     * // => 3.2
     */
    function toNumber(value) {
        if (typeof value == 'number') {
            return value;
        }
        if (isSymbol(value)) {
            return NAN;
        }
        if (isObject(value)) {
            var other = typeof value.valueOf == 'function' ? value.valueOf() : value;
            value = isObject(other) ? (other + '') : other;
        }
        if (typeof value != 'string') {
            return value === 0 ? value : +value;
        }
        value = value.replace(reTrim, '');
        var isBinary = reIsBinary.test(value);
        return (isBinary || reIsOctal.test(value))
            ? freeParseInt(value.slice(2), isBinary ? 2 : 8)
            : (reIsBadHex.test(value) ? NAN : +value);
    }

    var lodash_throttle = throttle;

    /**
     * lodash (Custom Build) <https://lodash.com/>
     * Build: `lodash modularize exports="npm" -o ./`
     * Copyright jQuery Foundation and other contributors <https://jquery.org/>
     * Released under MIT license <https://lodash.com/license>
     * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
     * Copyright Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
     */

    /** Used as the `TypeError` message for "Functions" methods. */
    var FUNC_ERROR_TEXT$1 = 'Expected a function';

    /** Used as references for various `Number` constants. */
    var NAN$1 = 0 / 0;

    /** `Object#toString` result references. */
    var symbolTag$1 = '[object Symbol]';

    /** Used to match leading and trailing whitespace. */
    var reTrim$1 = /^\s+|\s+$/g;

    /** Used to detect bad signed hexadecimal string values. */
    var reIsBadHex$1 = /^[-+]0x[0-9a-f]+$/i;

    /** Used to detect binary string values. */
    var reIsBinary$1 = /^0b[01]+$/i;

    /** Used to detect octal string values. */
    var reIsOctal$1 = /^0o[0-7]+$/i;

    /** Built-in method references without a dependency on `root`. */
    var freeParseInt$1 = parseInt;

    /** Detect free variable `global` from Node.js. */
    var freeGlobal$1 = typeof commonjsGlobal == 'object' && commonjsGlobal && commonjsGlobal.Object === Object && commonjsGlobal;

    /** Detect free variable `self`. */
    var freeSelf$1 = typeof self == 'object' && self && self.Object === Object && self;

    /** Used as a reference to the global object. */
    var root$1 = freeGlobal$1 || freeSelf$1 || Function('return this')();

    /** Used for built-in method references. */
    var objectProto$1 = Object.prototype;

    /**
     * Used to resolve the
     * [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
     * of values.
     */
    var objectToString$1 = objectProto$1.toString;

    /* Built-in method references for those with the same name as other `lodash` methods. */
    var nativeMax$1 = Math.max,
        nativeMin$1 = Math.min;

    /**
     * Gets the timestamp of the number of milliseconds that have elapsed since
     * the Unix epoch (1 January 1970 00:00:00 UTC).
     *
     * @static
     * @memberOf _
     * @since 2.4.0
     * @category Date
     * @returns {number} Returns the timestamp.
     * @example
     *
     * _.defer(function(stamp) {
     *   console.log(_.now() - stamp);
     * }, _.now());
     * // => Logs the number of milliseconds it took for the deferred invocation.
     */
    var now$1 = function() {
        return root$1.Date.now();
    };

    /**
     * Creates a debounced function that delays invoking `func` until after `wait`
     * milliseconds have elapsed since the last time the debounced function was
     * invoked. The debounced function comes with a `cancel` method to cancel
     * delayed `func` invocations and a `flush` method to immediately invoke them.
     * Provide `options` to indicate whether `func` should be invoked on the
     * leading and/or trailing edge of the `wait` timeout. The `func` is invoked
     * with the last arguments provided to the debounced function. Subsequent
     * calls to the debounced function return the result of the last `func`
     * invocation.
     *
     * **Note:** If `leading` and `trailing` options are `true`, `func` is
     * invoked on the trailing edge of the timeout only if the debounced function
     * is invoked more than once during the `wait` timeout.
     *
     * If `wait` is `0` and `leading` is `false`, `func` invocation is deferred
     * until to the next tick, similar to `setTimeout` with a timeout of `0`.
     *
     * See [David Corbacho's article](https://css-tricks.com/debouncing-throttling-explained-examples/)
     * for details over the differences between `_.debounce` and `_.throttle`.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Function
     * @param {Function} func The function to debounce.
     * @param {number} [wait=0] The number of milliseconds to delay.
     * @param {Object} [options={}] The options object.
     * @param {boolean} [options.leading=false]
     *  Specify invoking on the leading edge of the timeout.
     * @param {number} [options.maxWait]
     *  The maximum time `func` is allowed to be delayed before it's invoked.
     * @param {boolean} [options.trailing=true]
     *  Specify invoking on the trailing edge of the timeout.
     * @returns {Function} Returns the new debounced function.
     * @example
     *
     * // Avoid costly calculations while the window size is in flux.
     * jQuery(window).on('resize', _.debounce(calculateLayout, 150));
     *
     * // Invoke `sendMail` when clicked, debouncing subsequent calls.
     * jQuery(element).on('click', _.debounce(sendMail, 300, {
     *   'leading': true,
     *   'trailing': false
     * }));
     *
     * // Ensure `batchLog` is invoked once after 1 second of debounced calls.
     * var debounced = _.debounce(batchLog, 250, { 'maxWait': 1000 });
     * var source = new EventSource('/stream');
     * jQuery(source).on('message', debounced);
     *
     * // Cancel the trailing debounced invocation.
     * jQuery(window).on('popstate', debounced.cancel);
     */
    function debounce$1(func, wait, options) {
        var lastArgs,
            lastThis,
            maxWait,
            result,
            timerId,
            lastCallTime,
            lastInvokeTime = 0,
            leading = false,
            maxing = false,
            trailing = true;

        if (typeof func != 'function') {
            throw new TypeError(FUNC_ERROR_TEXT$1);
        }
        wait = toNumber$1(wait) || 0;
        if (isObject$1(options)) {
            leading = !!options.leading;
            maxing = 'maxWait' in options;
            maxWait = maxing ? nativeMax$1(toNumber$1(options.maxWait) || 0, wait) : maxWait;
            trailing = 'trailing' in options ? !!options.trailing : trailing;
        }

        function invokeFunc(time) {
            var args = lastArgs,
                thisArg = lastThis;

            lastArgs = lastThis = undefined;
            lastInvokeTime = time;
            result = func.apply(thisArg, args);
            return result;
        }

        function leadingEdge(time) {
            // Reset any `maxWait` timer.
            lastInvokeTime = time;
            // Start the timer for the trailing edge.
            timerId = setTimeout(timerExpired, wait);
            // Invoke the leading edge.
            return leading ? invokeFunc(time) : result;
        }

        function remainingWait(time) {
            var timeSinceLastCall = time - lastCallTime,
                timeSinceLastInvoke = time - lastInvokeTime,
                result = wait - timeSinceLastCall;

            return maxing ? nativeMin$1(result, maxWait - timeSinceLastInvoke) : result;
        }

        function shouldInvoke(time) {
            var timeSinceLastCall = time - lastCallTime,
                timeSinceLastInvoke = time - lastInvokeTime;

            // Either this is the first call, activity has stopped and we're at the
            // trailing edge, the system time has gone backwards and we're treating
            // it as the trailing edge, or we've hit the `maxWait` limit.
            return (lastCallTime === undefined || (timeSinceLastCall >= wait) ||
                (timeSinceLastCall < 0) || (maxing && timeSinceLastInvoke >= maxWait));
        }

        function timerExpired() {
            var time = now$1();
            if (shouldInvoke(time)) {
                return trailingEdge(time);
            }
            // Restart the timer.
            timerId = setTimeout(timerExpired, remainingWait(time));
        }

        function trailingEdge(time) {
            timerId = undefined;

            // Only invoke if we have `lastArgs` which means `func` has been
            // debounced at least once.
            if (trailing && lastArgs) {
                return invokeFunc(time);
            }
            lastArgs = lastThis = undefined;
            return result;
        }

        function cancel() {
            if (timerId !== undefined) {
                clearTimeout(timerId);
            }
            lastInvokeTime = 0;
            lastArgs = lastCallTime = lastThis = timerId = undefined;
        }

        function flush() {
            return timerId === undefined ? result : trailingEdge(now$1());
        }

        function debounced() {
            var time = now$1(),
                isInvoking = shouldInvoke(time);

            lastArgs = arguments;
            lastThis = this;
            lastCallTime = time;

            if (isInvoking) {
                if (timerId === undefined) {
                    return leadingEdge(lastCallTime);
                }
                if (maxing) {
                    // Handle invocations in a tight loop.
                    timerId = setTimeout(timerExpired, wait);
                    return invokeFunc(lastCallTime);
                }
            }
            if (timerId === undefined) {
                timerId = setTimeout(timerExpired, wait);
            }
            return result;
        }
        debounced.cancel = cancel;
        debounced.flush = flush;
        return debounced;
    }

    /**
     * Checks if `value` is the
     * [language type](http://www.ecma-international.org/ecma-262/7.0/#sec-ecmascript-language-types)
     * of `Object`. (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is an object, else `false`.
     * @example
     *
     * _.isObject({});
     * // => true
     *
     * _.isObject([1, 2, 3]);
     * // => true
     *
     * _.isObject(_.noop);
     * // => true
     *
     * _.isObject(null);
     * // => false
     */
    function isObject$1(value) {
        var type = typeof value;
        return !!value && (type == 'object' || type == 'function');
    }

    /**
     * Checks if `value` is object-like. A value is object-like if it's not `null`
     * and has a `typeof` result of "object".
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
     * @example
     *
     * _.isObjectLike({});
     * // => true
     *
     * _.isObjectLike([1, 2, 3]);
     * // => true
     *
     * _.isObjectLike(_.noop);
     * // => false
     *
     * _.isObjectLike(null);
     * // => false
     */
    function isObjectLike$1(value) {
        return !!value && typeof value == 'object';
    }

    /**
     * Checks if `value` is classified as a `Symbol` primitive or object.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a symbol, else `false`.
     * @example
     *
     * _.isSymbol(Symbol.iterator);
     * // => true
     *
     * _.isSymbol('abc');
     * // => false
     */
    function isSymbol$1(value) {
        return typeof value == 'symbol' ||
            (isObjectLike$1(value) && objectToString$1.call(value) == symbolTag$1);
    }

    /**
     * Converts `value` to a number.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to process.
     * @returns {number} Returns the number.
     * @example
     *
     * _.toNumber(3.2);
     * // => 3.2
     *
     * _.toNumber(Number.MIN_VALUE);
     * // => 5e-324
     *
     * _.toNumber(Infinity);
     * // => Infinity
     *
     * _.toNumber('3.2');
     * // => 3.2
     */
    function toNumber$1(value) {
        if (typeof value == 'number') {
            return value;
        }
        if (isSymbol$1(value)) {
            return NAN$1;
        }
        if (isObject$1(value)) {
            var other = typeof value.valueOf == 'function' ? value.valueOf() : value;
            value = isObject$1(other) ? (other + '') : other;
        }
        if (typeof value != 'string') {
            return value === 0 ? value : +value;
        }
        value = value.replace(reTrim$1, '');
        var isBinary = reIsBinary$1.test(value);
        return (isBinary || reIsOctal$1.test(value))
            ? freeParseInt$1(value.slice(2), isBinary ? 2 : 8)
            : (reIsBadHex$1.test(value) ? NAN$1 : +value);
    }

    var lodash_debounce = debounce$1;

    /**
     * lodash (Custom Build) <https://lodash.com/>
     * Build: `lodash modularize exports="npm" -o ./`
     * Copyright jQuery Foundation and other contributors <https://jquery.org/>
     * Released under MIT license <https://lodash.com/license>
     * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
     * Copyright Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
     */

    /** Used as the `TypeError` message for "Functions" methods. */
    var FUNC_ERROR_TEXT$2 = 'Expected a function';

    /** Used to stand-in for `undefined` hash values. */
    var HASH_UNDEFINED = '__lodash_hash_undefined__';

    /** `Object#toString` result references. */
    var funcTag = '[object Function]',
        genTag = '[object GeneratorFunction]';

    /**
     * Used to match `RegExp`
     * [syntax characters](http://ecma-international.org/ecma-262/7.0/#sec-patterns).
     */
    var reRegExpChar = /[\\^$.*+?()[\]{}|]/g;

    /** Used to detect host constructors (Safari). */
    var reIsHostCtor = /^\[object .+?Constructor\]$/;

    /** Detect free variable `global` from Node.js. */
    var freeGlobal$2 = typeof commonjsGlobal == 'object' && commonjsGlobal && commonjsGlobal.Object === Object && commonjsGlobal;

    /** Detect free variable `self`. */
    var freeSelf$2 = typeof self == 'object' && self && self.Object === Object && self;

    /** Used as a reference to the global object. */
    var root$2 = freeGlobal$2 || freeSelf$2 || Function('return this')();

    /**
     * Gets the value at `key` of `object`.
     *
     * @private
     * @param {Object} [object] The object to query.
     * @param {string} key The key of the property to get.
     * @returns {*} Returns the property value.
     */
    function getValue(object, key) {
        return object == null ? undefined : object[key];
    }

    /**
     * Checks if `value` is a host object in IE < 9.
     *
     * @private
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a host object, else `false`.
     */
    function isHostObject(value) {
        // Many host objects are `Object` objects that can coerce to strings
        // despite having improperly defined `toString` methods.
        var result = false;
        if (value != null && typeof value.toString != 'function') {
            try {
                result = !!(value + '');
            } catch (e) {}
        }
        return result;
    }

    /** Used for built-in method references. */
    var arrayProto = Array.prototype,
        funcProto = Function.prototype,
        objectProto$2 = Object.prototype;

    /** Used to detect overreaching core-js shims. */
    var coreJsData = root$2['__core-js_shared__'];

    /** Used to detect methods masquerading as native. */
    var maskSrcKey = (function() {
        var uid = /[^.]+$/.exec(coreJsData && coreJsData.keys && coreJsData.keys.IE_PROTO || '');
        return uid ? ('Symbol(src)_1.' + uid) : '';
    }());

    /** Used to resolve the decompiled source of functions. */
    var funcToString = funcProto.toString;

    /** Used to check objects for own properties. */
    var hasOwnProperty = objectProto$2.hasOwnProperty;

    /**
     * Used to resolve the
     * [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
     * of values.
     */
    var objectToString$2 = objectProto$2.toString;

    /** Used to detect if a method is native. */
    var reIsNative = RegExp('^' +
        funcToString.call(hasOwnProperty).replace(reRegExpChar, '\\$&')
            .replace(/hasOwnProperty|(function).*?(?=\\\()| for .+?(?=\\\])/g, '$1.*?') + '$'
    );

    /** Built-in value references. */
    var splice = arrayProto.splice;

    /* Built-in method references that are verified to be native. */
    var Map = getNative(root$2, 'Map'),
        nativeCreate = getNative(Object, 'create');

    /**
     * Creates a hash object.
     *
     * @private
     * @constructor
     * @param {Array} [entries] The key-value pairs to cache.
     */
    function Hash(entries) {
        var index = -1,
            length = entries ? entries.length : 0;

        this.clear();
        while (++index < length) {
            var entry = entries[index];
            this.set(entry[0], entry[1]);
        }
    }

    /**
     * Removes all key-value entries from the hash.
     *
     * @private
     * @name clear
     * @memberOf Hash
     */
    function hashClear() {
        this.__data__ = nativeCreate ? nativeCreate(null) : {};
    }

    /**
     * Removes `key` and its value from the hash.
     *
     * @private
     * @name delete
     * @memberOf Hash
     * @param {Object} hash The hash to modify.
     * @param {string} key The key of the value to remove.
     * @returns {boolean} Returns `true` if the entry was removed, else `false`.
     */
    function hashDelete(key) {
        return this.has(key) && delete this.__data__[key];
    }

    /**
     * Gets the hash value for `key`.
     *
     * @private
     * @name get
     * @memberOf Hash
     * @param {string} key The key of the value to get.
     * @returns {*} Returns the entry value.
     */
    function hashGet(key) {
        var data = this.__data__;
        if (nativeCreate) {
            var result = data[key];
            return result === HASH_UNDEFINED ? undefined : result;
        }
        return hasOwnProperty.call(data, key) ? data[key] : undefined;
    }

    /**
     * Checks if a hash value for `key` exists.
     *
     * @private
     * @name has
     * @memberOf Hash
     * @param {string} key The key of the entry to check.
     * @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
     */
    function hashHas(key) {
        var data = this.__data__;
        return nativeCreate ? data[key] !== undefined : hasOwnProperty.call(data, key);
    }

    /**
     * Sets the hash `key` to `value`.
     *
     * @private
     * @name set
     * @memberOf Hash
     * @param {string} key The key of the value to set.
     * @param {*} value The value to set.
     * @returns {Object} Returns the hash instance.
     */
    function hashSet(key, value) {
        var data = this.__data__;
        data[key] = (nativeCreate && value === undefined) ? HASH_UNDEFINED : value;
        return this;
    }

    // Add methods to `Hash`.
    Hash.prototype.clear = hashClear;
    Hash.prototype['delete'] = hashDelete;
    Hash.prototype.get = hashGet;
    Hash.prototype.has = hashHas;
    Hash.prototype.set = hashSet;

    /**
     * Creates an list cache object.
     *
     * @private
     * @constructor
     * @param {Array} [entries] The key-value pairs to cache.
     */
    function ListCache(entries) {
        var index = -1,
            length = entries ? entries.length : 0;

        this.clear();
        while (++index < length) {
            var entry = entries[index];
            this.set(entry[0], entry[1]);
        }
    }

    /**
     * Removes all key-value entries from the list cache.
     *
     * @private
     * @name clear
     * @memberOf ListCache
     */
    function listCacheClear() {
        this.__data__ = [];
    }

    /**
     * Removes `key` and its value from the list cache.
     *
     * @private
     * @name delete
     * @memberOf ListCache
     * @param {string} key The key of the value to remove.
     * @returns {boolean} Returns `true` if the entry was removed, else `false`.
     */
    function listCacheDelete(key) {
        var data = this.__data__,
            index = assocIndexOf(data, key);

        if (index < 0) {
            return false;
        }
        var lastIndex = data.length - 1;
        if (index == lastIndex) {
            data.pop();
        } else {
            splice.call(data, index, 1);
        }
        return true;
    }

    /**
     * Gets the list cache value for `key`.
     *
     * @private
     * @name get
     * @memberOf ListCache
     * @param {string} key The key of the value to get.
     * @returns {*} Returns the entry value.
     */
    function listCacheGet(key) {
        var data = this.__data__,
            index = assocIndexOf(data, key);

        return index < 0 ? undefined : data[index][1];
    }

    /**
     * Checks if a list cache value for `key` exists.
     *
     * @private
     * @name has
     * @memberOf ListCache
     * @param {string} key The key of the entry to check.
     * @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
     */
    function listCacheHas(key) {
        return assocIndexOf(this.__data__, key) > -1;
    }

    /**
     * Sets the list cache `key` to `value`.
     *
     * @private
     * @name set
     * @memberOf ListCache
     * @param {string} key The key of the value to set.
     * @param {*} value The value to set.
     * @returns {Object} Returns the list cache instance.
     */
    function listCacheSet(key, value) {
        var data = this.__data__,
            index = assocIndexOf(data, key);

        if (index < 0) {
            data.push([key, value]);
        } else {
            data[index][1] = value;
        }
        return this;
    }

    // Add methods to `ListCache`.
    ListCache.prototype.clear = listCacheClear;
    ListCache.prototype['delete'] = listCacheDelete;
    ListCache.prototype.get = listCacheGet;
    ListCache.prototype.has = listCacheHas;
    ListCache.prototype.set = listCacheSet;

    /**
     * Creates a map cache object to store key-value pairs.
     *
     * @private
     * @constructor
     * @param {Array} [entries] The key-value pairs to cache.
     */
    function MapCache(entries) {
        var index = -1,
            length = entries ? entries.length : 0;

        this.clear();
        while (++index < length) {
            var entry = entries[index];
            this.set(entry[0], entry[1]);
        }
    }

    /**
     * Removes all key-value entries from the map.
     *
     * @private
     * @name clear
     * @memberOf MapCache
     */
    function mapCacheClear() {
        this.__data__ = {
            'hash': new Hash,
            'map': new (Map || ListCache),
            'string': new Hash
        };
    }

    /**
     * Removes `key` and its value from the map.
     *
     * @private
     * @name delete
     * @memberOf MapCache
     * @param {string} key The key of the value to remove.
     * @returns {boolean} Returns `true` if the entry was removed, else `false`.
     */
    function mapCacheDelete(key) {
        return getMapData(this, key)['delete'](key);
    }

    /**
     * Gets the map value for `key`.
     *
     * @private
     * @name get
     * @memberOf MapCache
     * @param {string} key The key of the value to get.
     * @returns {*} Returns the entry value.
     */
    function mapCacheGet(key) {
        return getMapData(this, key).get(key);
    }

    /**
     * Checks if a map value for `key` exists.
     *
     * @private
     * @name has
     * @memberOf MapCache
     * @param {string} key The key of the entry to check.
     * @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
     */
    function mapCacheHas(key) {
        return getMapData(this, key).has(key);
    }

    /**
     * Sets the map `key` to `value`.
     *
     * @private
     * @name set
     * @memberOf MapCache
     * @param {string} key The key of the value to set.
     * @param {*} value The value to set.
     * @returns {Object} Returns the map cache instance.
     */
    function mapCacheSet(key, value) {
        getMapData(this, key).set(key, value);
        return this;
    }

    // Add methods to `MapCache`.
    MapCache.prototype.clear = mapCacheClear;
    MapCache.prototype['delete'] = mapCacheDelete;
    MapCache.prototype.get = mapCacheGet;
    MapCache.prototype.has = mapCacheHas;
    MapCache.prototype.set = mapCacheSet;

    /**
     * Gets the index at which the `key` is found in `array` of key-value pairs.
     *
     * @private
     * @param {Array} array The array to inspect.
     * @param {*} key The key to search for.
     * @returns {number} Returns the index of the matched value, else `-1`.
     */
    function assocIndexOf(array, key) {
        var length = array.length;
        while (length--) {
            if (eq(array[length][0], key)) {
                return length;
            }
        }
        return -1;
    }

    /**
     * The base implementation of `_.isNative` without bad shim checks.
     *
     * @private
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a native function,
     *  else `false`.
     */
    function baseIsNative(value) {
        if (!isObject$2(value) || isMasked(value)) {
            return false;
        }
        var pattern = (isFunction(value) || isHostObject(value)) ? reIsNative : reIsHostCtor;
        return pattern.test(toSource(value));
    }

    /**
     * Gets the data for `map`.
     *
     * @private
     * @param {Object} map The map to query.
     * @param {string} key The reference key.
     * @returns {*} Returns the map data.
     */
    function getMapData(map, key) {
        var data = map.__data__;
        return isKeyable(key)
            ? data[typeof key == 'string' ? 'string' : 'hash']
            : data.map;
    }

    /**
     * Gets the native function at `key` of `object`.
     *
     * @private
     * @param {Object} object The object to query.
     * @param {string} key The key of the method to get.
     * @returns {*} Returns the function if it's native, else `undefined`.
     */
    function getNative(object, key) {
        var value = getValue(object, key);
        return baseIsNative(value) ? value : undefined;
    }

    /**
     * Checks if `value` is suitable for use as unique object key.
     *
     * @private
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is suitable, else `false`.
     */
    function isKeyable(value) {
        var type = typeof value;
        return (type == 'string' || type == 'number' || type == 'symbol' || type == 'boolean')
            ? (value !== '__proto__')
            : (value === null);
    }

    /**
     * Checks if `func` has its source masked.
     *
     * @private
     * @param {Function} func The function to check.
     * @returns {boolean} Returns `true` if `func` is masked, else `false`.
     */
    function isMasked(func) {
        return !!maskSrcKey && (maskSrcKey in func);
    }

    /**
     * Converts `func` to its source code.
     *
     * @private
     * @param {Function} func The function to process.
     * @returns {string} Returns the source code.
     */
    function toSource(func) {
        if (func != null) {
            try {
                return funcToString.call(func);
            } catch (e) {}
            try {
                return (func + '');
            } catch (e) {}
        }
        return '';
    }

    /**
     * Creates a function that memoizes the result of `func`. If `resolver` is
     * provided, it determines the cache key for storing the result based on the
     * arguments provided to the memoized function. By default, the first argument
     * provided to the memoized function is used as the map cache key. The `func`
     * is invoked with the `this` binding of the memoized function.
     *
     * **Note:** The cache is exposed as the `cache` property on the memoized
     * function. Its creation may be customized by replacing the `_.memoize.Cache`
     * constructor with one whose instances implement the
     * [`Map`](http://ecma-international.org/ecma-262/7.0/#sec-properties-of-the-map-prototype-object)
     * method interface of `delete`, `get`, `has`, and `set`.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Function
     * @param {Function} func The function to have its output memoized.
     * @param {Function} [resolver] The function to resolve the cache key.
     * @returns {Function} Returns the new memoized function.
     * @example
     *
     * var object = { 'a': 1, 'b': 2 };
     * var other = { 'c': 3, 'd': 4 };
     *
     * var values = _.memoize(_.values);
     * values(object);
     * // => [1, 2]
     *
     * values(other);
     * // => [3, 4]
     *
     * object.a = 2;
     * values(object);
     * // => [1, 2]
     *
     * // Modify the result cache.
     * values.cache.set(object, ['a', 'b']);
     * values(object);
     * // => ['a', 'b']
     *
     * // Replace `_.memoize.Cache`.
     * _.memoize.Cache = WeakMap;
     */
    function memoize(func, resolver) {
        if (typeof func != 'function' || (resolver && typeof resolver != 'function')) {
            throw new TypeError(FUNC_ERROR_TEXT$2);
        }
        var memoized = function() {
            var args = arguments,
                key = resolver ? resolver.apply(this, args) : args[0],
                cache = memoized.cache;

            if (cache.has(key)) {
                return cache.get(key);
            }
            var result = func.apply(this, args);
            memoized.cache = cache.set(key, result);
            return result;
        };
        memoized.cache = new (memoize.Cache || MapCache);
        return memoized;
    }

    // Assign cache to `_.memoize`.
    memoize.Cache = MapCache;

    /**
     * Performs a
     * [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
     * comparison between two values to determine if they are equivalent.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to compare.
     * @param {*} other The other value to compare.
     * @returns {boolean} Returns `true` if the values are equivalent, else `false`.
     * @example
     *
     * var object = { 'a': 1 };
     * var other = { 'a': 1 };
     *
     * _.eq(object, object);
     * // => true
     *
     * _.eq(object, other);
     * // => false
     *
     * _.eq('a', 'a');
     * // => true
     *
     * _.eq('a', Object('a'));
     * // => false
     *
     * _.eq(NaN, NaN);
     * // => true
     */
    function eq(value, other) {
        return value === other || (value !== value && other !== other);
    }

    /**
     * Checks if `value` is classified as a `Function` object.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a function, else `false`.
     * @example
     *
     * _.isFunction(_);
     * // => true
     *
     * _.isFunction(/abc/);
     * // => false
     */
    function isFunction(value) {
        // The use of `Object#toString` avoids issues with the `typeof` operator
        // in Safari 8-9 which returns 'object' for typed array and other constructors.
        var tag = isObject$2(value) ? objectToString$2.call(value) : '';
        return tag == funcTag || tag == genTag;
    }

    /**
     * Checks if `value` is the
     * [language type](http://www.ecma-international.org/ecma-262/7.0/#sec-ecmascript-language-types)
     * of `Object`. (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is an object, else `false`.
     * @example
     *
     * _.isObject({});
     * // => true
     *
     * _.isObject([1, 2, 3]);
     * // => true
     *
     * _.isObject(_.noop);
     * // => true
     *
     * _.isObject(null);
     * // => false
     */
    function isObject$2(value) {
        var type = typeof value;
        return !!value && (type == 'object' || type == 'function');
    }

    var lodash_memoize = memoize;

    var resizeObservers = [];

    var hasActiveObservations = function () {
        return resizeObservers.some(function (ro) { return ro.activeTargets.length > 0; });
    };

    var hasSkippedObservations = function () {
        return resizeObservers.some(function (ro) { return ro.skippedTargets.length > 0; });
    };

    var msg = 'ResizeObserver loop completed with undelivered notifications.';
    var deliverResizeLoopError = function () {
        var event;
        if (typeof ErrorEvent === 'function') {
            event = new ErrorEvent('error', {
                message: msg
            });
        }
        else {
            event = document.createEvent('Event');
            event.initEvent('error', false, false);
            event.message = msg;
        }
        window.dispatchEvent(event);
    };

    var ResizeObserverBoxOptions;
    (function (ResizeObserverBoxOptions) {
        ResizeObserverBoxOptions["BORDER_BOX"] = "border-box";
        ResizeObserverBoxOptions["CONTENT_BOX"] = "content-box";
        ResizeObserverBoxOptions["DEVICE_PIXEL_CONTENT_BOX"] = "device-pixel-content-box";
    })(ResizeObserverBoxOptions || (ResizeObserverBoxOptions = {}));

    var freeze = function (obj) { return Object.freeze(obj); };

    var ResizeObserverSize = (function () {
        function ResizeObserverSize(inlineSize, blockSize) {
            this.inlineSize = inlineSize;
            this.blockSize = blockSize;
            freeze(this);
        }
        return ResizeObserverSize;
    }());

    var DOMRectReadOnly = (function () {
        function DOMRectReadOnly(x, y, width, height) {
            this.x = x;
            this.y = y;
            this.width = width;
            this.height = height;
            this.top = this.y;
            this.left = this.x;
            this.bottom = this.top + this.height;
            this.right = this.left + this.width;
            return freeze(this);
        }
        DOMRectReadOnly.prototype.toJSON = function () {
            var _a = this, x = _a.x, y = _a.y, top = _a.top, right = _a.right, bottom = _a.bottom, left = _a.left, width = _a.width, height = _a.height;
            return { x: x, y: y, top: top, right: right, bottom: bottom, left: left, width: width, height: height };
        };
        DOMRectReadOnly.fromRect = function (rectangle) {
            return new DOMRectReadOnly(rectangle.x, rectangle.y, rectangle.width, rectangle.height);
        };
        return DOMRectReadOnly;
    }());

    var isSVG = function (target) { return target instanceof SVGElement && 'getBBox' in target; };
    var isHidden = function (target) {
        if (isSVG(target)) {
            var _a = target.getBBox(), width = _a.width, height = _a.height;
            return !width && !height;
        }
        var _b = target, offsetWidth = _b.offsetWidth, offsetHeight = _b.offsetHeight;
        return !(offsetWidth || offsetHeight || target.getClientRects().length);
    };
    var isElement = function (obj) {
        var _a, _b;
        if (obj instanceof Element) {
            return true;
        }
        var scope = (_b = (_a = obj) === null || _a === void 0 ? void 0 : _a.ownerDocument) === null || _b === void 0 ? void 0 : _b.defaultView;
        return !!(scope && obj instanceof scope.Element);
    };
    var isReplacedElement = function (target) {
        switch (target.tagName) {
            case 'INPUT':
                if (target.type !== 'image') {
                    break;
                }
            case 'VIDEO':
            case 'AUDIO':
            case 'EMBED':
            case 'OBJECT':
            case 'CANVAS':
            case 'IFRAME':
            case 'IMG':
                return true;
        }
        return false;
    };

    var global$1 = typeof window !== 'undefined' ? window : {};

    var cache = new WeakMap();
    var scrollRegexp = /auto|scroll/;
    var verticalRegexp = /^tb|vertical/;
    var IE = (/msie|trident/i).test(global$1.navigator && global$1.navigator.userAgent);
    var parseDimension = function (pixel) { return parseFloat(pixel || '0'); };
    var size = function (inlineSize, blockSize, switchSizes) {
        if (inlineSize === void 0) { inlineSize = 0; }
        if (blockSize === void 0) { blockSize = 0; }
        if (switchSizes === void 0) { switchSizes = false; }
        return new ResizeObserverSize((switchSizes ? blockSize : inlineSize) || 0, (switchSizes ? inlineSize : blockSize) || 0);
    };
    var zeroBoxes = freeze({
        devicePixelContentBoxSize: size(),
        borderBoxSize: size(),
        contentBoxSize: size(),
        contentRect: new DOMRectReadOnly(0, 0, 0, 0)
    });
    var calculateBoxSizes = function (target, forceRecalculation) {
        if (forceRecalculation === void 0) { forceRecalculation = false; }
        if (cache.has(target) && !forceRecalculation) {
            return cache.get(target);
        }
        if (isHidden(target)) {
            cache.set(target, zeroBoxes);
            return zeroBoxes;
        }
        var cs = getComputedStyle(target);
        var svg = isSVG(target) && target.ownerSVGElement && target.getBBox();
        var removePadding = !IE && cs.boxSizing === 'border-box';
        var switchSizes = verticalRegexp.test(cs.writingMode || '');
        var canScrollVertically = !svg && scrollRegexp.test(cs.overflowY || '');
        var canScrollHorizontally = !svg && scrollRegexp.test(cs.overflowX || '');
        var paddingTop = svg ? 0 : parseDimension(cs.paddingTop);
        var paddingRight = svg ? 0 : parseDimension(cs.paddingRight);
        var paddingBottom = svg ? 0 : parseDimension(cs.paddingBottom);
        var paddingLeft = svg ? 0 : parseDimension(cs.paddingLeft);
        var borderTop = svg ? 0 : parseDimension(cs.borderTopWidth);
        var borderRight = svg ? 0 : parseDimension(cs.borderRightWidth);
        var borderBottom = svg ? 0 : parseDimension(cs.borderBottomWidth);
        var borderLeft = svg ? 0 : parseDimension(cs.borderLeftWidth);
        var horizontalPadding = paddingLeft + paddingRight;
        var verticalPadding = paddingTop + paddingBottom;
        var horizontalBorderArea = borderLeft + borderRight;
        var verticalBorderArea = borderTop + borderBottom;
        var horizontalScrollbarThickness = !canScrollHorizontally ? 0 : target.offsetHeight - verticalBorderArea - target.clientHeight;
        var verticalScrollbarThickness = !canScrollVertically ? 0 : target.offsetWidth - horizontalBorderArea - target.clientWidth;
        var widthReduction = removePadding ? horizontalPadding + horizontalBorderArea : 0;
        var heightReduction = removePadding ? verticalPadding + verticalBorderArea : 0;
        var contentWidth = svg ? svg.width : parseDimension(cs.width) - widthReduction - verticalScrollbarThickness;
        var contentHeight = svg ? svg.height : parseDimension(cs.height) - heightReduction - horizontalScrollbarThickness;
        var borderBoxWidth = contentWidth + horizontalPadding + verticalScrollbarThickness + horizontalBorderArea;
        var borderBoxHeight = contentHeight + verticalPadding + horizontalScrollbarThickness + verticalBorderArea;
        var boxes = freeze({
            devicePixelContentBoxSize: size(Math.round(contentWidth * devicePixelRatio), Math.round(contentHeight * devicePixelRatio), switchSizes),
            borderBoxSize: size(borderBoxWidth, borderBoxHeight, switchSizes),
            contentBoxSize: size(contentWidth, contentHeight, switchSizes),
            contentRect: new DOMRectReadOnly(paddingLeft, paddingTop, contentWidth, contentHeight)
        });
        cache.set(target, boxes);
        return boxes;
    };
    var calculateBoxSize = function (target, observedBox, forceRecalculation) {
        var _a = calculateBoxSizes(target, forceRecalculation), borderBoxSize = _a.borderBoxSize, contentBoxSize = _a.contentBoxSize, devicePixelContentBoxSize = _a.devicePixelContentBoxSize;
        switch (observedBox) {
            case ResizeObserverBoxOptions.DEVICE_PIXEL_CONTENT_BOX:
                return devicePixelContentBoxSize;
            case ResizeObserverBoxOptions.BORDER_BOX:
                return borderBoxSize;
            default:
                return contentBoxSize;
        }
    };

    var ResizeObserverEntry = (function () {
        function ResizeObserverEntry(target) {
            var boxes = calculateBoxSizes(target);
            this.target = target;
            this.contentRect = boxes.contentRect;
            this.borderBoxSize = freeze([boxes.borderBoxSize]);
            this.contentBoxSize = freeze([boxes.contentBoxSize]);
            this.devicePixelContentBoxSize = freeze([boxes.devicePixelContentBoxSize]);
        }
        return ResizeObserverEntry;
    }());

    var calculateDepthForNode = function (node) {
        if (isHidden(node)) {
            return Infinity;
        }
        var depth = 0;
        var parent = node.parentNode;
        while (parent) {
            depth += 1;
            parent = parent.parentNode;
        }
        return depth;
    };

    var broadcastActiveObservations = function () {
        var shallowestDepth = Infinity;
        var callbacks = [];
        resizeObservers.forEach(function processObserver(ro) {
            if (ro.activeTargets.length === 0) {
                return;
            }
            var entries = [];
            ro.activeTargets.forEach(function processTarget(ot) {
                var entry = new ResizeObserverEntry(ot.target);
                var targetDepth = calculateDepthForNode(ot.target);
                entries.push(entry);
                ot.lastReportedSize = calculateBoxSize(ot.target, ot.observedBox);
                if (targetDepth < shallowestDepth) {
                    shallowestDepth = targetDepth;
                }
            });
            callbacks.push(function resizeObserverCallback() {
                ro.callback.call(ro.observer, entries, ro.observer);
            });
            ro.activeTargets.splice(0, ro.activeTargets.length);
        });
        for (var _i = 0, callbacks_1 = callbacks; _i < callbacks_1.length; _i++) {
            var callback = callbacks_1[_i];
            callback();
        }
        return shallowestDepth;
    };

    var gatherActiveObservationsAtDepth = function (depth) {
        resizeObservers.forEach(function processObserver(ro) {
            ro.activeTargets.splice(0, ro.activeTargets.length);
            ro.skippedTargets.splice(0, ro.skippedTargets.length);
            ro.observationTargets.forEach(function processTarget(ot) {
                if (ot.isActive()) {
                    if (calculateDepthForNode(ot.target) > depth) {
                        ro.activeTargets.push(ot);
                    }
                    else {
                        ro.skippedTargets.push(ot);
                    }
                }
            });
        });
    };

    var process = function () {
        var depth = 0;
        gatherActiveObservationsAtDepth(depth);
        while (hasActiveObservations()) {
            depth = broadcastActiveObservations();
            gatherActiveObservationsAtDepth(depth);
        }
        if (hasSkippedObservations()) {
            deliverResizeLoopError();
        }
        return depth > 0;
    };

    var trigger;
    var callbacks = [];
    var notify = function () { return callbacks.splice(0).forEach(function (cb) { return cb(); }); };
    var queueMicroTask = function (callback) {
        if (!trigger) {
            var toggle_1 = 0;
            var el_1 = document.createTextNode('');
            var config = { characterData: true };
            new MutationObserver(function () { return notify(); }).observe(el_1, config);
            trigger = function () { el_1.textContent = "" + (toggle_1 ? toggle_1-- : toggle_1++); };
        }
        callbacks.push(callback);
        trigger();
    };

    var queueResizeObserver = function (cb) {
        queueMicroTask(function ResizeObserver() {
            requestAnimationFrame(cb);
        });
    };

    var watching = 0;
    var isWatching = function () { return !!watching; };
    var CATCH_PERIOD = 250;
    var observerConfig = { attributes: true, characterData: true, childList: true, subtree: true };
    var events = [
        'resize',
        'load',
        'transitionend',
        'animationend',
        'animationstart',
        'animationiteration',
        'keyup',
        'keydown',
        'mouseup',
        'mousedown',
        'mouseover',
        'mouseout',
        'blur',
        'focus'
    ];
    var time = function (timeout) {
        if (timeout === void 0) { timeout = 0; }
        return Date.now() + timeout;
    };
    var scheduled = false;
    var Scheduler = (function () {
        function Scheduler() {
            var _this = this;
            this.stopped = true;
            this.listener = function () { return _this.schedule(); };
        }
        Scheduler.prototype.run = function (timeout) {
            var _this = this;
            if (timeout === void 0) { timeout = CATCH_PERIOD; }
            if (scheduled) {
                return;
            }
            scheduled = true;
            var until = time(timeout);
            queueResizeObserver(function () {
                var elementsHaveResized = false;
                try {
                    elementsHaveResized = process();
                }
                finally {
                    scheduled = false;
                    timeout = until - time();
                    if (!isWatching()) {
                        return;
                    }
                    if (elementsHaveResized) {
                        _this.run(1000);
                    }
                    else if (timeout > 0) {
                        _this.run(timeout);
                    }
                    else {
                        _this.start();
                    }
                }
            });
        };
        Scheduler.prototype.schedule = function () {
            this.stop();
            this.run();
        };
        Scheduler.prototype.observe = function () {
            var _this = this;
            var cb = function () { return _this.observer && _this.observer.observe(document.body, observerConfig); };
            document.body ? cb() : global$1.addEventListener('DOMContentLoaded', cb);
        };
        Scheduler.prototype.start = function () {
            var _this = this;
            if (this.stopped) {
                this.stopped = false;
                this.observer = new MutationObserver(this.listener);
                this.observe();
                events.forEach(function (name) { return global$1.addEventListener(name, _this.listener, true); });
            }
        };
        Scheduler.prototype.stop = function () {
            var _this = this;
            if (!this.stopped) {
                this.observer && this.observer.disconnect();
                events.forEach(function (name) { return global$1.removeEventListener(name, _this.listener, true); });
                this.stopped = true;
            }
        };
        return Scheduler;
    }());
    var scheduler = new Scheduler();
    var updateCount = function (n) {
        !watching && n > 0 && scheduler.start();
        watching += n;
        !watching && scheduler.stop();
    };

    var skipNotifyOnElement = function (target) {
        return !isSVG(target)
            && !isReplacedElement(target)
            && getComputedStyle(target).display === 'inline';
    };
    var ResizeObservation = (function () {
        function ResizeObservation(target, observedBox) {
            this.target = target;
            this.observedBox = observedBox || ResizeObserverBoxOptions.CONTENT_BOX;
            this.lastReportedSize = {
                inlineSize: 0,
                blockSize: 0
            };
        }
        ResizeObservation.prototype.isActive = function () {
            var size = calculateBoxSize(this.target, this.observedBox, true);
            if (skipNotifyOnElement(this.target)) {
                this.lastReportedSize = size;
            }
            if (this.lastReportedSize.inlineSize !== size.inlineSize
                || this.lastReportedSize.blockSize !== size.blockSize) {
                return true;
            }
            return false;
        };
        return ResizeObservation;
    }());

    var ResizeObserverDetail = (function () {
        function ResizeObserverDetail(resizeObserver, callback) {
            this.activeTargets = [];
            this.skippedTargets = [];
            this.observationTargets = [];
            this.observer = resizeObserver;
            this.callback = callback;
        }
        return ResizeObserverDetail;
    }());

    var observerMap = new WeakMap();
    var getObservationIndex = function (observationTargets, target) {
        for (var i = 0; i < observationTargets.length; i += 1) {
            if (observationTargets[i].target === target) {
                return i;
            }
        }
        return -1;
    };
    var ResizeObserverController = (function () {
        function ResizeObserverController() {
        }
        ResizeObserverController.connect = function (resizeObserver, callback) {
            var detail = new ResizeObserverDetail(resizeObserver, callback);
            observerMap.set(resizeObserver, detail);
        };
        ResizeObserverController.observe = function (resizeObserver, target, options) {
            var detail = observerMap.get(resizeObserver);
            var firstObservation = detail.observationTargets.length === 0;
            if (getObservationIndex(detail.observationTargets, target) < 0) {
                firstObservation && resizeObservers.push(detail);
                detail.observationTargets.push(new ResizeObservation(target, options && options.box));
                updateCount(1);
                scheduler.schedule();
            }
        };
        ResizeObserverController.unobserve = function (resizeObserver, target) {
            var detail = observerMap.get(resizeObserver);
            var index = getObservationIndex(detail.observationTargets, target);
            var lastObservation = detail.observationTargets.length === 1;
            if (index >= 0) {
                lastObservation && resizeObservers.splice(resizeObservers.indexOf(detail), 1);
                detail.observationTargets.splice(index, 1);
                updateCount(-1);
            }
        };
        ResizeObserverController.disconnect = function (resizeObserver) {
            var _this = this;
            var detail = observerMap.get(resizeObserver);
            detail.observationTargets.slice().forEach(function (ot) { return _this.unobserve(resizeObserver, ot.target); });
            detail.activeTargets.splice(0, detail.activeTargets.length);
        };
        return ResizeObserverController;
    }());

    var ResizeObserver = (function () {
        function ResizeObserver(callback) {
            if (arguments.length === 0) {
                throw new TypeError("Failed to construct 'ResizeObserver': 1 argument required, but only 0 present.");
            }
            if (typeof callback !== 'function') {
                throw new TypeError("Failed to construct 'ResizeObserver': The callback provided as parameter 1 is not a function.");
            }
            ResizeObserverController.connect(this, callback);
        }
        ResizeObserver.prototype.observe = function (target, options) {
            if (arguments.length === 0) {
                throw new TypeError("Failed to execute 'observe' on 'ResizeObserver': 1 argument required, but only 0 present.");
            }
            if (!isElement(target)) {
                throw new TypeError("Failed to execute 'observe' on 'ResizeObserver': parameter 1 is not of type 'Element");
            }
            ResizeObserverController.observe(this, target, options);
        };
        ResizeObserver.prototype.unobserve = function (target) {
            if (arguments.length === 0) {
                throw new TypeError("Failed to execute 'unobserve' on 'ResizeObserver': 1 argument required, but only 0 present.");
            }
            if (!isElement(target)) {
                throw new TypeError("Failed to execute 'unobserve' on 'ResizeObserver': parameter 1 is not of type 'Element");
            }
            ResizeObserverController.unobserve(this, target);
        };
        ResizeObserver.prototype.disconnect = function () {
            ResizeObserverController.disconnect(this);
        };
        ResizeObserver.toString = function () {
            return 'function ResizeObserver () { [polyfill code] }';
        };
        return ResizeObserver;
    }());

    // Helper function to retrieve options from element attributes
    var getOptions = function getOptions(obj) {
        var options = Array.prototype.reduce.call(obj, function (acc, attribute) {
            var option = attribute.name.match(/data-asp_simplebar-(.+)/);

            if (option) {
                var key = option[1].replace(/\W+(.)/g, function (x, chr) {
                    return chr.toUpperCase();
                });

                switch (attribute.value) {
                    case 'true':
                        acc[key] = true;
                        break;

                    case 'false':
                        acc[key] = false;
                        break;

                    case undefined:
                        acc[key] = true;
                        break;

                    default:
                        acc[key] = attribute.value;
                }
            }

            return acc;
        }, {});
        return options;
    };
    function getElementWindow(element) {
        if (!element || !element.ownerDocument || !element.ownerDocument.defaultView) {
            return window;
        }

        return element.ownerDocument.defaultView;
    }
    function getElementDocument(element) {
        if (!element || !element.ownerDocument) {
            return document;
        }

        return element.ownerDocument;
    }

    var cachedScrollbarWidth = null;
    var cachedDevicePixelRatio = null;
    window.addEventListener('resize', function () {
        if (cachedDevicePixelRatio !== window.devicePixelRatio) {
            cachedDevicePixelRatio = window.devicePixelRatio;
            cachedScrollbarWidth = null;
        }
    });
    function scrollbarWidth(el) {
        if (cachedScrollbarWidth === null) {
            var document = getElementDocument(el);

            if (typeof document === 'undefined') {
                cachedScrollbarWidth = 0;
                return cachedScrollbarWidth;
            }

            var body = document.body;
            var box = document.createElement('div');
            box.classList.add('asp_simplebar-hide-scrollbar');
            body.appendChild(box);
            var width = box.getBoundingClientRect().right;
            body.removeChild(box);
            cachedScrollbarWidth = width;
        }

        return cachedScrollbarWidth;
    }

    var asp_SimpleBar =
        /*#__PURE__*/
        function () {
            function asp_SimpleBar(element, options) {
                var _this = this;

                this.onScroll = function () {
                    var elWindow = getElementWindow(_this.el);

                    if (!_this.scrollXTicking) {
                        elWindow.requestAnimationFrame(_this.scrollX);
                        _this.scrollXTicking = true;
                    }

                    if (!_this.scrollYTicking) {
                        elWindow.requestAnimationFrame(_this.scrollY);
                        _this.scrollYTicking = true;
                    }
                };

                this.scrollX = function () {
                    if (_this.axis.x.isOverflowing) {
                        _this.showScrollbar('x');

                        _this.positionScrollbar('x');
                    }

                    _this.scrollXTicking = false;
                };

                this.scrollY = function () {
                    if (_this.axis.y.isOverflowing) {
                        _this.showScrollbar('y');

                        _this.positionScrollbar('y');
                    }

                    _this.scrollYTicking = false;
                };

                this.onMouseEnter = function () {
                    _this.showScrollbar('x');

                    _this.showScrollbar('y');
                };

                this.onMouseMove = function (e) {
                    _this.mouseX = e.clientX;
                    _this.mouseY = e.clientY;

                    if (_this.axis.x.isOverflowing || _this.axis.x.forceVisible) {
                        _this.onMouseMoveForAxis('x');
                    }

                    if (_this.axis.y.isOverflowing || _this.axis.y.forceVisible) {
                        _this.onMouseMoveForAxis('y');
                    }
                };

                this.onMouseLeave = function () {
                    _this.onMouseMove.cancel();

                    if (_this.axis.x.isOverflowing || _this.axis.x.forceVisible) {
                        _this.onMouseLeaveForAxis('x');
                    }

                    if (_this.axis.y.isOverflowing || _this.axis.y.forceVisible) {
                        _this.onMouseLeaveForAxis('y');
                    }

                    _this.mouseX = -1;
                    _this.mouseY = -1;
                };

                this.onWindowResize = function () {
                    // Recalculate scrollbarWidth in case it's a zoom
                    _this.scrollbarWidth = _this.getScrollbarWidth();

                    _this.hideNativeScrollbar();
                };

                this.hideScrollbars = function () {
                    _this.axis.x.track.rect = _this.axis.x.track.el.getBoundingClientRect();
                    _this.axis.y.track.rect = _this.axis.y.track.el.getBoundingClientRect();

                    if (!_this.isWithinBounds(_this.axis.y.track.rect)) {
                        _this.axis.y.scrollbar.el.classList.remove(_this.classNames.visible);

                        _this.axis.y.isVisible = false;
                    }

                    if (!_this.isWithinBounds(_this.axis.x.track.rect)) {
                        _this.axis.x.scrollbar.el.classList.remove(_this.classNames.visible);

                        _this.axis.x.isVisible = false;
                    }
                };

                this.onPointerEvent = function (e) {
                    var isWithinTrackXBounds, isWithinTrackYBounds;
                    _this.axis.x.track.rect = _this.axis.x.track.el.getBoundingClientRect();
                    _this.axis.y.track.rect = _this.axis.y.track.el.getBoundingClientRect();

                    if (_this.axis.x.isOverflowing || _this.axis.x.forceVisible) {
                        isWithinTrackXBounds = _this.isWithinBounds(_this.axis.x.track.rect);
                    }

                    if (_this.axis.y.isOverflowing || _this.axis.y.forceVisible) {
                        isWithinTrackYBounds = _this.isWithinBounds(_this.axis.y.track.rect);
                    } // If any pointer event is called on the scrollbar


                    if (isWithinTrackXBounds || isWithinTrackYBounds) {
                        // Preventing the event's default action stops text being
                        // selectable during the drag.
                        e.preventDefault(); // Prevent event leaking

                        e.stopPropagation();

                        if (e.type === 'mousedown') {
                            if (isWithinTrackXBounds) {
                                _this.axis.x.scrollbar.rect = _this.axis.x.scrollbar.el.getBoundingClientRect();

                                if (_this.isWithinBounds(_this.axis.x.scrollbar.rect)) {
                                    _this.onDragStart(e, 'x');
                                } else {
                                    _this.onTrackClick(e, 'x');
                                }
                            }

                            if (isWithinTrackYBounds) {
                                _this.axis.y.scrollbar.rect = _this.axis.y.scrollbar.el.getBoundingClientRect();

                                if (_this.isWithinBounds(_this.axis.y.scrollbar.rect)) {
                                    _this.onDragStart(e, 'y');
                                } else {
                                    _this.onTrackClick(e, 'y');
                                }
                            }
                        }
                    }
                };

                this.drag = function (e) {
                    var eventOffset;
                    var track = _this.axis[_this.draggedAxis].track;
                    var trackSize = track.rect[_this.axis[_this.draggedAxis].sizeAttr];
                    var scrollbar = _this.axis[_this.draggedAxis].scrollbar;
                    var contentSize = _this.contentWrapperEl[_this.axis[_this.draggedAxis].scrollSizeAttr];
                    var hostSize = parseInt(_this.elStyles[_this.axis[_this.draggedAxis].sizeAttr], 10);
                    e.preventDefault();
                    e.stopPropagation();

                    if (_this.draggedAxis === 'y') {
                        eventOffset = e.pageY;
                    } else {
                        eventOffset = e.pageX;
                    } // Calculate how far the user's mouse is from the top/left of the scrollbar (minus the dragOffset).


                    var dragPos = eventOffset - track.rect[_this.axis[_this.draggedAxis].offsetAttr] - _this.axis[_this.draggedAxis].dragOffset; // Convert the mouse position into a percentage of the scrollbar height/width.

                    var dragPerc = dragPos / (trackSize - scrollbar.size); // Scroll the content by the same percentage.

                    var scrollPos = dragPerc * (contentSize - hostSize); // Fix browsers inconsistency on RTL

                    if (_this.draggedAxis === 'x') {
                        scrollPos = _this.isRtl && asp_SimpleBar.getRtlHelpers().isRtlScrollbarInverted ? scrollPos - (trackSize + scrollbar.size) : scrollPos;
                        scrollPos = _this.isRtl && asp_SimpleBar.getRtlHelpers().isRtlScrollingInverted ? -scrollPos : scrollPos;
                    }

                    _this.contentWrapperEl[_this.axis[_this.draggedAxis].scrollOffsetAttr] = scrollPos;
                };

                this.onEndDrag = function (e) {
                    var elDocument = getElementDocument(_this.el);
                    var elWindow = getElementWindow(_this.el);
                    e.preventDefault();
                    e.stopPropagation();

                    _this.el.classList.remove(_this.classNames.dragging);

                    elDocument.removeEventListener('mousemove', _this.drag, true);
                    elDocument.removeEventListener('mouseup', _this.onEndDrag, true);
                    _this.removePreventClickId = elWindow.setTimeout(function () {
                        // Remove these asynchronously so we still suppress click events
                        // generated simultaneously with mouseup.
                        elDocument.removeEventListener('click', _this.preventClick, true);
                        elDocument.removeEventListener('dblclick', _this.preventClick, true);
                        _this.removePreventClickId = null;
                    });
                };

                this.preventClick = function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                };

                this.el = element;
                this.minScrollbarWidth = 20;
                this.options = Object.assign({}, asp_SimpleBar.defaultOptions, {}, options);
                this.classNames = Object.assign({}, asp_SimpleBar.defaultOptions.classNames, {}, this.options.classNames);
                this.axis = {
                    x: {
                        scrollOffsetAttr: 'scrollLeft',
                        sizeAttr: 'width',
                        scrollSizeAttr: 'scrollWidth',
                        offsetSizeAttr: 'offsetWidth',
                        offsetAttr: 'left',
                        overflowAttr: 'overflowX',
                        dragOffset: 0,
                        isOverflowing: true,
                        isVisible: false,
                        forceVisible: false,
                        track: {},
                        scrollbar: {}
                    },
                    y: {
                        scrollOffsetAttr: 'scrollTop',
                        sizeAttr: 'height',
                        scrollSizeAttr: 'scrollHeight',
                        offsetSizeAttr: 'offsetHeight',
                        offsetAttr: 'top',
                        overflowAttr: 'overflowY',
                        dragOffset: 0,
                        isOverflowing: true,
                        isVisible: false,
                        forceVisible: false,
                        track: {},
                        scrollbar: {}
                    }
                };
                this.removePreventClickId = null; // Don't re-instantiate over an existing one

                if (asp_SimpleBar.instances.has(this.el)) {
                    return;
                }

                this.recalculate = lodash_throttle(this.recalculate.bind(this), 64);
                this.onMouseMove = lodash_throttle(this.onMouseMove.bind(this), 64);
                this.hideScrollbars = lodash_debounce(this.hideScrollbars.bind(this), this.options.timeout);
                this.onWindowResize = lodash_debounce(this.onWindowResize.bind(this), 64, {
                    leading: true
                });
                asp_SimpleBar.getRtlHelpers = lodash_memoize(asp_SimpleBar.getRtlHelpers);
                this.init();
            }
            /**
             * Static properties
             */

            /**
             * Helper to fix browsers inconsistency on RTL:
             *  - Firefox inverts the scrollbar initial position
             *  - IE11 inverts both scrollbar position and scrolling offset
             * Directly inspired by @KingSora's OverlayScrollbars https://github.com/KingSora/OverlayScrollbars/blob/master/js/OverlayScrollbars.js#L1634
             */


            asp_SimpleBar.getRtlHelpers = function getRtlHelpers() {
                var dummyDiv = document.createElement('div');
                dummyDiv.innerHTML = '<div class="hs-dummy-scrollbar-size"><div style="height: 200%; width: 200%; margin: 10px 0;"></div></div>';
                var scrollbarDummyEl = dummyDiv.firstElementChild;
                document.body.appendChild(scrollbarDummyEl);
                var dummyContainerChild = scrollbarDummyEl.firstElementChild;
                scrollbarDummyEl.scrollLeft = 0;
                var dummyContainerOffset = asp_SimpleBar.getOffset(scrollbarDummyEl);
                var dummyContainerChildOffset = asp_SimpleBar.getOffset(dummyContainerChild);
                scrollbarDummyEl.scrollLeft = 999;
                var dummyContainerScrollOffsetAfterScroll = asp_SimpleBar.getOffset(dummyContainerChild);
                return {
                    // determines if the scrolling is responding with negative values
                    isRtlScrollingInverted: dummyContainerOffset.left !== dummyContainerChildOffset.left && dummyContainerChildOffset.left - dummyContainerScrollOffsetAfterScroll.left !== 0,
                    // determines if the origin scrollbar position is inverted or not (positioned on left or right)
                    isRtlScrollbarInverted: dummyContainerOffset.left !== dummyContainerChildOffset.left
                };
            };

            asp_SimpleBar.getOffset = function getOffset(el) {
                var rect = el.getBoundingClientRect();
                var elDocument = getElementDocument(el);
                var elWindow = getElementWindow(el);
                return {
                    top: rect.top + (elWindow.pageYOffset || elDocument.documentElement.scrollTop),
                    left: rect.left + (elWindow.pageXOffset || elDocument.documentElement.scrollLeft)
                };
            };

            var _proto = asp_SimpleBar.prototype;

            _proto.init = function init() {
                // Save a reference to the instance, so we know this DOM node has already been instancied
                asp_SimpleBar.instances.set(this.el, this);
                this.initDOM();
                this.scrollbarWidth = this.getScrollbarWidth();
                this.recalculate();
                this.initListeners();
            };

            _proto.initDOM = function initDOM() {
                var _this2 = this;

                // make sure this element doesn't have the elements yet
                if (Array.prototype.filter.call(this.el.children, function (child) {
                    return child.classList.contains(_this2.classNames.wrapper);
                }).length) {
                    // assume that element has his DOM already initiated
                    this.wrapperEl = this.el.querySelector("." + this.classNames.wrapper);
                    this.contentWrapperEl = this.options.scrollableNode || this.el.querySelector("." + this.classNames.contentWrapper);
                    this.contentEl = this.options.contentNode || this.el.querySelector("." + this.classNames.contentEl);
                    this.offsetEl = this.el.querySelector("." + this.classNames.offset);
                    this.maskEl = this.el.querySelector("." + this.classNames.mask);
                    this.placeholderEl = this.findChild(this.wrapperEl, "." + this.classNames.placeholder);
                    this.heightAutoObserverWrapperEl = this.el.querySelector("." + this.classNames.heightAutoObserverWrapperEl);
                    this.heightAutoObserverEl = this.el.querySelector("." + this.classNames.heightAutoObserverEl);
                    this.axis.x.track.el = this.findChild(this.el, "." + this.classNames.track + "." + this.classNames.horizontal);
                    this.axis.y.track.el = this.findChild(this.el, "." + this.classNames.track + "." + this.classNames.vertical);
                } else {
                    // Prepare DOM
                    this.wrapperEl = document.createElement('div');
                    this.contentWrapperEl = document.createElement('div');
                    this.offsetEl = document.createElement('div');
                    this.maskEl = document.createElement('div');
                    this.contentEl = document.createElement('div');
                    this.placeholderEl = document.createElement('div');
                    this.heightAutoObserverWrapperEl = document.createElement('div');
                    this.heightAutoObserverEl = document.createElement('div');
                    this.wrapperEl.classList.add(this.classNames.wrapper);
                    this.contentWrapperEl.classList.add(this.classNames.contentWrapper);
                    this.offsetEl.classList.add(this.classNames.offset);
                    this.maskEl.classList.add(this.classNames.mask);
                    this.contentEl.classList.add(this.classNames.contentEl);
                    this.placeholderEl.classList.add(this.classNames.placeholder);
                    this.heightAutoObserverWrapperEl.classList.add(this.classNames.heightAutoObserverWrapperEl);
                    this.heightAutoObserverEl.classList.add(this.classNames.heightAutoObserverEl);

                    while (this.el.firstChild) {
                        this.contentEl.appendChild(this.el.firstChild);
                    }

                    this.contentWrapperEl.appendChild(this.contentEl);
                    this.offsetEl.appendChild(this.contentWrapperEl);
                    this.maskEl.appendChild(this.offsetEl);
                    this.heightAutoObserverWrapperEl.appendChild(this.heightAutoObserverEl);
                    this.wrapperEl.appendChild(this.heightAutoObserverWrapperEl);
                    this.wrapperEl.appendChild(this.maskEl);
                    this.wrapperEl.appendChild(this.placeholderEl);
                    this.el.appendChild(this.wrapperEl);
                }

                if (!this.axis.x.track.el || !this.axis.y.track.el) {
                    var track = document.createElement('div');
                    var scrollbar = document.createElement('div');
                    track.classList.add(this.classNames.track);
                    scrollbar.classList.add(this.classNames.scrollbar);
                    track.appendChild(scrollbar);
                    this.axis.x.track.el = track.cloneNode(true);
                    this.axis.x.track.el.classList.add(this.classNames.horizontal);
                    this.axis.y.track.el = track.cloneNode(true);
                    this.axis.y.track.el.classList.add(this.classNames.vertical);
                    this.el.appendChild(this.axis.x.track.el);
                    this.el.appendChild(this.axis.y.track.el);
                }

                this.axis.x.scrollbar.el = this.axis.x.track.el.querySelector("." + this.classNames.scrollbar);
                this.axis.y.scrollbar.el = this.axis.y.track.el.querySelector("." + this.classNames.scrollbar);

                if (!this.options.autoHide) {
                    this.axis.x.scrollbar.el.classList.add(this.classNames.visible);
                    this.axis.y.scrollbar.el.classList.add(this.classNames.visible);
                }

                this.el.setAttribute('data-asp_simplebar', 'init');
            };

            _proto.initListeners = function initListeners() {
                var _this3 = this;

                var elWindow = getElementWindow(this.el); // Event listeners

                if (this.options.autoHide) {
                    this.el.addEventListener('mouseenter', this.onMouseEnter);
                }

                ['mousedown', 'click', 'dblclick'].forEach(function (e) {
                    _this3.el.addEventListener(e, _this3.onPointerEvent, true);
                });
                ['touchstart', 'touchend', 'touchmove'].forEach(function (e) {
                    _this3.el.addEventListener(e, _this3.onPointerEvent, {
                        capture: true,
                        passive: true
                    });
                });
                this.el.addEventListener('mousemove', this.onMouseMove);
                this.el.addEventListener('mouseleave', this.onMouseLeave);
                this.contentWrapperEl.addEventListener('scroll', this.onScroll); // Browser zoom triggers a window resize

                elWindow.addEventListener('resize', this.onWindowResize); // Hack for https://github.com/WICG/ResizeObserver/issues/38

                var resizeObserverStarted = false;
                var resizeObserver = elWindow.ResizeObserver || ResizeObserver;
                this.resizeObserver = new resizeObserver(function () {
                    if (!resizeObserverStarted) return;

                    _this3.recalculate();
                });
                this.resizeObserver.observe(this.el);
                this.resizeObserver.observe(this.contentEl);
                elWindow.requestAnimationFrame(function () {
                    resizeObserverStarted = true;
                }); // This is required to detect horizontal scroll. Vertical scroll only needs the resizeObserver.

                this.mutationObserver = new elWindow.MutationObserver(this.recalculate);
                this.mutationObserver.observe(this.contentEl, {
                    childList: true,
                    subtree: true,
                    characterData: true
                });
            };

            _proto.recalculate = function recalculate() {
                var elWindow = getElementWindow(this.el);
                this.elStyles = elWindow.getComputedStyle(this.el);
                this.isRtl = this.elStyles.direction === 'rtl';
                var isHeightAuto = this.heightAutoObserverEl.offsetHeight <= 1;
                var isWidthAuto = this.heightAutoObserverEl.offsetWidth <= 1;
                var contentElOffsetWidth = this.contentEl.offsetWidth;
                var contentWrapperElOffsetWidth = this.contentWrapperEl.offsetWidth;
                var elOverflowX = this.elStyles.overflowX;
                var elOverflowY = this.elStyles.overflowY;
                this.contentEl.style.padding = this.elStyles.paddingTop + " " + this.elStyles.paddingRight + " " + this.elStyles.paddingBottom + " " + this.elStyles.paddingLeft;
                this.wrapperEl.style.margin = "-" + this.elStyles.paddingTop + " -" + this.elStyles.paddingRight + " -" + this.elStyles.paddingBottom + " -" + this.elStyles.paddingLeft;
                var contentElScrollHeight = this.contentEl.scrollHeight;
                var contentElScrollWidth = this.contentEl.scrollWidth;
                this.contentWrapperEl.style.height = isHeightAuto ? 'auto' : '100%'; // Determine placeholder size

                this.placeholderEl.style.width = isWidthAuto ? contentElOffsetWidth + "px" : 'auto';
                this.placeholderEl.style.height = contentElScrollHeight + "px";
                var contentWrapperElOffsetHeight = this.contentWrapperEl.offsetHeight;
                this.axis.x.isOverflowing = contentElScrollWidth > contentElOffsetWidth;
                this.axis.y.isOverflowing = contentElScrollHeight > contentWrapperElOffsetHeight; // Set isOverflowing to false if user explicitely set hidden overflow

                this.axis.x.isOverflowing = elOverflowX === 'hidden' ? false : this.axis.x.isOverflowing;
                this.axis.y.isOverflowing = elOverflowY === 'hidden' ? false : this.axis.y.isOverflowing;
                this.axis.x.forceVisible = this.options.forceVisible === 'x' || this.options.forceVisible === true;
                this.axis.y.forceVisible = this.options.forceVisible === 'y' || this.options.forceVisible === true;
                this.hideNativeScrollbar(); // Set isOverflowing to false if scrollbar is not necessary (content is shorter than offset)

                var offsetForXScrollbar = this.axis.x.isOverflowing ? this.scrollbarWidth : 0;
                var offsetForYScrollbar = this.axis.y.isOverflowing ? this.scrollbarWidth : 0;
                this.axis.x.isOverflowing = this.axis.x.isOverflowing && contentElScrollWidth > contentWrapperElOffsetWidth - offsetForYScrollbar;
                this.axis.y.isOverflowing = this.axis.y.isOverflowing && contentElScrollHeight > contentWrapperElOffsetHeight - offsetForXScrollbar;
                this.axis.x.scrollbar.size = this.getScrollbarSize('x');
                this.axis.y.scrollbar.size = this.getScrollbarSize('y');
                this.axis.x.scrollbar.el.style.width = this.axis.x.scrollbar.size + "px";
                this.axis.y.scrollbar.el.style.height = this.axis.y.scrollbar.size + "px";
                this.positionScrollbar('x');
                this.positionScrollbar('y');
                this.toggleTrackVisibility('x');
                this.toggleTrackVisibility('y');
            }
            /**
             * Calculate scrollbar size
             */
            ;

            _proto.getScrollbarSize = function getScrollbarSize(axis) {
                if (axis === void 0) {
                    axis = 'y';
                }

                if (!this.axis[axis].isOverflowing) {
                    return 0;
                }

                var contentSize = this.contentEl[this.axis[axis].scrollSizeAttr];
                var trackSize = this.axis[axis].track.el[this.axis[axis].offsetSizeAttr];
                var scrollbarSize;
                var scrollbarRatio = trackSize / contentSize; // Calculate new height/position of drag handle.

                scrollbarSize = Math.max(~~(scrollbarRatio * trackSize), this.options.scrollbarMinSize);

                if (this.options.scrollbarMaxSize) {
                    scrollbarSize = Math.min(scrollbarSize, this.options.scrollbarMaxSize);
                }

                return scrollbarSize;
            };

            _proto.positionScrollbar = function positionScrollbar(axis) {
                if (axis === void 0) {
                    axis = 'y';
                }

                if (!this.axis[axis].isOverflowing) {
                    return;
                }

                var contentSize = this.contentWrapperEl[this.axis[axis].scrollSizeAttr];
                var trackSize = this.axis[axis].track.el[this.axis[axis].offsetSizeAttr];
                var hostSize = parseInt(this.elStyles[this.axis[axis].sizeAttr], 10);
                var scrollbar = this.axis[axis].scrollbar;
                var scrollOffset = this.contentWrapperEl[this.axis[axis].scrollOffsetAttr];
                scrollOffset = axis === 'x' && this.isRtl && asp_SimpleBar.getRtlHelpers().isRtlScrollingInverted ? -scrollOffset : scrollOffset;
                var scrollPourcent = scrollOffset / (contentSize - hostSize);
                var handleOffset = ~~((trackSize - scrollbar.size) * scrollPourcent);
                handleOffset = axis === 'x' && this.isRtl && asp_SimpleBar.getRtlHelpers().isRtlScrollbarInverted ? handleOffset + (trackSize - scrollbar.size) : handleOffset;
                scrollbar.el.style.transform = axis === 'x' ? "translate3d(" + handleOffset + "px, 0, 0)" : "translate3d(0, " + handleOffset + "px, 0)";
            };

            _proto.toggleTrackVisibility = function toggleTrackVisibility(axis) {
                if (axis === void 0) {
                    axis = 'y';
                }

                var track = this.axis[axis].track.el;
                var scrollbar = this.axis[axis].scrollbar.el;

                if (this.axis[axis].isOverflowing || this.axis[axis].forceVisible) {
                    track.style.visibility = 'visible';
                    this.contentWrapperEl.style[this.axis[axis].overflowAttr] = 'scroll';
                } else {
                    track.style.visibility = 'hidden';
                    this.contentWrapperEl.style[this.axis[axis].overflowAttr] = 'hidden';
                } // Even if forceVisible is enabled, scrollbar itself should be hidden


                if (this.axis[axis].isOverflowing) {
                    scrollbar.style.display = 'block';
                } else {
                    scrollbar.style.display = 'none';
                }
            };

            _proto.hideNativeScrollbar = function hideNativeScrollbar() {
                this.offsetEl.style[this.isRtl ? 'left' : 'right'] = this.axis.y.isOverflowing || this.axis.y.forceVisible ? "-" + this.scrollbarWidth + "px" : 0;
                this.offsetEl.style.bottom = this.axis.x.isOverflowing || this.axis.x.forceVisible ? "-" + this.scrollbarWidth + "px" : 0;
            }
            /**
             * On scroll event handling
             */
            ;

            _proto.onMouseMoveForAxis = function onMouseMoveForAxis(axis) {
                if (axis === void 0) {
                    axis = 'y';
                }

                this.axis[axis].track.rect = this.axis[axis].track.el.getBoundingClientRect();
                this.axis[axis].scrollbar.rect = this.axis[axis].scrollbar.el.getBoundingClientRect();
                var isWithinScrollbarBoundsX = this.isWithinBounds(this.axis[axis].scrollbar.rect);

                if (isWithinScrollbarBoundsX) {
                    this.axis[axis].scrollbar.el.classList.add(this.classNames.hover);
                } else {
                    this.axis[axis].scrollbar.el.classList.remove(this.classNames.hover);
                }

                if (this.isWithinBounds(this.axis[axis].track.rect)) {
                    this.showScrollbar(axis);
                    this.axis[axis].track.el.classList.add(this.classNames.hover);
                } else {
                    this.axis[axis].track.el.classList.remove(this.classNames.hover);
                }
            };

            _proto.onMouseLeaveForAxis = function onMouseLeaveForAxis(axis) {
                if (axis === void 0) {
                    axis = 'y';
                }

                this.axis[axis].track.el.classList.remove(this.classNames.hover);
                this.axis[axis].scrollbar.el.classList.remove(this.classNames.hover);
            };

            /**
             * Show scrollbar
             */
            _proto.showScrollbar = function showScrollbar(axis) {
                if (axis === void 0) {
                    axis = 'y';
                }

                var scrollbar = this.axis[axis].scrollbar.el;

                if (!this.axis[axis].isVisible) {
                    scrollbar.classList.add(this.classNames.visible);
                    this.axis[axis].isVisible = true;
                }

                if (this.options.autoHide) {
                    this.hideScrollbars();
                }
            }
            /**
             * Hide Scrollbar
             */
            ;

            /**
             * on scrollbar handle drag movement starts
             */
            _proto.onDragStart = function onDragStart(e, axis) {
                if (axis === void 0) {
                    axis = 'y';
                }

                var elDocument = getElementDocument(this.el);
                var elWindow = getElementWindow(this.el);
                var scrollbar = this.axis[axis].scrollbar; // Measure how far the user's mouse is from the top of the scrollbar drag handle.

                var eventOffset = axis === 'y' ? e.pageY : e.pageX;
                this.axis[axis].dragOffset = eventOffset - scrollbar.rect[this.axis[axis].offsetAttr];
                this.draggedAxis = axis;
                this.el.classList.add(this.classNames.dragging);
                elDocument.addEventListener('mousemove', this.drag, true);
                elDocument.addEventListener('mouseup', this.onEndDrag, true);

                if (this.removePreventClickId === null) {
                    elDocument.addEventListener('click', this.preventClick, true);
                    elDocument.addEventListener('dblclick', this.preventClick, true);
                } else {
                    elWindow.clearTimeout(this.removePreventClickId);
                    this.removePreventClickId = null;
                }
            }
            /**
             * Drag scrollbar handle
             */
            ;

            _proto.onTrackClick = function onTrackClick(e, axis) {
                var _this4 = this;

                if (axis === void 0) {
                    axis = 'y';
                }

                if (!this.options.clickOnTrack) return;
                var elWindow = getElementWindow(this.el);
                this.axis[axis].scrollbar.rect = this.axis[axis].scrollbar.el.getBoundingClientRect();
                var scrollbar = this.axis[axis].scrollbar;
                var scrollbarOffset = scrollbar.rect[this.axis[axis].offsetAttr];
                var hostSize = parseInt(this.elStyles[this.axis[axis].sizeAttr], 10);
                var scrolled = this.contentWrapperEl[this.axis[axis].scrollOffsetAttr];
                var t = axis === 'y' ? this.mouseY - scrollbarOffset : this.mouseX - scrollbarOffset;
                var dir = t < 0 ? -1 : 1;
                var scrollSize = dir === -1 ? scrolled - hostSize : scrolled + hostSize;

                var scrollTo = function scrollTo() {
                    if (dir === -1) {
                        if (scrolled > scrollSize) {
                            var _this4$contentWrapper;

                            scrolled -= _this4.options.clickOnTrackSpeed;

                            _this4.contentWrapperEl.scrollTo((_this4$contentWrapper = {}, _this4$contentWrapper[_this4.axis[axis].offsetAttr] = scrolled, _this4$contentWrapper));

                            elWindow.requestAnimationFrame(scrollTo);
                        }
                    } else {
                        if (scrolled < scrollSize) {
                            var _this4$contentWrapper2;

                            scrolled += _this4.options.clickOnTrackSpeed;

                            _this4.contentWrapperEl.scrollTo((_this4$contentWrapper2 = {}, _this4$contentWrapper2[_this4.axis[axis].offsetAttr] = scrolled, _this4$contentWrapper2));

                            elWindow.requestAnimationFrame(scrollTo);
                        }
                    }
                };

                scrollTo();
            }
            /**
             * Getter for content element
             */
            ;

            _proto.getContentElement = function getContentElement() {
                return this.contentEl;
            }
            /**
             * Getter for original scrolling element
             */
            ;

            _proto.getScrollElement = function getScrollElement() {
                return this.contentWrapperEl;
            };

            _proto.getScrollbarWidth = function getScrollbarWidth() {
                // Try/catch for FF 56 throwing on undefined computedStyles
                try {
                    // Detect browsers supporting CSS scrollbar styling and do not calculate
                    if (getComputedStyle(this.contentWrapperEl, '::-webkit-scrollbar').display === 'none' || 'scrollbarWidth' in document.documentElement.style || '-ms-overflow-style' in document.documentElement.style) {
                        return 0;
                    } else {
                        return scrollbarWidth(this.el);
                    }
                } catch (e) {
                    return scrollbarWidth(this.el);
                }
            };

            _proto.removeListeners = function removeListeners() {
                var _this5 = this;

                var elWindow = getElementWindow(this.el); // Event listeners

                if (this.options.autoHide) {
                    this.el.removeEventListener('mouseenter', this.onMouseEnter);
                }

                ['mousedown', 'click', 'dblclick'].forEach(function (e) {
                    _this5.el.removeEventListener(e, _this5.onPointerEvent, true);
                });
                ['touchstart', 'touchend', 'touchmove'].forEach(function (e) {
                    _this5.el.removeEventListener(e, _this5.onPointerEvent, {
                        capture: true,
                        passive: true
                    });
                });
                this.el.removeEventListener('mousemove', this.onMouseMove);
                this.el.removeEventListener('mouseleave', this.onMouseLeave);

                if (this.contentWrapperEl) {
                    this.contentWrapperEl.removeEventListener('scroll', this.onScroll);
                }

                elWindow.removeEventListener('resize', this.onWindowResize);

                if (this.mutationObserver) {
                    this.mutationObserver.disconnect();
                }

                if (this.resizeObserver) {
                    this.resizeObserver.disconnect();
                } // Cancel all debounced functions


                this.recalculate.cancel();
                this.onMouseMove.cancel();
                this.hideScrollbars.cancel();
                this.onWindowResize.cancel();
            }
            /**
             * UnMount mutation observer and delete asp_SimpleBar instance from DOM element
             */
            ;

            _proto.unMount = function unMount() {
                this.removeListeners();
                asp_SimpleBar.instances.delete(this.el);
            }
            /**
             * Check if mouse is within bounds
             */
            ;

            _proto.isWithinBounds = function isWithinBounds(bbox) {
                return this.mouseX >= bbox.left && this.mouseX <= bbox.left + bbox.width && this.mouseY >= bbox.top && this.mouseY <= bbox.top + bbox.height;
            }
            /**
             * Find element children matches query
             */
            ;

            _proto.findChild = function findChild(el, query) {
                var matches = el.matches || el.webkitMatchesSelector || el.mozMatchesSelector || el.msMatchesSelector;
                return Array.prototype.filter.call(el.children, function (child) {
                    return matches.call(child, query);
                })[0];
            };

            return asp_SimpleBar;
        }();

    asp_SimpleBar.defaultOptions = {
        autoHide: true,
        forceVisible: false,
        clickOnTrack: true,
        clickOnTrackSpeed: 40,
        classNames: {
            contentEl: 'asp_simplebar-content',
            contentWrapper: 'asp_simplebar-content-wrapper',
            offset: 'asp_simplebar-offset',
            mask: 'asp_simplebar-mask',
            wrapper: 'asp_simplebar-wrapper',
            placeholder: 'asp_simplebar-placeholder',
            scrollbar: 'asp_simplebar-scrollbar',
            track: 'asp_simplebar-track',
            heightAutoObserverWrapperEl: 'asp_simplebar-height-auto-observer-wrapper',
            heightAutoObserverEl: 'asp_simplebar-height-auto-observer',
            visible: 'asp_simplebar-visible',
            horizontal: 'asp_simplebar-horizontal',
            vertical: 'asp_simplebar-vertical',
            hover: 'asp_simplebar-hover',
            dragging: 'asp_simplebar-dragging'
        },
        scrollbarMinSize: 25,
        scrollbarMaxSize: 0,
        timeout: 1000
    };
    asp_SimpleBar.instances = new WeakMap();

    asp_SimpleBar.initDOMLoadedElements = function () {
        document.removeEventListener('DOMContentLoaded', this.initDOMLoadedElements);
        window.removeEventListener('load', this.initDOMLoadedElements);
        Array.prototype.forEach.call(document.querySelectorAll('[data-asp_simplebar]'), function (el) {
            if (el.getAttribute('data-asp_simplebar') !== 'init' && !asp_SimpleBar.instances.has(el)) new asp_SimpleBar(el, getOptions(el.attributes));
        });
    };

    asp_SimpleBar.removeObserver = function () {
        this.globalObserver.disconnect();
    };

    asp_SimpleBar.initHtmlApi = function () {
        this.initDOMLoadedElements = this.initDOMLoadedElements.bind(this); // MutationObserver is IE11+

        if (typeof MutationObserver !== 'undefined') {
            // Mutation observer to observe dynamically added elements
            this.globalObserver = new MutationObserver(asp_SimpleBar.handleMutations);
            this.globalObserver.observe(document, {
                childList: true,
                subtree: true
            });
        } // Taken from jQuery `ready` function
        // Instantiate elements already present on the page


        if (document.readyState === 'complete' || document.readyState !== 'loading' && !document.documentElement.doScroll) {
            // Handle it asynchronously to allow scripts the opportunity to delay init
            window.setTimeout(this.initDOMLoadedElements);
        } else {
            document.addEventListener('DOMContentLoaded', this.initDOMLoadedElements);
            window.addEventListener('load', this.initDOMLoadedElements);
        }
    };

    asp_SimpleBar.handleMutations = function (mutations) {
        mutations.forEach(function (mutation) {
            Array.prototype.forEach.call(mutation.addedNodes, function (addedNode) {
                if (addedNode.nodeType === 1) {
                    if (addedNode.hasAttribute('data-asp_simplebar')) {
                        !asp_SimpleBar.instances.has(addedNode) && document.documentElement.contains(addedNode) && new asp_SimpleBar(addedNode, getOptions(addedNode.attributes));
                    } else {
                        Array.prototype.forEach.call(addedNode.querySelectorAll('[data-asp_simplebar]'), function (el) {
                            if (el.getAttribute('data-asp_simplebar') !== 'init' && !asp_SimpleBar.instances.has(el) && document.documentElement.contains(el)) new asp_SimpleBar(el, getOptions(el.attributes));
                        });
                    }
                }
            });
            Array.prototype.forEach.call(mutation.removedNodes, function (removedNode) {
                if (removedNode.nodeType === 1) {
                    if (removedNode.getAttribute('data-asp_simplebar') === 'init') {
                        asp_SimpleBar.instances.has(removedNode) && !document.documentElement.contains(removedNode) && asp_SimpleBar.instances.get(removedNode).unMount();
                    } else {
                        Array.prototype.forEach.call(removedNode.querySelectorAll('[data-asp_simplebar="init"]'), function (el) {
                            asp_SimpleBar.instances.has(el) && !document.documentElement.contains(el) && asp_SimpleBar.instances.get(el).unMount();
                        });
                    }
                }
            });
        });
    };

    asp_SimpleBar.getOptions = getOptions;
    /**
     * HTML API
     * Called only in a browser env.
     */

    asp_SimpleBar.initHtmlApi();

    return asp_SimpleBar;

}));
})(asljQuery, asljQuery, window);