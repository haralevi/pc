import {utils} from "./utils";

const app = {
    winW: $(window).width(), winH: $(window).height(),
    isCrop: false, cropX: 0, cropY: 0, cropW: 0, cropH: 0,
    viewedNudes: [],
    isFirstClick: true,
    isInZoom: false,
    clickDelay: 200,
    clicksCnt: 0,
    clickTimer: 0,
    isCommentFocus: false,
    id_auth_answer: 0,
    id_comm_parent: 0,

    toggleCrop: function () {
        app.resetImgZoom();
        if (app.cropW > 0 && app.cropH > 0) {
            if (app.isCrop) {
                $(".crop_overlay_dark").remove();
                $(".crop_overlay_light").remove();
            }
            else {
                $("html, body").animate({scrollTop: 0}, 300);
                const $mainImage = $("#mainImage");
                if ($mainImage.attr("class") === "nudePreview") {
                    if (typeof $mainImage.data("isAllowedNude") === "undefined")
                        app.showNude();
                }
                else {
                    let pos = $mainImage.offset();
                    let size = {w: $mainImage.width(), h: $mainImage.height()};
                    let imgRatioW = size.w / parseInt($mainImage.data("phMainW"));
                    let imgRatioH = size.h / parseInt($mainImage.data("phMainH"));

                    $('<div class="crop_overlay_dark"></div>').css({
                        left: pos.left + "px",
                        top: pos.top + "px",
                        width: (size.w + 0.1) + "px", // +0.1 because sometimes tiny light line appears
                        height: (size.h + 0.1) + "px" // +0.1 because sometimes tiny light line appears
                    }).appendTo("body");

                    $('<div class="crop_overlay_light" style="background: transparent url(' + $mainImage.attr("src") + ') no-repeat -' + (app.cropX * imgRatioW) + 'px -' + (app.cropY * imgRatioH) + 'px; background-size: ' + size.w + 'px ' + size.h + 'px;"></div>').css({
                        left: pos.left + (app.cropX * imgRatioW) + "px",
                        top: pos.top + (app.cropY * imgRatioH) + "px",
                        width: (app.cropW * imgRatioW) + "px",
                        height: (app.cropH * imgRatioH) + "px"
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
        const $mainImage = $("#mainImage");
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
        const $mainImage = $("#mainImage");
        const $nudeWarn = $("#nudeWarn");

        if (!$mainImage.length) return;
        if ($mainImage.attr("class") === "nudePreview") {
            for (let i = 0; i < app.viewedNudes.length; i++) {
                if ($mainImage.data("idPhoto") === app.viewedNudes[i]) {
                    $mainImage.removeClass("nudePreview");
                    $mainImage.attr("src", $mainImage.data("phPath") + $mainImage.data("idPhoto") + "_mobile.jpg").css({
                        width: "auto",
                        height: "auto"
                    });
                    break;
                }
            }
        }
        let imgSrc = $mainImage.attr("src");

        let srcReplace = 'main';
        if (typeof $mainImage.data("isAllowedNude") !== "undefined")
            srcReplace = 'council';

        if (app.winW >= 420) {
            const $mobileImage = $("#mobileImage");
            $mobileImage.attr("src", imgSrc.replace(/mobile/g, srcReplace));
            $mobileImage.waitForImages(function () {
                $mainImage.attr("src", imgSrc.replace(/mobile/g, srcReplace));
            });

            // resize image according to app width
            let mainImageW = $mainImage.data("phMainW");
            let mainImageH = $mainImage.data("phMainH");
            if (mainImageW > app.winW) {
                mainImageH = (mainImageH / mainImageW) * app.winW;
                mainImageW = app.winW;
            }

            $mainImage.css({width: mainImageW + "px", height: mainImageH + "px"});
            $nudeWarn.css({"margin-top": +(mainImageH / 2 - 30) + "px"});
        }
        else {
            $mainImage.attr("src", imgSrc.replace(new RegExp(srcReplace, 'g'), 'mobile'));
        }

        app.resetImgZoom();
        app.fixCommText();

        utils.stopRightClick($mainImage);
    },

    resetImgZoom: function () {
        if (!app.isInZoom)
            return true;
        const $mainImage = $("#mainImage");
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
            const $mainImage = $("#mainImage");
            if (app.winW < 420)
                $mainImage.attr("src", $mainImage.attr("src").replace(/mobile/g, 'main'));
            let zoom = $mainImage.data("phMainW") / $mainImage.width();
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
        let loginVal = utils.trim($("#auth_login").val());
        let passVal = utils.trim($("#auth_pass").val());
        if (loginVal !== "" && passVal !== "") {
            let postUrl = location.href.replace(/&wrn_login=1/, "");
            if (window.location.search === "" && !utils.endsWith(postUrl, "?")) postUrl += "?";
            utils.postForm(postUrl, {auth_login: encodeURI(loginVal), auth_pass: encodeURI(passVal)});
        }
    },

    getCommTextVal: function ($commText) {
        let commTextVal = $commText.val();
        if (utils.trim(commTextVal)) commTextVal += "\n";
        return commTextVal;
    },

    updateTextareaHeight: function (el) {
        const minH = 100;
        const lht = parseInt(el.css('line-height'), 10);
        const lines = Math.floor(el.prop('scrollHeight') / lht);
        const elH = lines * lht;
        if (elH > minH) el.css({'height': elH + 'px'});
        else el.css({'height': minH + 'px'});
    },

    focusCommText: function ($commText, commTextVal) {
        app.isCommentFocus = true;
        $commText.focus().val("").val(commTextVal);
    },

    emoticon: function (id_auth, id_comm) {
        app.id_auth_answer = id_auth;
        app.id_comm_parent = id_comm;
        const $commText = $("#commText");
        let commTextVal = app.getCommTextVal($commText);
        commTextVal += "[b]" + $("#authName" + id_comm).text() + "[/b], ";
        app.focusCommText($commText, commTextVal);
        app.updateTextareaHeight($commText);
    },

    setAnswer: function (id_auth, id_comm) {
        app.id_auth_answer = id_auth;
        app.id_comm_parent = id_comm;

        let answer = $("#commText" + id_comm).html();
        answer = answer.replace(/<div class="commQuote">[\s\S]+<\/div>/g, '');
        answer = answer.replace(/<br>/g, '').replace(/&nbsp;/g, '');
        answer = answer.replace(/<strong>/g, '[b]').replace(/<\/strong>/g, '[/b]').replace(/<u>/g, '[u]').replace(/<\/u>/g, '[/u]').replace(/<i>/g, '[i]').replace(/<\/i>/g, '[/i]');
        answer = answer.replace(/data-crop-coordinates="(\d+);(\d+);(\d+);(\d+)"/g, '>[Crop:$1:$2:$3:$4]<');
        answer = answer.replace(/<\/?[^>]+(>|$)/g, "");
        answer = answer.replace(/^\n+/, '').replace(/\n+$/, '');
        answer = '[quote]' + utils.trim(answer) + '[/quote]\n\n[b]' + $('#authName' + id_comm).text() + '[/b], ';

        const $commText = $("#commText");
        let commTextVal = app.getCommTextVal($commText);
        commTextVal += answer;

        app.focusCommText($commText, commTextVal);
        app.updateTextareaHeight($commText);
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
    },
};

export {app};
