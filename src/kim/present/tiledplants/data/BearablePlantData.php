<?php
declare(strict_types=1);

namespace kim\present\tiledplants\data;

class BearablePlantData extends PlantData{
    protected float $bearSeconds;

    public function __construct(float $growSeconds, float $bearSeconds){
        parent::__construct($growSeconds);
        $this->bearSeconds = $bearSeconds;
    }

    public function getBearSeconds() : float{
        return $this->bearSeconds;
    }

    public function isTemporary() : bool{
        return false;
    }
}