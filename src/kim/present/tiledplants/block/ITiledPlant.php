<?php
declare(strict_types=1);

namespace kim\present\tiledplants\block;

use kim\present\tiledplants\data\PlantData;

interface ITiledPlant{
    public function isRipe() : bool;

    public function grow() : void;

    public function onGrow() : void;

    public function getPlantData() : PlantData;
}