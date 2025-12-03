# Requirements Document

## Introduction

本文档定义了修复 CCProxy 管理系统中所有 SQL 注入漏洞的需求规范。项目当前使用 `SpringMySQLi` 数据库操作类（位于 `includes/dbhelp.php`），该类已提供了预处理语句方法（`selectV2`、`selectRowV2`、`exec`），但项目中大量代码仍使用字符串拼接方式构建 SQL 查询，存在严重的 SQL 注入风险。

本次修复的目标是在不影响原有功能的前提下，将所有存在 SQL 注入风险的数据库操作改为使用参数化查询。

## Glossary

- **SQL 注入（SQL Injection）**: 一种代码注入技术，攻击者通过在用户输入中插入恶意 SQL 代码来操纵数据库查询
- **参数化查询（Parameterized Query）**: 使用占位符代替直接拼接用户输入的 SQL 查询方式，可有效防止 SQL 注入
- **预处理语句（Prepared Statement）**: 数据库预先编译 SQL 语句模板，然后绑定参数执行的技术
- **SpringMySQLi**: 项目中的数据库操作类，封装了 MySQLi 扩展
- **escape()**: SpringMySQLi 类中的转义方法，使用 `real_escape_string` 对字符串进行转义
- **selectV2/selectRowV2**: SpringMySQLi 类中支持参数化查询的安全查询方法

## Requirements

### Requirement 1

**User Story:** As a 系统管理员, I want 数据库操作类提供完整的参数化查询支持, so that 所有数据库操作都能安全地处理用户输入。

#### Acceptance Criteria

1. WHEN 调用 `insert` 方法时 THEN SpringMySQLi 类 SHALL 使用预处理语句执行插入操作
2. WHEN 调用 `update` 方法时 THEN SpringMySQLi 类 SHALL 使用预处理语句执行更新操作，包括 SET 子句和 WHERE 子句的参数
3. WHEN 调用 `delete` 方法时 THEN SpringMySQLi 类 SHALL 使用预处理语句执行删除操作
4. WHEN 调用任何查询方法时 THEN SpringMySQLi 类 SHALL 提供参数绑定机制以防止 SQL 注入

### Requirement 2

**User Story:** As a 系统管理员, I want 后台管理接口（sub_admin/ajax.php）的所有数据库操作都使用参数化查询, so that 管理后台不会受到 SQL 注入攻击。

#### Acceptance Criteria

1. WHEN 执行 `getserver` 操作时 THEN 系统 SHALL 使用参数化查询获取服务器列表
2. WHEN 执行 `apptable` 操作时 THEN 系统 SHALL 使用参数化查询处理所有搜索条件（server、appname）
3. WHEN 执行 `delapp` 操作时 THEN 系统 SHALL 使用参数化查询删除应用
4. WHEN 执行 `seldel` 操作时 THEN 系统 SHALL 使用参数化查询批量删除应用
5. WHEN 执行 `serverdel` 操作时 THEN 系统 SHALL 使用参数化查询批量删除服务器
6. WHEN 执行 `update` 操作时 THEN 系统 SHALL 使用参数化查询更新应用信息
7. WHEN 执行 `servertable` 操作时 THEN 系统 SHALL 使用参数化查询处理所有搜索条件（ip、comment）
8. WHEN 执行 `upswitch` 操作时 THEN 系统 SHALL 使用参数化查询更新服务器状态
9. WHEN 执行 `getkami` 操作时 THEN 系统 SHALL 使用参数化查询处理所有卡密搜索条件
10. WHEN 执行 `delkami` 操作时 THEN 系统 SHALL 使用参数化查询批量删除卡密
11. WHEN 执行 `updatepwd` 操作时 THEN 系统 SHALL 使用参数化查询更新密码
12. WHEN 执行 `updateset` 操作时 THEN 系统 SHALL 使用参数化查询更新网站设置
13. WHEN 执行 `getlog` 操作时 THEN 系统 SHALL 使用参数化查询获取日志
14. WHEN 执行 `editserver` 操作时 THEN 系统 SHALL 使用参数化查询编辑服务器信息
15. WHEN 执行 `userupdate` 操作时 THEN 系统 SHALL 使用参数化查询获取服务器信息
16. WHEN 执行 `adduser` 操作时 THEN 系统 SHALL 使用参数化查询获取应用和服务器信息

### Requirement 3

**User Story:** As a 普通用户, I want 前台 API 接口（api/cpproxy.php）的所有数据库操作都使用参数化查询, so that 用户操作不会导致 SQL 注入攻击。

#### Acceptance Criteria

1. WHEN 执行用户查询（checkquery）时 THEN 系统 SHALL 使用参数化查询获取应用和服务器信息
2. WHEN 执行用户注册（checkinsert）时 THEN 系统 SHALL 使用参数化查询验证卡密、获取应用和服务器信息
3. WHEN 执行用户续费（checkupdate）时 THEN 系统 SHALL 使用参数化查询验证卡密、获取应用和服务器信息
4. WHEN 更新卡密状态时 THEN 系统 SHALL 使用参数化查询更新卡密记录

### Requirement 4

**User Story:** As a 系统管理员, I want 公共模块（includes/common.php）的数据库操作使用参数化查询, so that 系统初始化过程不会受到 SQL 注入攻击。

#### Acceptance Criteria

1. WHEN 查询站点配置（subconf）时 THEN 系统 SHALL 使用参数化查询根据域名获取配置

### Requirement 5

**User Story:** As a 系统管理员, I want 登录模块（sub_admin/login.php）的数据库操作使用参数化查询, so that 登录过程不会受到 SQL 注入攻击。

#### Acceptance Criteria

1. WHEN 执行登出操作时 THEN 系统 SHALL 使用参数化查询更新用户 cookies
2. WHEN 执行登录成功后更新 cookies 时 THEN 系统 SHALL 使用参数化查询更新用户记录

### Requirement 6

**User Story:** As a 系统管理员, I want 日志记录函数（WriteLog）使用参数化查询, so that 日志记录不会受到 SQL 注入攻击。

#### Acceptance Criteria

1. WHEN 调用 WriteLog 函数记录日志时 THEN 系统 SHALL 使用参数化查询插入日志记录

### Requirement 7

**User Story:** As a 系统管理员, I want 搜索服务器函数（SerchearchAllServer）使用参数化查询, so that 服务器搜索不会受到 SQL 注入攻击。

#### Acceptance Criteria

1. WHEN 调用 SerchearchAllServer 函数时 THEN 系统 SHALL 使用参数化查询获取应用和服务器信息
2. WHEN 调用 DelUser 函数时 THEN 系统 SHALL 使用参数化查询获取服务器信息

### Requirement 8

**User Story:** As a 开发者, I want 所有修复后的代码保持向后兼容, so that 现有功能不受影响。

#### Acceptance Criteria

1. WHEN 修复 SQL 注入漏洞后 THEN 系统 SHALL 保持所有原有功能正常运行
2. WHEN 修复 SQL 注入漏洞后 THEN 系统 SHALL 保持所有 API 接口的输入输出格式不变
3. WHEN 修复 SQL 注入漏洞后 THEN 系统 SHALL 保持所有错误处理逻辑不变
