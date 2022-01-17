# fuckcpdaily 


## 使用说明

### 1) 请求参数

>http(s)://域名/?userId=学号

### 2) 调用方式：HTTP get

### 3) 要求：

* 需要将配置文件放在程序根目录下的config目录内，并命名为“学号.json”，此处学号与请求参数中的学号必须一致。
* 配置文件不能带注释


### 4) 空白配置模板:

```
{
    "system":{
        "userId":"",
        "passWord":"",
        "delay":""
    },
    "location":{
        "longitude":"",
        "latitude":"",
        "itudeOffset":"",
        "position":""
    },
    "fillForm":{
        "optionKeyword":"",
        "tempKeyword":"",
        "temperature":"",
        "tempOffset":""
    },
    "push":{
        "email":"",
        "token":""
    }
}
```

### 5) 配置模板:

```
{
    "system":{
        "userId":"",//学号
        "password":"",//密码
        "delay":""//延迟时间，不需要就别填
    },
    "location":{
        "longitude":"",//经度，精确到小数点后六位
        "latitude":"",//纬度，精确到小数点后六位
        "itudeOffset":"",//经纬度随机量，填数字0-20均可，不填就不随机
        "position":""//定位地址，例如中国山西省太原市万柏林区千峰街道清泽东路
    },
    "fillForm":{
        "optionKeyword":"",//选项关键词（多个关键词用英文逗号隔开），有关键词直接就选了，勿填多个选项均包含的关键词
        "tempKeyword":"",//体温填空的关键词
        "temperature":"",//体温中值，随机是以此温度为中心±随机量为范围随机（）建议36.5
        "tempOffset":""//体温随机量，不填不随机（建议0.4）
    },
    "push":{
        "email":"",//推送邮箱，请将DonaldTrump@email.cn列入白名单，不填就不推送邮箱
        "token":""//pushplus推送token，不填不推送
    }
}
```


### 6) 配置参数说明:
|字段名称       |字段说明         |类型            |必填            |备注     |
| -------------|:--------------:|:--------------:|:--------------:| ------:|
|userId||string|Y|-|
|password||string|Y|-|
|delay||string||-|
|longitude||float|Y|-|
|latitude||float|Y|-|
|itudeOffset||int||-|
|position||string|Y|-|
|optionKeyword||string|Y|-|
|tempKeyword||string|Y|-|
|temperature||float|Y|-|
|tempOffset||float||-|
|email||string||-|
|token||string||-|

### 7) 展望未来
- [x] 实现多用户配置
- [ ] 透到施越
- [ ] 根据所选校区经纬度范围完成随机

