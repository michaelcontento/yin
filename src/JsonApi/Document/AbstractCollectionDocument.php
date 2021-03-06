<?php
declare(strict_types=1);

namespace WoohooLabs\Yin\JsonApi\Document;

use Traversable;
use WoohooLabs\Yin\JsonApi\Schema\Data\CollectionData;
use WoohooLabs\Yin\JsonApi\Schema\Data\DataInterface;
use WoohooLabs\Yin\JsonApi\Transformer\ResourceTransformerInterface;
use WoohooLabs\Yin\JsonApi\Transformer\Transformation;

abstract class AbstractCollectionDocument extends AbstractSuccessfulDocument
{
    /**
     * @var ResourceTransformerInterface
     */
    protected $transformer;

    /**
     * @param ResourceTransformerInterface $transformer
     */
    public function __construct(ResourceTransformerInterface $transformer)
    {
        $this->transformer = $transformer;
    }

    protected function createData(): DataInterface
    {
        return new CollectionData();
    }

    protected function hasItems(): bool
    {
        return empty($this->getItems()) === false;
    }

    /**
     * @return array|Traversable
     */
    protected function getItems()
    {
        return $this->domainObject;
    }

    protected function fillData(Transformation $transformation)
    {
        foreach ($this->getItems() as $item) {
            $transformation->data->addPrimaryResource($this->transformer->transformToResource($transformation, $item));
        }
    }

    protected function getRelationshipMember(
        string $relationshipName,
        Transformation $transformation,
        array $additionalMeta = []
    ): array {
        if ($this->hasItems() === false) {
            return [];
        }

        $result = [];
        foreach ($this->getItems() as $item) {
            $result[] = $this->transformer->transformRelationship(
                $relationshipName,
                $transformation,
                $item,
                $additionalMeta
            );
        }

        return $result;
    }
}
