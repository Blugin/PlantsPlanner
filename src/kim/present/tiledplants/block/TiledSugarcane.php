<?php
declare(strict_types=1);

namespace kim\present\tiledplants\block;

use kim\present\tiledplants\data\PlantData;
use kim\present\tiledplants\Loader;
use kim\present\tiledplants\traits\TiledStackTrait;
use pocketmine\block\Sugarcane;

final class TiledSugarcane extends Sugarcane implements ITiledPlant{
    use TiledStackTrait {
        grow as Plants_grow;
    }

    public function getPlantData() : PlantData{
        return Loader::SUGARCANE();
    }

    public function grow() : void{
        $this->Plants_grow();
    }
}