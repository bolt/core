<?php

declare(strict_types=1);

namespace Bolt\Event;

use Bolt\Entity\Content;
use Symfony\Contracts\EventDispatcher\Event;

class ContentEvent extends Event
{
    public const PRE_SAVE = 'bolt.pre_save';
    public const POST_SAVE = 'bolt.post_save';
    public const ON_EDIT = 'bolt.pre_edit';
    public const ON_PREVIEW = 'bolt.pre_edit';
    public const ON_DUPLICATE = 'bolt.on_duplicate';
    public const PRE_STATUS_CHANGE = 'bolt.pre_status_change';
    public const POST_STATUS_CHANGE = 'bolt.post_status_change';
    public const PRE_DELETE = 'bolt.pre_delete';
    public const POST_DELETE = 'bolt.post_delete';

    /** @var Content */
    private $content;

    public function __construct(Content $content)
    {
        $this->content = $content;
    }

    public function getContent(): Content
    {
        return $this->content;
    }
}
