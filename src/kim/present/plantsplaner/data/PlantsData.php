<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\data;

class PlantsData{
    private float $growSeconds;

    public function __construct(float $growSeconds){
        $this->growSeconds = $growSeconds;
    }

    public function getGrowSeconds() : float{
        return $this->growSeconds;
    }

    public function isTemporary() : bool{
        return true;
    }

    public static function fromArray(array $array) : PlantsData{
        return new PlantsData((float) ($array["grow-seconds"] ?? 60.0));
    }
}