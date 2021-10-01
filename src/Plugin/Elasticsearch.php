<?php declare(strict_types=1);

namespace Itonomy\Flowbox\Plugin;

class Elasticsearch
{
    /**
     * @var \Magento\Framework\Search\Request\Config
     */
    private $config;

    /**
     * Elasticsearch constructor.
     * @param \Magento\Framework\Search\Request\Config $config
     */
    public function __construct(\Magento\Framework\Search\Request\Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param \Magento\Elasticsearch7\Model\Client\Elasticsearch $subject
     * @param array $fields
     * @param string $index
     * @param string $entityType
     * @return array
     */
    public function beforeAddFieldsMapping(
        \Magento\Elasticsearch7\Model\Client\Elasticsearch $subject,
        array $fields,
        string $index,
        string $entityType
    ) : array {
        $mappings = $this->config->get();

        foreach ($mappings as $config) {
            foreach($config['aggregations'] as $aggregation) {
                if (\array_key_exists('field', $aggregation)) {
                    $field = $aggregation['field'];
                    if (\array_key_exists($field, $fields) &&
                        $fields[$field]['type'] === 'text'
                    ) {
                        $fields[$field]['type'] = 'keyword';
                    }
                    if (\array_key_exists($field . '_value', $fields) &&
                        $fields[$field . '_value']['type'] === 'text'
                    ) {
                        $fields[$field]['type'] = 'keyword';
                    }
                }
            }
        }

        return [$fields, $index, $entityType];
    }
}
