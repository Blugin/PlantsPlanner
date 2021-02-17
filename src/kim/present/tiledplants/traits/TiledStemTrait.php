<?php
declare(strict_types=1);

namespace kim\present\tiledplants\traits;

use kim\present\tiledplants\block\ITiledPlant;
use kim\present\tiledplants\data\BearablePlantData;
use kim\present\tiledplants\Loader;
use kim\present\tiledplants\tile\Plants;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Stem;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\math\Facing;

/**
 * This trait provides a implementation for `Stem` and `ITiledPlant` to reduce boilerplate.
 *
 * @see Stem, ITiledPlant
 */
trait TiledStemTrait{
    use TiledCropsTrait;

    public function grow() : void{
        /** @var Stem|ITiledPlant $this */
        if(!$this->isRipe()){
            if($this->age < 7){
                $block = clone $this;
                ++$block->age;
                $ev = new BlockGrowEvent($this, $block);
                $ev->call();
                if(!$ev->isCancelled()){
                    $this->pos->getWorld()->setBlock($this->pos, $ev->getNewState());
                    $this->onGrow();
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
                            $this->pos->getWorld()->setBlock($side->pos, $ev->getNewState());
                            $this->onGrow();
                            return;
                        }
                    }
                }
            }
        }
    }

    public function onNearbyBlockChange() : void{
        parent::onNearbyBlockChange();
        if(!$this->isRipe()){
            $plantsTile = $this->pos->getWorld()->getTile($this->pos);
            if($plantsTile instanceof Plants){
                $plantsTile->setLastTime(microtime(true));
            }
            $this->pos->getWorld()->scheduleDelayedBlockUpdate($this->pos, Loader::$updateDelay);
        }
    }

    public function isRipe() : bool{
        /** @var Stem|ITiledPlant $this */
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
        /** @var BearablePlantData $plantData */
        $plantData = $this->getPlantData();
        return $this->age < 7 ? $plantData->getGrowSeconds() : $plantData->getBearSeconds();
    }
}