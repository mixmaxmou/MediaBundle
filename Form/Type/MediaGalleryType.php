<?php
/**
 * Created by PhpStorm.
 * User: tpn
 * Date: 18/04/2017
 * Time: 17:00
 */

namespace Donjohn\MediaBundle\Form\Type;


use Doctrine\ORM\EntityManagerInterface;
use Donjohn\MediaBundle\Form\Transformer\MediaIdTransformer;
use Donjohn\MediaBundle\Model\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class MediaGalleryType extends AbstractType
{
    /**
     * @var EntityManagerInterface $em
     */
    protected $em;

    /**
     * @var string $classMedia
     */
    protected $classMedia;

    /**
     * MediaGalleryType constructor.
     * @param EntityManagerInterface $em
     * @param string $classMedia
     */
    public function __construct(EntityManagerInterface $em, $classMedia)
    {
        $this->em = $em;
        $this->classMedia = $classMedia;
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return TextType::class;
    }

     /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
     {
         $builder->addModelTransformer(new MediaIdTransformer($this->em, $this->classMedia));
     }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults(array(
            'translation_domain' => 'DonjohnMediaBundle',
            'label' => 'media',
            'data_class' => $this->classMedia,
            'required' => false,
            'provider' => 'file'
        ));
    }

    /**
     * @inheritdoc
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $vars = $view->vars;
        /** @var Media $media */
        $media = $vars['data'];
        if ($vars['value']) $vars['value'] = $media->getId();
        $vars['provider'] = $options['provider'];
        $view->vars = $vars;
    }
}
