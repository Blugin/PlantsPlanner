<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\traits;

use kim\present\plantsplaner\block\IPlants;
use kim\present\plantsplaner\data\BearablePlantsData;
use kim\present\plantsplaner\data\PlantsData;
use kim\present\plantsplaner\Loader;
use kim\present\plantsplaner\tile\Plants;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Stem;
use pocketmine\math\Facing;

/**
 * This trait provides a implementation for `Stem` and `IPlants` to reduce boilerplate.
 *
 * @see Stem, IPlants
 */
trait StemPlantsTrait{
    use CropsPlantsTrait;

    public function grow() : void{
        /** @var Stem|IPlants $this */
        if(!$this->canGrow()){
            if($this->age < 7){
                $block = clone $this;
                ++$block->age;

                Plants::growPlants($this, $block);
            }else{
                $grow = $this->getPlant();

                $facings = Facing::HORIZONTAL;
                shuffle($facings);
                foreach($facings as $face){
                    $side = $this->getSide($face);
                    $down = $side->getSide(Facing::DOWN);
                    if($side->canBeReplaced() && ($down->getId() === BlockLegacyIds::FARMLAND || $down->getId() === BlockLegacyIds::GRASS || $down->getId() === BlockLegacyIds::DIRT)){
                        Plants::growPlants($side, $grow);
                    }
                }
            }
        }
    }

    public function onNearbyBlockChange() : void{
        parent::onNearbyBlockChange();
        if(!$this->canGrow()){
            $plantsTile = $this->pos->getWorld()->getTile($this->pos);
            if($plantsTile instanceof Plants){
                $plantsTile->setLastTime(microtime(true));
            }
            $this->pos->getWorld()->scheduleDelayedBlockUpdate($this->pos, Plants::$updateDelay);
        }
    }

    public function canGrow() : bool{
        /** @var Stem|IPlants $this */
        if($this->age < 7){
            return $this->age >= 7;
        }else{
            $stemPlant = $this->getPlant();
            foreach(Facing::HORIZONTAL as $face){
                if($this->getSide($face)->isSameType($stemPlant)){
                    return true;
                }
            }
            return false;
        }
    }

    public function getGrowSeconds() : float{
        /**
         * @see PlantsData::getGrowSeconds()
         * @see BearablePlantsData::getBearSeconds()
         */
        return $this->age < 7 ? $this->getPlantsData()->getGrowSeconds() : $this->getPlantsData()->getBearSeconds();
    }
}