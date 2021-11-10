<?php

namespace Itonomy\Flowbox\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Data extends AbstractHelper
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getAttributeCode(): string
    {
        return $this->scopeConfig->getValue(
            'itonomy_flowbox/general/checkout_identifier',
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }
}
