<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\traits;

use kim\present\plantsplaner\block\IPlants;
use kim\present\plantsplaner\tile\Plants;
use pocketmine\block\Crops;

/**
 * This trait provides a implementation for `Crops` and `IPlants` to reduce boilerplate.
 *
 * @see Crops, IPlants
 */
trait CropsPlantsTrait{
    use PlantsTrait;

    public function grow() : void{
        /** @var Crops|IPlants $this */
        if(!$this->canGrow()){
            $block = clone $this;
            ++$block->age;
            Plants::growPlants($this, $block);
        }
    }

    public function canGrow() : bool{
        /** @var Crops|IPlants $this */
        return $this->age >= 7;
    }
}