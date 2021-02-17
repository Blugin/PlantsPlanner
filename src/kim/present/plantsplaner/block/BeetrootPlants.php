<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\block;

use kim\present\plantsplaner\data\PlantsData;
use kim\present\plantsplaner\Loader;
use kim\present\plantsplaner\traits\CropsPlantsTrait;
use pocketmine\block\Beetroot;

final class BeetrootPlants extends Beetroot implements IPlants{
    use CropsPlantsTrait;

    public function getPlantsData() : PlantsData{
        return Loader::BEETROOT();
    }
}