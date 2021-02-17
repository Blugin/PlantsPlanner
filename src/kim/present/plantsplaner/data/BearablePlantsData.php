<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\data;

/**
 * It used for Crops growing into different blocks, such as watermelon and pumpkin.
 * It has a data for bear the fruit time.
 */
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

    public static function fromArray(array $array) : PlantsData{
        return new BearablePlantsData(
            (float) ($array["grow-seconds"] ?? 30.0),
            (float) ($array["bear-seconds"] ?? 300.0)
        );
    }
}