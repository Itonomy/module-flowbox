<?php

namespace Itonomy\Flowbox\Helper;

/**
 * Class Tags
 * @package Itonomy\Flowbox\Helper
 */
class Data
{
    /**
     * @var \Magento\Framework\Escaper
     */
    private \Magento\Framework\Escaper $escaper;

    public function __construct(\Magento\Framework\Escaper $escaper)
    {
        $this->escaper = $escaper;
    }

    public function configToTagArray(string $config, bool $withHashes = true): array
    {
        return array_map(
            $withHashes ? [$this, 'prependHash'] : [$this, 'stripHash']
            , array_filter(
                \explode(
                    ',',
                    \preg_replace('/\s+/', ',', $config)
                ),
                function ($tag) {
                    return !empty($tag);
                }
            )
        );
    }

    public function stripHash(string $tag): string
    {
        return ltrim($this->escaper->escapeHtml($tag),'#');
    }

    public function prependHash(string $tag): string
    {
        return '#' . $this->stripHash($tag);
    }
}
