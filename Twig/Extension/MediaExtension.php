<?php
/**
 * @author jgn
 * @date 07/03/2016
 * @description For ...
 */

namespace Donjohn\MediaBundle\Twig\Extension;


use Donjohn\MediaBundle\Model\Media;
use Donjohn\MediaBundle\Provider\Exception\NotFoundProviderException;
use Donjohn\MediaBundle\Provider\Factory\ProviderFactory;
use Donjohn\MediaBundle\Twig\TokenParser\DownloadTokenParser;
use Donjohn\MediaBundle\Twig\TokenParser\MediaTokenParser;
use Donjohn\MediaBundle\Twig\TokenParser\PathTokenParser;
use Symfony\Component\Routing\RouterInterface;


class MediaExtension extends \Twig_Extension
{
    /**
     * @var ProviderFactory $providerFactory
     */
    protected $providerFactory;

    /**
     * @var \Twig_Environment $twig
     */
    protected $twig;

    /**
     * @var RouterInterface $router
     */
    protected $router;

    /**
     * MediaExtension constructor.
     * @param ProviderFactory $providerFactory
     * @param \Twig_Environment $twig
     * @param RouterInterface $router
     */
    public function __construct(ProviderFactory $providerFactory, \Twig_Environment $twig, RouterInterface $router)
    {
        $this->providerFactory = $providerFactory;
        $this->twig = $twig;
        $this->router = $router;
    }


    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return array(
            new MediaTokenParser(self::class),
            new PathTokenParser(self::class),
            new DownloadTokenParser(self::class),
        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('mediaPath', array($this, 'path')),
        );
    }

    public function getName()
    {
        return 'donjohn_media';
    }

    public function media(Media $media = null, $filter, $attributes)
    {

        try {
            $provider = $this->providerFactory->getProvider($media);
        }
        catch (NotFoundProviderException $e) {
            return '';
        }
        return $provider->render($this->twig, $media, $filter, $attributes);

    }


    public function path(Media $media = null, $filter)
    {
        try {
            $provider = $this->providerFactory->getProvider($media);
        }
        catch (NotFoundProviderException $e) {
            return '';
        }
        return $provider->getPath($media, $filter);

    }

    public function download(Media $media = null)
    {
        return $media && $media->getId() ? $this->router->generate('donjohn_media_download',array('id' => $media->getId())) : null;

    }

}
