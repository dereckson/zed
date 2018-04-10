<?php

/**
 * User profile
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This is a controller allowing user profile view and edit.
 *
 * It handles the following URLs:
 *     /who/<perso nickname>  views the nickname's profile,
 *     /who/random            views a random profile,
 *     /who/edit/profile      edits its profile
 *     /who/edit/account      edits its account (disabled on Zed, cf. settings),
 *     /who/edit/photo(s)     manages its profile's photos,
 *          /who/edit/photo/edit/<photo id>      edits a photo properties,
 *          /who/edit/photo/delete/<photo id>    deletes a photo,
 *          /who/edit/photo/avatar/<photo id>    promotes a photo to avatar.
 *
 * The following views are used:
 *     profile.tpl,
 *     profile_edit.tpl,
 *     user_account.tpl,
 *     profile_photo.tpl,
 *     profile_photo_edit.tpl.
 *
 * The following models are used:
 *     Profile,
 *     ProfilePhoto,
 *     ProfileComment.
 *
 * The view profile_tags.tpl is indirectly used by the Profile model.
 *
 * This code is maintained in // with Azhàr.
 *
 * @package     Zed
 * @subpackage  Controllers
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

//Loads language file
lang_load('profile.conf');

//Gets perso nickname from URL
$who = $url[1];

switch ($who) {
    case 'edit':
        $mode = 'edit';
        $who = $CurrentPerso->nickname;
        break;

    case 'random':
        $mode = 'view';
        $who = $db->sql_query_express("SELECT perso_id FROM " . TABLE_PROFILES . " ORDER  BY rand() LIMIT 1");
        break;

    default:
        $mode = 'view';
}

if (!$who) {
    message_die(GENERAL_ERROR, "Who?", "URL error");
}

//Libs
require_once('includes/objects/profile.php');
require_once('includes/objects/profilecomment.php');
require_once('includes/objects/profilephoto.php');

//Gets perso information
require_once('includes/objects/perso.php');
$perso = Perso::get($who);
if ($perso->lastError) {
    message_die(GENERAL_ERROR, $perso->lastError, "Error");
}
$smarty->assign('perso', $perso);

//Gets profile
$profile = new Profile($perso->id);

//Handles form
if ($_POST['EditProfile']) {
    $profile->load_from_form();
    $profile->updated = time();
    $profile->save_to_database();
    $mode = 'view';
} elseif ($_POST['UserAccount']) {
    $smarty->assign('WAP', "This form have been deprecated. You can write instead settings in the SmartLine");
} elseif ($_POST['message_type'] == 'private_message') {
    //Sends a message
    require_once('includes/objects/message.php');
    $msg = new Message();
    $msg->from = $CurrentPerso->id;
    $msg->to = $perso->id;
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
    $comment->author = $CurrentPerso->id;
    $comment->perso_id = $perso->id;
    $comment->text = $_POST['message'];
    $comment->publish();
    $smarty->assign('NOTIFY', lang_get('CommentPublished'));
} elseif ($_FILES['photo']) {
    #We've a file !

    $hash = md5(microtime() . serialize($_FILES));
    $extension = get_extension($_FILES['photo']['name']);
    $filename = $CurrentPerso->id . '_' . $hash . '.' . $extension;

    #We ignore $_FILES[photo][error] 4, this means no file has been uploaded
    #(so user doesn't want upload a new file)
    #See http:/www.php.net/features.file-upload and http://www.php.net/manual/en/features.file-upload.errors.php about common errors
    #Not valid before PHP 4.2.0
    switch ($_FILES['photo']['error']) {
        case 0:
        #There is no error, the file uploaded with success.

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], PHOTOS_DIR . '/' . $filename)) {
            $errors[] = "Upload successful, but error saving it.";
        } else {
            //Attaches the picture to the profile
            $photo = new ProfilePhoto();
            $photo->name = $filename;
            $photo->perso_id = $CurrentPerso->id;
            $photo->description = $_POST['description'];
            if ($photo->avatar) $photo->promote_to_avatar();
            $photo->save_to_database();

            //Generates thumbnail
            if (!$photo->generate_thumbnail()) {
                $smarty->assign('WAP', "Error generating thumbnail.");
            }

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
    } elseif ($photo->perso_id != $CurrentPerso->id) {
        $smarty->assign('WAP', lang_get('NotYourPic'));
        $mode = 'view';
    } else {
        //OK
        $wereAvatar = $photo->avatar;
        $photo->load_from_form();
        if (!$wereAvatar && $photo->avatar) {
            //Promote to avatar
            $photo->promote_to_avatar();
        }
        $photo->save_to_database();
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
    $self = $CurrentPerso->id == $profile->perso_id;

    //Gets profiles comments, photos, tags
    $comments = ProfileComment::get_comments($profile->perso_id);
    $photos   = ProfilePhoto::get_photos($profile->perso_id);
    $tags     = $profile->get_cached_tags();

    //Records timestamp, to be able to track new comments
    if ($self) $CurrentPerso->set_flag('profile.lastvisit', time());

    //Template
    $smarty->assign('PROFILE_COMMENTS', $comments);
    $smarty->assign('PROFILE_SELF', $self);
    if ($tags) $smarty->assign('PROFILE_TAGS', $tags);
    $smarty->assign('USERNAME', $perso->username);
    $smarty->assign('NAME', $perso->name ? $perso->name : $perso->nickname);
    $template = 'profile.tpl';
} elseif ($mode == 'edit') {
    switch ($url[2]) {
        case 'profile':
            $smarty->assign('USERNAME', $perso->name);
            $smarty->assign('DIJIT', true);
            $css[] = THEME . '/forms.css';
            $template = 'profile_edit.tpl';
            break;

        case 'account':
            $smarty->assign('user', $CurrentUser);
            $smarty->assign('DIJIT', true);
            $css[] = THEME . '/forms.css';
            $template = 'user_account.tpl';
            break;

        case '':
            $smarty->assign('NOTIFY', "What do you want to edit ? Append /profile, /account or /photos to the URL");
            break;

        case 'photo':
        case 'photos':
            $smarty->assign('USERNAME', $perso->name);
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
                        } elseif ($photo->perso_id != $CurrentPerso->id) {
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
                        } elseif ($photo->perso_id != $CurrentPerso->id) {
                            $smarty->assign('WAP', lang_get('NotYourPic'));
                        } else {
                            //Photo information edit form
                            $smarty->assign('photo', $photo);
                            $template = 'profile_photo_edit.tpl';
                        }
                    }
                    break;

                case 'avatar':
                    //Promotes a picture to avatar
                    if (!$id = $url[4]) {
                        $smarty->assign('WAP', "URL error. Parameter missing: picture id.");
                    } else {
                        $photo = new ProfilePhoto($id);
                        if ($photo->lastError) {
                            $smarty->assign('WAP', $photo->lastError);
                        } elseif ($photo->perso_id != $CurrentPerso->id) {
                            $smarty->assign('WAP', lang_get('NotYourPic'));
                        } else {
                            //OK, promote it to avatar
                            $photo->promote_to_avatar();
                            $photo->save_to_database();
                            $smarty->assign('NOTIFY', lang_get('PromotedToAvatar'));
                        }
                    }
                    break;

                default:
                    $smarty->assign('WAP', "Unknown URL. To delete a picture it's /delete/<picture id>. To edit it /edit/<picture id>");
                    break;
            }

            if (!$template) {
                $photos = ProfilePhoto::get_photos($profile->perso_id);
                if (!$smarty->tpl_vars['NOTIFY']) {
                    $smarty->assign('NOTIFY', "Your feedback is valued. Report any bug or suggestion on the graffiti wall.");
                }
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
    $smarty->assign('URL_PICS', PHOTOS_URL);
    $css[] = 'lightbox.css';
    $smarty->assign('PAGE_JS', ['prototype.js', 'effects.js', 'lightbox.js']);
    $smarty->assign('PICS', $photos);
}

//Serves header
$css[] = THEME . "/profile.css";
$smarty->assign('PAGE_CSS', $css);
$smarty->assign('PAGE_TITLE', $perso->name);
include('header.php');

//Serves content
if ($template) $smarty->display($template);

//Serves footer
include('footer.php');
