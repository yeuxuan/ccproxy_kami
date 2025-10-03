<?php
/*
 * @Author: yihua
 * @Date: 2025-01-05 11:25:08
 * @LastEditTime: 2025-01-05 11:26:12
 * @LastEditors: yihua
 * @Description: 
 * @FilePath: \ccproxy_end\includes\cache.php
 * 💊物物而不物于物，念念而不念于念🍁
 * Copyright (c) 2025 by yihua, All Rights Reserved. 
 */


/**
 * 缓存类
 * 使用文件系统实现的缓存机制，支持自动过期、垃圾回收和调试
 */
class Cache
{
    private static $instance = null;
    private $cache_dir;
    private $enabled = true;
    private $gc_probability = 100; // 垃圾回收概率：1/100
    private $file_prefix = 'cache_';
    private $default_ttl = 300; // 默认缓存时间：5分钟

    /**
     * 私有构造函数，初始化缓存目录
     */
    private function __construct()
    {
        $this->cache_dir = dirname(__FILE__) . '/../cache/';
        if (!file_exists($this->cache_dir)) {
            if (!@mkdir($this->cache_dir, 0777, true)) {
                throw new Exception('Failed to create cache directory');
            }
        }
        
        // 随机执行垃圾回收
        if (mt_rand(1, $this->gc_probability) === 1) {
            $this->garbageCollect();
        }
    }

    /**
     * 获取缓存实例（单例模式）
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * 调试方法，返回缓存状态和内容
     */
    public function debug()
    {
        $files = glob($this->cache_dir . $this->file_prefix . '*');
        $cache_content = [];
        $total_size = 0;
        
        foreach ($files as $file) {
            $key = str_replace($this->file_prefix, '', basename($file));
            $content = $this->get($key);
            if ($content !== null) {
                $cache_content[$key] = [
                    'data' => $content,
                    'size' => filesize($file),
                    'modified' => date('Y-m-d H:i:s', filemtime($file))
                ];
                $total_size += filesize($file);
            }
        }

        return [
            'enabled' => $this->enabled,
            'cache_dir' => $this->cache_dir,
            'cache_count' => count($cache_content),
            'total_size' => $this->formatSize($total_size),
            'cache_content' => $cache_content
        ];
    }

    /**
     * 获取缓存内容
     */
    public function get($key)
    {
        if (!$this->enabled || !$this->validateKey($key)) {
            error_log("Cache disabled or invalid key: " . $key);
            return null;
        }

        $cache_file = $this->getCacheFilePath($key);
        if (file_exists($cache_file)) {
            try {
                $data = unserialize(file_get_contents($cache_file));
                if ($data && isset($data['expires']) && $data['expires'] > time()) {
                    error_log("Cache hit for key: " . $key);
                    return $data['data'];
                }
                // 删除过期缓存
                @unlink($cache_file);
            } catch (Exception $e) {
                error_log("Cache read error: " . $e->getMessage());
                @unlink($cache_file);
            }
        }
        error_log("Cache miss for key: " . $key);
        return null;
    }

    /**
     * 设置缓存内容
     */
    public function set($key, $value, $ttl = null)
    {
        if (!$this->enabled || !$this->validateKey($key)) {
            error_log("Cache set failed - disabled or invalid key");
            return false;
        }

        $ttl = $ttl ?? $this->default_ttl;
        $cache_file = $this->getCacheFilePath($key);
        $data = [
            'data' => $value,
            'expires' => time() + $ttl
        ];

        try {
            if (file_put_contents($cache_file, serialize($data), LOCK_EX) === false) {
                throw new Exception("Failed to write cache file");
            }
            error_log("Cache set successfully for key: " . $key . " with TTL: " . $ttl);
            return true;
        } catch (Exception $e) {
            error_log("Cache write error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 检查缓存是否存在且有效
     */
    public function has($key)
    {
        return $this->get($key) !== null;
    }

    /**
     * 删除指定缓存
     */
    public function delete($key)
    {
        if (!$this->validateKey($key)) {
            return false;
        }
        $cache_file = $this->getCacheFilePath($key);
        return file_exists($cache_file) ? @unlink($cache_file) : false;
    }

    /**
     * 清除所有缓存
     */
    public function clear()
    {
        $files = glob($this->cache_dir . $this->file_prefix . '*');
        $success = true;
        foreach ($files as $file) {
            if (!@unlink($file)) {
                $success = false;
                error_log("Failed to delete cache file: " . $file);
            }
        }
        return $success;
    }

    /**
     * 禁用缓存
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * 启用缓存
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * 垃圾回收
     */
    private function garbageCollect()
    {
        $files = glob($this->cache_dir . $this->file_prefix . '*');
        foreach ($files as $file) {
            if (filemtime($file) < time() - 86400) { // 清理超过24小时的文件
                @unlink($file);
            }
        }
    }

    /**
     * 验证缓存键名
     */
    private function validateKey($key)
    {
        return is_string($key) && strlen($key) <= 255 && preg_match('/^[a-zA-Z0-9_.-]+$/', $key);
    }

    /**
     * 获取缓存文件路径
     */
    private function getCacheFilePath($key)
    {
        return $this->cache_dir . $this->file_prefix . md5($key);
    }

    /**
     * 格式化文件大小
     */
    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), 2) . ' ' . $units[$pow];
    }

    // 防止克隆
    private function __clone() {}

    // 防止反序列化
    private function __wakeup() {}
}