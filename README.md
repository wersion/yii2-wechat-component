#基于Yii2开发的微信API组件
目前实现了下面接口
##功能列表

* 验证消息真实性
* 接收数据并转换成数组

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