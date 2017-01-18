<table>
    <tr>
        <td style="vertical-align: top;">
            <div style="position: relative;">
                <a rel="author" href="{home_url}author.php?id_auth={id_auth_photo}"><img src="{auth_avatar_src}" class="auth_avatar img-rounded" alt=""></a>
                {auth_premium_badge}
            </div>
        </td>
        <td style="padding-top: 10px; vertical-align: top; white-space: nowrap;">
            <div style="font-size: 18px; font-weight: 700; max-width: 308px; overflow: hidden; padding: 0 6px 10px 10px;" class="clearfix;">
                {auth_name_photo}
            </div>
            <div style="clear: left; float:left; width: 60px; font-size: 11px; text-align: center; padding: 6px 0; border-right: 1px solid #333;">
                <b id="authImgCnt" style="font-size: 16px;">{auth_img_cnt_total}</b><br>{works_loc}
            </div>
            <div style="float:left; width: 60px; font-size: 11px; text-align: center; padding: 6px 0; border-right: 1px solid #333;">
                <b id="authRating" style="font-size: 16px;">{auth_rating_work}</b><br>{rating_loc}
            </div>
            <div style="float:left; padding: 0;">
                <div id="followBtnBlk" style="width: 80px; height: 24px; padding: 0 0 8px 10px;{is_display_follow_btn}">
                    <a id="{follow_btn_id}" href="#" class="saveBtn {follow_btn_class}" style="width: 60px;">{follow_btn_val}</a>
                </div>
                <div style="clear: left; height: 24px; padding: 0 0 25px 10px;">
                    <a id="portfolioLnk" href="{portfolio_a}" target="_blank" class="saveBtn" style="width: 60px;">{portfolio_loc}</a>
                </div>
            </div>
        </td>
    </tr>
</table>
