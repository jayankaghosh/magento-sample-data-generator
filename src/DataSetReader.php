<?php

namespace MagentoSampleDataGenerator;

class DataSetReader
{

    public function __construct(
        private readonly string $dataSetPath
    )
    {
    }

    public function getRandomImage(): string
    {
        $images = glob($this->dataSetPath . '/images/*');
        $image = (string)$images[array_rand($images)];
        return basename($image);
    }

    public function getRandomString(string $format): string
    {
        $string = $format;
        $availableDataSets = $this->getAvailableDataSets();
        foreach ($availableDataSets as $availableDataSet) {
            $filename = pathinfo($availableDataSet, PATHINFO_FILENAME);
            $value = $this->getRandomValue($filename);
            if ($value) {
                $string = str_replace(sprintf('{{%s}}', $filename), $value, $string);
            }
        }
        return $string;
    }

    public function getRandomValue(string $dataset): ?string
    {
        $fileName =  sprintf('%s/%s.json', $this->dataSetPath, $dataset);
        if (file_exists($fileName)) {
            $data = \json_decode(\file_get_contents($fileName), true) ?? null;
            if ($data) {
                return (string)$data[array_rand($data)];
            }
        }
        return null;
    }

    public function getAvailableDataSets(): array
    {
        return (array)glob($this->dataSetPath . '/*.json');
    }

    public function getAdditionalAttributes(): array
    {
        return (array)glob(sprintf('%s/attributes/*.json', $this->dataSetPath));
    }
}