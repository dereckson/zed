/*  -------------------------------------------------------------
    Zed
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    Author:         Dereckson
    Tags:           map
    Filename:       map.js
    Version:        1.0
    Created:        2010-12-23
    Updated:        2010-12-23
    Licence:        Dual licensed: BSD and Creative Commons BY 3.0.
    Dependencies:   dojo
    -------------------------------------------------------------    */
/**
 * Map
 */
var map = {
    id: null,
    zones: null,
    bounds: null,

    init: function (id, zones) {
        map.id = id;
        map.zones = zones;
        map.render();
    },

    /**
     * Get coordinates
     *
     * @returns An array [x, y, z]
     */
    get_coordinates: function (expr) {
        var coordinates = expr.substring(1, expr.length - 1).split(', ');
        return [parseInt(coordinates[0]), parseInt(coordinates[1]), parseInt(coordinates[2])];
    },

    /**
     * Calculates the zones bounds and stores the result in bounds property
     */
    calculate_bounds: function () {
        var start = map.get_coordinates(map.zones[0][0]);
        map.bounds = [
            [start[0], start[0]],
            [start[1], start[1]],
            [start[2], start[2]]
        ];
        for (i = 1 ; i < map.zones.length ; i++) {
            point = map.get_coordinates(map.zones[i][0]);
            if (point[0] < map.bounds[0][0]) map.bounds[0][0] = point[0];
            if (point[1] < map.bounds[1][0]) map.bounds[1][0] = point[1];
            if (point[2] < map.bounds[2][0]) map.bounds[2][0] = point[2];
            if (point[0] > map.bounds[0][1]) map.bounds[0][1] = point[0];
            if (point[1] > map.bounds[1][1]) map.bounds[1][1] = point[1];
            if (point[2] > map.bounds[2][1]) map.bounds[2][1] = point[2];
        }
    },

    render_zone: function (x, y, z) {
        var location = "(" + x + ", " + y + ", " + z + ")";
        for (i = 0 ; i < map.zones.length ; i++) {
            if (location == map.zones[i][0]) {
                return '<span class="zone zone-edit" id="zone-' + map.zones[i][1] + '" onMouseOut="map.reset_info()" onMouseOver="map.set_info(\'' + location + '\')" onClick="map.menu_edit(\'' + location + '\', ' + map.zones[i][1] + ');"><img src="/img/map/map-kub-top.png" alt="Built" /></span>';
            }
        }
        return '<span class="zone zone-build" onMouseOut="map.reset_info()" onMouseOver="map.set_info(\'' + location + '\')" onClick="map.menu_build(\'' + location + '\');"><img src="/img/map/map-kub-top-build.png" alt="Build" /></span>';
    },

    render: function () {
        map.calculate_bounds();
        var html = "";
        z = map.bounds[2][0];
        for (y = map.bounds[1][1] ; y >= map.bounds[1][0] ; y--) {
            for (x = map.bounds[0][0] ; x <= map.bounds[0][1] ; x++) {
                html += '<div class="grid_1">' + map.render_zone(x, y, z) + "</div>";
            }
            html += '</div><div class="clear fixclear"></div>';
            dojo.byId(map.id).innerHTML = html;
        }
    },

    menu_edit: function (local_location, zone_id) {
        //alert("Goto or edit #" + zone_id);
        //map/zone-edit.png
        //map/zone-goto.png
        window.location = "/do.php/set_local_location/" + local_location + "?redirectTo=/";
    },

    menu_build: function (local_location) {
        //alert("Build at " + local_location);
        //map/zone-build.png
        //window.location = "/do.php/set_local_location/" + escape(local_location) + "?redirectTo=/builder";
        window.location = "/do.php/set_local_location/" + local_location + "?redirectTo=/";
    },

    set_info: function (local_location) {
        coord = map.get_coordinates(local_location);
        dojo.byId("info_area").innerHTML = 'Zone <span id="area">' + Math.abs(coord[0]) + '-' +  Math.abs(coord[1]) + '</span>';
    },

    reset_info: function () {
        dojo.byId("info_area").innerHTML = "&nbsp;";
    }
}
