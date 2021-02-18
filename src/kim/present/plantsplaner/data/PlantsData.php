<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\data;

use kim\present\plantsplaner\tile\Plants;

/**
 * Plants handles growth based on PlantsData.
 * It has setting values for the time it takes for the block to grow and many other options.
 *
 * @see Plants
 */
class PlantsData{
    /** Seconds it takes to grow. (It means grow to the next level, not full-growth time) */
    private float $growSeconds;

    public function __construct(float $growSeconds){
        $this->growSeconds = $growSeconds;
    }

    public function getGrowSeconds() : float{
        return $this->growSeconds;
    }

    /** Returns a PlantsData generated as values in an array. */
    public static function fromArray(array $array) : PlantsData{
        return new PlantsData((float) ($array["grow-seconds"] ?? 60.0));
    }
}