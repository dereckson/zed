<?php

/*
 * Zed
 * (c) 2010, Dereckson, some rights reserved
 * Released under BSD license
 *
 * Content for anonymous users
 */

//
// Prepares the page
//

switch ($url[0]) {
    case 'tour':
        //The user have forgotten .html, let's redirect him
        header('Location: ' . $Config['StaticContentURL'] . '/tour.html');
        exit;
    
    case 'invite':
        //Invite form
        if ($_POST['form'] == 'account.create') {
            //User tries to claim its invite to create an account
            require_once('includes/objects/invite.php');
            require_once('includes/objects/user.php');
            
            //Gets invite
            $invite = new Invite($_POST['invite_code']);
            if ($invite->lastError != '') {
                //Not existant invite.
                $smarty->assign('NOTIFY', lang_get("IncorrectInviteCode"));
            } elseif ($invite->is_claimed()) {
                //The invitation have already claimed by someone else.
                $smarty->assign('NOTIFY', lang_get("InviteCodeAlreadyClaimed"));
            } else {
                //Checks if the given information is correct
                //We ignore bad mails. All we really need is a login and a pass.
                //We fill our array $errors with all the errors
                $errors = array();
                if (!$_POST['username']) {
                    $errors[] = lang_get('MissingUsername');
                } elseif (!User::is_available_login($_POST['username'])) {
                    $errors[] =  lang_get('LoginUnavailable');
                }
                
                if (User::get_username_from_email($_POST['email']) !== false) {
                    $errors[] = "There is already an account with this e-mail.";
                }
                
                if (!$_POST['passwd']) {
                    $errors[] = lang_get('MissingPassword');
                }
                               
                if (count($errors)) {
                    $smarty->assign('WAP', join('<br />', $errors));
                } else {
                    //Creates account
                    $user = new User();
                    $user->regdate = time();
                    $user->generate_id();
                    $user->name = $_POST['username'];
                    $user->active = 1;
                    $user->email = $_POST['email'];
                    $user->set_password($_POST['passwd']);
                    $user->save_to_database();
                    
                    //Updates invite
                    $invite->to_user_id = $user->id;
                    $invite->save_to_database();
                    
                    
                    //Notifies inviter
                    require_once('includes/objects/message.php');
                    $message = new Message();
                    $message->from = 0;
                    $message->to = $invite->from_perso_id;
                    $message->text =  sprintf(lang_get('InviteHaveBeenClaimed'), $invite->code);
                    $message->send();
                    
                    //Logs in user
                    login($row[user_id], $Login);
                    
                    //Prints confirm message
                    $smarty->assign('WAP', lang_get("AccountCreated"));
                    $template = 'void.tpl';
                    
                    //Redirects users to homepage
                    header('refresh: 5; url=' . get_url());
                    $message = new Message();
                    $message->from = 0;
                    $message->to = $invite->from_perso_id;
                    $message->text =  sprintf(lang_get('InviteHaveBeenClaimed', $invite->code));
                    $message->send();
                    
                    //Logs in user
                    login($row[user_id], $Login);
                    
                    //Redirects users to homepage
                    header('refresh: 5; url=' . get_url());
                    
                    //Prints confirm message
                    $smarty->assign('WAP', lang_get("AccountCreated"));
                    
                    include("void.php");

                    break;
                }
            }
            
            //Keeps username, email, invite code printed on account create form
            $smarty->assign('username', $_POST['username']);
            $smarty->assign('invite_code', $_POST['invite_code']);
            $smarty->assign('email', $_POST['email']);
        }
        
        //If the invite code is specified, checks format
        if ($url[1]) {
            if (preg_match("/^([A-Z]){3}([0-9]){3}$/i", $url[1])) {
                $smarty->assign('invite_code', strtoupper($url[1]));
            } else {
                $smarty->assign('NOTIFY', lang_get("IncorrectInviteCode"));
            }
        }
        
        $template = 'account_create.tpl';
        break;
    
    default:
        //Login form
        if (array_key_exists('LastUsername', $_COOKIE))
            $smarty->assign('username', $_COOKIE['LastUsername']);
        if (array_key_exists('LastOpenID', $_COOKIE))
            $smarty->assign('OpenID', $_COOKIE['LastOpenID']);
        $smarty->assign('LoginError', $LoginError);
        $template = 'login.tpl';
        break;
}

//
// HTML output
//

if ($template) $smarty->display($template);

?>