<?php
/**
 *
 *
 * @author Bob den Otter <bobdenotter@gmail.com>
 */

namespace Bolt\Snippets;


class BaseWidget
{
    protected $name = 'Nameless widget';
    protected $type = 'widget';
    protected $target = Target::NOWHERE;
    protected $priority = 0;

    /**
     * @param string $name
     * @return BaseWidget
     */
    public function setName(string $name): BaseWidget
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $type
     * @return BaseWidget
     */
    public function setType(string $type): BaseWidget
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $target
     * @return BaseWidget
     */
    public function setTarget(string $target): BaseWidget
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @return string
     */
    public function getTarget(): string
    {
        return $this->target;
    }

    /**
     * @param int $priority
     * @return BaseWidget
     */
    public function setPriority(int $priority): BaseWidget
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }


}