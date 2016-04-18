<?php

namespace TaggerBundle\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Respect\Validation\Validator as v;
use TaggerBundle\Process\Git;

/**
 * Class Tagger
 *
 * @package  TaggerBundle\Console\Command
 * @author   List of contributors <https://github.com/libgraviton/graviton/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
 */
class Tagger extends Command
{
    /**
     * @var Git
     */
    private $git;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->git = new Git();
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('graviton:tag')
            ->setDescription('Tag a new Version');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->git->getGitVersion();
        $lastTag = $this->git->getLastTag();

        if ($lastTag === 0) {
            $version = $this->askForVersion($input, $output);
        } else {
            $output->writeln(sprintf('<info>The last Tag was: %s</info>', $lastTag));
            $bumpedVersions = $this->bumpVersion($lastTag);

            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                'Please select your desired bump',
                array(
                    'Major: ' . $bumpedVersions['major'],
                    'Minor: ' . $bumpedVersions['minor'],
                    'Patch: ' . $bumpedVersions['patch'],
                    'Custom'
                ),
                0
            );
            $version = $helper->ask($input, $output, $question);

            if ($version === 'Custom') {
                $version = $this->askForVersion($input, $output);
            }
        }

        $text = sprintf('The desired version tag for the next release is: %s', $version);

        $output->writeln($text);
    }

    /**
     * @param $version
     * @return array
     */
    private function bumpVersion($version)
    {
        $matches = [];
        $tagRegex = '/^(?<version>[v]?(?<major>[0-9]+)\.(?<minor>[0-9]+)\.(?<patch>[0-9]+))$/';
        preg_match($tagRegex, $version, $matches);
        $bump['major'] = $matches['major'] + 1 . '.' . 0 . '.' . 0;
        $bump['minor'] = $matches['major']  . '.' . ($matches['minor'] + 1) . '.' . 0;
        $bump['patch'] = $matches['major']  . '.' . $matches['minor'] . '.' . ($matches['patch'] + 1);

        return $bump;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return string
     */
    private function askForVersion(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $question = new Question('What is the SemVer string of the next release?  ', 'v0.1.0');
        $question->setValidator(function ($answer) {
            if (!v::version()->validate($answer)) {
                throw new \RuntimeException(
                    'The desired version is not valid'
                );
            }
            return $answer;
        });
        $question->setMaxAttempts(10);
        return $helper->ask($input, $output, $question);
    }

}