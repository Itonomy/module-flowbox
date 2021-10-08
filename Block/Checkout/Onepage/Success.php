<?php declare(strict_types=1);

/**
 * Copyright © Itonomy BV. All rights reserved.
 * See LICENSE.md for license details.
 */

namespace Itonomy\Flowbox\Block\Checkout\Onepage;

class Success extends \Itonomy\Flowbox\Block\Base
{
    private \Magento\Checkout\Model\Session $checkoutSession;

    /**
     * Success constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Cookie\Helper\Cookie $cookie
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Cookie\Helper\Cookie $cookie,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ) {
        parent::__construct($context, $cookie, $encryptor, $data);
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
                    function ($item) {
                        return [
                            'id' => (string) $item->getSku(),
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
