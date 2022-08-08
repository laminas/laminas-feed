<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Renderer\Entry;

use DateTime;
use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer;
use Laminas\Feed\Writer\Renderer;

use function array_key_exists;

class AtomDeleted extends Renderer\AbstractRenderer implements Renderer\RendererInterface
{
    public function __construct(Writer\Deleted $container)
    {
        parent::__construct($container);
    }

    /**
     * Render atom entry
     *
     * @return $this
     */
    public function render()
    {
        $this->dom               = new DOMDocument('1.0', $this->container->getEncoding());
        $this->dom->formatOutput = true;
        $entry                   = $this->dom->createElement('at:deleted-entry');
        $this->dom->appendChild($entry);

        $entry->setAttribute('ref', $this->container->getReference());
        $entry->setAttribute('when', $this->container->getWhen()->format(DateTime::ATOM));

        $this->_setBy($this->dom, $entry);
        $this->_setComment($this->dom, $entry);

        return $this;
    }

    // phpcs:disable PSR2.Methods.MethodDeclaration.Underscore

    /**
     * Set tombstone comment
     *
     * @return void
     */
    protected function _setComment(DOMDocument $dom, DOMElement $root)
    {
        if (! $this->getDataContainer()->getComment()) {
            return;
        }
        $c = $dom->createElement('at:comment');
        $root->appendChild($c);
        $c->setAttribute('type', 'html');
        $cdata = $dom->createCDATASection($this->getDataContainer()->getComment());
        $c->appendChild($cdata);
    }

    /**
     * Set entry authors
     *
     * @return void
     */
    protected function _setBy(DOMDocument $dom, DOMElement $root)
    {
        $data = $this->container->getBy();
        if (! $data || empty($data)) {
            return;
        }
        $author = $this->dom->createElement('at:by');
        $name   = $this->dom->createElement('name');
        $author->appendChild($name);
        $root->appendChild($author);
        $text = $dom->createTextNode((string) $data['name']);
        $name->appendChild($text);
        if (array_key_exists('email', $data)) {
            $email = $this->dom->createElement('email');
            $author->appendChild($email);
            $text = $dom->createTextNode((string) $data['email']);
            $email->appendChild($text);
        }
        if (array_key_exists('uri', $data)) {
            $uri = $this->dom->createElement('uri');
            $author->appendChild($uri);
            $text = $dom->createTextNode((string) $data['uri']);
            $uri->appendChild($text);
        }
    }

    // phpcs:enable PSR2.Methods.MethodDeclaration.Underscore
}
