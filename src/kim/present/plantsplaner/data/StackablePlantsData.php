<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\data;

/**
 * It used for plants that grow upward, such as sugarcane and cactus.
 * It has a data for growth height limit.
 */
class StackablePlantsData extends PlantsData{
    /** The limit of block growth */
    protected int $maxGrowth;

    public function __construct(float $growSeconds, int $maxGrowth){
        parent::__construct($growSeconds);
        $this->maxGrowth = $maxGrowth;
    }

    public function getMaxGrowth() : int{
        return $this->maxGrowth;
    }

    /** @inheritDoc */
    public function isTemporary() : bool{
        return false;
    }

    /** @inheritDoc */
    public static function fromArray(array $array) : PlantsData{
        return new StackablePlantsData(
            (float) ($array["grow-seconds"] ?? 60.0),
            (int) ($array["max-height"] ?? $array["max-growth"] ?? 3)
        );
    }
}