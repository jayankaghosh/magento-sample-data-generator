<?php

namespace MagentoSampleDataGenerator;

use Symfony\Component\Dotenv\Dotenv;

class Generator
{

    private DataSetReader $dataSetReader;

    public function __construct(
        private readonly string $projectRoot
    )
    {
        $dotenv = new Dotenv();
        $dotenv->load($this->projectRoot . '/.env');
        $this->dataSetReader = new DataSetReader(
            sprintf('%s/%s', $this->projectRoot, $_ENV['DATA_SET_PATH'])
        );
    }

    public function generate(int $count): array
    {
        $rows = [];
        while (count($rows) < $count) {
            $name = $this->dataSetReader->getRandomString($this->getRandomFormat($this->getNameFormats()));
            $sku = str_replace(
                [' ', '.', '/', '\\'],
                '-',
                strtolower($name)
            );
            $sku = substr($sku, 0, 50);
            $sku = $sku . '-' . strtotime('now');
            if (in_array($sku, array_keys($rows))) {
                continue;
            }
            $image = $this->dataSetReader->getRandomImage();
            $price = rand(50, 45000);
            $data = [
                'product_type' => 'simple',
                'attribute_set_code' => 'Default',
                'categories' => 'Default Category/Imaginary Products',
                'product_websites' => 'base',
                'visibility' => 'Catalog, Search',
                'sku' => $sku,
                'url_key' => $sku,
                'name' => $name,
                'description' => str_replace('{{product_name}}', $name, $this->dataSetReader->getRandomString('{{descriptions}}')),
                'image' => $image,
                'small_image' => $image,
                'thumbnail' => $image,
                'price' => $price,
                'special_price' => rand(0, 1) ? ($price - rand(1, floor($price/2))): '',
                'qty' => 50000,
                'is_in_stock' => 1
            ];
            $additionalAttributes = $this->dataSetReader->getAdditionalAttributes();
            foreach ($additionalAttributes as $additionalAttribute) {
                $attribute = pathinfo($additionalAttribute, PATHINFO_FILENAME);
                $value = $this->dataSetReader->getRandomValue('attributes/' . $attribute);
                $data[$attribute] = $value;
            }
            $rows[$sku] = $data;
        }
        return array_values($rows);
    }

    protected function getRandomFormat(array $formats): string
    {
        return $formats[array_rand($formats)];
    }

    protected function getNameFormats(): array
    {
        return [
            '{{adjectives}} {{names}} {{nouns}} {{numbers}}{{units}}',
            '{{numbers}}{{units}} {{names}} {{nouns}}',
            '{{names}} {{adjectives}} {{nouns}}',
            '{{adjectives}} {{nouns}} by {{names}}',
        ];
    }
}