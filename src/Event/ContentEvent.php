<?php

declare(strict_types=1);

namespace Bolt\Event;

use Bolt\Entity\Content;
use Symfony\Contracts\EventDispatcher\Event;

class ContentEvent extends Event
{
    public const PRE_SAVE = 'bolt.pre_save';
    public const POST_SAVE = 'bolt.post_save';
    public const PRE_PERSIST = 'bolt.pre_persist';
    public const POST_PERSIST = 'bolt.post_persist';
    public const PRE_FLUSH = 'bolt.pre_flush';
    public const POST_FLUSH = 'bolt.post_flush';
    public const ON_EDIT = 'bolt.pre_edit';
    public const ON_PREVIEW = 'bolt.pre_edit';
    public const ON_DUPLICATE = 'bolt.on_duplicate';
    public const PRE_STATUS_CHANGE = 'bolt.pre_status_change';
    public const POST_STATUS_CHANGE = 'bolt.post_status_change';
    public const PRE_DELETE = 'bolt.pre_delete';
    public const POST_DELETE = 'bolt.post_delete';
    public const PRE_REMOVE = 'bolt.pre_remove';
    public const POST_REMOVE = 'bolt.post_remove';

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
