<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\block;

use kim\present\plantsplaner\data\PlantsData;

interface IPlants{
    public function getPlantsData() : PlantsData;

    public function getGrowSeconds() : float;

    public function canGrow() : bool;

    public function grow() : void;
}