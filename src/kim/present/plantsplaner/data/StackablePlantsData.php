<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\data;

/**
 * It used for plants that grow upward, such as sugarcane and cactus.
 * It has a data for growth height limit.
 */
class StackablePlantsData extends PlantsData{
    /** The limit of block grow height */
    protected int $maxHeight;

    public function __construct(float $growSeconds, int $maxHeight){
        parent::__construct($growSeconds);
        $this->maxHeight = $maxHeight;
    }

    public function getMaxHeight() : int{
        return $this->maxHeight;
    }

    /** @inheritDoc */
    public function isTemporary() : bool{
        return false;
    }

    /** @inheritDoc */
    public static function fromArray(array $array) : PlantsData{
        return new BearablePlantsData(
            (float) ($array["grow-seconds"] ?? 60.0),
            (int) ($array["max-height"] ?? 3)
        );
    }
}