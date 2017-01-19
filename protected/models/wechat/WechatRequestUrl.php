<?php

class WechatRequestUrl {

    //获取最新access权限的链接
    const qy_access_token = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=%s&corpsecret=%s";
    //获取code的连接
    const qy_code_get = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
    //获取userid/openid的连接
    const qy_user_get = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=%s&code=%s";
    //userId to openId
    const qy_user_open = "https://qyapi.weixin.qq.com/cgi-bin/user/convert_to_openid?access_token=%s";
    //获取部门列表
    const qy_department_list = "https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token=%s&id=%s";
    //获取素材列表
    const qy_item_list = "https://qyapi.weixin.qq.com/cgi-bin/material/batchget?access_token=%s";
    //创建会话
    const qy_chat_create = "https://qyapi.weixin.qq.com/cgi-bin/chat/create?access_token=%s";
    //发消息
    const qy_message_send = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=%s";

}
