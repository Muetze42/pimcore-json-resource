<?php

namespace App\Http\Resources;

use ArgumentCountError;
use JsonSerializable;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Listing\Concrete as ConcreteListing;
use Pimcore\Model\DataObject\Localizedfield;
use Pimcore\Tool;
use Symfony\Component\HttpFoundation\Request;

class JsonResource implements JsonSerializable
{
    /**
     * The DataObject instance.
     *
     * @var Concrete|null
     */
    protected ?Concrete $object;

    /**
     * Language for Localizedfield.
     *
     * @var string|null
     */
    protected ?string $language;

    /**
     * Create a new resource instance.
     *
     * @param Concrete|null $object
     * @param string|null   $language
     */
    public function __construct(?Concrete $object, ?string $language = null)
    {
        $this->language = $language ?: Tool::getDefaultLanguage();
        $this->object = $object;
    }

    /**
     * Return an array collection of resources
     *
     * @param ConcreteListing|array $entries
     * @param string|null           $language
     * @return array
     */
    public static function collection(ConcreteListing|array $entries, ?string $language = null): array
    {
        if (!is_array($entries)) {
            $entries = $entries->load();
        }

        // Prevent multiple class Tool call in constructor
        $language = $language ?: Tool::getDefaultLanguage();

        return self::map($entries, (function (Concrete $entry) use ($language) {
            return new static($entry, $language);
        }));
    }

    /**
     * Run a map over each of the items in the array.
     * Source: https://github.com/illuminate/collections/blob/10.x/Arr.php
     *
     * @param array    $array
     * @param callable $callback
     * @return array
     */
    public static function map(array $array, callable $callback): array
    {
        $keys = array_keys($array);

        try {
            $items = array_map($callback, $array, $keys);
        } catch (ArgumentCountError) {
            $items = array_map($callback, $array);
        }

        return array_combine($keys, $items);
    }

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return $this->resourceToArray();
    }

    /**
     * Get resource array by object values without the dao.
     *
     * @return array
     */
    public function resourceToArray(): array
    {
        if (is_null($this->object)) {
            return [];
        }

        $objectVars = $this->object->getObjectVars();

        unset(
            $objectVars['dao'],
            $objectVars['o_class'],
            $objectVars['__objectAwareFields'],
            $objectVars['loadedLazyKeys'],
            $objectVars['____pimcore_cache_item__'],
        );

        return self::map($objectVars, (function ($value) {
            if ($value instanceof Localizedfield) {
                $items = $value->getItems();
                return !empty($items[$this->language]) ? $items[$this->language] : $items;
            }
            return $value;
        }));
    }

    /**
     * Resolve the resource to an array.
     *
     * @param Request|null $request
     * @return array
     */
    public function resolve(?Request $request = null): array
    {
        return $this->toArray($request ?: Request::createFromGlobals());
    }

    /**
     * Prepare the resource for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->resolve();
    }
}
