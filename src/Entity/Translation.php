<?php

declare(strict_types=1);

namespace Gedmo\Translatable\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Gedmo\Translatable\Entity\Translation
 *
 * @ORM\Table(
 *         name="translation",
 *         options={"row_format":"DYNAMIC"},
 *         indexes={@ORM\Index(name="translations_lookup_idx", columns={
 *             "locale", "object_class", "foreign_key"
 *         })},
 *         uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *             "locale", "object_class", "field", "foreign_key"
 *         })}
 * )
 * @ORM\Entity(repositoryClass="Gedmo\Translatable\Entity\Repository\TranslationRepository")
 */
class Translation extends MappedSuperclass\AbstractTranslation
{
    /**
     * All required columns are mapped through inherited superclass, except
     * for $objectClass, because we need to limit the length to 191, for MySQL 5.6
     * compatibility. See https://github.com/doctrine/orm/issues/7416
     */

    /**
     * @var string
     *
     * @ORM\Column(name="object_class", type="string", length=191)
     */
    protected $objectClass;
}
