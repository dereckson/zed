<?php

namespace Zed\Engines\Builder\Map;

use Hypership\Geo\Octocube;
use Hypership\Geo\Point3D;

use GeoLocation;

class OctocubeBuilder {

    private Point3D $localLocation;

    const HYPERSHIP_CORE_LOCATION = "B00001002";

    public function __construct (GeoLocation $location) {
        $this->localLocation = Point3D::fromString($location->local);
    }

    private function getSector () : int {
        return Octocube::getSectorFromPoint3D($this->localLocation);
    }

    /**
     * Gets SQL RLIKE pattern for the specified sector
     *
     * @return string a SQL clause like "([0-9]+, -[0,9]+, [0,9]+)"
     */
    public function getRlikePattern() : string {
        $sector = $this->getSector();

        if ($sector == 0) {
            return "(0, 0, 0)";
        }

        $vector = Octocube::getBaseVector($sector);

        //x
        if ($vector[0] == 1) {
            $query = "([0-9]+, ";
        } else {
            $query = "(-[0-9]+, ";
        }

        //y
        if ($vector[1] == 1) {
            $query .= "[0-9]+, ";
        } else {
            $query .= "-[0-9]+, ";
        }

        //z
        $query .= $this->localLocation->z . ")";

        return $query;
    }

    static public function canBuildAt($location) : bool {
        return $location->global === self::HYPERSHIP_CORE_LOCATION;
    }

}
