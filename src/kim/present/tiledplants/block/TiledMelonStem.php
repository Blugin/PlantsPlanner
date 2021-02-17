<?php
declare(strict_types=1);

namespace kim\present\tiledplants\block;

use kim\present\tiledplants\data\PlantData;
use kim\present\tiledplants\Loader;
use kim\present\tiledplants\traits\TiledStemTrait;
use pocketmine\block\MelonStem;

final class TiledMelonStem extends MelonStem implements ITiledPlant{
    use TiledStemTrait;

    public function getPlantData() : PlantData{
        return Loader::MELON_STEM();
    }
}