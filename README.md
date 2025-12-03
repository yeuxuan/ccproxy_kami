# CCProxy 卡密管理系统

<p align="center">
  <img src="https://img.shields.io/badge/version-v2.0.0-blue.svg" alt="Version">
  <img src="https://img.shields.io/badge/PHP-7.3--7.4-purple.svg" alt="PHP Version">
  <img src="https://img.shields.io/badge/MySQL-5.7-orange.svg" alt="MySQL Version">
  <img src="https://img.shields.io/badge/license-MIT-green.svg" alt="License">
</p>

<p align="center">
  一款专为 CCProxy 代理服务器设计的卡密管理系统，支持卡密生成、用户注册、续费充值、在线管理等功能。
</p>

---

## ✨ 功能特性

- 🎫 **卡密管理** - 批量生成、导出、状态追踪
- 👥 **用户管理** - 注册、续费、到期时间查询
- 🖥️ **多服务器支持** - 同时管理多台 CCProxy 服务器
- 📊 **数据统计** - 可视化图表展示运营数据
- 🔐 **安全防护** - SQL 注入防护、CC 攻击防御
- 🚀 **高性能缓存** - 内置缓存机制，提升响应速度
- 📱 **响应式设计** - 完美适配 PC 与移动端

## 📋 环境要求

| 组件 | 版本要求 |
|------|----------|
| PHP | 7.3 - 7.4 |
| MySQL | 5.7+ |
| Web Server | Apache / Nginx |

> ⚠️ **重要提示**: 请严格按照版本要求配置环境，其他版本可能导致兼容性问题。

## 🚀 快速开始

### 安装步骤

1. **上传程序文件**
   ```bash
   # 将程序文件上传至网站根目录
   ```

2. **访问安装向导**
   ```
   http://your-domain.com/install
   ```

3. **按照提示完成配置**
   - 填写数据库连接信息
   - 设置管理员账号
   - 完成安装

### 目录结构

```
├── api/                # API 接口
│   ├── api.php         # 通用 API
│   └── cpproxy.php     # CCProxy 通信接口
├── assets/             # 静态资源
├── includes/           # 核心类库
│   ├── common.php      # 公共引入
│   ├── dbhelp.php      # 数据库助手
│   ├── function.php    # 功能函数
│   └── cache.php       # 缓存处理
├── sub_admin/          # 管理后台
├── config.php          # 配置文件
└── index.php           # 前台入口
```

## 📖 文档

详细使用文档请访问：[官方文档](https://yeuxuan.github.io/ccproxy_kami/#/)

## 📝 更新日志

### v2.0.0 (Latest)
- 🔒 修复 SQL 注入漏洞
- 🛡️ 增强安全防护机制
- 🎨 全新首页 UI 设计
- ⚡ 性能优化，响应更快
- 🔧 重构核心代码架构
- 🐛 修复已知 Bug

### v1.5.2
- 新增卡密时长类型
- 优化域名与 IP 验证
- 后台统计图表动态化
- 移动端适配优化

<details>
<summary>查看更多历史版本</summary>

### v1.5.1
- 修复前端应用显示问题
- 修复用户管理相关 Bug
- 优化安装流程

### v1.5
- 新增连接数、带宽设置
- 卡密扩展参数支持
- 多项 Bug 修复

### v1.4s2
- 数据库 SQL 优化
- 新增 404 页面
- 稳定性提升

</details>

## ⚠️ 升级须知

从 **v1.5.2 之前版本** 升级时：
1. 备份现有数据库
2. 清空数据库并重新安装
3. 旧版卡密将无法使用

## 🤝 贡献

欢迎提交 Issue 和 Pull Request！

## 📄 许可证

本项目基于 [MIT License](LICENSE) 开源。

## 🔗 相关链接

- [GitHub 仓库](https://github.com/yeuxuan/ccproxy_kami)
- [官方文档](https://yeuxuan.github.io/ccproxy_kami/#/)
- [问题反馈](https://github.com/yeuxuan/ccproxy_kami/issues)
- [QQ 交流群](https://qun.qq.com/universal-share/share?ac=1&authKey=gBz74gD3j9lKOyuZlGRo2dtkrzJ6nL3ptnZWff2gGTN0HMLdMUvZUQUuT%2BzC%2BOel&busi_data=eyJncm91cENvZGUiOiI5MjE5NzEwODUiLCJ0b2tlbiI6InFtRi9FY0hoWVhpTTFjbUt3S1FIMGY2dEwza0J1T29laEdleUNSaW02MzVNTXM4cjZLWTl2WFQ4Tm13aVM4b1QiLCJ1aW4iOiI0ODc3MzU5MTMifQ%3D%3D&data=ZWDls6Ls4xuJLwNNQ4iVYcCOaZjH2zSOhdPn2ojllviGh_5kw0AOw5wkVa8QxWTKAFMNSjvpJieWKFZnBA4tuA&svctype=4&tempid=h5_group_info) - Preface 软件交流群

---

<p align="center">
  <a href="https://dartnode.com" title="Powered by DartNode - Free VPS for Open Source">
    <img src="https://dartnode.com/branding/DN-Open-Source-sm.png" alt="Powered by DartNode">
  </a>
</p>

<p align="center">
  Made with ❤️ by <a href="https://github.com/yeuxuan">一花</a>
</p>
