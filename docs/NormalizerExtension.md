# Normalizer Extension

This bundle brings the Normalizer Extension. It is a component that behaves like a regular [Normalizer](https://symfony.com/doc/current/serializer/normalizers.html) but acts after the normalization process. The goal is to provide a way to add data to the normalization process without impacting the default behaviour.


## AbstractObjectNormalizerExtension

This extension lets you add whatever data you want to any object. It is designed to normalize only one object class and can support multiple serialization groups.

The method linked to the serialization group is based on the name of the property that will be set in the normalization. In the following example, `dummy_entity_name` will call `getWonderfulName` and the index `wonderfulName` will have its value in the normalized data. So the method must be prefixed with `get` except for the variable beginning with `is`, `has`, `can` or `does`.

Also note that in the following example, there is no matching method for the serialization group `dummy_entity_database_id`. In this case, the extension will try to find a `getId()` method within the object itself. If it fails, it will throw an error.

```php
use RichCongress\NormalizerExtensionBundle\Serializer\Normalizer\Extension\AbstractObjectNormalizerExtension;

class DummyEntityNormalizerExtension extends AbstractObjectNormalizerExtension
{
    /**
     * @var string
     */
    public static $objectClass = DummyEntity::class;

    /**
     * Prefix to add before every context
     */
    public static $contextPrefix = 'dummy_entity_';


    /**
     * ['serialization_group' => 'propertyName']
     * 
     * @return array
     */
    public static function getSupportedGroups(): array
    {
        return [
            'is_beautiful_enough' => 'isBeautifulEnough',
            'name'                => 'wonderfulName',
            'database_id'         => 'id',
        ];
    }
    
    public function isBeautifulEnough(): bool
    {
        return true;
    }   
    
    public function getWonderfulName(): string
    {
        return 'DummyEntity is its name';
    }   
}
```

### Stop the normalization

To stop the normalization and then, do not add an entry to the normalized object, you may use the exception `SkipSerializationException`.

```php
public function isBeautifulEnough(): bool
{
    ...
    if ($notConnected) {
        throw new SkipSerializationException('User not connected');
    }
    ...
}   
```

### Batch normalization

If you have to perform a database query or call a rest api to compute the normalized value you may run into performance issues. One way to fix it is to batch similar requests into one. 

First you need to add your computation to a batch with an id:

```php
use RichCongress\NormalizerExtensionBundle\Serializer\Batch\DeferredValue
use RichCongress\NormalizerExtensionBundle\Serializer\Normalizer\Extension\AbstractObjectNormalizerExtension;

class DummyEntityNormalizerExtension extends AbstractObjectNormalizerExtension
{
    public static $objectClass = DummyEntity::class;
    public static $contextPrefix = 'dummy_entity_';

    /** @var DummyBatch */
    protected $dummyBatch;

    public function __construct(DummyBatch $dummyBatch)
    {
        $this->dummyBatch = $dummyBatch;
    }

    public static function getSupportedGroups(): array
    {
        return [
            'batched_value' => 'batchedValue',
        ];
    }

    public function batchedValue(DummyEntity $entity): DeferredValue
    {
        // Return a placeholder value that will be resolved later once we're done adding elements to the batch
        // You provide a key that will be used as cache key and as an id in the batch query.
        return $this->dummyBatch->defer($entity->getId())
    }   
}
```

Second you need to provide the query:

```php
use RichCongress\NormalizerExtensionBundle\Serializer\Batch\AbstractBatch;

class DummyBatch extends AbstractBatch
{
    protected const CACHE_LIFETIME = 'PT1H'; // Here you can define the validity period of the cache (default null)

    /**
     * @param array<array-key> $keys
     *
     * @return array<array-key, mixed>
     */
    public function query(array $keys): array
    {
        // Here you have the list of keys in the batch to resolve.
        // You must return a list of key => value pairs where
        // the key is the batch key and
        // the value is the normalized data for that key.
    }
}
```

## Write your own Normalizer Extension

To write your own Normalizer Extension, create your class and implements the `NormalizerExtensionInterface`.

You need to declare your extension as a service. If you don't autoconfigure your services, please add the `serializer.normalizer.extension` tag. You may also attach a priority to it which will define the order of passage of the extensions, higher priority means earlier execution.
