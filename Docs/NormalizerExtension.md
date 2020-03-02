# Normalizer Extension

This bundle brings the Normalizer Extension. It is a component that behaves like a regular [Normalizer](https://symfony.com/doc/current/serializer/normalizers.html) but acts after the normalization process. The goal is to provide a way to add data to the normalization process without impacting the default behaviour.


## SerializedNameNormalizerExtension

This extension provides support for the `@SerializedName` annotation. You must have the appropriated [configuration](Configuration.md) to use it. This annotation has to be set on a method of an objet like following.

```php
use RichCongress\NormalizerBundle\Serializer\Annotation\SerializedName;

class DummyEntity
{
    /**
     * @SerializedName("data", groups={"dummy_entity_wonderful_data"})
     * 
     * @return array
     */
    public function getWonderfulData(): array
    {
        return ['something'];
    }
}
```


## AbstractObjectNormalizerExtension

This extension lets you add whatever data you want to any object. It is designed to normalize only one object class and can support multiple serialization groups.

The method linked to the serialization group is based on the name of the property that will be set in the normalization. In the following example, `dummy_entity_name` will call `getWonderfulName` and the index `wonderfulName` will have its value in the normalized data. So the method must be prefixed with `get` except for the variable beginning with `is`, `has`, `can` or `does`.

Also note that in the following example, there is no matching method for the serialization group `dummy_entity_database_id`. In this case, the extension will try to find a `getId()` method within the object itself. If it fails, it will throw an error.

```php
use RichCongress\NormalizerBundle\Serializer\Normalizer\Extension\AbstractObjectNormalizerExtension;

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
    
    public function isBeautifulEnought(): bool
    {
        return true;
    }   
    
    public function getWonderfulName(): string
    {
        return 'DummyEntity is its name';
    }   
}
```

## Write your own Normalizer Extension

To write your own Normalizer Extension, create your class and implements the `NormalizerExtensionInterface`.

You need to declare your extension as a service. If you don't autoconfigure your services, please add the `serializer.normalizer.extension` tag. You may also attach a priority to it which will define the order of passage of the extensions, higher priority means earlier execution.
