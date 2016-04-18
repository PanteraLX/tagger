<?php

namespace TaggerBundle\Process;

use GitElephant\Repository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Class Git
 *
 * @package  TaggerBundle\Process
 * @author   List of contributors <https://github.com/libgraviton/graviton/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
 */
class Git extends Controller
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var string
     */
    private $gitDirectory;

    /**
     *
     */
    public function __construct()
    {
        $this->gitDirectory = $this->getGitDirectory();
        $this->repository = new Repository($this->gitDirectory);
    }

    /**
     * @return string
     */
    public function getGitVersion()
    {
        $process = new Process('git --version');
        $process->run();

        if (!$process->isSuccessful()) {
            throw new \RuntimeException("Git is not installed. Please install it");
        }

        return $process->getOutput();
    }

    /**
     *
     */
    public function getRepository()
    {
        var_dump($this->repository->getBranches(false));
    }

    /**
     * @return string
     */
    public function getLastTag()
    {
        try {
            $lastTag = $this->repository->getLastTag()->getName();
        } catch (\InvalidArgumentException $e) {
            $lastTag = '0';
        }

        return $lastTag;
    }

    /**
     * @return string
     */
    private function getGitDirectory()
    {
        $path = __DIR__;
        $dirFound = false;
        while (!$dirFound) {
            $folders = scandir($path);
            $dirFound = array_search('.git', $folders);
            if ($dirFound) {
                $dirFound = $this->checkSelf($path);
            }
            if (!$dirFound) {
                $path .= '/..';
            }
            $path = realpath($path);
        };
        return $path;
    }

    /**
     * @param string $path Path
     * @return bool
     */
    private function checkSelf($path)
    {
        if (strpos($path, 'graviton-tagger') !== false) {
            echo false;
        }
        return true;
    }
}
