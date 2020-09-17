<?php declare(strict_types=1);

namespace Itonomy\Flowbox\Block;

abstract class Base extends \Magento\Framework\View\Element\Template
{
    const XML_PATH_FLOWBOX_ENABLE = 'itonomy_flowbox/general/enable';

    const XML_PATH_FLOWBOX_DEBUG_JS = 'itonomy_flowbox/general/debug_javascript';

    const XML_PATH_API_KEY = 'itonomy_flowbox/general/api_key';

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
     * @return string|null
     */
    public function getApiKey(): ?string
    {
        return $this->_scopeConfig->getValue(static::XML_PATH_API_KEY);
    }

    /**
     * Prepare configuration for javascript component
     *
     * You should set an array 'flowbox' containing configuration, or an array
     * 'errors' containing error messages.
     */
    abstract protected function prepareConfig(): void;

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
