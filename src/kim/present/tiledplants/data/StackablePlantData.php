<?php
declare(strict_types=1);

namespace kim\present\tiledplants\data;

class StackablePlantData extends PlantData{
    protected int $maxHeight;

    public function __construct(float $growSeconds, int $maxHeight){
        parent::__construct($growSeconds);
        $this->maxHeight = $maxHeight;
    }

    public function getMaxHeight() : int{
        return $this->maxHeight;
    }
}