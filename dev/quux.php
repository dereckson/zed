<?php

    require_once('includes/objects/ship.php');
    require_once('includes/objects/port.php');
    require_once('includes/objects/application.php');
    require_once('includes/objects/content.php');
    require_once('includes/objects/message.php');
    require_once('includes/objects/invite.php');
    require_once('includes/cache/cache.php');
        
    include('controllers/header.php');
    
    $case = 'spherical';
    
    switch ($case) {
        case 'spherical':
            require_once('includes/geo/galaxy.php');
            echo '<H2>Spherical coordinates test</H2>';
            echo '<table cellpadding=8>';
            echo "<tr><th>Name</th><th>Type</th><th>Cartesian coords</th><th>Spherical I</th><th>Spherical II</th><th>Pencil coordinates</th></tr>";
            $objects = GeoGalaxy::get_coordinates();
            foreach ($objects as $row) {
                echo "<tr><th style='text-align: left'>$row[0]</th><td>$row[1]</td><td>$row[2]</td>";
                $pt = $row[2];
                echo '<td>(', implode(', ', $pt->to_spherical()), ')</td>';
                echo '<td>(', implode(', ', $pt->to_spherical2()), ')</td>';
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
            $testperso = Perso::get(4733);
            $message = new Message();
            $message->from = 0;
            $message->to = invite::who_invited(4733);
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
            $content = new Content(1);
            dprint_r($content);
            $content->generate_thumbnail();
            break;
        
        case 'port':
            echo '<h2>Port::from_location test</h2>';
            $locations = array("B00002", "B00002123", "B00001001", "xyz: [800, 42, 220]");
            foreach ($locations as $location) {
                dprint_r(Port::from_location($location));
            }
            break;
        
        case 'ext':
            $file = 'dev/foo.tar';
            echo "<h2>$file</h2>";
            echo "<h3>.tar.bz2</h3>";
            echo ereg('\.tar\.bz2$', $file);
            echo "<h3>.tar</h3>";
            echo ereg('\.tar$', $file);
            break;
        
        case 'app':
            echo Application::from_api_key("37d839ba-f9fc-42ca-a3e8-28053e979b90")->generate_userkey();
            break;
        
        case '':
            dieprint_r("No case currently selected.");
            break;
    }
    
    include('controllers/footer.php');

?>