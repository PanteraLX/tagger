<?php

namespace TaggerBundle\Branch;

use GitElephant\Repository;
/**
 * Class Production
 *
 * @package TaggerBundle\Branch
 * @author   List of contributors <https://github.com/libgraviton/graviton/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
 */
class Production extends Branch
{
    /**
     * @var string
     */
    private $productionName;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param string     $productionName
     * @param Repository $repository
     */
    public function __construct($productionName = 'master', Repository $repository)
    {
        $this->productionName = $productionName;
        $this->repository = $repository;
    }

    public function checkoutProduction()
    {
        $this->repository->checkout('master');
    }

    /**
     * @param string $releaseBranchName Name of the release branch
     */
    public function mergeRelease($releaseBranchName)
    {
        $releaseBranch = $this->repository->getBranch($releaseBranchName);
        $this->repository->merge($releaseBranch);
    }


}