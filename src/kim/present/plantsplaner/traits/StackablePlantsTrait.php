<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\traits;

use kim\present\plantsplaner\block\IPlants;
use kim\present\plantsplaner\data\StackablePlantsData;
use pocketmine\block\Block;
use pocketmine\event\block\BlockGrowEvent;

/**
 * This trait provides a implementation for stackable `IPlants` to reduce boilerplate.
 *
 * @see IPlants
 */
trait StackablePlantsTrait{
    use PlantsTrait;

    /** @inheritDoc */
    public function growPlants() : void{
        /** @var Block|IPlants $this */
        $world = $this->pos->getWorld();

        //Check if above block is replaceable block and this plant is the top block
        $up = $this->pos->add(0, 1, 0);
        if(!$world->isInWorld($up->x, $up->y, $up->z))
            return;

        $upBlock = $world->getBlock($up);
        if(!$upBlock->canBeReplaced() || $upBlock->isSameType($this))
            return;

        //Check if this plant is shorter than the maximum length
        $max = $this->getMaxGrowth();
        for($i = 1; $i < $max; ++$i){
            $vec = $this->pos->subtract(0, $i, 0);
            if(!$world->isInWorld($vec->x, $vec->y, $vec->z))
                return;

            if(!$world->getBlock($vec)->isSameType($this)){
                $ev = new BlockGrowEvent($upBlock, clone $this);
                $ev->call();
                if(!$ev->isCancelled()){
                    $world->setBlock($up, $ev->getNewState());
                }
            }
        }
    }

    /** @inheritDoc */
    public function canGrow() : bool{
        /** @var Block|IPlants $this */
        $world = $this->pos->getWorld();

        //Check if above block is replaceable block and this plant is the top block
        $up = $this->pos->add(0, 1, 0);
        if(!$world->isInWorld($up->x, $up->y, $up->z))
            return false;

        $upBlock = $world->getBlock($up);
        if(!$upBlock->canBeReplaced() || $upBlock->isSameType($this))
            return false;

        //Check if this plant is shorter than the maximum length
        $max = $this->getMaxGrowth();
        for($i = 1; $i < $max; ++$i){
            $vec = $this->pos->subtract(0, $i, 0);
            if(!$world->isInWorld($vec->x, $vec->y, $vec->z))
                return false;

            if(!$world->getBlock($vec)->isSameType($this)){
                return true;
            }
        }
        return false;
    }

    /** @see StackablePlantsData::getMaxGrowth() */
    public function getMaxGrowth() : int{
        return $this->getPlantsData()->getMaxGrowth();
    }
}