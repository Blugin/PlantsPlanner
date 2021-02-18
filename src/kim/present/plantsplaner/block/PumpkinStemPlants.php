<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\block;

use kim\present\plantsplaner\data\DefaultPlants;
use kim\present\plantsplaner\data\PlantsData;
use kim\present\plantsplaner\traits\StemPlantsTrait;
use pocketmine\block\MelonStem;

final class PumpkinStemPlants extends MelonStem implements IPlants{
    use StemPlantsTrait;

    public function getPlantsData() : PlantsData{
        return DefaultPlants::PUMPKIN_STEM();
    }
}