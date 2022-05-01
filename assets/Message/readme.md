# Qmsg 消息提示插件

## 使用

兼容IE>=9。

```html
<link rel="stylesheet" href="./css/message.css">

<!-- your html -->

<script src="./js/message.js"></script>

<script>
    var configs = {};
    // configs 为配置参数，可省略
    Qmsg.info("这是提示消息",configs);
</script>

```

## 全局配置
在引入message.js之前可以通过全局变量 QMSG_GLOBALS.DEFAULTS 来进行配置
```javascript
window.QMSG_GLOBALS = {
    DEFAULTS:{
        showClose:true,
        timeout:5000
    }
}
```
或者通过`Qmsg.config({})`来动态修改全局配置:
```js
Qmsg.config({
    showClose:true,
    timeout:5000
})
```
所有支持的配置信息如下:
|参数名|类型|描述|默认|
|  ----  | ----  |  ----  | ----  |
|position|String|消息显示的位置,居于顶部的左中右,'left','right','center'可选|'center'|
|showClose|Boolean|是否显示关闭图标|false|
|timeout|Number|多久后自动关闭，单位ms|2000|
|autoClose|Boolean|是否自动关闭|true|
|content|String|提示的内容|''|
|onClose|Function|关闭的回调函数|null|
|html|Boolean|是否将内容作为html渲染|false|
|maxNums|Number|最多显示消息(autoClose:true)的数量|5|

## Qmsg支持的方法
### Qmsg.info()
### Qmsg.warning()
### Qmsg.error()
### Qmsg.success()
### Qmsg.loading()
以上方法均可传递1-2个参数，如下：
```js
Qmsg.loading("我是加载条");
Qmsg.info("给你个眼神，你懂得",{
    showClose:true,
    onClose:function(){
        console.log('我懂了')
    }
})
Qmsg.error({
    content:"1+1=3",
    timeout:5000
})
//...

```
注意：`Qmsg.loading()`默认修改`autoClose=false`,一般来说需要手动关闭：
```js
var loadingMsg = Qmsg.loading('我是加载条');
//do something
loadingMsg.close();
```
如需要自动关闭则需要如下调用:
```js
Qmsg.loading("我是加载条",{
    autoClose:true
})
//或者
Qmsg.loading({
    autoClose:true,
    content:"我是加载条"
})
```
### Qmsg.closeAll()
关闭所有消息，包括`autoClose=false`的消息

## 消息实例支持的方法和属性
```js
var aMsg = Qmsg.info("这是个info消息")
```
### close()
关闭当前消息,会触发`onClose`回调函数。
```
aMsg.close()
```
### destroy()
销毁消息，不会触发`onClose`回调函数。
```js
aMsg.destroy()
```

### timeout

多久后关闭，单位ms,设置该值可动态修改该消息实例的关闭时间，前提是该消息实例的`autoClose==true`。

