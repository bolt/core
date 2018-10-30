<?php

declare(strict_types=1);

namespace Bolt\Twig\Runtime;

use Bolt\Configuration\Config;
use Bolt\Repository\ContentRepository;

class SetcontentRuntime
{
    /** @var Config $config */
    private $config;
    /** @var ContentRepository $repo */
    private $repo;

    /**
     * @param Config $config
     * @param ContentRepository $repo
     */
    public function __construct(Config $config, ContentRepository $repo)
    {
        $this->config = $config;
        $this->repo = $repo;
    }

    /**
     * @return ContentRepository
     */
    public function getContentRepository()
    {
        return $this->repo;
    }

    /**
     * @param string $textQuery
     * @param array $parameters
     */
    public function getContent($textQuery, array $parameters = [])
    {
        // fix BC break
        if (func_num_args() === 3) {
            $whereparameters = func_get_arg(2);
            if (is_array($whereparameters) && !empty($whereparameters)) {
                $parameters = array_merge($parameters, $whereparameters);
            }
        }

        $qb = $this->repo->createQueryBuilder('content')
            ->addSelect('a')
            ->innerJoin('content.author', 'a')
            ->orderBy('content.modifiedAt', 'DESC')
        ;

        $contentType = explode('/', $textQuery)[0];

        if ($contentType) {
            $qb
                ->where('content.contentType = :ct')
                ->setParameter('ct', $contentType)
            ;
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
        // return $this->createPaginator($qb->getQuery(), $page);

        // return $this->recordsView->createView(
        //     $this->getContentByScope('frontend', $textQuery, $parameters)
        // );
    }
}
