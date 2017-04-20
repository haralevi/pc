import {ajax} from "./ajax";

/* waitForFinalEvent - is used for delayed window resize event */
const waitForFinalEvent = (function () {
    let timers = {};
    return function (callback, ms, uniqueId) {
        if (!uniqueId) {
            uniqueId = "Don't call this twice without a uniqueId";
        }
        if (timers[uniqueId]) {
            clearTimeout(timers[uniqueId]);
        }
        timers[uniqueId] = setTimeout(callback, ms);
    };
})();

/* domain */
let domain = document.domain;
let dotPos = domain.lastIndexOf(".");
let domainEnd = domain.substr(dotPos + 1);
let SiteDom = domain.substr(0, dotPos);
if (SiteDom.lastIndexOf(".") != -1)
    SiteDom = SiteDom.substr(SiteDom.lastIndexOf(".") + 1);

/* error logger */
window.onerror = function (message, file, line, column) {
    try {
        if (parseInt(line) > 1 && file.indexOf("uptolike") == -1 && file.indexOf("bot") == -1 && file.indexOf("yandex") == -1 && file.indexOf("leechlink") == -1 && file != "") {
            let id_auth_log = id_auth_log || -1;
            let jserror = "<i>" + (new Date()).toString() + "</i><br><a href=\"" + window.location.href + "\" target=\"_blank\">" + window.location.href + "</a><br><b>id_auth</b>: " + id_auth_log + "<br>" + navigator.userAgent + "<br>" + message + "<br><b>file</b>: " + file + "<b>line</b>: " + line + "<b>column</b>: " + column;
            $.get(ajax.ajaxFld + '/JsErrorHandler.php?jserror=' + encodeURIComponent(jserror));
        }
    } catch (e) {
    }
};

/* cookie class */
let expireDate = new Date;
expireDate.setTime(expireDate.getTime() + (30 * 24 * 60 * 60 * 1000));
expireDate = expireDate.toUTCString();
const cookie = {
    setCookie: function (a, b) {
        document.cookie = a + "=" + b + ";expires=" + expireDate + ";path=/;domain=." + SiteDom + "." + domainEnd
    }, getCookie: function (a) {
        a = new RegExp(a + "=[^;]+", "i");
        return document.cookie.match(a) ? document.cookie.match(a)[0].split("=")[1] : null
    }
};
const lang = cookie.getCookie("lang");

/* jQuery ajax cache */
const localCache = {
    timeout: 10000, data: {}, remove: function (a) {
        delete localCache.data[a]
    }, exist: function (a) {
        return !!localCache.data[a] && (new Date).getTime() - localCache.data[a]._ < localCache.timeout
    }, get: function (a) {
        return localCache.data[a].data
    }, set: function (a, b, c) {
        localCache.remove(a);
        localCache.data[a] = {_: (new Date).getTime(), data: b};
        $.isFunction(c) && c(b)
    }
};

$.ajaxPrefilter(function (options, originalOptions) {
    if (options.cache) {
        let complete = originalOptions.complete || $.noop,
            url = originalOptions.url;
        //remove jQuery cache as we have our own localCache
        options.cache = false;
        options.beforeSend = function () {
            if (localCache.exist(url)) {
                complete(localCache.get(url));
                return false;
            }
            return true;
        };
        options.complete = function (data) {
            localCache.set(url, data, complete);
        };
    }
});

/* utils class */
const utils = {
    info: function (o) {
        if (typeof console != "undefined") console.info(o);
    },
    dir: function (o) {
        if (typeof console != "undefined") console.dir(o);
    },
    table: function (o) {
        if (typeof console != "undefined" && typeof console.table != "undefined") console.table(o);
    },
    trim: function (str) {
        let res = str.replace(/^ */, '');
        return (res.replace(/ *$/, ''));
    },
    floor: function (num, decimals) {
        return Math.floor(num * Math.pow(10, decimals)) / Math.pow(10, decimals);
    },
    winW: function () {
        return $(window).width();
    },
    winH: function () {
        return $(window).height();
    },
    isMobile: function () {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    },
    isTouch: function () {
        return (('ontouchstart' in window) || window.DocumentTouch && document instanceof DocumentTouch)
    },
    getURLParam: function (param, url) {
        return decodeURIComponent((url.match(new RegExp("[?|&]" + param + "=(.+?)(&|$)")) || [, null])[1]);
    },
    endsWith: function (str, suffix) {
        return str.indexOf(suffix, str.length - suffix.length) !== -1;
    },
    isIE: function () {
        return (/MSIE (\d+\.\d+);/.test(navigator.userAgent));
    },
    getIEVer: function () {
        let ieversion = 9;
        if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) ieversion = Number(RegExp.$1);
        return ieversion;
    },
    getBrowserLang: function () {
        let browserLang = 'ru';
        if (typeof navigator.language != 'undefined') browserLang = navigator.language;
        else if (typeof navigator.browserLanguage != 'undefined') browserLang = navigator.browserLanguage;
        else if (typeof navigator.systemLanguage != 'undefined') browserLang = navigator.systemLanguage;
        else if (typeof navigator.userLanguage != 'undefined') browserLang = navigator.userLanguage;
        return browserLang;
    },
    isIE7: function () {
        return !!(utils.isIE() && utils.getIEVer() <= 7);
    },
    isIE8: function () {
        return !!(utils.isIE() && utils.getIEVer() == 8);
    },
    rotateVal: function (el, type) {
        if (!el.length || !(type == "int" || type == "float")) return;
        let origV = el.text();
        if (parseFloat(origV) <= 0) return;
        let maxV = (type == "int") ? parseInt(origV) : parseFloat(origV);
        let step = 1;
        if (type == "int" && maxV > 10) step = maxV / 10;
        if (type == "float" && maxV > 0.1) step = maxV / 10;
        let curV = 0;
        let intId = setInterval(function () {
            if (curV < maxV) {
                (type == "int") ? el.text(parseInt(curV)) : el.text(utils.floor(curV, 2).toFixed(2));
                curV += step;
            } else {
                el.text(origV).addClass("animated bounceIn");
                clearInterval(intId);
            }
        }, 100);
    },
    postForm: function (path, params, method) {
        method = method || "post"; // Set method to post by default if not specified.
        let form = document.createElement("form");
        form.setAttribute("method", method);
        form.setAttribute("action", path);
        for(let key in params) {
            if(params.hasOwnProperty(key)) {
                let hiddenField = document.createElement("input");
                hiddenField.setAttribute("type", "hidden");
                hiddenField.setAttribute("name", key);
                hiddenField.setAttribute("value", params[key]);
                form.appendChild(hiddenField);
            }
        }
        document.body.appendChild(form);
        form.submit();
    },
    handleHistory: function (links, ajaxFld) {
        const appRoot = location.pathname.substr(0, location.pathname.lastIndexOf("/"));
        if ((history.pushState && history.replaceState)) {
            // click listener for ajax calls
            $(document).on("click", links, function (e) {
                if ($(this)[0].id !== "undefined") {
                    if ($(this)[0].id == "prevLnkKey")
                        cookie.setCookie("nav_dir", "prev");
                    else if ($(this)[0].id == "nextLnkKey")
                        cookie.setCookie("nav_dir", "next");
                }
                let href = this.href;
                if (href != location.href) {
                    href = href.substr(href.lastIndexOf("/"));
                    window.history.pushState(null, "", appRoot + href);
                    ajax.handelAjax(appRoot + ajaxFld + href);
                }
                e.preventDefault();
            });
            // handle browser back-forward buttons
            let popped = ('state' in window.history);
            let initialURL = location.href;
            $(window).bind("popstate", function () {
                let initialPop = !popped && location.href == initialURL;
                popped = true;
                if (initialPop) return; // prevent from firing on first page load
                let href = location.href;
                href = href.substr(href.lastIndexOf("/"));
                ajax.handelAjax(appRoot + ajaxFld + href);
            });
        }
    },

    stopRightClick: function (el) {
        if(domainEnd != 'ru' && domainEnd != 'by') {
            el.bind('mousedown', function (e) {
                (e.stopPropagation? e.stopPropagation() : (e.preventDefault ? e.preventDefault() : (e.returnValue=false)));
                return false;
            });
            el.bind("contextmenu", function(e) {
                (e.stopPropagation? e.stopPropagation() : (e.preventDefault ? e.preventDefault() : (e.returnValue=false)));
                return false;
            });
        }
    },
};

/* localizer class */
const minCommLen = 5, maxCommLen = 30000;
let ploc = {
    by: {
        already_rec_note_loc: 'Спасибо, ваша<br />рекомендация принята',
        be_constructive_loc: 'Текст сообщения должен содержать хотя бы ' + minCommLen + ' букв',
        max_comm_len_loc: 'Сообщение должно содержать не более ' + maxCommLen + ' символов',
        really_do_home_album_loc: 'Вы действительно считаете, что эта работа\nне должна присутствовать в галерее?',
        loc: ''
    },
    en: {
        already_rec_note_loc: 'Thanks, your<br />recommendation is received',
        be_constructive_loc: 'Please write some words',
        max_comm_len_loc: 'Max ' + maxCommLen + ' characters',
        really_do_home_album_loc: 'Do you really want that this work will be deleted from the gallery?',
        loc: ''
    },
    de: {
        already_rec_note_loc: 'Danke, Ihre Empfehlung<br />wurde gezählt',
        be_constructive_loc: 'Ihre Anmerkung soll mindestens ' + minCommLen + ' Buchstaben enthalten',
        max_comm_len_loc: 'Eine Anmerkung darf bis zu ' + maxCommLen + ' Zeichen enthalten',
        really_do_home_album_loc: 'Möchten Sie wirklich, dass dieses Bild von der Bildergalerie entfernt wird?',
        loc: ''
    },
    ru: {
        already_rec_note_loc: 'Рекомендация<br />принята',
        be_constructive_loc: 'Текст сообщения должен содержать хотя бы ' + minCommLen + ' букв',
        max_comm_len_loc: 'Сообщение должно содержать не более ' + maxCommLen + ' символов',
        really_do_home_album_loc: 'Вы действительно считаете, что эта работа\nне должна присутствовать в галерее?',
        loc: ''
    }
};
if (lang == 'by') ploc = ploc.by;
else if (lang == 'en') ploc = ploc.en;
else if (lang == 'de') ploc = ploc.de;
else ploc = ploc.ru;

export {waitForFinalEvent, cookie, lang, localCache, ploc, utils};