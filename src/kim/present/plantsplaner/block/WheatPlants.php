<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\block;

use kim\present\plantsplaner\data\PlantsData;
use kim\present\plantsplaner\Loader;
use kim\present\plantsplaner\traits\CropsPlantsTrait;
use pocketmine\block\Wheat;

final class WheatPlants extends Wheat implements IPlants{
    use CropsPlantsTrait;

    public function getPlantsData() : PlantsData{
        return Loader::WHEAT();
    }
}