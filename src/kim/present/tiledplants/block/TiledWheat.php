<?php
declare(strict_types=1);

namespace kim\present\tiledplants\block;

use kim\present\tiledplants\data\PlantData;
use kim\present\tiledplants\Loader;
use kim\present\tiledplants\traits\TiledCropsTrait;
use pocketmine\block\Wheat;

final class TiledWheat extends Wheat implements ITiledPlant{
    use TiledCropsTrait;

    public function getPlantData() : PlantData{
        return Loader::WHEAT();
    }
}