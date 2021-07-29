<?php

namespace Itonomy\Flowbox\Model\Config\Source;

/**
 * Class TagBarInputType
 * @package Itonomy\Flowbox\Model\Config\Source
 */
class TagBarInputType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Radio buttons
     */
    const RADIO = 'radio';
    /**
     * Checkbox
     */
    const CHECKBOX = 'checkbox';

    /**
     * @return \string[][]
     */
    public function toOptionArray(): array
    {
        return [
            ['label' => 'Radio buttons', 'value' => self::RADIO],
            ['label' => 'Checkbox', 'value' =>  self::CHECKBOX]
        ];
    }
}
