<?php

/**
 * Copyright © Itonomy BV. All rights reserved.
 * See LICENSE.md for license details.
 */
namespace Itonomy\Flowbox\Block\Checkout\Onepage;

use Itonomy\Flowbox\Helper\Data;
use Magento\Checkout\Model\Session;
use Magento\Cookie\Helper\Cookie;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\View\Element\Template\Context;

class Success extends \Itonomy\Flowbox\Block\Base
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var Data
     */
    private $helper;

    /**
     * Success constructor.
     * @param Context $context
     * @param EncryptorInterface $encryptor
     * @param Cookie $cookie
     * @param Session $checkoutSession
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        EncryptorInterface $encryptor,
        Cookie $cookie,
        Session $checkoutSession,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $cookie, $encryptor, $data);
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @inheritDoc
     */
    protected function prepareConfig(): void
    {
        try {
            $order = $this->checkoutSession->getLastRealOrder();

            $this->setData('flowbox', [
                'allowCookies' => $this->isUserAllowSaveCookies(),
                'apiKey' => (string) $this->getApiKey(),
                'debug' => $this->isDebugJavaScript(),
                'orderId' => \ltrim($order->getIncrementId(), '#'),
                'products' => \array_map(
                    function ($item){
                        return [
                            'id' => (string) $item->getData($this->helper->getAttributeCode()),
                            'quantity' => (int) $item->getQtyOrdered()
                        ];
                    },
                    $this->getAllVisibleItems($order)
                ),
            ]);
        } catch (\Exception $e) {
            $errorMessage = $this->escapeHtml(
                (string) __(
                    '%flowbox: could not compile configuration: %error',
                    ['flowbox' => 'Flowbox', 'error' => $e->getMessage()]
                )
            );
            $this->addError($errorMessage);
            $this->_logger->error($errorMessage, ['exception' => $e]);
        }
    }
    /**
     * Retrieves visible products of the order, omitting its children (yes, this is different than Magento's method)
     * @param Magento\Sales\Model\Order $order
     *
     * @return array
     */
    protected function getAllVisibleItems($order)
    {
        $items = [];
        foreach ($order->getItems() as $item) {
            if (!$item->isDeleted() && !$item->getParentItem()) {
                $items[] = $item;
            }
        }
        return $items;
    }
}
