<?php

/*
	SmartLine 0.1
	http://www.espace-win.org/EWOSP/SmartLine
	(c) 2007 Espace Win Open Source Project, some rights reserved.
	Released under BSD License
	
	Developer leader: Sébastien Santoro aka Dereckson
	http://purl.espace-win.org/Who/Dereckson
	
	Support: http://purl.espace-win.org/EWOSP/Support
	
	0.1    2007-07-28 01:36 [DcK]    Initial release
*/

///////////////////////////////////////////////////////////////////////////////
// SECTION I - INITIALIZATION
///////////////////////////////////////////////////////////////////////////////

//Constants
if (!defined('STDOUT')) define('STDOUT',  1, true);
if (!defined('STDERR')) define('STDERR', -1, true);

///////////////////////////////////////////////////////////////////////////////
// SECTION Ibis - L10n
///////////////////////////////////////////////////////////////////////////////


//Assumes $lang is a standard array
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

//Error Handler
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
    $_SESSION['SmartLineOutput'][STDERR][] = "[PHP $type] $error ";
    return true;
}

///////////////////////////////////////////////////////////////////////////////
// SECTION III - BASE CLASSES
///////////////////////////////////////////////////////////////////////////////

//SmartLineCommand is a class implemanting a SmartLine command.
//If you want to create a more complex command, extends this class.
class SmartLineCommand {
	public function __construct ($SmartLine) {
		$this->SmartLine = $SmartLine;
	}
	
	//Gets command help 
	//Returns help text to print
	//Returns false for default behavior
	//(ie prints $lang['Help'][$command])
	public function help () {
        return false;
	}
	
	//Runs command
	//$argv is an array containing args, $argc = count($argv)
	public function run ($argv, $argc) {
	
	}

	//Gets the SmartLine where this instance of the command is registered
	public $SmartLine;
}

//This class represents a SmartLine instance.
//If you use only register_object, you can use it directly
//If you use register_method, extends this class in your SmartLine.
class SmartLine {
	public function __construct () {
		//Assumes we've an empty array where store registered commands.
		$this->commands = array();
		//Let's register standard commands
		$this->register_object('showcommands', 'ShowCommandsSmartLineCommand');
		$this->register_object('help', 'HelpSmartLineCommand');
	}
	
	//Registers a private method as command 
	public function register_method ($command, $function = null, $useArgvArgc = false) {
		if (is_null($function)) $function = $command;
		
		if (!method_exists($this, $function)) {
			$this->lastError = "Registration failed. Unknown method $function";
			return false;
		}

		$className = ucfirst($function) . 'SmartLineCommand';
		//If class exists, add a uniqid after function
		while (class_exists($className)) {
			$className = uniqid(ucfirst($function)) . 'SmartLineCommand';
		}
		//Creates the class
		if ($useArgvArgc) {
		    $call = "$this->SmartLine->$function(\$argv, \$argc);";
		} else {
		    //We don't know how many args we've, so we use call_user_func_array 
		    $call = "array_shift(\$argv);
		             call_user_func_array(
		                array(&\$this->SmartLine, '$function'),
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
	
	//Registers an object extending SmartLineCommand as command
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
	
	//Returns true if $command has been registred
	public function isRegistered ($command) {
	    if (!$this->caseSensitive) $command = strtolower($command);
	    return array_key_exists($command, $this->commands);
	}
		
	//Executes an expression
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
		$result = $this->commands[$command]->run($argv, $argc);
		restore_error_handler();
		return $result;
	}
	
	public function puts ($message, $output = STDOUT) {
	    //Adds message to current output queue
	    $_SESSION['SmartLineOutput'][$output][] = $message;
	}
	
	public function truncate ($output = STDOUT) {
		unset($_SESSION['SmartLineOutput'][$output]);
	}
	
	public function gets ($output = STDOUT) {
		if (count($_SESSION['SmartLineOutput'][$output] > 0))
			return array_pop($_SESSION['SmartLineOutput'][$output]);
	}
	
	public function count ($output = STDOUT) {
		return count($_SESSION['SmartLineOutput'][$output]);
	}
	
	public function gets_all ($output = STDOUT, $prefix = '<p>', $suffix = '</p>') {
		$count = count($_SESSION['SmartLineOutput'][$output]);
		if ($count == 0) return;
		for ($i = 0 ; $i < $count ; $i++)
			$buffer .= $prefix . $_SESSION['SmartLineOutput'][$output][$i] . $suffix;
		unset ($_SESSION['SmartLineOutput'][$output]);
        return $buffer;
	}
    
	public function prints_all ($output = STDOUT, $prefix = '<p>', $suffix = '</p>') {
		$count = count($_SESSION['SmartLineOutput'][$output]);
		if ($count == 0) return;
		for ($i = 0 ; $i < $count ; $i++)
			echo $prefix, $_SESSION['SmartLineOutput'][$output][$i], $suffix;
		unset ($_SESSION['SmartLineOutput'][$output]);
	}
	
	public function gethelp ($command) {
		return $this->commands[$command]->help();
	}
	
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

//Standard command "showcommands"
class ShowCommandsSmartLineCommand extends SmartLineCommand {
	public function run ($argv, $argc) {
	    $commands = array_keys($this->SmartLine->commands);
	    sort($commands);
	    $this->SmartLine->puts(implode(' ', $commands));
	}
}

//Standard command "help"
class HelpSmartLineCommand extends SmartLineCommand {
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

?>