<?php

namespace Nip\I18n\Translator\Backend;

/**
 * Class Po.
 */
class Po extends AbstractBackend
{
    /**
     * @var string
     */
    protected $path;

    /**
     * Sets and binds the text domain.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;

        bindtextdomain('messages', $this->path);
        bind_textdomain_codeset('messages', 'UTF-8');
        textdomain('messages');

        return $this;
    }

    /**
     * Returns gettext translation for $slug in $language.
     *
     * @see http://php.net/gettext
     *
     * @param string      $slug
     * @param string|bool $language
     *
     * @return string
     */
    protected function doTranslation($slug, $language = false)
    {
        return gettext($slug);
    }
}
