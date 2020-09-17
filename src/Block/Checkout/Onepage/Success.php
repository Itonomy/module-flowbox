<?php declare(strict_types=1);

/**
 * Copyright © Itonomy BV. All rights reserved.
 * See LICENSE.md for license details.
 */

namespace Itonomy\Flowbox\Block\Checkout\Onepage;

class Success extends \Itonomy\Flowbox\Block\Base
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        \Magento\Framework\View\Element\Template::__construct($context, $data);
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
                'debug' => $this->isDebugJavaScript(),
                'apiKey' => (string) $this->getApiKey(),
                'orderId' => \ltrim($order->getIncrementId(), '#'),
                'products' => \array_map(
                    function ($item) {
                        return [
                            'id' => (string) $item->getSku(),
                            'quantity' => (int) $item->getQtyOrdered()
                        ];
                    },
                    $order->getItems()
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
}
