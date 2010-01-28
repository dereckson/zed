<?php
/*
 * Azhàr, faeries intranet
 * (c) 2009-2010, Wolfæym, some rights reserved
 * Released under BSD license
 *
 * Homepage
 */

//Loads language file
lang_load('profile.conf');
 
//Gets username from URL
$username = $url[1];

switch ($username) {
    case 'edit':
	$mode = 'edit';
	$username = $CurrentUser->username;
	break;
    
    case 'random':
	$mode = 'view';
	$username = $db->sql_query_express("SELECT user_id FROM azhar_profiles ORDER  BY rand() LIMIT 1");
	break;
       
    default:
	$mode = 'view';
}
 
if (!$username) {
	$title = lang_get('UnknownFaerie');
	$smarty->assign('CONTENT', lang_get('Who'));
	include('raw.php');
	die();
}

//Libs
require_once('includes/objects/profile.php');
require_once('includes/objects/profilecomment.php');
require_once('includes/objects/profilephoto.php');
require_once('includes/objects/phone.php');
require_once('includes/objects/resources.php');

//Gets user information
require_once('includes/objects/user.php');
$user = new User($username);
if ($user->lastError) {
	$title = lang_get('UnknownFaerie');
	$smarty->assign('CONTENT', sprintf(lang_get('WhoIsFaerie'), $username));
	include('raw.php');
	die();
}

//Gets profile
$profile = new Profile($user->id);

//Handles form
if ($_POST['EditProfile']) {
    $profile->loadFromForm();
    $profile->updated = time();
    $profile->saveToDatabase();
    $mode = 'view';
} elseif ($_POST['UserAccount']) {
    $user->loadFromForm(false);
    $mode = 'view';
} elseif ($_POST['message_type'] == 'private_message') {
    //Sends a message
    require_once('includes/objects/message.php');
    $msg = new Message();
    $msg->from = $CurrentUser->id;
    $msg->to = $user->id;
    $msg->text = $_POST['message'];
    $msg->send();
    if ($msg->from == $msg->to) {
        $smarty->assign('NOTIFY', lang_get('MessageSentSelf'));
    } else {
       $smarty->assign('NOTIFY', lang_get('MessageSent'));
    }
} elseif ($_POST['message_type'] == 'profile_comment') {
    //New profile comment
    $comment = new ProfileComment();
    $comment->author = $CurrentUser->id;
    $comment->user_id = $user->id;
    $comment->text = $_POST['message'];
    $comment->publish();
    $smarty->assign('NOTIFY', lang_get('CommentPublished'));    
} elseif ($_FILES['photo']) {
    #We've a file !
    
    $hash = md5(microtime() . serialize($_FILES));
    $extension = get_extension($_FILES['photo']['name']);
    $filename = $CurrentUser->id . '_' . $hash . '.' . $extension;

	#We ignore $_FILES[photo][error] 4, this means no file has been uploaded
	#(so user doesn't want upload a new file)
	#See http:/www.php.net/features.file-upload and http://www.php.net/manual/en/features.file-upload.errors.php about common errors
	#Not valid before PHP 4.2.0
	switch ($_FILES['photo']['error']) {
		case 0:
		#There is no error, the file uploaded with success.

		if (!move_uploaded_file($_FILES['photo']['tmp_name'], DIR_PHOTOS . '/' . $filename)) {
			$errors[] = "Upload successful, but error saving it.";
		} else {
			//Attaches the picture to the profile
			$photo = new ProfilePhoto();
			$photo->name = $filename;
			$photo->user_id = $CurrentUser->id;
			$photo->description = $_POST['description'];
			$photo->safe = $_POST['SafeForWork'];
			if ($photo->avatar) $photo->promoteToAvatar();
			$photo->saveToDatabase();
			
			//Generates thumbnail
			@exec('c:\WebServer\wwwroot\folleterre.org\faeries\pics\tn\c.bat');
			
			$smarty->assign('NOTIFY', lang_get('PhotoUploaded'));
			$mode = 'view';
		}
		break;
		
		case 1:
		$errors[] = "The file is too large.";
		break;

		#TODO : more explicit error messages

		default:
		$errors[] = "Unknown error (#" . $_FILES['photo']['error'] . ")";
		break;
	}
	
	if (count($errors)) {
	    $smarty->assign('WAP', join($errors, '<br />'));
	}
} elseif ($_POST['id']) {
    //Edits photo properties
    $photo = new ProfilePhoto($_POST['id']);
    if ($photo->lastError) {
        $smarty->assign('WAP', $photo->lastError);
        $mode = 'view';
    } elseif ($photo->user_id != $CurrentUser->id) {
        $smarty->assign('WAP', lang_get('NotYourPic'));
        $mode = 'view';
    } else {
        //OK
	$wereAvatar = $photo->avatar;
        $photo->loadFromForm();
	if (!$wereAvatar && $photo->avatar) {
	    //Promote to avatar
	    $photo->promoteToAvatar();
	}
        $photo->saveToDatabase();
    }
}

//Prepares output
if ($profile->text) {
    //Profile
	$smarty->assign('PROFILE_TEXT', $profile->text);
	$smarty->assign('PROFILE_FIXEDWIDTH', $profile->fixedwidth);
}

if ($mode == 'view') {
    require_once('includes/objects/profilephoto.php');
    
    //Self profile?
    $self = $CurrentUser->id == $profile->user_id;
    
    //Gets profiles comments, photos
    $comments = ProfileComment::get_comments($profile->user_id);
    $photos   = ProfilePhoto::get_photos($profile->user_id);
    
    //Gets phone
    $ids = Resources::getChildIds('User', $profile->user_id, 'Phone');
    if (count($ids)) {
        foreach ($ids as $id) {
            $phone = new Phone($id);
            //We avoid faxes or private numbers
            if (!$phone->isPrivate && $phone->isVoice) {
               $smarty->assign('PHONE', $phone->number);
               break;
            }
        }
    }
    
    //Records timestamp, to be able to track new comments
    if ($self) record_timestamp('profile');
    
    //Warning for new accounts
    if (!$user->active)
	$smarty->assign('NOTIFY', lang_get('InactivatedUser'));
	
    //TODO: move to sidebar manager
    //Sidebar content - gets photos.folleterre.org most recent upload
    $sql = "SELECT CONCAT(filepath, 'thumb_', filename) AS url FROM cpg14x_pictures WHERE owner_id = $profile->user_id ORDER BY ctime DESC LIMIT 2";
    if (!$result = $db->sql_query($sql)) {
	message_die(SQL_ERROR, "Unable to query the table", '', __LINE__, __FILE__, $sql);
    }
    $i = 0;
    while ($row = $db->sql_fetchrow($result)) {
	$lastpics[$i]->link  = "http://photos.folleterre.org/displayimage.php?album=lastupby&amp;cat=0&amp;pos=$i&amp;uid=";
	$lastpics[$i]->link .= $profile->user_id;
	$lastpics[$i]->url   = "http://photos.folleterre.org/albums/" . $row['url'];
	$i++;
    }
    $smarty->assign('SIDEBAR_LASTPICS_URL', 'http://photos.folleterre.org/thumbnails.php?album=lastupby&uid=' . $profile->user_id);
    $smarty->assign('SIDEBAR_LASTPICS', $lastpics);
       
    //Template
    $smarty->assign('PROFILE_COMMENTS', $comments);
    $smarty->assign('PROFILE_SELF', $self);
    $smarty->assign('USERNAME', $user->username);
    $smarty->assign('NAME', $user->longname ? $user->longname : $user->username);
    $smarty->assign('MAIL', $user->email);
    $template = 'profile.tpl';
} elseif ($mode == 'edit') {
    switch ($url[2]) {
        case 'profile':
            $smarty->assign('USERNAME', $user->longname);
	    $smarty->assign('DIJIT', true);
	    $css[] = 'forms.css';
	    $template = 'profile_edit.tpl';	    
            break;
        
        case 'account':
	    $smarty->assign('user', $user);
	    $smarty->assign('DIJIT', true);
	    $css[] = 'forms.css';
            $template = 'user_account.tpl';
            break;
        
	
        case '':
            $smarty->assign('NOTIFY', "What do you want to edit ? Append /profile, /account or /photos to the URL");
            break;

        case 'photo':
        case 'photos':
            $smarty->assign('USERNAME', $user->longname);
	    switch ($action = $url[3]) {
                case '':
                //Nothing to do
                break;
                
                case 'delete':
                //Deletes a picture
                if (!$id = $url[4]) {
                    $smarty->assign('WAP', "URL error. Parameter missing: picture id.");
                } else {
                    $photo = new ProfilePhoto($id);
                    if ($photo->lastError) {
                        //Probably an non existent id (e.g. double F5, photo already deleted)
                        $smarty->assign('WAP', $photo->lastError);
                    } elseif ($photo->user_id != $CurrentUser->id) {
                        $smarty->assign('WAP', lang_get('NotYourPic'));
                    } else {
                        //OK we can delete it
                        $photo->delete();
                        $smarty->assign('NOTIFY', lang_get('PictureDeleted'));
                    }
                }
                break;
                
                case 'edit':
                if (!$id = $url[4]) {
                    $smarty->assign('WAP', "URL error. Parameter missing: picture id.");
                } else {
                    $photo = new ProfilePhoto($id);
                    if ($photo->lastError) {
                        //Probably an non existent id (e.g. double F5, photo already deleted)
                        $smarty->assign('WAP', $photo->lastError);
                    } elseif ($photo->user_id != $CurrentUser->id) {
                        $smarty->assign('WAP', lang_get('NotYourPic'));
                    } else {
                        //Photo information edit form
                        $smarty->assign('photo', $photo);
                        $template = 'profile_photo_edit.tpl';
                    }
                }
                break;
	    
		case 'avatar':
                //Deletes a picture
                if (!$id = $url[4]) {
                    $smarty->assign('WAP', "URL error. Parameter missing: picture id.");
                } else {
                    $photo = new ProfilePhoto($id);
                    if ($photo->lastError) {
                        $smarty->assign('WAP', $photo->lastError);
                    } elseif ($photo->user_id != $CurrentUser->id) {
                        $smarty->assign('WAP', lang_get('NotYourPic'));
                    } else {
                        //OK, promote it to avatar
                        $photo->promoteToAvatar();
			$photo->saveToDatabase();
                        $smarty->assign('NOTIFY', lang_get('PromotedToAvatar'));
                    }
                }
		break;
                
                default:
                $smarty->assign('WAP', "Unknown URL. To delete a picture it's /delete/<picture id>. To edit it /edit/<picture id>");
                break;
            }
            if (!$template) {
                $photos = ProfilePhoto::get_photos($profile->user_id);
                if (!$smarty->_tpl_vars['NOTIFY'])
                    $smarty->assign('NOTIFY', "Your feedback is valued. Report any bug or suggestion on the graffiti wall.");
                $template = 'profile_photo.tpl';
            }
            break;

        default:
            $smarty->assign('WAP', "URL error. You can use /edit with profile, account or photos.");
            break;
    }
}

//
// HTML output
//

//Photos
if (count($photos) || $photo) {
    $smarty->assign('URL_PICS', '/' . DIR_PHOTOS);
    $css[] = 'lightbox.css';
    $smarty->assign('PAGE_JS', array('prototype.js', 'scriptaculous.js?load=effects', 'lightbox.js'));
    $smarty->assign('PICS', $photos);
}

//Serves header
$css[] = "profile.css";
$smarty->assign('PAGE_CSS', $css);
$smarty->assign('PAGE_TITLE', $user->longname);
include('header.php'); 

//Serves content
if ($template) $smarty->display($template);

//Serves footer
include('footer.php');
 
?>