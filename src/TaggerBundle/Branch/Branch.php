<?php
namespace TaggerBundle\Branch;

use GitElephant\Repository;

/**
 * Class Branch
 *
 * @package TaggerBundle\Branch
 */
abstract class Branch
{
    /**
     * @param Repository $repository
     */
    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    public function push()
    {

    }


}