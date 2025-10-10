<?php

declare(strict_types=1);

namespace Laminas\Feed\Writer\Extension\PodcastIndex\Renderer;

use DOMDocument;
use DOMElement;
use Laminas\Feed\Writer\Extension;
use Laminas\Feed\Writer\Extension\PodcastIndex;
use Laminas\Feed\Writer\Feed as FeedWriter;

use function assert;

/**
 * Renders PodcastIndex data of a RSS Feed
 *
 * @psalm-import-type FundingArray from PodcastIndex\Validator
 * @psalm-import-type LicenseArray from PodcastIndex\Validator
 * @psalm-import-type LocationArray from PodcastIndex\Validator
 * @psalm-import-type BlockArray from PodcastIndex\Validator
 * @psalm-import-type TxtArray from PodcastIndex\Validator
 * @psalm-import-type PersonArray from PodcastIndex\Validator
 * @psalm-import-type UpdateFrequencyArray from PodcastIndex\Validator
 * @psalm-import-type TrailerArray from PodcastIndex\Validator
 * @psalm-import-type RemoteItemArray from PodcastIndex\Validator
 * @psalm-import-type ValueRecipientArray from PodcastIndex\Validator
 * @psalm-import-type ValueArray from PodcastIndex\Validator
 * @psalm-import-type ImagesArray from PodcastIndex\Validator
 * @psalm-import-type DetailedImageArray from PodcastIndex\Validator
 * @psalm-import-type SocialInteractArray from PodcastIndex\Validator
 * @psalm-import-type LiveItemArray from PodcastIndex\Validator
 * @psalm-import-type ChatArray from PodcastIndex\Validator
 */
class Feed extends Extension\AbstractRenderer
{
    /**
     * Set to TRUE if a rendering method actually renders something. This
     * is used to prevent premature appending of a XML namespace declaration
     * until an element which requires it is actually appended.
     *
     * @var bool
     */
    protected $called = false;

    /**
     * Render feed
     */
    public function render(): void
    {
        $this->setLocked($this->dom, $this->base);
        $this->setFunding($this->dom, $this->base);
        $this->setFundings($this->dom, $this->base);
        $this->setLicense($this->dom, $this->base);
        $this->setLocation($this->dom, $this->base);
        $this->setLocations($this->dom, $this->base);
        $this->setImages($this->dom, $this->base);
        $this->setDetailedImages($this->dom, $this->base);
        $this->setUpdateFrequency($this->dom, $this->base);
        $this->setPeople($this->dom, $this->base);
        $this->setTrailer($this->dom, $this->base);
        $this->setGuid($this->dom, $this->base);
        $this->setMedium($this->dom, $this->base);
        $this->setBlocks($this->dom, $this->base);
        $this->setTxts($this->dom, $this->base);
        $this->setPodping($this->dom, $this->base);
        $this->setRemoteItems($this->dom, $this->base);
        $this->setPodroll($this->dom, $this->base);
        $this->setPublisher($this->dom, $this->base);
        $this->setValues($this->dom, $this->base);
        $this->setSocialInteracts($this->dom, $this->base);
        $this->setChat($this->dom, $this->base);

        /** @var FeedWriter $feedWriter */
        $feedWriter = $this->getDataContainer();
        /** @var list<PodcastIndex\LiveItem> $liveItems */
        $liveItems = $feedWriter->getPodcastIndexLiveItems();
        if ($liveItems) {
            foreach ($liveItems as $liveItem) {
                $encoding = $feedWriter->getEncoding();
                if ($encoding) {
                    $liveItem->setEncoding($encoding);
                }
                $renderer = new LiveItem($liveItem, $this->dom, $this->base);
                $renderer->setType($this->getType());
                $renderer->setRootElement($this->dom->documentElement);
                $renderer->render();
                $element  = $renderer->getElement();
                $imported = $this->dom->importNode($element, true);
                $this->base->appendChild($imported);
            }
        }

        if ($this->called) {
            $this->_appendNamespaces();
        }
    }

    /**
     * Append feed namespaces
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _appendNamespaces(): void
    {
        $this->getRootElement()->setAttribute(
            'xmlns:podcast',
            'https://github.com/Podcastindex-org/podcast-namespace/blob/main/docs/1.0.md'
        );
    }

    private function getFeedWriter(): FeedWriter
    {
        $container = $this->getDataContainer();
        assert($container instanceof FeedWriter);

        return $container;
    }

    /**
     * Set feed lock
     */
    protected function setLocked(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var null|array<string, string> $locked */
        $locked = $container->getPodcastIndexLocked();
        if ($locked === null) {
            return;
        }
        $el = ElementGenerator::createPodcastIndexElement($dom, $locked, 'locked', 'value');
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set a single feed funding tag
     */
    protected function setFunding(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var null|FundingArray $funding */
        $funding = $container->getPodcastIndexFunding();
        if ($funding === null) {
            return;
        }
        $el = ElementGenerator::createPodcastIndexElement($dom, $funding, 'funding', 'title');
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set multiple funding tags
     */
    protected function setFundings(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var null|list<FundingArray> $fundings */
        $fundings = $container->getPodcastIndexFundings();
        if ($fundings === null) {
            return;
        }

        foreach ($fundings as $funding) {
            $el = ElementGenerator::createPodcastIndexElement($dom, $funding, 'funding', 'title');
            $root->appendChild($el);
        }

        $this->called = true;
    }

    /**
     * Set feed license
     */
    private function setLicense(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var null|LicenseArray $license */
        $license = $container->getPodcastIndexLicense();
        if ($license === null) {
            return;
        }
        $el = ElementGenerator::createPodcastIndexElement($dom, $license, 'license', 'identifier');

        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set a single feed location
     */
    private function setLocation(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var null|LocationArray $location */
        $location = $container->getPodcastIndexLocation();
        if ($location === null) {
            return;
        }
        $el = ElementGenerator::createPodcastIndexElement($dom, $location, 'location', 'description');
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set multiple location tags
     */
    protected function setLocations(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var null|list<LocationArray> $locations */
        $locations = $container->getPodcastIndexLocations();
        if ($locations === null) {
            return;
        }

        foreach ($locations as $location) {
            $el = ElementGenerator::createPodcastIndexElement($dom, $location, 'location', 'description');
            $root->appendChild($el);
        }

        $this->called = true;
    }

    /**
     * Set feed images srcset
     */
    private function setImages(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var null|ImagesArray $images */
        $images = $container->getPodcastIndexImages();
        if ($images === null) {
            return;
        }
        $el = ElementGenerator::createPodcastIndexElement($dom, $images, 'images');
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed detailed images
     */
    private function setDetailedImages(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var list<DetailedImageArray>|null $detailedImages */
        $detailedImages = $container->getPodcastIndexDetailedImages();
        if ($detailedImages === null || $detailedImages === []) {
            return;
        }

        foreach ($detailedImages as $detailedImage) {
            $el = ElementGenerator::createPodcastIndexElement($dom, $detailedImage, 'image');
            $root->appendChild($el);
        }

        $this->called = true;
    }

    /**
     * Set feed update frequency
     */
    private function setUpdateFrequency(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var null|UpdateFrequencyArray $updateFrequency */
        $updateFrequency = $container->getPodcastIndexUpdateFrequency();
        if ($updateFrequency === null) {
            return;
        }
        $el = ElementGenerator::createPodcastIndexElement($dom, $updateFrequency, 'updateFrequency', 'description');
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed people
     */
    private function setPeople(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var null|list<PersonArray> $people */
        $people = $container->getPodcastIndexPeople();
        if ($people === null || $people === []) {
            return;
        }
        foreach ($people as $person) {
            $el = ElementGenerator::createPodcastIndexElement($dom, $person, 'person', 'name');
            $root->appendChild($el);
        }
        $this->called = true;
    }

    /**
     * Set feed trailer
     */
    private function setTrailer(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var null|TrailerArray $trailer */
        $trailer = $container->getPodcastIndexTrailer();
        if ($trailer === null) {
            return;
        }
        $el = ElementGenerator::createPodcastIndexElement($dom, $trailer, 'trailer', 'title');
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed guid
     */
    private function setGuid(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var null|array{value: string} $guid */
        $guid = $container->getPodcastIndexGuid();
        if ($guid === null) {
            return;
        }
        $el = ElementGenerator::createPodcastIndexElement($dom, $guid, 'guid', 'value');
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed medium
     */
    private function setMedium(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var null|array{value: string} $medium */
        $medium = $container->getPodcastIndexMedium();
        if ($medium === null) {
            return;
        }
        $el = ElementGenerator::createPodcastIndexElement($dom, $medium, 'medium', 'value');
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed blocks
     */
    private function setBlocks(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var list<BlockArray>|null $blocks */
        $blocks = $container->getPodcastIndexBlocks();
        if ($blocks === null || $blocks === []) {
            return;
        }

        foreach ($blocks as $block) {
            $el = ElementGenerator::createPodcastIndexElement($dom, $block, 'block', 'value');
            $root->appendChild($el);
        }
        $this->called = true;
    }

    /**
     * Set feed txts
     */
    private function setTxts(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var list<TxtArray>|null $txts */
        $txts = $container->getPodcastIndexTxts();
        if ($txts === null || $txts === []) {
            return;
        }

        foreach ($txts as $txt) {
            $el = ElementGenerator::createPodcastIndexElement($dom, $txt, 'txt', 'value');
            $root->appendChild($el);
        }
        $this->called = true;
    }

    /**
     * Set feed podping
     */
    private function setPodping(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var null|array{usesPodping: bool} $podping */
        $podping = $container->getPodcastIndexPodping();
        if ($podping === null) {
            return;
        }

        $el = ElementGenerator::createPodcastIndexElement($dom, $podping, 'podping');
        $root->appendChild($el);
        $this->called = true;
    }

    /**
     * Set feed remote items
     */
    private function setRemoteItems(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var list<RemoteItemArray>|null $remoteItems */
        $remoteItems = $container->getPodcastIndexRemoteItems();
        if ($remoteItems === null || $remoteItems === []) {
            return;
        }

        foreach ($remoteItems as $remoteItem) {
            $el = ElementGenerator::createPodcastIndexElement($dom, $remoteItem, 'remoteItem');
            $root->appendChild($el);
        }

        $this->called = true;
    }

    /**
     * Set podroll element with remote items
     */
    private function setPodroll(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var list<RemoteItemArray>|null $podrollItems */
        $podrollItems = $container->getPodcastIndexPodroll();
        if ($podrollItems === null || $podrollItems === []) {
            return;
        }

        $podroll = $dom->createElement('podcast:podroll');

        foreach ($podrollItems as $remoteItem) {
            $el = ElementGenerator::createPodcastIndexElement($dom, $remoteItem, 'remoteItem');
            $podroll->appendChild($el);
        }

        $root->appendChild($podroll);

        $this->called = true;
    }

    /**
     * Set publisher element with remote items
     */
    private function setPublisher(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var RemoteItemArray|null $publisherItem */
        $publisherItem = $container->getPodcastIndexPublisher();
        if ($publisherItem === null) {
            return;
        }

        $publisher = $dom->createElement('podcast:publisher');
        $el        = ElementGenerator::createPodcastIndexElement($dom, $publisherItem, 'remoteItem');
        $publisher->appendChild($el);
        $root->appendChild($publisher);

        $this->called = true;
    }

    /**
     * Set values with the valueRecipients
     */
    private function setValues(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var list<ValueArray>|null $values */
        $values = $container->getPodcastIndexValues();
        if ($values === null || $values === []) {
            return;
        }

        foreach ($values as $value) {
            if (! isset($value['valueRecipients'])) {
                continue;
            }
            $valueElement = ElementGenerator::createPodcastIndexElement($dom, $value, 'value');
            foreach ($value['valueRecipients'] as $valueRecipient) {
                $el = ElementGenerator::createPodcastIndexElement($dom, $valueRecipient, 'valueRecipient');
                $valueElement->appendChild($el);
            }
            $root->appendChild($valueElement);
        }

        $this->called = true;
    }

    /**
     * Set feed social interacts
     */
    private function setSocialInteracts(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var list<SocialInteractArray>|null $socialInteracts */
        $socialInteracts = $container->getPodcastIndexSocialInteracts();
        if ($socialInteracts === null || $socialInteracts === []) {
            return;
        }

        foreach ($socialInteracts as $socialInteract) {
            $el = ElementGenerator::createPodcastIndexElement($dom, $socialInteract, 'socialInteract');
            $root->appendChild($el);
        }

        $this->called = true;
    }

    /**
     * Set chat element
     */
    private function setChat(DOMDocument $dom, DOMElement $root): void
    {
        $container = $this->getFeedWriter();

        /** @psalm-var ChatArray|null $chat */
        $chat = $container->getPodcastIndexChat();
        if ($chat === null) {
            return;
        }

        $el = ElementGenerator::createPodcastIndexElement($dom, $chat, 'chat');
        $root->appendChild($el);
        $this->called = true;
    }
}
