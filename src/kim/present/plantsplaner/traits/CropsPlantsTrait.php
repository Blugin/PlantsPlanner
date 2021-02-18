<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\traits;

use kim\present\plantsplaner\block\IPlants;
use pocketmine\block\Crops;
use pocketmine\event\block\BlockGrowEvent;

/**
 * This trait provides a implementation for `Crops` and `IPlants` to reduce boilerplate.
 *
 * @see Crops, IPlants
 */
trait CropsPlantsTrait{
    use PlantsTrait;

    /** @inheritDoc */
    public function grow() : void{
        /** @var Crops|IPlants $this */
        if($this->canGrow()){
            $block = clone $this;
            ++$block->age;

            $ev = new BlockGrowEvent($this, $block);
            $ev->call();
            if(!$ev->isCancelled()){
                $pos = $this->getPos();
                $world = $pos->getWorld();
                $world->setBlock($pos, $ev->getNewState());
            }
        }
    }

    /** @inheritDoc */
    public function canGrow() : bool{
        /** @var Crops|IPlants $this */
        return $this->age < 7;
    }
}