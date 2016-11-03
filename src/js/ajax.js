import {localCache, ploc, utils} from "./utils";
import {app} from "./app";

const ajax = {
    ajaxFld: "/ajax",
    loader: '<img src="/assets/css/black/' + 'loader_gray.new.gif' + '" alt="">',

    handelAjax: function (url) {
        $("html, body").animate({scrollTop: 0}, 300);
        $("#submenu").slideUp();
        app.removeCrop();

        const $ajaxBody = $("#ajaxBody");
        let pos = $ajaxBody.offset();
        let size = {w: $ajaxBody.width(), h: $ajaxBody.height()};
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
        let isFormCache = false;
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
        const id = $("#mainImage").data("idPhoto");
        const $comment = $("#commText");
        let comm_text = utils.trim($comment.val());
        if (comm_text.length < minCommLen) {
            alert(ploc.be_constructive_loc);
            $comment.focus();
        }
        else if (comm_text.length > 30000) {
            alert(ploc.max_comm_len_loc);
            $comment.focus();
        }
        else {
            const $loaderComm = $("#loaderComm");
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
                    const $submenu = $("#submenu");
                    $("#commTbl").find("tbody").append('<tr><td class="commAuth"><a href="' + $submenu.data("authUrl") + '"><img src="' + $submenu.data("authAvatar") + '"></a></td>' +
                        '<td class="commTextTd"><div class="commText"><a href="' + $submenu.data("authUrl") + '">' + $submenu.data("authName") + '</a><br>' + comm_text + '</div></td></tr>');
                    $comment.val("");
                    $loaderComm.hide();
                }, "json");
        }
    },

    getRecs: function () {
        const id = $("#mainImage").data("idPhoto");
        $("#addRecAjax").html('<span class="recNote">' + ploc.already_rec_note_loc + '</span>');
        $("#homeAlbumAjax").hide();
        const $phRating = $("#phRating");
        let oldRate = $phRating.text();
        $phRating.text();
        let isUp = false;
        setTimeout(function () {
            if (!isUp) $phRating.html(ajax.loader);
        }, 500);
        $.get("/ajax/get_recs.php", {id_photo: id}, function (data) {
            isUp = true;
            $phRating.text(utils.floor((parseFloat(oldRate) + parseFloat(data)), 2)).addClass("animated bounceIn");
        })
    },

    getFineart: function () {
        const id = $("#mainImage").data("idPhoto");
        const fineart = $("#fineartBtn").data("fineart");
        $("#fineartAjax").text('');
        $.get("/ajax/get_fineart.php", {id: id, fineart: fineart}, function (data) {
        })
    },

    getHomeAlbum: function () {
        const id = $("#mainImage").data("idPhoto");
        $("#homeAlbumAjax").hide();
        $.get("/ajax/get_home_album.php", {id_photo: id, is_vote: 1}, function (data) {
        })
    },

    getViews: function () {
        const $mainImage = $("#mainImage");
        if ($mainImage.length)
            $.get("/ajax/get_views.php", {
                id: $mainImage.data("idPhoto")
            }, function (data) {
            });
    },

    getFollow: function (act) {
        const id = $("#ajaxHeader").data("idAuthPhoto");
        const $followBtnBlk = $("#followBtnBlk");
        let isUp = false;
        setTimeout(function () {
            if (!isUp) $followBtnBlk.html(ajax.loader);
        }, 500);
        $.get("/ajax/get_follow.php", {id: id, act: act, type: "auth"}, function (data) {
            isUp = true;
            $followBtnBlk.html(data + "<br>&nbsp;");
        });
    }
};

export {ajax};