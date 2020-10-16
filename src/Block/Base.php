<?php declare(strict_types=1);

/**
 * Copyright © Itonomy BV. All rights reserved.
 * See LICENSE.md for license details.
 */

namespace Itonomy\Flowbox\Block;

abstract class Base extends \Magento\Framework\View\Element\Template
{
    const XML_PATH_FLOWBOX_ENABLE = 'itonomy_flowbox/general/enable';

    const XML_PATH_FLOWBOX_DEBUG_JS = 'itonomy_flowbox/general/debug_javascript';

    const XML_PATH_API_KEY = 'itonomy_flowbox/general/api_key';

    const FLOW_TYPE_DEFAULT = 'default';

    const FLOW_TYPE_DYNAMIC_PRODUCT = 'dynamic-product';

    const FLOW_TYPE_DYNAMIC_TAG = 'dynamic-tag';

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;

    /**
     * Base constructor.
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\View\Element\Template $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\View\Element\Template $context,
        array $data = []
    ) {
        \Magento\Framework\View\Element\Template::__construct($context, $data);
        $this->encryptor = $encryptor;
    }

    /**
     * @return bool
     */
    public function isFlowboxEnabled(): bool
    {
        return $this->_scopeConfig->isSetFlag(static::XML_PATH_FLOWBOX_ENABLE);
    }

    /**
     * @return bool
     */
    public function isDebugJavaScript(): bool
    {
        return $this->_scopeConfig->isSetFlag(static::XML_PATH_FLOWBOX_DEBUG_JS);
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->encryptor->decrypt(
            $this->_scopeConfig->getValue(static::XML_PATH_API_KEY)
        );
    }

    /**
     * Return json configuration for javascript component
     * @return string
     */
    public function getJsConfig(): string
    {
        if (!$this->hasData('flowbox')) {
            $this->unsetData('errors');
            $this->prepareConfig();
        }
        if ($this->hasData('errors')) {
            return $this->toJson(['errors']);
        }
        return $this->toJson(['flowbox']);
    }

    /**
     * Prepare configuration for javascript component
     *
     * You should set an array 'flowbox' containing configuration, or an array
     * 'errors' containing error messages.
     */
    abstract protected function prepareConfig(): void;

    /**
     * @param string $message
     */
    protected function addError(string $message): void
    {
        if (!$this->hasData('errors')) {
            $this->_data['errors'] = [];
        }
        $this->_data['errors'][] = $message;
    }
}
