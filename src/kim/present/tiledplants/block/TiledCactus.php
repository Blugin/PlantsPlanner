<?php
declare(strict_types=1);

namespace kim\present\tiledplants\block;

use kim\present\tiledplants\data\PlantData;
use kim\present\tiledplants\Loader;
use kim\present\tiledplants\traits\TiledStackTrait;
use pocketmine\block\Cactus;

final class TiledCactus extends Cactus implements ITiledPlant{
    use TiledStackTrait;

    public function getPlantData() : PlantData{
        return Loader::CACTUS();
    }
}