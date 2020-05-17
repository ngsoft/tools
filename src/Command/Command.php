<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Command;

use InvalidArgumentException;
use NGSOFT\Tools\IO;
use Throwable;

abstract class Command {

    /** @var array<string,Option> */
    protected $options = [];

    /** @var array */
    protected $extra;

    /** @var IO */
    protected $io;

    /**
     * Get command short name
     * script <name>
     */
    abstract public function getName(): string;

    /**
     * Get Command description (for the usage)
     */
    abstract public function getDescription(): string;

    /**
     * Required to run the app
     */
    abstract protected function run(array $args): bool;

    /**
     * Configure your command
     */
    abstract public function configure();

    /** @return array<string,Option> */
    public function getOptions(): array {
        return $this->options;
    }

    /**
     * @param Option $option
     * @return static
     */
    public function addOption(Option $option) {
        $this->options[$option->getLong()] = $option;
        return $this;
    }

    /**
     * @param array $args Args without the script name to parse with the options
     * @param array $extra Extra args to inject into the command
     */
    public function __construct(array $args = [], array $extra = []) {
        $this->extra = $extra;
        $this->io = IO::create();
        $args = implode(' ', $args);
        $this->configure();
        try {
            $this->parseOpts($args);
        } catch (\Throwable $exc) {
            $exc->getCode();
            $this->displayUsage();
        }
    }

    /**
     * Run the command
     */
    public function execute(): bool {
        $retval = false;

        $args = [];
        foreach ($this->options as $opt) {
            $args[$opt->getLong()] = $opt->getValue();
        }
        $args = array_replace_recursive($args, $this->extra);
        try {
            $retval = $this->run($args);
        } catch (Throwable $exc) {
            $t = new IO\Terminal();
            $w = $t->getWidth();
            $message = $exc->getMessage();
            $len = strlen($message) + 24;
            if ($w > $len) {
                $spaces = $w - $len;
                $spaces = floor($spaces / 2);
                $this->io->writeln([
                    '',
                    sprintf('{:space*%u:}<span class="bgred">{:space*%u:}</span>', $spaces, $len),
                    sprintf('{:space*%u:}<span class="bgred">{:space*12:}%s{:space*12:}</span>', $spaces, $message),
                    sprintf('{:space*%u:}<span class="bgred">{:space*%u:}</span>', $spaces, $len),
                    '',
                ]);
            } else $this->io->writeerrln("<div class=\"red\">$message</div>");


            //$this->io->writeln(sprintf('<rect class="bgred default" width="64">%s</rect>', $exc->getMessage()));
            exit(1);
        }
        return $retval;
    }

    private function parseOpts(string $input) {

        foreach ($this->options as $opt) {
            $regex = "";
            if ($opt->getFlag() == Option::VALUE_NONE) {
                if (!empty($opt->getShort())) $regex = sprintf('/(:?--%s|-%s)/', $opt->getLong(), $opt->getShort());
                else $regex .= sprintf('/--%s/', $opt->getLong());
                $opt->setValues([false]);
                $input = preg_replace_callback($regex, function() use($opt) {
                    $opt->setValues([true]);
                    return '';
                }, $input);
            } else {
                if (!empty($opt->getShort())) $regex = sprintf('/(--%s[=\ ]?|-%s )(.*)/', $opt->getLong(), $opt->getShort());
                else $regex .= sprintf('/(--%s[=\ ]?)(.*)/', $opt->getLong());
                $input = preg_replace_callback($regex, function($matches) use($opt) {
                    $return = "";
                    list(,, $value) = $matches;
                    $next = stripos($value, '-');
                    if ($next !== false) {
                        $return = substr($value, $next);
                        $value = substr($value, 0, $next);
                        $value = trim($value);
                    }
                    if (is_numeric($value)) $value = intval($value);
                    switch ($value) {
                        case "true":
                            $value = true;
                            break;
                        case "false":
                            $value = false;
                            break;
                        default :
                            break;
                    }
                    if (empty($value)) $opt->addValue($opt->getDefault());
                    else $opt->addValue($value);
                    return $return;
                }, $input);
                if (($opt->getFlag() === Option::VALUE_REQUIRED) && is_null($opt->getValue())) {
                    throw new InvalidArgumentException('No value for ' . $opt->getLong());
                }
                //elseif (($opt->getDefault() !== null) && ($opt->getValue() === null)) {
                //    $opt->addValue($opt->getDefault());
                //}
            }
        }
    }

    /**
     * Displays command line usage
     * @param bool $exit
     */
    protected function displayUsage(bool $exit = true) {
        $io = $this->io;
        $required = Option::VALUE_REQUIRED; $optional = Option::VALUE_OPTIONAL;

        $io->writeln([
            $this->getDescription(),
            '<yellow>Usage:</yellow>',
            sprintf('{:space*4:}<green>%s </green><default>[options]</default>', $this->getName()),
        ]);

        $ropts = [];
        $oopts = [];
        $nopts = [];
        $maxlength = 0;

        foreach ($this->options as $opt) {

            if ($opt->getFlag() === $required) {

                $len = strlen(sprintf('    %s--%s=value',
                                !empty($opt->getShort()) ? '-' . $opt->getShort() . ' value ' : '',
                                $opt->getLong()
                ));
                if ($len > $maxlength) $maxlength = $len;
                $text = sprintf('{:space*4:}<green>%s--%s=</green><default>value</default>',
                        !empty($opt->getShort()) ? '-' . $opt->getShort() . ' </green><default>value </default><green>' : '',
                        $opt->getLong()
                );
                $ropts[] = [
                    "len" => $len,
                    "prefix" => $text,
                    "suffix" => $opt->getDescription()
                ];
            } elseif ($opt->getFlag() === $optional) {
                $len = strlen(sprintf('    %s[--%s=value]',
                                !empty($opt->getShort()) ? '[-' . $opt->getShort() . ' value] ' : '',
                                $opt->getLong()
                ));
                if ($len > $maxlength) $maxlength = $len;
                $text = sprintf(
                        '{:space*4:}<green>%s[--%s=</green><default>value</default><green>]</green>',
                        !empty($opt->getShort()) ? '[-' . $opt->getShort() . ' </green><default>value</default><green>] ' : '',
                        $opt->getLong(),
                        $opt->getDescription()
                );
                $oopts[] = [
                    "len" => $len,
                    "prefix" => $text,
                    "suffix" => $opt->getDescription()
                ];
            } else {
                $len = strlen(sprintf('    %s[--%s]',
                                !empty($opt->getShort()) ? '[-' . $opt->getShort() . '] ' : '',
                                $opt->getLong()
                ));
                if ($len > $maxlength) $maxlength = $len;
                $text = sprintf(
                        '{:space*4:}<green>%s[--%s]</green>',
                        !empty($opt->getShort()) ? '[-' . $opt->getShort() . '] ' : '',
                        $opt->getLong(),
                        $opt->getDescription()
                );
                $nopts[] = [
                    "len" => $len,
                    "prefix" => $text,
                    "suffix" => $opt->getDescription()
                ];
            }
        }

        if (count($ropts) > 0) {
            $io->writeln('<yellow>Required Options:</yellow>');
            foreach ($ropts as $o) {
                $repeat = ($maxlength - $o['len']);
                $io->writeln(
                        $o["prefix"] . sprintf('{:space*%d:}', $repeat + 4) .
                        sprintf("<default>%s</default>", $o['suffix'])
                );
            }
        }
        if (count($oopts) > 0) {
            $io->writeln('<yellow>Optional Options:</yellow>');
            foreach ($oopts as $o) {
                $repeat = ($maxlength - $o['len']);
                $io->writeln(
                        $o["prefix"] . sprintf('{:space*%d:}', $repeat + 4) .
                        sprintf("<default>%s</default>", $o['suffix'])
                );
            }
        }
        if (count($nopts) > 0) {
            $io->writeln('<yellow>Flags:</yellow>');
            foreach ($nopts as $o) {
                $repeat = ($maxlength - $o['len']);
                $io->writeln(
                        $o["prefix"] . sprintf('{:space*%d:}', $repeat + 4) .
                        sprintf("<default>%s</default>", $o['suffix'])
                );
            }
        }
        if ($exit === true) exit(1);
    }

}
