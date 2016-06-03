<?php

namespace AsciiSoup\PhpSpec\TeamCityFormatter\Formatter;

use PhpSpec\Event\SpecificationEvent;
use PhpSpec\Event\SuiteEvent;
use PhpSpec\Event\ExampleEvent;
use PhpSpec\Formatter\ConsoleFormatter;

/**
 * TeamCityFormatter
 *
 * @package TeamCityFormatter
 *
 * @author Nils Luxton <ascii.soup@gmail.com>
 */

class TeamCityFormatter extends ConsoleFormatter
{
    /**
     * @var int
     */
    private $examplesCount = 0;

    private function teamCityMessage($messageType, array $values)
    {
        $message = "##teamcity[{$messageType} ";
        foreach ($values as $key => $value) {
            if (is_string($key)) {
                $message .= $key . "='" . $this->escape($value) . "' ";
            } else {
                $message .= $this->escape($value);
            }
        }
        return $message . "]";
    }

    /**
     * @param SuiteEvent $event
     */
    public function beforeSuite(SuiteEvent $event)
    {
        $this->examplesCount = count($event->getSuite());

        $this->getIO()->writeln($this->teamCityMessage("testCount", array(
            "count" => $this->examplesCount
        )));
    }

    public function beforeExample(ExampleEvent $event)
    {
        $this->getIO()->writeln(
            $this->teamCityMessage(
                'testStarted',
                array(
                    'name' => $event->getTitle(),
                    'captureStandardOutput' => 'false'
                )
            )
        );

        ob_start();
    }

    /**
     * @param ExampleEvent $event
     */
    public function afterExample(ExampleEvent $event)
    {
        $io = $this->getIO();

        $output = ob_get_clean();
        if ($output) {
            $io->writeln($this->teamCityMessage("testStdOut", array(
                "name" => $event->getTitle(),
                "out" => "Test Output\n>>>>>>>>>>>\n{$output}\n<<<<<<<<<<<\n"
            )));
        }
        switch ($event->getResult()) {
            case ExampleEvent::PASSED:
                break;
            case ExampleEvent::PENDING:
                $io->writeln($this->teamCityMessage('testIgnored', array(
                    'name' => $event->getTitle(),
                    'details' => $event->getMessage()
                )));
                break;
            case ExampleEvent::FAILED:
                $io->writeln($this->teamCityMessage('testFailed', array(
                    'name' => $event->getTitle(),
                    'message' => "Failed Test\n\n" . $event->getMessage(),
                    'details' => $event->getException()->getTraceAsString()
                )));
                break;
            case ExampleEvent::BROKEN:
                $io->writeln($this->teamCityMessage('testFailed', array(
                    'name' => $event->getTitle(),
                    'message' => "Broken Test\n\n" . $event->getMessage(),
                    'details' => $event->getException()->getTraceAsString(),
                )));
                break;
        }

        $io->writeln($this->teamCityMessage('testFinished', array(
            'name' => $event->getTitle(),
        )));
    }

    public function beforeSpecification(SpecificationEvent $event)
    {
        $this->getIO()->writeln($this->teamCityMessage('testSuiteStarted', array(
            'name' => $event->getTitle(),
            'locationHint' => "php_qn://{$event->getSpecification()->getClassReflection()->getFileName()}::\\{$event->getTitle()}"
        )));
    }

    public function afterSpecification(SpecificationEvent $event)
    {
        $this->getIO()->writeln($this->teamCityMessage('testSuiteFinished', array(
            'name' => $event->getTitle()
        )));
    }

    private function escape($string)
    {
        $string = str_replace("|", "||", $string);
        $string = str_replace("'", "|'", $string);
        $string = str_replace("\n", "|n", $string);
        $string = str_replace("\r", "|r", $string);
        $string = str_replace("]", "|]", $string);

        return $string;
    }
}