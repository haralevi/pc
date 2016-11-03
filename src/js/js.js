/* domain vars */
var domain=document.domain,dotPos=domain.lastIndexOf("."),domainEnd=domain.substr(dotPos+1),SiteDom=domain.substr(0,dotPos);-1!=SiteDom.lastIndexOf(".")&&(SiteDom=SiteDom.substr(SiteDom.lastIndexOf(".")+1));var expireDate=new Date;expireDate.setTime(expireDate.getTime()+2592E6);

/* error logger */
window.onerror = function (message, file, line, column) {
try{if(line!==1 && file.indexOf("uptolike")==-1 && file.indexOf("bot")==-1 && file.indexOf("yandex")==-1 && file.indexOf("leechlink")==-1 && file!=""){
if(typeof id_auth_log === "undefined") id_auth_log = -1;
var jserror = "<i>" + (new Date()).toString() + "</i><br><a href=\"" + window.location.href + "\" target=\"_blank\">" + window.location.href + "</a><br><b>id_auth</b>: " + id_auth_log + "<br>" + navigator.userAgent + "<br>" + message + "<br><b>file</b>: " + file + " <b>line</b>: " + line + " <b>column</b>: " + column;
$.get('/classes/JsErrorHandler.php?jserror=' + encodeURIComponent(jserror));
}}catch(e){}};

/* cookie class */
var cookie={setCookie:function(a,b){document.cookie=a+"="+b+";expires="+expireDate.toGMTString()+";path=/;domain=."+SiteDom+"."+domainEnd},getCookie:function(a){a=new RegExp(a+"=[^;]+","i");return document.cookie.match(a)?document.cookie.match(a)[0].split("=")[1]:null}};

/* jQuery ajax cache */
var localCache={timeout:6E4,data:{},remove:function(a){delete localCache.data[a]},exist:function(a){return!!localCache.data[a]&&(new Date).getTime()-localCache.data[a]._<localCache.timeout},get:function(a){return localCache.data[a].data},set:function(a,b,c){localCache.remove(a);localCache.data[a]={_:(new Date).getTime(),data:b};$.isFunction(c)&&c(b)}};$.ajaxPrefilter(function(a,b,c){if(a.cache){var e=b.complete||$.noop,d=b.url;a.cache=!1;a.beforeSend=function(){return localCache.exist(d)?(e(localCache.get(d)),!1):!0};a.complete=function(a,b){localCache.set(d,a,e)}}});

/* utils class */
var utils = {
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
    var res = str.replace(/^ */, '');
    return (res.replace(/ *$/, ''));
},
floor: function (num, decimals) {
    return Math.floor(num * Math.pow(10, decimals)) / Math.pow(10, decimals);
},
winW: function () {
    return $(window).width();
}, winH: function () {
    return $(window).height();
},
isMobile: function () {
    return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
},
isTouch: function () {
    return 'ontouchstart' in document.documentElement;
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
    var ieversion = 9;
    if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) ieversion = Number(RegExp.$1);
    return ieversion;
},
getBrowserLang: function () {
    if (typeof navigator.language != 'undefined') lang = navigator.language;
    else if (typeof navigator.browserLanguage != 'undefined') lang = navigator.browserLanguage;
    else if (typeof navigator.systemLanguage != 'undefined') lang = navigator.systemLanguage;
    else if (typeof navigator.userLanguage != 'undefined') lang = navigator.userLanguage;
    else lang = 'ru';
    return lang;
},
isIE7: function () {
    return (utils.isIE() && utils.getIEVer() <= 7) ? true : false;
}, isIE8: function () {
    return (utils.isIE() && utils.getIEVer() == 8) ? true : false;
},
rotateVal: function (el, type) {
    if (!el.length || !(type == "int" || type == "float")) return;
    var origV = el.text();
    if (parseFloat(origV) <= 0) return;
    var maxV = (type == "int") ? parseInt(origV) : parseFloat(origV);
    var step = 1;
    if (type == "int" && maxV > 10) step = maxV / 10;
    if (type == "float" && maxV > 0.1) step = maxV / 10;
    var curV = 0;
    var intId = setInterval(function () {
        if (curV < maxV) {
            (type == "int") ? el.text(parseInt(curV)) : el.text(utils.floor(curV, 2).toFixed(2));
            curV += step;
        } else {
            el.text(origV).addClass("animated bounceIn");
            clearInterval(intId);
        }
    }, 100);
},
handleHistory: function (links, ajaxFld) {
    var appRoot = location.pathname.substr(0, location.pathname.lastIndexOf("/"));
    if ((history.pushState && history.replaceState)) {
        // click listener for ajax calls
        $(document).on("click", links, function (e) {
            var href = this.href;
            if (href != location.href) {
                href = href.substr(href.lastIndexOf("/"));
                window.history.pushState(null, null, appRoot + href);
                ajax.handelAjax(appRoot + ajaxFld + href);
            }
            e.preventDefault();
        });
        // handle browser back-forward buttons
        var popped = ('state' in window.history);
        var initialURL = location.href;
        $(window).bind("popstate", function () {
            var initialPop = !popped && location.href == initialURL;
            popped = true;
            if (initialPop) return; // prevent from firing on first page load
            var href = location.href;
            href = href.substr(href.lastIndexOf("/"));
            ajax.handelAjax(appRoot + ajaxFld + href);
        });
    }
}
};

/* localizer class */
var minCommLen = 5, maxCommLen = 30000;
var ploc = {
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
var $lang = cookie.getCookie("lang");
if ($lang == 'by') ploc = ploc.by;
else if ($lang == 'en') ploc = ploc.en;
else if ($lang == 'de') ploc = ploc.de;
else ploc = ploc.ru;

var ajax = {
    ajaxFld: "/ajax",
    loader: '<img src="/css/black/' + 'loader_gray.new.gif' + '" alt="">',

    handelAjax: function (url) {
        $("html, body").animate({scrollTop: 0}, 300);
        $("#submenu").slideUp();
        app.removeCrop();

        var $ajaxBody = $("#ajaxBody");
        var pos = $ajaxBody.offset();
        var size = {w: $ajaxBody.width(), h: $ajaxBody.height()};
        if (!$("#ajax_overlay").length) {
            $ajaxBody.css({height: size.h + "px"});
            $ajaxBody.removeClass("animated fadeIn");
            $('<div id="ajax_overlay"></div>').css({
                left: pos.left + "px",
                top: pos.top + "px",
                width: size.w + "px",
                height: size.h + "px"
            }).appendTo("body");
            setTimeout(function () {
                $("#ajax_overlay").addClass("loader");
            }, 500);
        }

        // check if data from url is in cache
        var isFormCache = false;
        if (localCache.exist(url))
            isFormCache = true;

        $.ajax({url: url, cache: true, complete: updateAjaxHtml});

        function updateAjaxHtml(data) {
            data.responseJSON = data.responseJSON || false;

            if (data.responseJSON && !jQuery.isEmptyObject(data.responseJSON)) {
                data = data.responseJSON;
                data.ajaxBody = data.ajaxBody || "";
                data.title = data.title || "";
                data.debug = data.debug || "";
                data.canonicalUrl = data.canonicalUrl || "";
                data.hrefPrev = data.hrefPrev.replace(/&amp;/g, "&") || "";
                data.hrefNext = data.hrefNext.replace(/&amp;/g, "&") || "";
                $ajaxBody.html(data.ajaxBody);
                if (!app.isFirstClick) // needed only for ipad, iphone
                    $ajaxBody.addClass("animated fadeIn");
                app.isFirstClick = false;
                if (data.title != "") document.title = data.title;
                $("link[rel='canonical']").attr("href", data.canonicalUrl);
                $("#canonicalUrl").attr("href", data.canonicalUrl);
                $(".prevLnk").attr("href", data.hrefPrev);
                $(".nextLnk").attr("href", data.hrefNext);
                $("#debug").html(data.debug);

                // update photo
                app.updateMainImg();

                // update photo views
                if (!isFormCache)
                    ajax.getViews();

                // track page, google analytics
                if (typeof ga === "function") ga("send", "pageview", {
                    "page": url.replace(/\/ajax/, ''),
                    "title": document.title
                });
            }
            $("#ajax_overlay").remove();
            $ajaxBody.waitForImages(function () {
                $ajaxBody.css({height: "auto"});
            });
            app.isInZoom = false;
        }
    },

    postComm: function () {
        var id = $("#mainImage").data("idPhoto");
        var $comment = $("#commText");
        var comm_text = utils.trim($comment.val());
        if (comm_text.length < minCommLen) {
            alert(ploc.be_constructive_loc);
            $comment.focus();
        }
        else if (comm_text.length > 30000) {
            alert(ploc.max_comm_len_loc);
            $comment.focus();
        }
        else {
            var $loaderComm = $("#loaderComm");
            $loaderComm.show();
            //noinspection JSUnresolvedFunction
            $.post("/ajax/post_comm.php", {
                    id: id,
                    comm_text: comm_text,
                    type: "work",
                    id_auth_answer: 0,
                    id_comm_parent: 0
                },
                function (data) {
                    data.cnt = data.cnt || 1;
                    var $submenu = $("#submenu");
                    $("#commTbl").find("tbody").append('<tr><td class="commAuth"><a href="' + $submenu.data("authUrl") + '"><img src="' + $submenu.data("authAvatar") + '"></a></td>' +
                        '<td class="commTextTd"><div class="commText"><a href="' + $submenu.data("authUrl") + '">' + $submenu.data("authName") + '</a><br>' + comm_text + '</div></td></tr>');
                    $comment.val("");
                    $loaderComm.hide();
                }, "json");
        }
    },

    getRecs: function () {
        var id = $("#mainImage").data("idPhoto");
        $("#addRecAjax").html('<span class="recNote">' + ploc.already_rec_note_loc + '</span>');
        $("#homeAlbumAjax").hide();
        var $phRating = $("#phRating");
        var $oldRate = $phRating.text();
        $phRating.text();
        var isUp = false;
        setTimeout(function () {
            if (!isUp) $phRating.html(ajax.loader);
        }, 500);
        $.get("/ajax/get_recs.php", {id_photo: id}, function (data) {
            isUp = true;
            $phRating.text(utils.floor((parseFloat($oldRate) + parseFloat(data)), 2)).addClass("animated bounceIn");
        })
    },

    getFineart: function () {
        var id = $("#mainImage").data("idPhoto");
        var fineart = $("#fineartBtn").data("fineart");
        $("#fineartAjax").text('');
        $.get("/ajax/get_fineart.php", {id: id, fineart: fineart}, function (data) {
        })
    },

    getHomeAlbum: function () {
        var id = $("#mainImage").data("idPhoto");
        $("#homeAlbumAjax").hide();
        $.get("/ajax/get_home_album.php", {id_photo: id, is_vote: 1}, function (data) {
        })
    },

    getViews: function () {
        var $mainImage = $("#mainImage");
        if ($mainImage.length)
            $.get("/ajax/get_views.php", {
                id: $mainImage.data("idPhoto")
            }, function (data) {
            });
    },

    getFollow: function (act) {
        var id = $("#ajaxHeader").data("idAuthPhoto");
        var $followBtnBlk = $("#followBtnBlk");
        var isUp = false;
        setTimeout(function () {
            if (!isUp) $followBtnBlk.html(ajax.loader);
        }, 500);
        $.get("/ajax/get_follow.php", {id: id, act: act, type: "auth"}, function (data) {
            isUp = true;
            $followBtnBlk.html(data + "<br>&nbsp;");
        });
    }
};

var app = {
    winW: utils.winW(), winH: utils.winH(),
    isCrop: false, cropX: 0, cropY: 0, cropW: 0, cropH: 0,
    viewedNudes: [],
    isFirstClick: true,
    isInZoom: false,
    clickDelay: 200,
    clicksCnt: 0,
    clickTimer: 0,

    toggleCrop: function () {
        app.resetImgZoom();
        if (app.cropW > 0 && app.cropH > 0) {
            if (app.isCrop) {
                $(".crop_overlay_dark").remove();
                $(".crop_overlay_light").remove();
            }
            else {
                $("html, body").animate({scrollTop: 0}, 300);
                var $mainImage = $("#mainImage");
                if ($mainImage.attr("class") == "nudePreview") {
                    app.showNude();
                    app.isCrop = !app.isCrop;
                }
                else {
                    var pos = $mainImage.offset();
                    var size = {w: $mainImage.width(), h: $mainImage.height()};
                    var imgRatio = (size.w / parseInt($mainImage.data("phMainW")));
                    $('<div class="crop_overlay_dark"></div>').css({
                        left: pos.left + "px",
                        top: pos.top + "px",
                        width: +size.w + "px",
                        height: size.h + "px"
                    }).appendTo("body");
                    $('<div class="crop_overlay_light" style="background: transparent url(' + $mainImage.attr("src") + ') no-repeat -' + Math.round(app.cropX * imgRatio) + 'px -' + Math.round(app.cropY * imgRatio) + 'px;"></div>')
                        .css({
                            left: (pos.left + Math.round(app.cropX * imgRatio)) + "px",
                            top: (pos.top + Math.round(app.cropY * imgRatio)) + "px",
                            width: +Math.round(app.cropW * imgRatio) + "px",
                            height: Math.round(app.cropH * imgRatio) + "px"
                        }).appendTo("body");
                }
            }
            app.isCrop = !app.isCrop;
        }
    },

    removeCrop: function () {
        $(".crop_overlay_dark").remove();
        $(".crop_overlay_light").remove();
        app.isCrop = false;
    },

    showNude: function () {
        var $mainImage = $("#mainImage");
        if (!$mainImage.length) return false;
        $mainImage.removeClass("nudePreview").addClass("animated fadeIn");
        if(typeof $mainImage.data("isAllowedNude") !== "undefined")
            $mainImage.addClass("blur");
        $mainImage.attr("src", $mainImage.data("phPath") + $mainImage.data("idPhoto") + "_mobile.jpg").css({width: "auto", height: "auto"});
        app.updateMainImg();
        app.viewedNudes.push($mainImage.data("idPhoto"));
        return true;
    },

    updateMainImg: function () {
        var $mainImage = $("#mainImage");
        if (!$mainImage.length) return;
        if ($mainImage.attr("class") == "nudePreview") {
            for (var i = 0; i < app.viewedNudes.length; i++) {
                if ($mainImage.data("idPhoto") == app.viewedNudes[i]) {
                    $mainImage.removeClass("nudePreview");
                    if(typeof $mainImage.data("isAllowedNude") !== "undefined")
                        $mainImage.addClass("blur");
                    $mainImage.attr("src", $mainImage.data("phPath") + $mainImage.data("idPhoto") + "_mobile.jpg").css({
                        width: "auto",
                        height: "auto"
                    });
                    break;
                }
            }
        }
        var imgSrc = $mainImage.attr("src");

        var srcReplace = 'main';
        if(typeof $mainImage.data("isAllowedNude") !== "undefined")
            srcReplace = 'council';

        if (app.winW >= 420) {
            var $mobileImage = $("#mobileImage");
            $mobileImage.attr("src", imgSrc.replace(/mobile/g, srcReplace));
            $mobileImage.waitForImages(function () {
                $mainImage.attr("src", imgSrc.replace(/mobile/g, srcReplace));
            });
        }
        else {
            $mainImage.attr("src", imgSrc.replace(new RegExp(srcReplace, 'g'), 'mobile'));
        }

        // resize image according to app width
        var $mainImageW = $mainImage.data("phMainW");
        var $mainImageH = $mainImage.data("phMainH");
        if ($mainImageW > app.winW) {
            $mainImageH = ($mainImageH / $mainImageW) * app.winW;
            $mainImageW = app.winW;
        }
        $mainImage.css({width: $mainImageW + "px", height: $mainImageH + "px"});

        app.resetImgZoom();
        app.fixCommText();
    },

    resetImgZoom: function () {
        if (!app.isInZoom)
            return true;
        var $mainImage = $("#mainImage");
        //if (app.winW < 420) $mainImage.attr("src", $mainImage.attr("src").replace(/main/g, 'mobile'));
        app.isInZoom = false;
        $mainImage.panzoom("reset", {duration: 500});
        setTimeout(function () {
            $mainImage.panzoom("destroy");
        }, 500);
    },

    handleImgZoom: function () {
        if (!utils.isTouch() || app.isCrop)
            return true;
        if (!app.isInZoom) {
            var $mainImage = $("#mainImage");
            if (app.winW < 420)
                $mainImage.attr("src", $mainImage.attr("src").replace(/mobile/g, 'main'));
            var zoom = $mainImage.data("phMainW") / $mainImage.width();
            if (zoom <= 1)
                return true;
            app.isInZoom = true;
            $mainImage.panzoom({
                minScale: zoom,
                maxScale: zoom,
                duration: 500,
                contain: 'invert'
            });
            $mainImage.panzoom("zoom");
        } else
            app.resetImgZoom();
    },

    submitLogin: function () {
        var $loginVal = utils.trim($("#auth_login").val());
        var $passVal = utils.trim($("#auth_pass").val());
        if ($loginVal != "" && $passVal != "") {
            var href = location.href.replace(/&wrn_login=1/, "");
            if (window.location.search == "" && !utils.endsWith(href, "?")) href += "?";
            href += "&auth_login=" + encodeURI($loginVal) + "&auth_pass=" + encodeURI($passVal);
            location.href = href;
        }
    },

    emoticon: function (id) {
        var $commText = $("#commText");
        var v = $commText.val();
        if (utils.trim(v)) v += "\n";
        v += "[b]" + $("#authName" + id).text() + "[/b], ";
        $commText.focus().val("").val(v).css({height: "100px"});
    },

    fixCommText: function () {
        if (app.winW < 400)
            $(".commText").css({"width": (app.winW - 60) + "px"});
    },

    fixLayout: function () {
        app.winW = utils.winW();
        app.winH = utils.winH();
        utils.info("winW: " + app.winW + ", winH: " + app.winH);

        app.updateMainImg();
        app.removeCrop();
    }
};

$(function () {
    // handle ajax navigation
    utils.handleHistory('a[rel="prev"], a[rel="next"], a[rel="author"]', ajax.ajaxFld);

    // fix layout after window resize
    $(window).resize(function () {
        waitForFinalEvent(function () {
            app.fixLayout();
        }, 500, "delayed_resize");
    });

    $(document)
    // submit login form
        .on("click", "#loginBtn", function (e) {
            app.submitLogin();
            e.preventDefault();
        })
        // show-hide submenu
        .on("click", "#menuLnk", function () {
            app.removeCrop();
            $("#submenu").slideToggle(200, function () {
            });
        })
        // remove crop
        .on("click", ".crop_overlay_dark, .crop_overlay_light", function () {
            app.removeCrop();
        })
        // add recommendation
        .on("click", "#addRecBtn", function (e) {
            ajax.getRecs();
            e.preventDefault();
        })
        // add fineart
        .on("click", "#fineartBtn", function (e) {
            ajax.getFineart();
            e.preventDefault();
        })
        // add homeAlbum
        .on("click", "#homeAlbumBtn", function (e) {
            if (confirm(ploc.really_do_home_album_loc))
                ajax.getHomeAlbum();
            e.preventDefault();
        })
        // add comment
        .on("click", "#addCommBtn", function (e) {
            ajax.postComm();
            e.preventDefault();
        })
        // follow - unfollow
        .on("click", "#followBtn, #unfollowBtn", function (e) {
            ($(this).attr("id") == "followBtn") ? ajax.getFollow(1) : ajax.getFollow(0);
            e.preventDefault();
        })
        // show nude
        .on("click", ".nudePreview", function (e) {
            if (app.showNude()) {
                e.stopPropagation();
                e.preventDefault();
            }
        })
        // allow full version
        .on("click", "#canonicalUrl", function (e) {
            cookie.setCookie("allowFullVer", 1);
        })
        // enlarge comm text area
        .on("click", "#commText", function () {
            $(this).css({height: "100px"});
        })
        // zoom image preview
        .on('doubletap', "#mainImageA", function (e) {
            app.handleImgZoom();
            e.preventDefault();
        })
        .on("dblclick", "#mainImageA", function (e) {
            e.preventDefault();  //cancel system double-click event
        })
        .on("click", "#mainImageA", function (e) {
            app.clicksCnt++;
            if (app.clicksCnt === 1) {
                app.clickTimer = setTimeout(function () {
                    app.clicksCnt = 0;
                    if (!app.isInZoom)
                        $("#nextLnkKey").trigger("click");
                    e.preventDefault();
                }, app.clickDelay);
            } else {
                clearTimeout(app.clickTimer);
                app.handleImgZoom();
                e.preventDefault();
                app.clicksCnt = 0;
            }
        })
        // keyboard next-prev navigation
        .on("keyup", function (e) {
            if (e.keyCode == 39) $("#nextLnkKey").click();
            else if (e.keyCode == 37) $("#prevLnkKey").click();
            e.preventDefault();
        });

    // hide messages
    setTimeout(function () {
        $("#resultMsg").slideUp();
    }, 3000);

    // update photo
    app.updateMainImg();

    // update photo views
    ajax.getViews();

    // rotate author metrics
    //utils.rotateVal($("#authImgCnt"), "int");
    utils.rotateVal($("#authRating"), "float");

    // dummy, never called -:)
    if (0) {
        app.emoticon(0);
        app.toggleCrop();
    }
});