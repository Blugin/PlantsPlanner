<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\data;

class StackablePlantsData extends PlantsData{
    protected int $maxHeight;

    public function __construct(float $growSeconds, int $maxHeight){
        parent::__construct($growSeconds);
        $this->maxHeight = $maxHeight;
    }

    public function getMaxHeight() : int{
        return $this->maxHeight;
    }

    public function isTemporary() : bool{
        return false;
    }
}