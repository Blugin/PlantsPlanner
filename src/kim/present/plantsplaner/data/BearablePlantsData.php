<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\data;

class BearablePlantsData extends PlantsData{
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