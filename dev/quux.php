<?php

    require_once('includes/objects/ship.php');
    require_once('includes/objects/port.php');
    require_once('includes/objects/application.php');
        
    include('controllers/header.php');
    
    $case = 'pushdata';
    switch ($case) {
        case 'pushdata';
            echo '
<h2>/api.php/app/pushdata</h2>
<form method="post" action="/api.php/app/pushdata?mode=file&key=37d839ba-f9fc-42ca-a3e8-28053e979b90" enctype="multipart/form-data">
    <input type="file" name="datafile" /><br />
    <input type="submit" value="Send file" />   
</form>
            ';
        
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