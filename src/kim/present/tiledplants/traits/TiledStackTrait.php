<?php
declare(strict_types=1);

namespace kim\present\tiledplants\traits;

use kim\present\tiledplants\block\ITiledPlant;
use kim\present\tiledplants\data\StackablePlantData;
use kim\present\tiledplants\Loader;
use kim\present\tiledplants\tile\Plants;
use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\math\Facing;

/**
 * This trait provides a implementation for stackable `ITiledPlant` to reduce boilerplate.
 *
 * @see ITiledPlant
 */
trait TiledStackTrait{
    use TiledPlantsTrait;

    public function grow() : void{
        /** @var Block|ITiledPlant $this */
        if(!$this->isRipe()){
            $world = $this->pos->getWorld();
            for($y = 1; $y < $this->getMaxHeight(); ++$y){
                $vec = $this->pos->add(0, $y, 0);
                if(!$world->isInWorld($vec->x, $vec->y, $vec->z))
                    break;

                $block = $world->getBlock($vec);
                if($block->getId() === BlockLegacyIds::AIR){
                    Plants::growPlant($block, clone $this);
                }
            }
        }
    }

    public function isRipe() : bool{
        if($this->getSide(Facing::DOWN)->isSameType($this))
            return true;

        $world = $this->pos->getWorld();
        $canGrow = false;
        for($y = 1; $y < $this->getMaxHeight(); ++$y){
            $vec = $this->pos->add(0, $y, 0);
            if(!$world->isInWorld($vec->x, $vec->y, $vec->z))
                break;

            $block = $world->getBlock($vec);
            if($block->isSameType($this))
                continue;

            if($block->getId() === BlockLegacyIds::AIR){
                $canGrow = true;
                break;
            }else{
                break;
            }
        }
        return !$canGrow;
    }

    public function getMaxHeight() : int{
        /** @var StackablePlantData $plantData */
        $plantData = $this->getPlantData();
        return $plantData->getMaxHeight();
    }

    public function onNearbyBlockChange() : void{
        parent::onNearbyBlockChange();

        $floor = $this;
        while(($down = $floor->getSide(Facing::DOWN))->isSameType($this)){
            $floor = $down;
        }

        $world = $this->pos->getWorld();
        $plantsTile = $world->getTile($floor->getPos());
        if($plantsTile instanceof Plants){
            $plantsTile->setLastTime(microtime(true));
        }
        $world->scheduleDelayedBlockUpdate($floor->getPos(), Loader::$updateDelay);
    }
}