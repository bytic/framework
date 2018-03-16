<?php

namespace Nip\I18n\Translator\Backend;

use Nip\Filesystem\FileDisk;
use League\Flysystem\Adapter\Local as LocalAdapter;

/**
 * Nip Framework
 *
 * @category   Nip
 * @copyright  2009 Nip Framework
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @version    SVN: $Id$
 */
class File extends AbstractBackend
{
    protected $variableName = 'lang';
    protected $dictionary;

    protected $filesystem = null;
    protected $baseDirectory;

    /**
     * @return mixed
     */
    public function getBaseDirectory()
    {
        return $this->baseDirectory;
    }

    /**
     * @param mixed $baseDirectory
     */
    public function setBaseDirectory($baseDirectory)
    {
        $this->baseDirectory = $baseDirectory;
    }

    /**
     * @param array $languages
     */
    public function addLanguages($languages)
    {
        foreach ($languages as $language) {
            $this->addLanguage($language);
        }
    }

    public function addLanguage($language)
    {
        $directory = $this->compileLanguageDirectory($language);

        return $this->addLanguageFromPath($language, $directory);
    }

    protected function compileLanguageDirectory($lang)
    {
        return DIRECTORY_SEPARATOR . $lang;
    }

    /**
     * Adds a language to the dictionary
     *
     * @param string $language
     * @param string $path Path to file containing translations
     *
     * @return $this
     */
    public function addLanguageFromPath($language, $path)
    {
        $this->languages[] = $language;

        $filesystem = $this->getFilesystem();
//        $resolvedIncludePath = stream_resolve_include_path($path);
//        $fromIncludePath     = ($resolvedIncludePath !== false) ? $resolvedIncludePath : $path;
        if ($filesystem->has($path)) {
            $handler = $filesystem->get($path);
            if ($handler->isDir()) {
                $this->loadDirectory($language, $path);

                return $this;
            } elseif ($handler->isFile()) {
                $this->loadFile($language, $path);

                return $this;

            }
        }

        trigger_error(
            "Language file [" . $language . "][" . $path . "][" . $this->getBaseDirectory() . "] does not exist",
            E_USER_ERROR
        );
    }

    /**
     * @param $language
     * @param $path
     */
    public function loadDirectory($language, $path)
    {
        $filesystem = $this->getFilesystem();
        /** @var \SplFileInfo[] $files */
        $files = $filesystem->listContents($path, true);
        if (is_array($files)) {
            foreach ($files as $file) {
                if ($file['extension'] == 'php') {
                    $this->loadFile($language, $file['path']);
                }
            }
        }
    }

    /**
     * @param $language
     * @param $path
     */
    protected function loadFile($language, $path)
    {
        $filesystem = $this->getFilesystem();
        if ($filesystem->has($path)) {
            /** @noinspection PhpIncludeInspection */
            $content = $filesystem->get($path)->read();
            $content = str_replace(['<?php','?>'],'', $content);
            $messages = eval($content);

            if (is_array($messages)) {
                $this->loadMessages($language, $messages);
            } else {
                trigger_error(
                    sprintf(
                        'Expected an array, but received %s [%s][%s]',
                        gettype($messages), $language, $messages
                    ),
                    E_USER_ERROR
                );
            }
        }
    }

    /**
     * @param $language
     * @param $messages
     */
    protected function loadMessages($language, $messages)
    {
        foreach ($messages as $slug => $translation) {
            if ($slug) {
                $this->dictionary[$language][$slug] = $translation;
            }
        }
    }

    /**
     * Returns dictionary entry for $slug in $language
     *
     * @param string $slug
     * @param string|bool $language
     *
     * @return string|bool
     */
    protected function doTranslation($slug, $language = false)
    {
        if (isset($this->dictionary[$language][$slug])) {
            return $this->dictionary[$language][$slug];
        }

        return false;
    }

    /**
     * @return FileDisk|null
     */
    protected function getFilesystem()
    {
        if ($this->filesystem === null) {
            $adapter          = new LocalAdapter($this->getBaseDirectory());
            $this->filesystem = new FileDisk($adapter);
        }

        return $this->filesystem;
    }
}
