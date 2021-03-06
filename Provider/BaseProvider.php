<?php

namespace Donjohn\MediaBundle\Provider;

use Donjohn\MediaBundle\Model\Media;
use Donjohn\MediaBundle\Provider\Exception\InvalidMimeTypeException;
use Donjohn\MediaBundle\Provider\Guesser\ProviderGuess;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\HttpFoundation\File\File;


/**
 * description 
 * @author Donjohn
 */
abstract class BaseProvider implements ProviderInterface {

    /**
     * @var string
     */
    private $template;

    /**
     * @var array
     */
    private $allowedTypes;

    /**
     * @param $template
     * @return $this
     */
    final public function setTemplate($template)
    {
        if (empty($template)) throw new \InvalidArgumentException('please configure a template name for '.$this->getAlias().' provider');
        $this->template = $template;
        return $this;
    }

    /**
     * @param array $allowedTypes
     * @return $this
     */
    final public function setAllowedTypes(array $allowedTypes)
    {
        $this->allowedTypes = $allowedTypes;
        return $this;
    }

    /**
     * @return array
     */
    final protected function getAllowedTypes()
    {
        return $this->allowedTypes;
    }

    /**
     * @return string
     */
    final protected function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return string
     */
    abstract public function getAlias();

    /**
     * @inheritdoc
     */
    public function render(\Twig_Environment $twig, Media $media, $filter = null, $options = array()){
        return $twig->render($this->getTemplate(),
                            array('media' => $media,
                                'filter' => $filter,
                                'options' => $options)
                            );
    }

    /**
     * {@inheritdoc}
     */
    final public function validateMimeType($type)
    {
        if (count($this->allowedTypes) && !preg_match('#'.implode('|',$this->allowedTypes).'#', $type)) throw new InvalidMimeTypeException(sprintf('%s is not supported', $type));

        return true;
    }

    /**
     * @inheritdoc
     * @param null|File $file
     */
    final public function guess($file = null){

        $guesses = [];
        if (count($this->allowedTypes) && $file) {
            if (preg_match('#'.implode('|',$this->allowedTypes).'#', $file->getMimeType())) {
                $guesses[] = new ProviderGuess($this->getAlias(), Guess::HIGH_CONFIDENCE);
            } else {
                $guesses[] = new ProviderGuess($this->getAlias(), Guess::LOW_CONFIDENCE);
            }
        } else {
            $guesses[] = new ProviderGuess($this->getAlias(), Guess::MEDIUM_CONFIDENCE);
        }

        return ProviderGuess::getBestGuess($guesses);

    }


}
