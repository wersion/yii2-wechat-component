#基于Yii2开发的微信API组件
##安装方法
在项目的`composer.json`的`require`数组内加入`"iit/yii2-wechat-component": "*"`

    "require": {
        "iit/yii2-wechat-component": "*"
    }

##使用方法
在配置文件里面加入下列代码

```php
component=>[
     'wechat' => [
        'class' => '\iit\wechat\Component',
        'appid' => 'your app id',
        'appsecret' => 'your app secret',
        'token' => 'your token',
    ],
]
```

在控制器里面加入下面`action` 

```php
    class WechatController extends Controller{
        // ...
        public function actionIndex($signature, $timestamp, $nonce, $echostr = null){
            if(\Yii::$app->wechat->receive->signature($signature, $timestamp, $nonce){
                if($echostr === null){
                    $receiveData = \Yii::$app->wechat->receive->getData();
                    if(empty($receiveData)){
                        // ...
                    }else{
                        // ...
                    }
                }else{
                    return $echostr;
                }
            }else{
                // ...
            }
        }
    }
```
##使用手册
此组件由下列子组件组成

* Mass(高级群发接口)
* Media(多媒体下载上传接口)
* Menu(自定义菜单接口)
* OAuth(网页OAuth 2.0验证接口)
* QRCode(二维码接口)
* Receive(接收微信消息接口)
* Response(发送响应消息接口)
* Service(发送客户消息接口)
* Template(发送模板消息接口)
* User(用户和用户分组管理接口)
