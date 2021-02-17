<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\block;

use kim\present\plantsplaner\data\PlantsData;
use kim\present\plantsplaner\Loader;
use kim\present\plantsplaner\traits\StackablePlantsTrait;
use pocketmine\block\Sugarcane;

final class SugarcanePlants extends Sugarcane implements IPlants{
    use StackablePlantsTrait {
        grow as Plants_grow;
    }

    public function getPlantsData() : PlantsData{
        return Loader::SUGARCANE();
    }

    public function grow() : void{
        $this->Plants_grow();
    }
}