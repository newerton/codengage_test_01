<?php

namespace App\Utils;

/**
 * Class Markdown
 * @package App\Utils
 */
class Markdown
{
    private $parser;
    private $purifier;

    /**
     * Markdown constructor.
     */
    public function __construct()
    {
        $this->parser = new \Parsedown();

        $purifierConfig = \HTMLPurifier_Config::create([
            'Cache.DefinitionImpl' => null, // Disable caching
        ]);
        $this->purifier = new \HTMLPurifier($purifierConfig);
    }

    /**
     * @param $text
     * @return mixed
     */
    public function toHtml($text)
    {
        $html = $this->parser->text($text);
        $safeHtml = $this->purifier->purify($html);

        return $safeHtml;
    }
}