<?php declare(strict_types=1);

namespace RichCongress\NormalizerBundle\DependencyInjection\CompilerPass;

use RichCongress\NormalizerBundle\DependencyInjection\RichCongressNormalizerExtension;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SerializerPass extends \Symfony\Component\Serializer\DependencyInjection\SerializerPass
{
    public const NORMALIZER_EXTENSION_TAG = 'serializer.normalizer.extension';

    use PriorityTaggedServiceTrait;

    /**
     * @var string
     */
    protected $serializerService;

    /**
     * @var string
     */
    protected $normalizerTag;

    /**
     * @var string
     */
    protected $encoderTag;

    /**
     * @var string
     */
    protected $extensionTag;

    public function __construct(
        string $serializerService = RichCongressNormalizerExtension::SERIALIZER_SERVICE,
        string $normalizerTag = 'serializer.normalizer',
        string $encoderTag = 'serializer.encoder',
        string $extensionTag = self::NORMALIZER_EXTENSION_TAG
    )
    {
        parent::__construct($serializerService, $normalizerTag, $encoderTag);

        $this->serializerService = $serializerService;
        $this->normalizerTag = $normalizerTag;
        $this->encoderTag = $encoderTag;
        $this->extensionTag = $extensionTag;
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function process(ContainerBuilder $container): void
    {
        parent::process($container);

        $extensions = $this->findAndSortTaggedServices($this->extensionTag, $container);

        $serializerDefinition = $container->getDefinition($this->serializerService);
        $serializerDefinition->replaceArgument(2, $extensions);
    }
}
