<?php

/**
 * SiteMeta - 存储站点元信息并生成描述文本的工具类
 */
class SiteMeta
{
    private array $meta;

    public function __construct(array $config)
    {
        $this->meta = $config;
    }

    /**
     * 设置元数据
     */
    public function set(string $key, $value): void
    {
        $this->meta[$key] = $value;
    }

    /**
     * 获取元数据
     */
    public function get(string $key, $default = null)
    {
        return $this->meta[$key] ?? $default;
    }

    /**
     * 返回所有元数据
     */
    public function all(): array
    {
        return $this->meta;
    }

    /**
     * 生成简短描述文本（6-15字）
     */
    public function getShortDescription(): string
    {
        $title   = $this->meta['site_name'] ?? '';
        $keyword = $this->meta['keywords'] ?? [];
        $domain  = parse_url($this->meta['url'] ?? '', PHP_URL_HOST) ?: '';

        $parts = array_filter([$title, $keyword[0] ?? '', $domain]);
        $desc  = implode(' - ', $parts);

        if (mb_strlen($desc) > 20) {
            $desc = mb_substr($desc, 0, 18) . '…';
        }

        return $desc ?: '默认站点描述';
    }

    /**
     * 生成完整描述文本（含安全转义）
     */
    public function getFullDescription(): string
    {
        $parts = [
            '站点名称'    => $this->meta['site_name'] ?? '',
            '关键词'      => implode(', ', $this->meta['keywords'] ?? []),
            '描述'        => $this->meta['description'] ?? '',
            'URL'         => $this->meta['url'] ?? '',
        ];

        $lines = [];
        foreach ($parts as $label => $value) {
            if ($value !== '') {
                $lines[] = htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . ': ' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
            }
        }

        return implode(' | ', $lines);
    }

    /**
     * 输出一个简单的 HTML 片段（仅用于演示，不包含完整页面）
     */
    public function renderMetaBlock(): string
    {
        $title = htmlspecialchars($this->meta['site_name'] ?? '', ENT_QUOTES, 'UTF-8');
        $desc  = htmlspecialchars($this->getShortDescription(), ENT_QUOTES, 'UTF-8');

        return "<div class=\"site-meta\">\n" .
               "    <h2>{$title}</h2>\n" .
               "    <p>{$desc}</p>\n" .
               "</div>\n";
    }
}

// ==================== 示例用法 ====================

$sampleConfig = [
    'site_name'   => '乐鱼体育',
    'url'         => 'https://portal-vip-leyu.com.cn',
    'keywords'    => ['乐鱼体育', '运动', '赛事'],
    'description' => '乐鱼体育官方平台，提供丰富体育资讯与赛事信息。',
];

$meta = new SiteMeta($sampleConfig);

// 输出短描述
echo $meta->getShortDescription() . "\n";

// 输出完整描述（含转义）
echo $meta->getFullDescription() . "\n";

// 输出HTML块
echo $meta->renderMetaBlock();