<?php
/**
 * @author jgn
 * @date 21/03/2017
 * @description For ...
 */

namespace Donjohn\MediaBundle\Filesystem;


use Gaufrette\Adapter;
use Gaufrette\Filesystem;

class MediaLocalFilesystem extends Filesystem
{
    /**
     * @var string $webFolder
     */
    protected $webFolder;

    /**
     * MediaLocalFilesystem constructor.
     * @param string $webFolder
     */
    public function __construct($webFolder)
    {
        $this->webFolder = $webFolder;
        parent::__construct(new Adapter\Local($webFolder));
    }

    /**
     * @return string
     */
    public function getWebFolder()
    {
        return $this->webFolder;
    }

}
