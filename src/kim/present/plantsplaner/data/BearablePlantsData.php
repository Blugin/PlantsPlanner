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

    public static function fromArray(array $array) : PlantsData{
        return new BearablePlantsData(
            (float) ($array["grow-seconds"] ?? PHP_INT_MAX),
            (float) ($array["bear-seconds"] ?? PHP_INT_MAX)
        );
    }
}