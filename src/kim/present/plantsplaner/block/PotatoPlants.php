<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\block;

use kim\present\plantsplaner\data\DefaultPlants;
use kim\present\plantsplaner\data\PlantsData;
use kim\present\plantsplaner\traits\CropsPlantsTrait;
use pocketmine\block\Potato;

final class PotatoPlants extends Potato implements IPlants{
    use CropsPlantsTrait;

    public function getPlantsData() : PlantsData{
        return DefaultPlants::POTATO();
    }
}