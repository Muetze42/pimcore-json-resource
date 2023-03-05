<?php

namespace App\Http\Resources;

use Symfony\Component\HttpFoundation\Request;

/**
 * @property \Pimcore\Model\DataObject\Car $object
 * @property string|null                   $language
 */
class CarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request); // Default output: Object values without the dao
        return [
            'name'  => $this->object->getName(),
            'color' => $this->object->getColor($this->language),
        ];
    }
}
