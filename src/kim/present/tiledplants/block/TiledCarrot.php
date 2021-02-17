<?php
declare(strict_types=1);

namespace kim\present\tiledplants\block;

use kim\present\tiledplants\data\PlantData;
use kim\present\tiledplants\Loader;
use kim\present\tiledplants\traits\TiledCropsTrait;
use pocketmine\block\Carrot;

final class TiledCarrot extends Carrot implements ITiledPlant{
    use TiledCropsTrait;

    public function getPlantData() : PlantData{
        return Loader::CARROT();
    }
}