<?php

namespace Nip\Html\Head\Entities;

use Nip\Html\Head\Tags\AbstractTag;
use Nip\Html\Head\Tags\Link;
use Nip\Html\Head\Tags\LinkIcon;
use Nip\Html\Head\Tags\Meta;

/**
 * Class Favicon
 * @package Nip\Html\Head\Entities
 */
class Favicon extends AbstractEntity
{
    /**
     * @var null
     */
    protected $baseDir = null;

    /**
     * @var AbstractTag[]
     */
    protected $tags = [];

    /**
     * @return AbstractTag[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    public function addAndroidManifest()
    {
        $tag = new Link();
        $tag->setRel('manifest')
            ->setHref($this->generateUrl('/manifest.json'));
        $this->addTag($tag, 'android-manifest');
    }

    /**
     * @param string $path
     * @return string
     */
    public function generateUrl($path)
    {
        return $this->getBaseDir() . $path;
    }

    /**
     * @return null
     */
    public function getBaseDir()
    {
        if ($this->baseDir === null) {
            $this->initBaseDir();
        }

        return $this->baseDir;
    }

    /**
     * @param string $baseDir
     */
    public function setBaseDir($baseDir)
    {
        $this->baseDir = $baseDir;
    }

    public function initBaseDir()
    {
        $this->setBaseDir($this->generateBaseDir());
    }

    /**
     * @return string
     */
    public function generateBaseDir()
    {
        return '';
    }

    /**
     * @param AbstractTag $tag
     * @param string $name
     * @return $this
     */
    public function addTag($tag, $name = null)
    {
        if ($name) {
            $this->tags[$name] = $tag;
        } else {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function addAll()
    {
        $this->addFavicon();
        $this->addAllDefault();
        $this->addAndroidIcon();
        $this->addAppleTouchIcon();
        $this->addSafariIcon();
        $this->addWindowsMeta();
    }

    public function addFavicon()
    {
        $tag = new Link();
        $tag->setRel('shortcut icon')
            ->setHref($this->generateUrl('/favicon.ico'));
        $this->addTag($tag, 'favicon.ico');
    }

    public function addAllDefault()
    {
        $this->addDefault(16);
        $this->addDefault(32);
        $this->addDefault(194);
    }

    /**
     * @param int $size
     */
    public function addDefault($size)
    {
        $fullSize = $size . 'x' . $size;
        $tag = new LinkIcon();
        $tag->setSizes($fullSize)
            ->setHref($this->generateUrl('/favicon-' . $fullSize . '.png'));
        $this->addTag($tag, 'default-' . $fullSize);
    }

    public function addAndroidIcon()
    {
        $tag = new LinkIcon();
        $tag->setSizes('192x192')
            ->setHref($this->generateUrl('/android-chrome-192x192.png'));
        $this->addTag($tag, 'android-icon');
    }

    public function addAppleTouchIcon()
    {
        $tag = new LinkIcon();
        $tag->setRel('apple-touch-icon')
            ->setSizes('180x180')
            ->setHref($this->generateUrl('/apple-touch-icon.png'));
        $this->addTag($tag, 'apple-touch-icon');
    }

    public function addSafariIcon()
    {
        $tag = new Link();
        $tag->setRel('mask-icon')
            ->setHref($this->generateUrl('/safari-pinned-tab.svg'))
            ->setAttribute('color', '#5bbad5');
        $this->addTag($tag, 'safari-icon');
    }

    public function addWindowsMeta()
    {
        $meta = new Meta();
        $meta->setName('msapplication-TileColor')->setContent('#2b5797');
        $meta->setName('msapplication-TileImage')->setContent($this->generateUrl('/mstile-144x144.png'));
        $meta->setName('msapplication-config')->setContent($this->generateUrl('/browserconfig.xml'));
        $meta->setName('theme-color')->setContent('#ffffff');
    }

    /**
     * @return string
     */
    public function render()
    {
        $return = '';
        foreach ($this->tags as $tag) {
            $return .= $tag->render();
        }

        return $return;
    }
}
