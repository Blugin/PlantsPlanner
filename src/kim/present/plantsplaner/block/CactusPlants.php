<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\block;

use kim\present\plantsplaner\data\DefaultPlants;
use kim\present\plantsplaner\data\PlantsData;
use kim\present\plantsplaner\traits\StackablePlantsTrait;
use pocketmine\block\Cactus;

final class CactusPlants extends Cactus implements IPlants{
    use StackablePlantsTrait;

    public function getPlantsData() : PlantsData{
        return DefaultPlants::CACTUS();
    }
}