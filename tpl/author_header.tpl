<table>
    <tr>
        <td>
            <div class="author__avatar">
                <a rel="author" href="{home_url}author.php?id_auth={id_auth_photo}">
                    <img src="{auth_avatar_src}" class="auth_avatar img_rounded" alt="">
                </a>
                {auth_premium_badge}
            </div>
        </td>
        <td>
            <div class="author__name clearfix">
                {auth_name_photo}
            </div>
            <div class="author__counter clearfix">
                <b id="authImgCnt" class="author__counter_val">{auth_img_cnt_total}</b><br>{works_loc}
            </div>
            <div class="author__counter">
                <b id="authRating" class="author__counter_val">{auth_rating_work}</b><br>{rating_loc}
            </div>
            <div class="author__links">
                <!--[HAS_PORTFOLIO_BLK]-->
                <div class="author__portfolio_lnk">
                    <a id="portfolioLnk" href="{portfolio_a}" target="_blank" class="saveBtn">{portfolio_loc}</a>
                </div>
                <!--[HAS_PORTFOLIO_BLK]-->
                <!--[FOLLOW_BTN_BLK]-->
                <div id="followBtnBlk" class="author__follow_lnk">
                    <a id="{follow_btn_id}" href="#" class="saveBtn {follow_btn_class}">{follow_btn_val}</a>
                </div>
                <!--[FOLLOW_BTN_BLK]-->
            </div>
        </td>
    </tr>
</table>
