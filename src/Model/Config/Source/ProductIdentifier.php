<?php


namespace Itonomy\Flowbox\Model\Config\Source;

class ProductIdentifier implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Stock Keeping Unit
     */
    public const SKU = 'sku';
    /**
     * Global Trade Item Number
     */
    public const EAN = 'ean';
    /**
     * Global Trade Item Number
     */
    public const GTIN = 'gtin';
    /**
     * Manufacturer Part Number
     */
    public const MPN = 'mpn';
    /**
     * Custom product identifier
     */
    public const CUSTOM = 'custom';

    /**
     * @return \string[][]
     */
    public function toOptionArray(): array
    {
        return [
            ['label' => 'SKU', 'value' => self::SKU],
            ['label' => 'EAN', 'value' =>  self::EAN],
            ['label' => 'GTIN', 'value' =>  self::GTIN],
            ['label' => 'MPN', 'value' => self::MPN],
            ['label' => 'Custom', 'value' => self::CUSTOM],
        ];
    }
}
