<?php

namespace TaggerBundle\Tests\Console\Command;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Tester\CommandTester;
use TaggerBundle\Console\Command\Tag;

/**
 * Class TagTest
 *
 * @package TaggerBundle\Tests\Console\Command
 * @author   List of contributors <https://github.com/libgraviton/graviton/graphs/contributors>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://swisscom.ch
 */
class TagTest
{
    public function testExecute()
    {
        $command = new Tag();
        $commandTester = new CommandTester($command);

        $helper = $command->getHelper('question');
        $helper->setInputStream($this->getInputStream('4.2\\n'));
        // Equals to a user inputting "Test" and hitting ENTER
        // If you need to enter a confirmation, "yes\n" will work

        $commandTester->execute(array('command' => $command->getName()));
        $this->assertRegExp('/.../', $commandTester->getDisplay());
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }
}