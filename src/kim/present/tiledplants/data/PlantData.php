<?php
declare(strict_types=1);

namespace kim\present\tiledplants\data;

class PlantData{
    private float $growSeconds;

    public function __construct(float $growSeconds){
        $this->growSeconds = $growSeconds;
    }

    public function getGrowSeconds() : float{
        return $this->growSeconds;
    }
}