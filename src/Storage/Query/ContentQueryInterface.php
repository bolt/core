<?php

declare(strict_types=1);

namespace Bolt\Storage\Query;

interface ContentQueryInterface extends QueryInterface
{
    /**
     * Returns the content type this query is executing on.
     */
    public function getContentType(): string;

    /**
     * Returns the value of a parameter by key name.
     *
     * @return mixed
     */
    public function getParameter(string $key);

    /**
     * Sets the value of a parameter by key name.
     *
     * @param mixed $value
     */
    public function setParameter(string $key, $value): void;
}
