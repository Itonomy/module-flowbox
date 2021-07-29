<?php declare(strict_types=1);

/**
 * Copyright © Itonomy BV. All rights reserved.
 * See LICENSE.md for license details.
 */

namespace Itonomy\Flowbox\Block\Widget;

class Flow extends \Itonomy\Flowbox\Block\Base implements \Magento\Widget\Block\BlockInterface
{
    /**
     * Static Flow
     */
    const FLOW_TYPE_STATIC = 'static';
    /**
     * Dynamic Product Flow
     */
    const FLOW_TYPE_DYNAMIC_PRODUCT = 'dynamic-product';
    /**
     * Dynamic Tag Flow
     */
    const FLOW_TYPE_DYNAMIC_TAG = 'dynamic-tag';

    /**
     * @var string
     */
    protected $_template = "widget/flow.phtml";

    public function getJsConfig(): string
    {
        try {
            $flow = $this->getFlow();

            $config = [
                'allowCookies' => $this->isUserAllowSaveCookie(),
                'debug' => $this->isDebugJavaScript(),
                'flow' => $flow,
                'key' => $this->_escaper->escapeHtml((string) $this->getData('key')),
                'lazyload' => (bool) $this->getData('lazyload'),
                'locale' => (string) $this->pageConfig->getElementAttribute('html', 'lang')
            ];

            if ($flow === static::FLOW_TYPE_DYNAMIC_TAG) {
                $config['tags'] = $this->getTags();
                $config['tagsOperator'] = $this->getTagsOperator();
                $config['showTagBar'] = (bool) $this->getData('show_tag_bar');
                $config['tagInputType'] = $this->getTagInputType();
            } elseif ($flow === static::FLOW_TYPE_DYNAMIC_PRODUCT) {
                if ($this->getProduct()->getData('is_flowbox_product')) {
                    $config['productId'] = $this->getProductIdentifier();
                }
            }

            $this->setData('flowbox', $config);
        } catch (\Exception $e) {
            $errorMessage = $this->_escaper->escapeHtml(
                (string) __(
                    '%flowbox: could not compile configuration: %error',
                    ['flowbox' => 'Flowbox', 'error' => $e->getMessage()]
                )
            );
            $this->addError($errorMessage);
            $this->_logger->error($errorMessage, ['exception' => $e]);
        }
        return parent::getJsConfig();
    }

    /**
     * Return flow identifier
     * @return string
     */
    private function getFlow(): string
    {
        if (!$this->hasData('flow')) {
            $this->setData('flow', self::FLOW_TYPE_STATIC);
        }
        $flow = (string) $this->getData('flow');

        return $this->_escaper->escapeHtml($flow);
    }

    private function getTagInputType(): string
    {
        return $this->_scopeConfig->getValue(self::XML_CONFIG_TAGBAR_INPUT_TYPE);
    }

    /**
     * Return array of tags
     * @return string[]
     */
    private function getTags(): array
    {
        return $this->dataHelper->configToTagArray(
            (string) $this->getData('tags'),
            $this->_scopeConfig->isSetFlag(static::XML_CONFIG_SHOW_HASHES)
        );
    }

    /**
     * @return string
     */
    private function getTagsOperator(): string
    {
        return (string) ($this->getData('tags_operator') ?: 'any');
    }
}
