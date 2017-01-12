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
                if ($mainImage.attr("class") == "nudePreview") {
                    if(typeof $mainImage.data("isAllowedNude") === "undefined")
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
        if (!$mainImage.length) return;
        if ($mainImage.attr("class") == "nudePreview") {
            for (let i = 0; i < app.viewedNudes.length; i++) {
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
        }
        else {
            $mainImage.attr("src", imgSrc.replace(new RegExp(srcReplace, 'g'), 'mobile'));
        }

        // resize image according to app width
        let mainImageW = $mainImage.data("phMainW");
        let mainImageH = $mainImage.data("phMainH");
        if (mainImageW > app.winW) {
            mainImageH = (mainImageH / mainImageW) * app.winW;
            mainImageW = app.winW;
        }
        $mainImage.css({width: mainImageW + "px", height: mainImageH + "px"});

        app.resetImgZoom();
        app.fixCommText();
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
        if (loginVal != "" && passVal != "") {
            let postUrl = location.href.replace(/&wrn_login=1/, "");
            if (window.location.search == "" && !utils.endsWith(postUrl, "?")) postUrl += "?";
            utils.postForm(postUrl, {auth_login: encodeURI(loginVal), auth_pass: encodeURI(passVal)});
        }
    },

    emoticon: function (id) {
        const $commText = $("#commText");
        let commTextVal = $commText.val();
        if (utils.trim(commTextVal)) commTextVal += "\n";
        commTextVal += "[b]" + $("#authName" + id).text() + "[/b], ";
        $commText.focus().val("").val(commTextVal).css({height: "100px"});
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

export {app};
