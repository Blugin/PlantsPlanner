<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\traits;

use kim\present\plantsplaner\block\IPlants;
use kim\present\plantsplaner\data\BearablePlantsData;
use kim\present\plantsplaner\data\PlantsData;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Stem;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\math\Facing;

/**
 * This trait provides a implementation for `Stem` and `IPlants` to reduce boilerplate.
 *
 * @see Stem, IPlants
 */
trait StemPlantsTrait{
    use CropsPlantsTrait;

    /** @inheritDoc */
    public function growPlants() : void{
        /** @var Stem|IPlants $this */
        if($this->canGrow()){
            if($this->age < 7){
                $block = clone $this;
                ++$block->age;

                $ev = new BlockGrowEvent($this, $block);
                $ev->call();
                if(!$ev->isCancelled()){
                    $world = $this->pos->getWorld();
                    $world->setBlock($this->pos, $ev->getNewState());
                }
            }else{
                $grow = $this->getPlant();

                $facings = Facing::HORIZONTAL;
                shuffle($facings);
                foreach($facings as $face){
                    $side = $this->getSide($face);
                    $down = $side->getSide(Facing::DOWN);
                    if($side->canBeReplaced() && ($down->getId() === BlockLegacyIds::FARMLAND || $down->getId() === BlockLegacyIds::GRASS || $down->getId() === BlockLegacyIds::DIRT)){
                        $ev = new BlockGrowEvent($side, $grow);
                        $ev->call();
                        if(!$ev->isCancelled()){
                            $world = $side->pos->getWorld();
                            $world->setBlock($side->pos, $ev->getNewState());
                        }
                        break;
                    }
                }
            }
        }
    }

    /** @inheritDoc */
    public function canGrow() : bool{
        /** @var Stem|IPlants $this */
        if($this->age < 7){
            return true;
        }else{
            $stemPlant = $this->getPlant();
            $hasSpace = false;
            foreach(Facing::HORIZONTAL as $face){
                $side = $this->getSide($face);
                $down = $side->getSide(Facing::DOWN);
                if($side->isSameType($stemPlant)){
                    return false;
                }elseif($side->canBeReplaced() && ($down->getId() === BlockLegacyIds::FARMLAND || $down->getId() === BlockLegacyIds::GRASS || $down->getId() === BlockLegacyIds::DIRT)){
                    $hasSpace = true;
                }
            }
            return $hasSpace;
        }
    }

    /**
     * @inheritDoc
     * @see PlantsData::getGrowSeconds()
     * @see BearablePlantsData::getBearSeconds()
     */
    public function getGrowSeconds() : float{
        return $this->age < 7 ? $this->getPlantsData()->getGrowSeconds() : $this->getPlantsData()->getBearSeconds();
    }
}