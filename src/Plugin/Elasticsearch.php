<?php declare(strict_types=1);

namespace Itonomy\Flowbox\Plugin;

class Elasticsearch
{
    /**
     * Whether to translate 'text' to 'keyword' field type for elasticsearch
     */
    const XML_CONFIG_ELASTIC_USE_KEYWORD_FIELD_TYPE = 'itonomy_flowbox/elasticsearch/use_keyword_fieldtype';

    /**
     * @var \Magento\Framework\Search\Request\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\Search\Request\Config $config,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) : void {
        $this->config = $config;
        $this->scopeConfig = $scopeConfig;
    }

    private function isElasticUseKeywordFieldType(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_CONFIG_ELASTIC_USE_KEYWORD_FIELD_TYPE);
    }

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
