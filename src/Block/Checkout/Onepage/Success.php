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
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Cookie\Helper\Cookie $cookieHelper,
        \Itonomy\Flowbox\Helper\Data $dataHelper,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $encryptor,
            $productRepository,
            $searchCriteriaBuilder,
            $cookieHelper,
            $dataHelper,
            $data
        );

        $this->checkoutSession = $checkoutSession;
    }

    public function getJsConfig(): string
    {
        try {
            $order = $this->checkoutSession->getLastRealOrder();
            $productIdAttribute = $this->getProductIdAttribute();

            $this->setData(
                'flowbox',
                [
                    'alowCookies' => $this->isUserAllowSaveCookie(),
                    'debug' => $this->isDebugJavaScript(),
                    'apiKey' => (string) $this->getApiKey(),
                    'orderId' => \ltrim($order->getIncrementId(), '#'),
                    'products' => \array_map(
                        function ($item) use ($productIdAttribute) {
                            return [
                                'id' => (string) $item->getData($productIdAttribute),
                                'quantity' => (int) $item->getQtyOrdered()
                            ];
                        },
                        $this->getAllVisibleOrderItems($order)
                    ),
                ]
            );
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
     * Retrieves visible products of the order, omitting its children (yes, this is different than Magento's method)
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    protected function getAllVisibleOrderItems(\Magento\Sales\Model\Order $order): array
    {
        $productIdAttribute = $this->getProductIdAttribute();

        $items = [];
        foreach ($order->getItems() as $item) {
            /** @var \Magento\Sales\Model\Order\Item $item */
            if (!$item->isDeleted() && !$item->getParentItem()) {
                $productIdAttributeValue = $item->getProduct()->getAttributeText($productIdAttribute);
                $item->setData($productIdAttribute, $productIdAttributeValue);

                $items[] = $item;
            }
        }

        return $items;
    }
}
