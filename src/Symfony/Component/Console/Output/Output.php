<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Output;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * Base class for output classes.
 *
 * There is three level of verbosity:
 *
 *  * normal: no option passed (normal output - information)
 *  * verbose: -v (more output - debug)
 *  * quiet: -q (no output)
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class Output implements OutputInterface
{
    const VERBOSITY_QUIET   = 0;
    const VERBOSITY_NORMAL  = 1;
    const VERBOSITY_VERBOSE = 2;

    const OUTPUT_NORMAL = 0;
    const OUTPUT_RAW = 1;
    const OUTPUT_PLAIN = 2;

    protected $verbosity;
    protected $formatter;

    /**
     * Constructor.
     *
     * @param integer                   $verbosity  The verbosity level (self::VERBOSITY_QUIET, self::VERBOSITY_NORMAL, self::VERBOSITY_VERBOSE)
     * @param Boolean                   $decorated  Whether to decorate messages or not (null for auto-guessing)
     * @param OutputFormatterInterface  $formatter  Output formatter instance
     */
    public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = null,
                                OutputFormatterInterface $formatter = null)
    {
        if (null === $formatter) {
            $formatter = new OutputFormatter();
        }

        $this->verbosity = null === $verbosity ? self::VERBOSITY_NORMAL : $verbosity;
        $this->formatter = $formatter;
        $this->formatter->setDecorated((Boolean) $decorated);
    }

    /**
     * Sets output formatter.
     *
     * @param   OutputFormatterInterface    $formatter
     */
    public function setFormatter(OutputFormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * Returns current output formatter instance.
     *
     * @return  OutputFormatterInterface
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * Sets the decorated flag.
     *
     * @param Boolean $decorated Whether to decorated the messages or not
     */
    public function setDecorated($decorated)
    {
        $this->formatter->setDecorated((Boolean) $decorated);
    }

    /**
     * Gets the decorated flag.
     *
     * @return Boolean true if the output will decorate messages, false otherwise
     */
    public function isDecorated()
    {
        return $this->formatter->isDecorated();
    }

    /**
     * Sets the verbosity of the output.
     *
     * @param integer $level The level of verbosity
     */
    public function setVerbosity($level)
    {
        $this->verbosity = (int) $level;
    }

    /**
     * Gets the current verbosity of the output.
     *
     * @return integer The current level of verbosity
     */
    public function getVerbosity()
    {
        return $this->verbosity;
    }

    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param string|array $messages The message as an array of lines of a single string
     * @param integer      $type     The type of output
     */
    public function writeln($messages, $type = 0)
    {
        $this->write($messages, true, $type);
    }

    /**
     * Writes a message to the output.
     *
     * @param string|array $messages The message as an array of lines of a single string
     * @param Boolean      $newline  Whether to add a newline or not
     * @param integer      $type     The type of output
     *
     * @throws \InvalidArgumentException When unknown output type is given
     */
    public function write($messages, $newline = false, $type = 0)
    {
        if (self::VERBOSITY_QUIET === $this->verbosity) {
            return;
        }

        if (!is_array($messages)) {
            $messages = array($messages);
        }

        foreach ($messages as $message) {
            switch ($type) {
                case Output::OUTPUT_NORMAL:
                    $message = $this->formatter->format($message);
                    break;
                case Output::OUTPUT_RAW:
                    break;
                case Output::OUTPUT_PLAIN:
                    $message = strip_tags($this->formatter->format($message));
                    break;
                default:
                    throw new \InvalidArgumentException(sprintf('Unknown output type given (%s)', $type));
            }

            $this->doWrite($message, $newline);
        }
    }

    /**
     * Writes a message to the output.
     *
     * @param string  $message A message to write to the output
     * @param Boolean $newline Whether to add a newline or not
     */
    abstract public function doWrite($message, $newline);
}
