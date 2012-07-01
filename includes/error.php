<?php

/**
 * Error handler
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This error handler uses the same idea and message_die methode signature
 * of the phpBB 2 one.
 * 
 * @package     Zed
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 *
 * @todo delete old_message_die method and write alternative HTML textual output
 *       in the message_die method
 */

///
/// Error constants
///

/**
 * SQL_ERROR is the constant meaning the error is a SQL error.
 *
 * As a message_die function parameter, it allows to add SQL specific debug information.
 */
define ("SQL_ERROR",  65);

/**
 * HACK_ERROR is the constant meaning access is non authorized to the resource.
 *
 * It encompasses two problematics:
 *     the URL points to a resource belonging to another user or for the current user have no access right (for malformed URL, pick instead GENERAL_ERROR) ;
 *     the user is anonymous, instead to be logged in.
 *
 * A suggested way to handle the second problematic is to store in hidden input
 * fields or better in the session the previous form data, and to print a login
 * form.
 *
 * If you implement this, you don't even need to distinguishes between the two
 * cases, as once logged in, the regular HACK_ERROR could also be printed.
 */
define ("HACK_ERROR", 99);

/**
 * GENERAL_ERROR is the constant meaning the error is general, ie not covered by
 * another more specific error constant.
 */
define ("GENERAL_ERROR", 117);

///
/// Error helper functions
///

/**
 * Output a general error, with human-readable information about the specified
 * expression as error message ; terminates the current script.
 *
 * @see message_die
 *
 * @param mixed $expression the expression to be printed
 * @param string $title the message title (optionnal, default will be 'Debug')
 */
function dieprint_r ($expression, $title = '') {
    if (!$title) {
	$title = 'Debug'; //if title is omitted or false/null, default title
    }
    message_die(GENERAL_ERROR, '<pre>' . print_r($expression, true) .'</pre>', $title);
}

/**
 * Outputs an error message and terminates the current script.
 *
 * Error will be output through Smarty one of the following templates :
 *     error_block.tpl if the header have already been printed ;
 *     error.tpl if the error ocurred before the header were called and printed.
 *
 * If smarty couldn't be loaded, old_message_die method will be called, which
 * produces a table output.
 *
 * @param int $msg_code an integer constant identifying the error (HACK_ERROR, SQL_ERROR, GENERAL_ERROR)
 * @param string $msg_text the error message text (optionnal, but recommanded)
 * @param string $msg_title the error message title (optionnal)
 * @param int $err_line the line number of the file where the error occured (optionnal, suggested value is __LINE__)
 * @param string $err_line the path of file where the error occured (optionnal, suggested value is __FILE__)
 * @param string $sql the SQL query (optionnal, used only if msg_code is SQL_ERROR)
 */
function message_die ($msg_code, $msg_text = '', $msg_title = '', $err_line = '', $err_file = '', $sql = '') {
    global $smarty, $db;

    if ($smarty) {
        $debug_text = $msg_text;

        if ($err_line && $err_file)
            $debug_text .= ' &mdash; ' . $err_file. ', ' . lang_get('line') . ' ' . $err_line ;
        
        switch ($msg_code) {
            case HACK_ERROR:
                $smarty->assign('TITLE', lang_get('UnauthorizedAccess'));
                break;
            
            case SQL_ERROR:
                $smarty->assign('TITLE', lang_get('SQLError'));
                $sql_error = $db->sql_error();
                if ($sql_error['message'] != '') {
                    $debug_text .= '<br />' . lang_get('Error') . ' n° ' . $sql_error['code'] . lang_get('_t') .
                                ' ' .$sql_error['message'];
                }
                $debug_text .= "</p><h2>Query:</h2><p>$sql";
                break;
            
            default:
                $smarty->assign('WAP', "Message code error.<br />Expected: HACK_ERROR, SQL_ERROR, GENERAL_ERROR");
                //Falls to GENERAL_ERROR
            
            case GENERAL_ERROR:
                if ($msg_title)
		    $smarty->assign('TITLE', $msg_title);
		else
		    $smarty->assign('TITLE', lang_get('GeneralError'));
                break;
        }
        
        
        $smarty->assign('ERROR_TEXT', $debug_text);
        $template = (defined('HEADER_PRINTED') &&  HEADER_PRINTED) ? "error_block.tpl" : "error.tpl";
	$smarty->display($template);
        exit;
    } else {
        old_message_die($msg_code, $msg_text, $msg_title, $err_line, $err_file, $sql);
    }
}

/**
 * Outputs an error message and terminates the current script.
 *
 * This is the message_die method from Espace Win, used on Zed as fallback if Smarty isn't initialized yet.
 *
 * @param int $msg_code an integer constant identifying the error (HACK_ERROR, SQL_ERROR, GENERAL_ERROR)
 * @param string $msg_text the error message text (optionnal, but recommanded)
 * @param string $msg_title the error message title (optionnal)
 * @param int $err_line the line number of the file where the error occured (optionnal, suggested value is __LINE__)
 * @param string $err_line the path of file where the error occured (optionnal, suggested value is __FILE__)
 * @param string $sql the SQL query (optionnal, used only if msg_code is SQL_ERROR)
 *
 * @deprecated since 0.1
 */
function old_message_die($msg_code, $msg_text = '', $msg_title = '', $err_line = '', $err_file = '', $sql = '')
{
	global $db, $Utilisateur;
	$sql_store = $sql;
	
	if ($msg_code == HACK_ERROR && $Utilisateur[user_id] < 1000) {
		global $LoginResult;
		foreach ($_POST as $name => $value) {
			$champs .= "<input type=hidden name=$name value=\"$value\" />";
		}
		$titre = "Qui êtes-vous ?";
		$debug_text = "Vous devez être authentifié pour accéder à cette page.";
		$debug_text .= "
		<FORM method='post'>
		$champs
		<table border='0'>
		  <tr>
			<td><STRONG>Login</STRONG></td>
			<td><input name='Login' type='text' id='Login'  value='$_POST[Login]' size='10' /></td>
			<td><STRONG>Mot de passe</STRONG></td>
			<td>
				<input name='MotDePasse' type='password' id='MotDePasse' size='10' />
				<input type='submit' name='LoginBox' value='Connexion' />
			</td>
		  </tr>
		  <tr> 
			<td align=center COLSPAN=4><a href='/?Topic=My&Article=Enregistrer'>Je d&eacute;sire ouvrir un compte</a></td>
		  </tr>
		</TABLE><span class=error>$LoginResult</span>
		</FORM>
		";
	} elseif ($msg_code == HACK_ERROR) {
		$titre = "Accès non autorisé";
		$debug_text = $msg_text;
	} elseif ($msg_code == SQL_ERROR) {
		$titre = "Erreur dans la requête SQL";
		$sql_error = $db->sql_error();
		$debug_text = $msg_text;
		if ( $err_line != '' && $err_file != '') $debug_text .= ' dans ' . $err_file. ', ligne ' . $err_line ;
		if ( $sql_error['message'] != '' ) $debug_text .= '<br />Erreur n° ' . $sql_error['code'] . ' : ' . $sql_error['message'];
		if ( $sql_store != '' ) $debug_text .= "<br /><strong>$sql_store</strong>";
	} elseif ($msg_code == GENERAL_ERROR) {
	    $titre = $msg_title;
	    $debug_text = $msg_text;
	    if ($err_line && $err_file) {
		    $debug_text .= "<BR />$err_file, ligne $err_line";
	    }
	}	
	
	echo "
	<TABLE height='100%' cellSpacing=0 cellPadding=0 width='100%' border=0>
  <TBODY>
  <TR>
    <TD vAlign=top align=middle>
      <TABLE cellSpacing=0 cellPadding=0 border=0>
        <TBODY> 
        <TR>
          <TD vAlign=top rowSpan=5><IMG height=177 alt='' 
            src='/_pict/error/notfound.jpg' width=163 border=0></TD>
          <TD colSpan=4><IMG height=2 alt='' src='/_pict/error/mrblue.gif' 
            width=500 border=0></TD>
          <TD><IMG height=2 alt='' src='/_pict/error/undercover.gif' width=1 
            border=0></TD></TR>
        <TR>
          <TD vAlign=bottom rowSpan=4 bgcolor='#FFFFFF'><IMG height=43 alt='' 
            src='/_pict/error/ecke.gif' width=14 border=0></TD>
          <TD vAlign=center align=middle rowSpan=2 bgcolor='#FFFFFF'> 
            <TABLE cellSpacing=1 cellPadding=0 width=470 border=0>
              <TBODY>
              <TR>
                <TD><FONT face='Verdana, Helvetica, sans-serif' color=red 
                  size=4><B>$titre</B></FONT><BR>
                  <IMG height=5 alt='' 
                  src='/_pict/error/undercover.gif' width=14 border=0><BR></TD></TR>
              <TR>
                <TD><FONT face='Verdana, Helvetica, sans-serif' color=black 
                  size=2>$debug_text</FONT></TD></TR></TBODY></TABLE></TD>
          <TD align=right width=2 rowSpan=2 bgcolor='#FFFFFF'><IMG height=146 alt='' 
            src='/_pict/error/mrblue.gif' width=2 border=0></TD>
          <TD bgcolor='#FFFFFF'><IMG height=132 alt='' src='/_pict/error/undercover.gif' width=1 
            border=0></TD>
        </TR>
        <TR>
          <TD><IMG height=14 alt='' src='/_pict/error/undercover.gif' width=1 
            border=0></TD></TR>
        <TR>
          <TD colSpan=2><IMG height=2 alt='' src='/_pict/error/mrblue.gif' 
            width=486 border=0></TD>
          <TD><IMG height=2 alt='' src='/_pict/error/undercover.gif' width=1 
            border=0></TD></TR>
        <TR>
          <TD colSpan=2><IMG height=27 alt='' src='/_pict/error/undercover.gif' 
            width=486 border=0></TD>
          <TD><IMG height=27 alt='' src='/_pict/error/undercover.gif' width=1 
            border=0></TD></TR></TBODY></TABLE>
      <P>&nbsp;</P>
      </TD></TR></TBODY></TABLE>
	";
	
	exit;
}

?>