import {waitForFinalEvent, cookie, utils, ploc} from "./utils";
import {ajax} from "./ajax";
import {app} from "./app";

$(function () {
    // handle ajax navigation
    utils.handleHistory('a[rel="prev"], a[rel="next"], a[rel="author"]', ajax.ajaxFld);

    // fix layout after window resize
    $(window).resize(function () {
        waitForFinalEvent(function () {
            app.fixLayout();
        }, 500, "delayed_resize");
    });

    $(document).ready(function() {
        // do, something
    });

    $(document)
        // submit login form
        .on("keyup", "#auth_login, #auth_pass", function (e) {
            if (e.keyCode == 13) {
                app.submitLogin();
                e.preventDefault();
            }
        })
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
            if (typeof $(this).data("isAllowedNude") === "undefined") {
                if (app.showNude()) {
                    e.stopPropagation();
                    e.preventDefault();
                }
            }
        })
        // author answer
        .on("click", ".authNameAnswer", function (e) {
            app.emoticon($(this).data("idAuth"));
            e.stopPropagation();
            e.preventDefault();
        })
        // comm answer
        .on("click", ".commAnswer", function (e) {
            app.setAnswer($(this).data("idAuth"), $(this).data("idComm"));
            e.stopPropagation();
            e.preventDefault();
        })
        //cropClick
        .on("click", ".cropClick", function (e) {
            let cropCoordinates = $(this).data("cropCoordinates").split(";");
            if (cropCoordinates.length == 4) {
                app.cropX = cropCoordinates[0];
                app.cropY = cropCoordinates[1];
                app.cropW = cropCoordinates[2];
                app.cropH = cropCoordinates[3];
                app.toggleCrop();
            }
            e.stopPropagation();
            e.preventDefault();
        })
        // allow full version
        .on("click", "#canonicalUrl", function () {
            cookie.setCookie("allowFullVer", 1);
        })
        // zoom image preview
        .on('doubletap', "#mainImageA", function (e) {
            app.handleImgZoom();
            e.preventDefault();
        })
        // cancel system double-click event
        .on("dblclick", "#mainImageA", function (e) {
            e.preventDefault();
        })
        // zoom / unzoom main image
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
        // prevent multiple clicks on slow login with facebook button
        .on("click", "#facebookBtn", function () {
            $(this).attr("disabled", true);
        })
        // enlarge comm text area
        .on("blur", "#commText", function () {
            app.isCommentFocus = false;
        })
        // enlarge comm text area
        .on("focus", "#commText", function () {
            app.isCommentFocus = true;
            app.updateTextareaHeight($(this));
        })
        // enlarge comm text area
        .on("keyup", "#commText", function () {
            app.updateTextareaHeight($(this));
        })
        // keyboard next-prev navigation
        .on("keyup", function (e) {
            if(!app.isCommentFocus) {
                if (e.keyCode == 39) $("#nextLnkKey").click();
                else if (e.keyCode == 37) $("#prevLnkKey").click();
                e.preventDefault();
            }
        })
        // replace link with image
        .on("click", "a.showImgFromLnk", function (e) {
            $(this).html('<img src="' + $(this).attr("href") + '">');
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
    utils.rotateVal($("#authRating"), "float");

    // ServiceWorker is a progressive technology. Ignore unsupported browsers
    /*
    if ('serviceWorker' in navigator) {
        //console.log('CLIENT: service worker registration in progress.');
        navigator.serviceWorker.register('../service-worker.js').then(function() {
            //console.log('CLIENT: service worker registration complete.');
        }, function() {
            //console.log('CLIENT: service worker registration failure.');
        });
    } else {
        //console.log('CLIENT: service worker is not supported.');
    }
     */
});