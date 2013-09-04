#!/usr/local/bin/tclsh8.5
#
# php-autoload-generator
# (c) Sébastien Santoro aka Dereckson, 2010-2013, some rights resrved.
# Released under BSD license
#
# Last version of the code can be fetch at:
# http://bitbucket.org/dereckson/php-autoload-generator
#
# This code generator write a __autoload() PHP method, when you don't have a
# consistent pattern for classes naming and don't want to register several
# autoloader with spl_autoload_register().
#
# Parses your classes folder & reads each file to check if it contains classes.
#

#
# Configuration
#

#A list of regexp, one per directory to ignore
set config(directoriesToIgnore) {Smarty SmartLine}

#The output to produce before the lines
set config(templateBefore) "<?php

/**
 * This magic method is called when a class can't be loaded
 */
function __autoload (\$className) {
    //Classes"

#The output to produce after the 
set config(templateAfter) "
    //Loader
    if (array_key_exists(\$className, \$classes)) {
        require_once(\$classes\[\$className]);
    }
}"

#The line format, for %%lines%%
set config(templateClassLine) {    $classes['%%class%%'] = '%%file%%';}

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

#
# Helpers methods
#

#Find .php script
proc find_scripts {directory} {
   set dirs {}
   foreach i [lsort [glob -nocomplain -dir $directory *]] {
      if {[file type $i] == "directory" && ![is_ignored_directory $i]} {
         lappend dirs $i
      } elseif {[file extension $i] == ".php"} {
         find_objects $i
      }
   }
   foreach subdir $dirs {
      #Adds a white line as a separator, then the classes in this dir
      puts ""
      find_scripts $subdir
   }
   
}

#Called when we've got a winner
proc add_class {script class} {
   global config
   set line $config(templateClassLine)
   regsub -all -- "%%class%%" $line $class line
   regsub -all -- "%%file%%" $line $script line
   puts $line
}

#Find objects like classes or interfaces in the file
#Thanks to Richard Suchenwirth for its glob-r code snippet this proc is forked
proc find_objects {file} {
   set fp [open $file]
   while {[gets $fp line] >= 0} {
      set pos1 [string first "class " $line]
      if {$pos1 == -1} {
         set pos1 [string first "interface " $line]
         set len 10
      } {
         set len 6
      }
      set pos2 [string first " \{" $line $pos1]
      set pos3 [string first " implements" $line $pos1]
      set pos4 [string first " extends" $line $pos1]
      if {$pos1 > -1 && $pos2 > -1} {
         if {$pos4 > -1} {
            #We test implements first, as if a class implements and extends
            #the syntax is class Plane extends Vehicle implements FlyingItem
            set pos $pos4
         } elseif {$pos3 > -1} {
            set pos $pos3
         } else {
            set pos $pos2
         }
         set class [string range $line [expr $pos1 + $len] [expr $pos - 1]]
         
         add_class $file $class
      }
   }
   close $fp
}

#Check if the directory is ignored
proc is_ignored_directory {directory} {
   global config
   foreach re $config(directoriesToIgnore) {
      if [regexp $re $directory] {return 1}
   }
   return 0
}

# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # #

#
# Procedural code
#

if {$argc > 0} {
   set directory [lindex $argv 0]
} {
   set directory .
}

puts $config(templateBefore)
find_scripts $directory/includes
puts $config(templateAfter)
