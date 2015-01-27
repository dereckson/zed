<?php

/**
 * SmartLine 0.1
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * 0.1    2007-07-28 01:36 [DcK]    Initial release
 *        2010-07-02 00:39 [Dck]    Documentation
 *
 * @package     Zed
 * @subpackage  SmartLine
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2007 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @link        http://bitbucket.org/dereckson/smartline
 * @filesource

///////////////////////////////////////////////////////////////////////////////
// SECTION I - INITIALIZATION
///////////////////////////////////////////////////////////////////////////////

//Constants

/**
 * The standard, regular output (like STDOUT on POSIX systems)
 */
if (!defined('STDOUT')) define('STDOUT',  1, true);

/**
 * The error output (like STDERR on POSIX systems)
 */
if (!defined('STDERR')) define('STDERR', -1, true);

///////////////////////////////////////////////////////////////////////////////
// SECTION Ibis - L10n
///////////////////////////////////////////////////////////////////////////////

//Ensures $lang is a standard array
if (empty($lang) || !is_array($lang)) {
    $lang = array();
}

$lang = array_merge($lang, array(
    //Errors
    'InvalidCommand' => "Invalid command %s. Use <strong>showcommands</strong> to show all commands.",
    'RegisteredButNotExistingCommand' => "[CRITICAL ERROR] The command %s has correctly been registered but its method or class doesn't exist.",
    'NotYetHelpForThiscommand' => "This command hasn't been documented yet.",

    //Help
    'DefaultHelp' => "This SmartLine is a command line interface.
                      <br /><br /><strong>showcommands</strong> prints the list.
                      <br /><strong>help &lt;command&gt;</strong> prints help for this command.",
    'Help' => array(
        'help' => "<strong>help &lt;command&gt;</strong> prints command help.",
        'showcommands' => 'show available commands'
    )
));

///////////////////////////////////////////////////////////////////////////////
// SECTION II - HELPERS FUNCTIONS
///////////////////////////////////////////////////////////////////////////////

/**
 * Error handler called during SmartLine command execution.
 *
 * Any error occuring during command execution will be set in STDERR.
 *
 * To get an array with all the errors:
 * <code>$errors = $yourSmartLine->gets_all(STDERR)</code>
 *
 * Or to prints all the error:
 * <code>$yourSmartLine->prints_all(STDERR)</code>
 *
 * Or to pops (gets and deletes) only the last error:
 * <code>$lastError = $yourSmartLine->gets(STDERR)</code>
 *
 * @link http://www.php.net/manual/en/function.set-error-handler.php set_error_handler, PHP manual
 * @link http://www.php.net/manual/en/errorfunc.examples.php Error handling examples, PHP manual
 *
 * @param int $level The PHP error level
 * @param string $error The error description
 * @param string $file The script where the error occured
 * @param int $line The line where the error occured
 */
function SmartLineHandler($level, $error, $file, $line) {
    switch ($level) {
       case E_NOTICE:
        $type = 'Notice';
        break;

       CASE E_WARNING:
        $type = 'Warning';
        break;

       CASE E_ERROR:
        $type = 'Error';
        break;

       default:
        $type = "#$level";
    }
    $_SESSION['SmartLineOutput'][STDERR][] = "[PHP $type] $error in $file line $line.";
    return true;
}

///////////////////////////////////////////////////////////////////////////////
// SECTION III - BASE CLASSES
///////////////////////////////////////////////////////////////////////////////

//SmartLineCommand is a class implemanting a SmartLine command.
//If you want to create a more complex command, extends this class.

/**
 * The SmartLine command base class.
 *
 * To add a command, create an instance of the class, like:
 * <code>
 * class HelloWorldSmartLineCommand extends SmartLineCommand {
 *   public function run ($argv, $argc) {
 *       $this->SmartLine->puts('Hello World!');
 *    }
 * }
 * </code>
 *
 * Then, registers your command:
 * <code>
 * $yourSmartLine->register_object('hello', 'HelloWorldSmartLineCommand');
 * </code>
 *
 * @see SmartLine::register_object
 */
class SmartLineCommand {
    /**
     * Initializes a new instance of the SmartLine Command
     *
     * @param SmartLine $SmartLine the SmartLine the command belongs
     */
	public function __construct ($SmartLine) {
		$this->SmartLine = $SmartLine;
	}

    /**
     * Gets the command help text or indicates help should be fetched from $lang array
     *
     * @return string|bool a string containing the command help or the bool value false, to enable the default behavior (ie prints $lang['help']['nameOfTheCommand'])
     */
	public function help () {
        return false;
	}

    /**
     * Runs the command
     *
     * @param array $argv an array of string, each item a command argument
     * @param int $argc the number of arguments
     */
	public function run ($argv, $argc) {

	}

	/**
     * The SmartLine where this instance of the command is registered
     *
     * @var SmartLine
     */
	public $SmartLine;
}

/**
 * This class represents a SmartLine instance
 *
 * If you use only register_object, you can use it directly.
 * If you use register_method, extends this class in your SmartLine.
 */
class SmartLine {
    /**
     * Initializes a new instance of the SmartLine object.
     */
	public function __construct () {
		//Assumes we've an empty array where store registered commands.
		$this->commands = array();

		//Let's register standard commands
		$this->register_object('showcommands', 'ShowCommandsSmartLineCommand');
		$this->register_object('help', 'HelpSmartLineCommand');
	}

	/**
     * Registers a private method as command.
     *
     * @param string $command The name of the command to register
     * @param string $method The method to register [OPTIONAL]. If omitted, the method regisered will be the method having the same name as the command.
     * @param bool $useArgvArgc If true, indicates the method uses $argv, $argc as parameters. If false, indicates the method uses its parameters (default behavior). [OPTIONAL]
     *
     * @return bool true if the command have successfully been registered ; otherwise, false.
     */
	public function register_method ($command, $method = null, $useArgvArgc = false) {
		if (is_null($function)) $method = $command;

		if (!method_exists($this, $method)) {
			$this->lastError = "Registration failed. Unknown method $method";
			return false;
		}

		$className = ucfirst($method) . 'SmartLineCommand';
		//If class exists, add a uniqid after function
		while (class_exists($method)) {
			$className = uniqid(ucfirst($method)) . 'SmartLineCommand';
		}
		//Creates the class
		if ($useArgvArgc) {
		    $call = "$this->SmartLine->$method(\$argv, \$argc);";
		} else {
		    //We don't know how many args we've, so we use call_user_func_array
		    $call = "array_shift(\$argv);
		             call_user_func_array(
		                array(&\$this->SmartLine, '$method'),
		                \$argv
		             );";
        }
		$code = "class $className extends SmartLineCommand {
    public function run (\$argv, \$argc) {
        $call
    }
}";
		eval($code);
		$this->register_object($command, $className);
		return true;
	}

	/**
     * Registers an object extending SmartLineCommand as command.
     *
     * @param string $command The name of the command to register
     * @param SmartLineCommand|string $object The object extending SmartLineCommand. This can be the name of the class (string) or an instance already initialized of the object (SmartLineCommand).
     * @return bool true if the command have successfully been registered ; otherwise, false.
     */
	public function register_object ($command, $object) {
		if (is_object($object)) {
			//Sets SmartLine property
			$object->SmartLine = $this;
		} elseif (is_string($object)) {
			//Creates a new instance of $object
			$object = new $object($this);
		} else {
			$this->lastError = "Registration failed. register_object second parameter must be a class name (string) or an already initialized instance of such class (object) and not a " . gettype($object);
			return false;
		}
		if (!$this->caseSensitive) $command = strtolower($command);
		$this->commands[$command] = $object;
		return true;
	}

    /**
     * Determines wheter the specified command have been registered.
     *
     * @param string $command The name of the command to check
     * @return true if the specified command have been registered ; otherwise, false.
     */
	public function isRegistered ($command) {
	    if (!$this->caseSensitive) $command = strtolower($command);
	    return array_key_exists($command, $this->commands);
	}

	/**
     * Executes the specified expression.
     *
     * If an error occurs during the command execution:
     *     the STDERR output will contains the errors,
     *     the value returned by this methos will be false.
     *
     * To execute the command and prints error:
     * <code>
     * $fooSmartLine = new SmartLine();
     * //...
     * $result = $fooSmartLine->execute($expression);
     * $fooSmartLine->prints_all();
     * if (!$result) {
     *     //Errors!
     *     echo "<h3>Errors</h3>";
     *     $fooSmartLine->prints_all(STDERR);
     * }
     * </code>
     *
     * @param string $expression The expression containing the command to execute
     * @return bool true if the command have been successfuly executed ; otherwise, false.
     */
	public function execute ($expression) {
		//Does nothing if blank line
		if (!$expression) return;

		//Prepares $argv and $argc
		$argv = $this->expression2argv($expression);
		$argc = count($argv);

		//Gets command
		$command = $this->caseSensitive ? $argv[0] : strtolower($argv[0]);

		//If command doesn't exist, throws an error
		if (!array_key_exists($command, $this->commands)) {
			global $lang;
			$this->puts(sprintf($lang['InvalidCommand'], $command), STDERR);
			return false;
		}

		//Executes command, intercepting error and returns result
		set_error_handler("SmartLineHandler");
        try {
            $result = $this->commands[$command]->run($argv, $argc);
        } catch (Exception $ex) {
            $this->puts("<pre>$ex</pre>", STDERR);
        }
		restore_error_handler();
		return $result;
	}

    /**
     * Adds a message to the specified output queue.
     *
     * @param string $message the message to queue
     * @param int $output The output queue (common values are STDERR and STDOUT constants). It's an optionnal parameter ; if ommited, the default value will be STDOUT.
     */
	public function puts ($message, $output = STDOUT) {
	    //
	    $_SESSION['SmartLineOutput'][$output][] = $message;
	}

    /**
     * Truncates the specified output queue.
     *
     * @param int $output The output queue (common values are STDERR and STDOUT constants). It's an optionnal parameter ; if ommited, the default value will be STDOUT.
     */
	public function truncate ($output = STDOUT) {
		unset($_SESSION['SmartLineOutput'][$output]);
	}

    /**
     * Pops (gets and clears) the first message from the specified output queue.
     *
     * @param int $output The output queue (common values are STDERR and STDOUT constants). It's an optionnal parameter ; if ommited, the default value will be STDOUT.
     * @return string the message
     */
	public function gets ($output = STDOUT) {
		if (count($_SESSION['SmartLineOutput'][$output] > 0))
			return array_pop($_SESSION['SmartLineOutput'][$output]);
	}

    /**
     * Gets the number of messages in the specified output queue.
     *
     * @param int $output The output queue (common values are STDERR and STDOUT constants). It's an optionnal parameter ; if ommited, the default value will be STDOUT.
     */
	public function count ($output = STDOUT) {
		return count($_SESSION['SmartLineOutput'][$output]);
	}

    /**
     * Gets all the message from the specified output queue.
     *
     * @param int $output The output queue (common values are STDERR and STDOUT constants). It's an optionnal parameter ; if ommited, the default value will be STDOUT.
     * @param string $prefix The string to prepend each message with. It's an optionnal parameter ; if ommited, '<p>'.
     * @param string $suffix The string to append each message with. It's an optionnal parameter ; if ommited, '</p>'.
     * @return Array an array of string, each item a message from the specified output queue
     */
	public function gets_all ($output = STDOUT, $prefix = '<p>', $suffix = '</p>') {
		$count = count($_SESSION['SmartLineOutput'][$output]);
		if ($count == 0) return;
		for ($i = 0 ; $i < $count ; $i++)
			$buffer .= $prefix . $_SESSION['SmartLineOutput'][$output][$i] . $suffix;
		unset ($_SESSION['SmartLineOutput'][$output]);
        return $buffer;
	}

    /**
     * Prints all the message from the specified output queue.
     *
     * @param int $output The output queue (common values are STDERR and STDOUT constants). It's an optionnal parameter ; if ommited, the default value will be STDOUT.
     * @param string $prefix The string to prepend each message with. It's an optionnal parameter ; if ommited, '<p>'.
     * @param string $suffix The string to append each message with. It's an optionnal parameter ; if ommited, '</p>'.
     */
	public function prints_all ($output = STDOUT, $prefix = '<p>', $suffix = '</p>') {
		$count = count($_SESSION['SmartLineOutput'][$output]);
		if ($count == 0) return;
		for ($i = 0 ; $i < $count ; $i++)
			echo $prefix, $_SESSION['SmartLineOutput'][$output][$i], $suffix;
		unset ($_SESSION['SmartLineOutput'][$output]);
	}

    /**
     * Gets the command help
     *
     * @param string $command The command to get help from
     * @param string The command help
     */
	public function gethelp ($command) {
		return $this->commands[$command]->help();
	}

    /**
     * Gets an an argv array from the specified expression
     *
     * @param string $expression The expression to transform into a argv array
     * @return Array An array of string, the first item the command, the others those arguments.
     */
	private function expression2argv ($expression) {
        //Checks if expression contains "
        $pos1 = strpos($expression, '"');

        //We isolate "subexpression"
        if ($pos1 !== false) {
            $pos2 = $pos1;
            do {
                $pos2 = strpos($expression, '"', $pos2 + 1);
            } while ($pos2 !== false && ($expression[$pos2 - 1] == "\\" && $expression[$pos2 - 2] != "\\"));

            if ($pos2 === false) {
                //If final quote is missing, throws a warning and autoadds it.
                $this->puts("[Warning] Final \" missing in $expression.", STDERR);
                $argv = $this->expression2argv(substr($expression, 0, $pos1));
                $argv[] = substr($expression, $pos1 + 1);
                return $argv;
            }
            return array_merge(
                $this->expression2argv(substr($expression, 0, $pos1)),
                array(substr($expression, $pos1 + 1, $pos2 - $pos1 - 1)),
                $this->expression2argv(substr($expression, $pos2 + 1))
            );
        }

        //Standard expression (ie without ")
        $argv = array();
        $items = explode(' ', $expression);
	    foreach ($items as $item) {
	        $item = trim($item);
	        if (!$item) {
	            //blank, we ignore
	            continue;
	        }
	        $argv[] = $item;
	    }
	    return $argv;
	}

	//Contains last error
	public $lastError = '';

	//If true, command isn't equal to Command
	public $caseSensitive = true;
}

///////////////////////////////////////////////////////////////////////////////
// SECTION IV - STANDARD COMMANDS
///////////////////////////////////////////////////////////////////////////////

/*
 * These commands are availaible in all default smartlines instance
 */

/**
 * The standard command "showcommands"
 *
 * This command returns a list, with all the available commands
 */
class ShowCommandsSmartLineCommand extends SmartLineCommand {
    /**
     * Runs the command
     *
     * @param array $argv an array of string, each item a command argument
     * @param int $argc the number of arguments
     */
	public function run ($argv, $argc) {
	    $commands = array_keys($this->SmartLine->commands);
	    sort($commands);
	    $this->SmartLine->puts(implode(' ', $commands));
	}
}

/**
 * The standard command "help"
 *
 * This command prints command help.
 *
 * Help could be defined
 *    in the command classes, as a return value from the help method ;
 *    in the $lang['Help'] array, at the command key (e.g. $lang['Help']['quux'] for the quux command).
 */
class HelpSmartLineCommand extends SmartLineCommand {
    /**
     * Runs the command
     *
     * @param array $argv an array of string, each item a command argument
     * @param int $argc the number of arguments
     */
	public function run ($argv, $argc) {
        global $lang;
        if ($argc == 1) {
            $this->SmartLine->puts($lang['DefaultHelp']);
        } elseif (!$this->SmartLine->isRegistered($argv[1])) {
            $this->SmartLine->puts(sprintf($lang['InvalidCommand'], str_replace(' ', '&nbsp;', $argv[1])), STDERR);
        } else {
            $command = strtolower($argv[1]);
            if (!$help = $this->SmartLine->gethelp($command)) {
                if (array_key_exists($command, $lang['Help'])) {
                    $help = $lang['Help'][$command];
                } else {
                    $help = $lang['NotYetHelpForThiscommand'];
                }
            }
            $this->SmartLine->puts($help);
        }
	}
}

///////////////////////////////////////////////////////////////////////////////
;
