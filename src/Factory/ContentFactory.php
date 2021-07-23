<?php

declare(strict_types=1);

namespace Bolt\Factory;

use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Content;
use Bolt\Entity\User;
use Bolt\Event\Listener\ContentFillListener;
use Bolt\Security\ContentVoter;
use Bolt\Storage\Query;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ContentFactory
{
    /** @var ContentFillListener */
    private $contentFillListener;

    /** @var Security */
    private $security;

    /** @var Query */
    private $query;

    /** @var Config */
    private $config;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(
        ContentFillListener $contentFillListener,
        Security $security,
        Query $query,
        Config $config,
        EntityManagerInterface $em)
    {
        $this->contentFillListener = $contentFillListener;
        $this->security = $security;
        $this->query = $query;
        $this->config = $config;
        $this->em = $em;
    }

    public static function createStatic(ContentType $contentType): Content
    {
        $content = new Content($contentType);
        $content->setStatus($contentType->get('default_status'));

        return $content;
    }

    public function create(ContentType $contentType): Content
    {
        $content = self::create($contentType);

        $this->contentFillListener->fillContent($content);

        if ($this->security->isGranted(ContentVoter::CONTENT_CREATE, $content)) {
            /** @var User $user */
            $user = $this->security->getUser();
            $content->setAuthor($user);
        }

        return $content;
    }

    /**
     * Fetch an existing record or create a new one,
     * based on the specified criteria (in setcontent-like format).
     */
    public function upsert(string $query, array $parameters = []): Content
    {
        $parameters['returnsingle'] = true;
        unset($parameters['returnmultiple']);

        $content = $this->query->getContent($query, $parameters);

        if (! $content instanceof Content) {
            /** @var ContentType $contentType */
            $contentType = $this->config->getContentType($query);
            $content = $this->create($contentType);
        }

        return $content;
    }

    /**
     * @param Content|Content[] $content
     */
    public function save($content): void
    {
        if ($content instanceof Content) {
            $this->em->persist($content);
        } elseif (is_iterable($content)) {
            foreach ($content as $c) {
                $this->em->persist($c);
            }
        }

        $this->em->flush();
    }
}
