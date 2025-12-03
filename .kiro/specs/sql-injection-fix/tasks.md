# Implementation Plan

- [x] 1. 增强 SpringMySQLi 数据库操作类





  - [x] 1.1 实现 insertV2 方法


    - 在 `includes/dbhelp.php` 中添加 `insertV2` 方法
    - 使用预处理语句构建 INSERT 语句
    - 自动处理字段名和占位符
    - 返回插入的 ID 或影响行数
    - _Requirements: 1.1_
  - [ ]* 1.2 编写 insertV2 属性测试
    - **Property 1: SQL 注入防护 - 插入操作**
    - **Validates: Requirements 1.1**
  - [x] 1.3 实现 updateV2 方法


    - 在 `includes/dbhelp.php` 中添加 `updateV2` 方法
    - 支持 SET 子句和 WHERE 子句的参数化
    - 合并 SET 参数和 WHERE 参数
    - 返回影响的行数
    - _Requirements: 1.2_
  - [ ]* 1.4 编写 updateV2 属性测试
    - **Property 2: SQL 注入防护 - 更新操作**
    - **Validates: Requirements 1.2**

  - [x] 1.5 实现 deleteV2 方法

    - 在 `includes/dbhelp.php` 中添加 `deleteV2` 方法
    - 支持 WHERE 子句的参数化
    - 返回影响的行数
    - _Requirements: 1.3_
  - [ ]* 1.6 编写 deleteV2 属性测试
    - **Property 3: SQL 注入防护 - 删除操作**
    - **Validates: Requirements 1.3**
  - [x] 1.7 实现 selectPageV2 方法


    - 在 `includes/dbhelp.php` 中添加 `selectPageV2` 方法
    - 支持参数化的分页查询
    - 自动计算 LIMIT 偏移量
    - _Requirements: 1.4_
  - [ ]* 1.8 编写查询方法属性测试
    - **Property 4: SQL 注入防护 - 查询操作**
    - **Validates: Requirements 1.4**

- [x] 2. Checkpoint - 确保数据库类测试通过


  - Ensure all tests pass, ask the user if questions arise.

- [x] 3. 修复公共模块 SQL 注入漏洞



  - [x] 3.1 修复 includes/common.php 中的站点配置查询

    - 将 `$subconf = $DB->selectRow('SELECT * FROM sub_admin WHERE siteurl = "'.$host.'" limit 1')` 改为参数化查询
    - 使用 `selectRowV2` 方法
    - _Requirements: 4.1_
  - [x] 3.2 修复 includes/function.php 中的 WriteLog 函数


    - 将 `$DB->insert('log', $arr)` 改为使用 `insertV2` 方法
    - 移除手动的 `addslashes` 调用
    - _Requirements: 6.1_

  - [x] 3.3 修复 includes/function.php 中的 SerchearchAllServer 函数

    - 将字符串拼接的 SQL 查询改为参数化查询
    - 修复 `$tj = (!empty($app)) ? "where appcode='$app'" : ""` 的注入风险
    - _Requirements: 7.1_
  - [x] 3.4 修复 includes/function.php 中的 DelUser 函数


    - 将服务器查询改为参数化查询
    - _Requirements: 7.2_

- [x] 4. 修复登录模块 SQL 注入漏洞



  - [x] 4.1 修复 sub_admin/login.php 中的登出操作


    - 将 `$where = 'username = "' . $subconf['username'] . '"'` 改为参数化
    - 使用 `updateV2` 方法

    - _Requirements: 5.1_
  - [x] 4.2 修复 sub_admin/login.php 中的登录成功更新

    - 将 cookies 更新操作改为参数化查询
    - _Requirements: 5.2_

- [x] 5. Checkpoint - 确保公共模块和登录模块正常工作


  - Ensure all tests pass, ask the user if questions arise.

- [x] 6. 修复后台管理接口 SQL 注入漏洞 - 第一部分


  - [x] 6.1 修复 getserver 和 getuseserver 操作


    - 将服务器列表查询改为参数化查询
    - _Requirements: 2.1_

  - [x] 6.2 修复 apptable 操作

    - 将应用表格查询改为参数化查询
    - 处理 server 和 appname 搜索条件的参数化
    - _Requirements: 2.2_

  - [x] 6.3 修复 delapp 操作

    - 将应用删除改为使用 `deleteV2` 方法
    - _Requirements: 2.3_
  - [x] 6.4 修复 seldel 操作


    - 将批量删除应用改为使用 `deleteV2` 方法
    - _Requirements: 2.4_

  - [x] 6.5 修复 serverdel 操作

    - 将批量删除服务器改为使用 `deleteV2` 方法
    - _Requirements: 2.5_
  - [x] 6.6 修复 update 操作


    - 将应用更新改为使用 `updateV2` 方法
    - _Requirements: 2.6_

- [x] 7. 修复后台管理接口 SQL 注入漏洞 - 第二部分


  - [x] 7.1 修复 servertable 操作


    - 将服务器表格查询改为参数化查询
    - 处理 ip 和 comment 搜索条件的参数化
    - _Requirements: 2.7_
  - [x] 7.2 修复 newserver 操作


    - 将服务器插入改为使用 `insertV2` 方法
    - _Requirements: 2.7_
  - [x] 7.3 修复 upswitch 操作


    - 将服务器状态更新改为使用 `updateV2` 方法
    - _Requirements: 2.8_

  - [x] 7.4 修复 getkami 操作

    - 将卡密查询改为参数化查询
    - 处理所有搜索条件的参数化
    - _Requirements: 2.9_
  - [x] 7.5 修复 newkami 操作


    - 将卡密插入改为使用 `insertV2` 方法
    - _Requirements: 2.9_

  - [x] 7.6 修复 delkami 操作

    - 将卡密删除改为使用 `deleteV2` 方法
    - _Requirements: 2.10_



- [x] 8. 修复后台管理接口 SQL 注入漏洞 - 第三部分

  - [x] 8.1 修复 updatepwd 操作

    - 将密码更新改为使用 `updateV2` 方法
    - _Requirements: 2.11_

  - [x] 8.2 修复 updateset 操作

    - 将网站设置更新改为使用 `updateV2` 方法
    - _Requirements: 2.12_


  - [x] 8.3 修复 getlog 操作

    - 将日志查询改为参数化查询



    - _Requirements: 2.13_


  - [x] 8.4 修复 editserver 操作

    - 将服务器编辑改为使用 `updateV2` 方法
    - _Requirements: 2.14_
  - [x] 8.5 修复 userupdate 和 adduser 操作中的服务器查询





    - 将服务器信息查询改为参数化查询


    - _Requirements: 2.15, 2.16_
  - [x] 8.6 修复 getuserall 操作中的应用查询


    - 将应用名称查询改为参数化查询
    - _Requirements: 2.15_

- [x] 9. Checkpoint - 确保后台管理接口正常工作


  - Ensure all tests pass, ask the user if questions arise.

- [x] 10. 修复前台 API 接口 SQL 注入漏洞

  - [x] 10.1 修复 checkquery 函数
    - 将应用和服务器查询改为参数化查询
    - _Requirements: 3.1_

  - [x] 10.2 修复 checkinsert 函数
    - 将卡密验证、应用和服务器查询改为参数化查询
    - 将卡密状态更新改为使用 `updateV2` 方法
    - _Requirements: 3.2, 3.4_

  - [x] 10.3 修复 checkupdate 函数

    - 将卡密验证、应用和服务器查询改为参数化查询
    - 将卡密状态更新改为使用 `updateV2` 方法
    - _Requirements: 3.3, 3.4_

- [ ]* 11. 编写功能等价性属性测试
  - **Property 5: 功能等价性 - 数据操作**
  - **Validates: Requirements 8.1, 8.2**

- [ ]* 12. 编写错误处理一致性属性测试
  - **Property 6: 错误处理一致性**
  - **Validates: Requirements 8.3**

- [x] 13. Final Checkpoint - 确保所有测试通过



  - Ensure all tests pass, ask the user if questions arise.
