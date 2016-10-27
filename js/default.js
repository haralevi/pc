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
                data.hrefPrev = data.hrefPrev.replace(/&amp;/g, "&") || "";
                data.hrefNext = data.hrefNext.replace(/&amp;/g, "&") || "";
                $ajaxBody.html(data.ajaxBody);
                if (!app.isFirstClick) // needed only for ipad, iphone
                    $ajaxBody.addClass("animated fadeIn");
                app.isFirstClick = false;
                if (data.title != "") document.title = data.title;
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
        $mainImage.attr("src", $mainImage.data("phPath") + $mainImage.data("idPhoto") + "_mobile.jpg").css({
            width: "auto",
            height: "auto"
        });
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
                    $mainImage.attr("src", $mainImage.data("phPath") + $mainImage.data("idPhoto") + "_mobile.jpg").css({
                        width: "auto",
                        height: "auto"
                    });
                    break;
                }
            }
        }
        var imgSrc = $mainImage.attr("src");

        if (app.winW >= 420) {
            var $mobileImage = $("#mobileImage");
            $mobileImage.attr("src", imgSrc.replace(/mobile/g, 'main'));
            $mobileImage.waitForImages(function () {
                $mainImage.attr("src", imgSrc.replace(/mobile/g, 'main'));
            });
        }
        else {
            $mainImage.attr("src", imgSrc.replace(/main/g, 'mobile'));
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