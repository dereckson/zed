<?php
    require_once('includes/objects/ship.php');
    $ship = new Ship(1);
    Ship::clean_ship_sessions();
    
    include('controllers/header.php');
    echo "<p>Sessions cleaned</p>";
    include('controllers/footer.php');
?>