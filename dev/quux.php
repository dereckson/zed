<?php

use Hypership\Geo\PointPolarZ;
use Zed\Models\Geo\Galaxy;
use Zed\Models\Geo\Port;
use Zed\Models\Geo\SceneIndex;
use Zed\Models\Objects\Application;
use Zed\Models\Objects\Content;
use Zed\Models\Objects\Invite;
use Zed\Models\Messages\Message;
use Zed\Models\Objects\Perso;

    require_once('includes/cache/cache.php');

    $case = 'YubiCloud';

    $smarty->assign('PAGE_TITLE', "Quux [$case]");
    include('controllers/header.php');

    switch ($case) {
        case 'YubiCloud':
            require_once('Auth/Yubico.php');
            echo '<h2>YubiKey</h2>';
            if (!array_key_exists('YubiCloud', $Config)) {
                message_die(GENERAL_ERROR, "YubiCloud authentication not configured. Add \$Config['YubiCloud']['ClientID'] and \$Config['YubiCloud']['SecretKey'] to your config.");
            }
            if (!$key = $_GET['OTP']) {
                message_die(GENERAL_ERROR, "Please add in URL ?OTP=, then put your cursor at right of the = and press your YubiKey button");
            }
            $yubi = new Auth_Yubico($Config['YubiCloud']['ClientID'], $Config['YubiCloud']['SecretKey']);
            if (!$data = $yubi->parsePasswordOTP($key)) {
                message_die(GENERAL_ERROR, "This is not an YubiKey OTP.");
            }
            $prefix = $data['prefix'];
            $auth = $yubi->verify($key);
            if (@PEAR::isError($auth)) {
                if ($auth->getMessage() == 'REPLAYED_OTP') {
                    message_die("This OTP has already been used.");
                }
                message_die(HACK_ERROR, "<p>Authentication failed: " . $auth->getMessage() . "</p><p>Debug: " . $yubi->getLastResponse() . "</p>");
            } else {
                print "<p>You are authenticated!</p>";
            }
            break;

        case 'PointPolarZ':
            echo "<H2>PointPolarZ</H2>";
            $point = PointPolarZ::fromString("(48, 30°, 3)");
            printf("Secteur T%dC%d", $point->getSection(), $point->z);
            dprint_r($point);
            break;

        case 'index_scenes':
            $time[] = microtime();
            $cache = Cache::load();
            if ($index = $cache->get('SceneIndex')) {
                $index = unserialize($index);
            } else {
                $index = SceneIndex::Load(SCENE_DIR);
                $cache->set('SceneIndex', serialize($index));
            }
            $time[] = microtime();
            echo '<H2>SceneIndex</H2>';
            dprint_r($index);
            echo '<H2>Time (ms)</H2>';
            dprint_r(1000 * ($time[1] - $time[0]));
            dprint_r($time);
            break;

        case 'travel':
            require_once('includes/travel/travel.php');
            $travel = Travel::load();
            dieprint_r($travel);
            break;

        case 'spherical':
            echo '<H2>Spherical coordinates test</H2>';
            echo '<table cellpadding=8>';
            echo "<tr><th>Name</th><th>Type</th><th>Cartesian coords</th><th>Spherical I</th><th>Spherical II</th><th>Pencil coordinates</th></tr>";
            $objects = Galaxy::getCoordinates($db);
            foreach ($objects as $row) {
                echo "<tr><th style='text-align: left'>$row[0]</th><td>$row[1]</td><td>$row[2]</td>";
                $pt = $row[2];
                echo '<td>(', implode(', ', $pt->toSpherical()), ')</td>';
                echo '<td>(', implode(', ', $pt->toSphericalAlternative()), ')</td>';
                $pt->translate(500, 300, 200, 2);
                echo '<td>', $pt, '</td>';
                echo '</tr>';
            }
            echo '</table>';
            break;

        case 'travel':
            require_once('includes/travel/travel.php');
            require_once('includes/travel/place.php');

            $cache = Cache::load();
            $travel = $cache->get('zed_travel');
            if ($travel == '') {
                $travel_nocached = new Travel();
                $travel_nocached->load_xml("content/travel.xml");
                $cache->set('zed_travel', serialize($travel_nocached));
            } else {
                $travel = unserialize($travel);
            }
            dieprint_r($travel);
            break;

        case 'perso.create.notify':
            $testperso = Perso::get($db, 4733);
            $message = new Message($db);
            $message->setAsSystem();
            $message->to = invite::who_invited($db, $testperso);
            $url = get_server_url() . get_url('who', $testperso->nickname);
            $message->text =  sprintf(lang_get('InvitePersoCreated'), $testperso->name, $url);
            $message->send();
            dieprint_r($message);
            break;

        case 'pushdata';
            echo '
<h2>/api.php/app/pushdata</h2>
<form method="post" action="/api.php/app/pushdata?mode=file&key=37d839ba-f9fc-42ca-a3e8-28053e979b90" enctype="multipart/form-data">
    <input type="file" name="datafile" /><br />
    <input type="submit" value="Send file" />
</form>
            ';
            break;

        case 'thumbnail':
            $content = new Content($db, 1);
            dprint_r($content);
            $content->generate_thumbnail();
            break;

        case 'port':
            echo '<h2>Port::from_location test</h2>';
            $locations = array("B00002", "B00002123", "B00001001", "xyz: [800, 42, 220]");
            foreach ($locations as $location) {
                dprint_r(Port::from_location($db, $location));
            }
            break;

        case 'ext':
            $file = 'dev/foo.tar';
            echo "<h2>$file</h2>";
            echo "<h3>.tar.bz2</h3>";
            echo preg_match('/\.tar\.bz2$/', $file);
            echo "<h3>.tar</h3>";
            echo preg_match('/\.tar$/', $file);
            break;

        case 'app':
            echo Application::from_api_key($db, "37d839ba-f9fc-42ca-a3e8-28053e979b90")
                ->generate_userkey($CurrentPerso);
            break;

        case '':
            dieprint_r("No case currently selected.");
            break;
    }

    include('controllers/footer.php');
