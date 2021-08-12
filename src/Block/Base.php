<?php declare(strict_types=1);

/**
 * Copyright © Itonomy BV. All rights reserved.
 * See LICENSE.md for license details.
 */

namespace Itonomy\Flowbox\Block;

/**
 * Class Base
 * @package Itonomy\Flowbox\Block
 */
abstract class Base extends \Magento\Framework\View\Element\Template
{
    const XML_CONFIG_ENABLE = 'itonomy_flowbox/general/enable';

    const XML_CONFIG_THIRD_PARTY_COOKIE_MGR = 'itonomy_flowbox/general/third_party_cookie_mgr';

    const XML_CONFIG_DEBUG_JAVASCRIPT = 'itonomy_flowbox/general/debug_javascript';

    const XML_CONFIG_API_KEY = 'itonomy_flowbox/general/api_key';

    const XML_CONFIG_PRODUCT_ID_ATTR = 'itonomy_flowbox/general/product_id_attribute';

    const XML_CONFIG_PRODUCT_ID_ATTR_CUSTOM = 'itonomy_flowbox/general/product_id_attribute_custom';

    const XML_CONFIG_TAGBAR_INPUT_TYPE = 'itonomy_flowbox/general/tagbar_input_type';

    const XML_CONFIG_SHOW_HASHES = 'itonomy_flowbox/general/show_hashes';


    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    private $encryptor;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var array
     */
    private $errors = [];
    /**
     * @var \Magento\Cookie\Helper\Cookie
     */
    private $cookieHelper;
    /**
     * @var \Itonomy\Flowbox\Helper\Data
     */
    protected $dataHelper;

    /**
     * Base constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Cookie\Helper\Cookie $cookieHelper
     * @param \Itonomy\Flowbox\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Cookie\Helper\Cookie $cookieHelper,
        \Itonomy\Flowbox\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->cookieHelper = $cookieHelper;
        $this->encryptor = $encryptor;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return bool
     */
    public function isFlowboxEnabled(): bool
    {
        return $this->_scopeConfig->isSetFlag(self::XML_CONFIG_ENABLE);
    }

    /**
     * @return string
     */
    public function getJsConfig(): string
    {
        $errors = $this->getErrors();
        if (\count($errors)) {
            $this->setData('errors', $errors);
        }
        return $this->toJson(['flowbox', 'errors']);
    }

    /**
     * Checks if user is allowed to save cookies
     *
     * True if any of the following conditions are met:
     *  - A third party cookie manager is in use;
     *  - Magento Cookie Restriction Mode is disabled or it allows the user to
     *    save cookies.
     *
     * @return bool
     */
    protected function isUserAllowSaveCookie(): bool
    {
        return ($this->_scopeConfig->isSetFlag(self::XML_CONFIG_THIRD_PARTY_COOKIE_MGR)
            || false === $this->cookieHelper->isUserNotAllowSaveCookie());
    }

    /**
     * Return product identifier
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getProductIdentifier(): ?string
    {
        // Return product ID attribute value for currently viewed product
        return $this->getProductIdAttributeValue(
            $this->getProductIdAttribute(),
            $this->getRequest()->getParam('id')
        );
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getProduct(): \Magento\Catalog\Api\Data\ProductInterface
    {
        return $this->productRepository->getById(
            $this->getRequest()->getParam('id')
        );
    }

    /**
     * @return bool
     */
    protected function isDebugJavaScript(): bool
    {
        return $this->_scopeConfig->isSetFlag(self::XML_CONFIG_DEBUG_JAVASCRIPT);
    }

    /**
     * @return string|null
     */
    protected function getApiKey(): ?string
    {
        return $this->encryptor->decrypt(
            $this->_scopeConfig->getValue(self::XML_CONFIG_API_KEY)
        );
    }

    /**
     * @return string|null
     */
    protected function getProductIdAttribute(): ?string
    {
        $value = $this->_scopeConfig->getValue(self::XML_CONFIG_PRODUCT_ID_ATTR);
        if ($value === \Itonomy\Flowbox\Model\Config\Source\ProductIdentifier::CUSTOM) {
            $value = $this->_scopeConfig->getValue(self::XML_CONFIG_PRODUCT_ID_ATTR_CUSTOM);
        }
        return $value;
    }

    /**
     * @param string $message
     */
    protected function addError(string $message): void
    {
        $this->errors[] = $message;
    }

    /**
     * @return string[]
     */
    protected function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param $attributeCode
     * @param $productId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProductIdAttributeValue($attributeCode, $productId): string
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('entity_id', $productId)->setPageSize(1);

        $products = $this->productRepository->getList(
            $searchCriteria->create()
        )->getItems();

        $product = \reset($products);
        if (!$product instanceof \Magento\Catalog\Api\Data\ProductInterface) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('No product found with entity_id=%product_id', ['product_id' => $productId])
            );
        }

        $value = (string) $product->getData($attributeCode);
        if (empty($value)) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __(
                    'Attribute with attribute_code=%attribute_code not set on product with entity_id=%product_id',
                    ['attribute_code' => $attributeCode, 'product_id' => $productId]
                )
            );
        }

        return $value;
    }
}
