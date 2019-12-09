<?php
/**
 * Created by PhpStorm.
 * User: emotionalJim
 * Date: 2019/12/2
 * Time: 20:55
 */

// 公众号配置
if(DEMOENV=='product'){
    return [
        "card"=>[
            "appid"=>"wxbd2e6e4ca3698173" ,
            "appid2"=>'gh_0943d922ef4a',
            "appsecret"=> "8a06bd14c2d5352f62ecdbd68000ae83",
            "token"=>"cards12",
            "encrypt_type"=>"SECURTY",
            "encoding_aes_key"=>"0HNHhPmiO4kfS2d9fOkZNHmUBWpFV1Crkbr6EoxHAJz",
            'wechat_id'=>258,
        ],
        "component"=>[
            "domain"=>"https://u.zhiliaoke.cn",
            "member_domain"=>"http://member.zhiliaoke.cn",
            "m_domain"=>"http://m.zhiliaoke.cn",
            "tel_domain"=>"http://tel.zhiliaoke.cn",
            "appid"=>"wx02e1f67dae531117" ,
            "appsecret"=> "5b6270a133237ff54296935605de09b5",
            "token"=>"zlk123po",
            "encrypt_type"=>"SECURTY",
            "encoding_aes_key"=>"25fcwad8c2545dd16fa889bc967d2f7f7d2f7f7d2f7",
            "url"=>"https://u.zhiliaoke.cn/",
            "authorizer"=>[//平台默认微信号
                'wechat_id'=>339,
                "appid"=>"wxaa3d043308041cb2",
                'wechat_appid'=>'gh_175782c2b676',
                "expire"=>300,
            ]
        ],
        'template_msg'=>[
            'primary_industry' => [
                'industry_id1'=>10,
                'first_class'=>'餐饮',
                'second_class' => '餐饮'],
            'secondary_industry' => [
                'industry_id2'=>41,
                'first_class'=> '其他' ,
                'second_class' => '其他']
        ],
        "matter_test"=>[
            "appid"=>"wxaa3d043308041cb2" ,
        ],
    ];
}

if(DEMOENV=='test'){
    return [
        "basic"=>[
            "appid"=>"wx8498f7efc631cd22" ,
            //"appid2"=>'gh_15ce37d205bd',
            "appsecret"=> "b6f258cc3865650e484575406f2f9c19",
            "token"=>"waterdance",
            "redirect"=>"http://tp5demo.com",
            "encrypt_type"=>"SECURTY",
            "encoding_aes_key"=>"deqixAmxW59cSxdrlwqCqJlCK32rgPExEwXcQrRgI5w",
            //'wechat_id'=>258,
        ],
        "component"=>[
            "domain"=>"http://192.168.1.169:8080",
            "member_domain"=>"http://192.168.1.169:8010",
            "m_domain"=>"http://192.168.1.169:9010",
            "tel_domain"=>"http://192.168.1.169:9020",
            "appid"=>"wx02e1f67dae531117" ,
            "appsecret"=> "5b6270a133237ff54296935605de09b5",
            "token"=>"zlk123po",
            "encrypt_type"=>"SECURTY",
            "encoding_aes_key"=>"25fcwad8c2545dd16fa889bc967d2f7f7d2f7f7d2f7",
            "url"=>"https://u.zhiliaoke.cn/",
            "authorizer"=>[//平台默认微信号
                'wechat_id'=>339,
                "appid"=>"wxaa3d043308041cb2",
                'wechat_appid'=>'gh_175782c2b676',
                "expire"=>300,
            ]
        ],
        'template_msg'=>[
            'primary_industry' => [
                'industry_id1'=>10,
                'first_class'=>'餐饮',
                'second_class' => '餐饮'],
            'secondary_industry' => [
                'industry_id2'=>41,
                'first_class'=> '其他' ,
                'second_class' => '其他']
        ],
        "matterTest"=>[
            "appid"=>"wx02e1f67dae531117" ,
        ],

    ];
}
return [
    "basic"=>[
        "appid"=>"wx8498f7efc631cd22" ,
        //"appid2"=>'gh_15ce37d205bd',
        "appsecret"=> "b6f258cc3865650e484575406f2f9c19",
        "token"=>"waterdance",
        "redirect"=>"http://tp5demo.com",
        "encrypt_type"=>"SECURTY",
        "encoding_aes_key"=>"deqixAmxW59cSxdrlwqCqJlCK32rgPExEwXcQrRgI5w",
        //'wechat_id'=>258,
    ],
    "component"=>[
        "domain"=>"http://localhost",
        "member_domain"=>"http://192.168.1.169:8010",
        "m_domain"=>"http://192.168.1.169:9010",
        "appid"=>"wx02e1f67dae531117" ,
        "appsecret"=> "5b6270a133237ff54296935605de09b5",
        "token"=>"zlk123po",
        "encrypt_type"=>"SECURTY",
        "encoding_aes_key"=>"25fcwad8c2545dd16fa889bc967d2f7f7d2f7f7d2f7",
        "url"=>"https://u.zhiliaoke.cn/",
        "authorizer"=>[//平台默认微信号
            'wechat_id'=>339,
            "appid"=>"wxaa3d043308041cb2",
            'wechat_appid'=>'gh_175782c2b676',
            "expire"=>300,
        ]
    ],
    'template_msg'=>[
        'primary_industry' => [
            'industry_id1'=>10,
            'first_class'=>'餐饮',
            'second_class' => '餐饮'],
        'secondary_industry' => [
            'industry_id2'=>41,
            'first_class'=> '其他' ,
            'second_class' => '其他']
    ],
    "matterTest"=>[
        "appid"=>"wx02e1f67dae531117" ,
    ],

];