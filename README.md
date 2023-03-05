# API Resources Pimcore

* _Snipped_
* Base resource class: [src/Http/Resources/JsonResource.php](src/Http/Resources/JsonResource.php)
* Example resource class: [src/Http/Resources/CarResource.php](src/Http/Resources/CarResource.php)

## Examples with `Car`

### Return single resource

```php
use Pimcore\Model\DataObject\Car;
use App\Http\Resources\CarResource;

$item = Car::getById(12);
$resource = new CarResource($item);

return $this->json($resource);
```

Example Response

```json
{
    "name": "Skoda Roomster",
    "color": "Blue"
}
```

### Return an array collection of resources

```php
use Pimcore\Model\DataObject\Car\Listing;
use App\Http\Resources\CarResource;

$entries = new Listing;
$collection = CarResource::collection($entries);

return $this->json($collection);
```

Example Response

```json
[
    {
        "name": "Skoda Roomster",
        "color": "Blue"
    },
    {
        "name": "VW Golf II",
        "color": "Yellow"
    },
    {
        "name": "BMW iX M60",
        "color": "Green"
    }
]
```

### Localized

```php
use App\Http\Resources\CarResource;

$resource = new CarResource($item, $language);
$collection = CarResource::collection($entries, $language);
```
