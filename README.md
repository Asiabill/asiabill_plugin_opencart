
Asiabill Magento2 支付插件
=

__仅适用于opencart3.x版本__

插件安装
-

1、把upload目录压缩成zip包，并命名asiabill-paymenth.ocmod

2、后台：Extensions -> Installer 上传asiabill-paymenth.ocmod.zip
![images](https://files.gitbook.com/v0/b/gitbook-x-prod.appspot.com/o/spaces%2FcSYgMg71VCxeEVhWhVFp%2Fuploads%2F9TkqNRB7MLSkPovSMQ3I%2Fopencart3-admin-upload.png?alt=media&token=b6e05627-d5a0-4acd-b840-dafaa889c86c)

3、如果后台无法上传，则可以把upload目录下代码上传至站点根目录

4、设置：Extensions -> Extensions -> Payments， 你可以看到Asiabill的相关支付列表

![image](https://files.gitbook.com/v0/b/gitbook-x-prod.appspot.com/o/spaces%2FcSYgMg71VCxeEVhWhVFp%2Fuploads%2FKjdWphuqO5xyL1WTz6hl%2Fopencart3-admin-list.png?alt=media&token=0f1c7824-56c9-41e5-9e83-4535cae17730)

__General Settings：基础设置（对插件中所有支付方式生效）__

* Edition：版本号
* Order Status(success)：交易成功更新订单状态
* Order Status(failed)：交易失败更新订单状态
* Order Status(cancel)：待处理交易更新订单状态
* Logging：开启支付数据日志
* Webhook：是否接受异步订单回调
* Display logo type：图标显示位置

__Mer Settings：账户信息（对当前支付方式生效）__

* Transaction Mode：交易模式
  * test：测试
  * live：正式
* Mer No、Gateway No、Sign Key：账户信息，非测试模式下使用
* Test Mer No、Test Gateway No、Test Sign Key：测试账户信息，测试模式下使用，默认已设置

__Payment Settings：支付设置（对当前支付)__

* Status：是否开启
* Sort Order：排序
* Payment Title：显示支付方式名称
* Geo Zone：地区
* Visa、MasterCard、JCB、American Express、Discover、Diners：显示卡种图标


信用卡支付
-
跳转支付模式：页面会跳出当前网站，在Asiabill页面进行输入卡号，支付完成后跳转回网站
![images](https://files.gitbook.com/v0/b/gitbook-x-prod.appspot.com/o/spaces%2FcSYgMg71VCxeEVhWhVFp%2Fuploads%2FJhjGY4FOLbq7UlkjkurH%2Fimage.png?alt=media&token=bd122e1d-42f3-491e-b8b9-2a6319f90671)


测试卡号
-
* 支付成功：4000020951595032
* 支付失败：4000000000009995
* 3D交易：4000002500003155

本地支付
-
需要额外开通才能交易
