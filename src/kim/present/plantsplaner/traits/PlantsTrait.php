<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\traits;

use kim\present\plantsplaner\block\IPlants;
use kim\present\plantsplaner\tile\Plants;
use pocketmine\block\Block;

/**
 * This trait provides a implementation for `IPlants` to reduce boilerplate.
 *
 * - Process scheduling and repeating it if needs.
 * - Remove tiles from plants that have fully-growth.
 * -
 *
 * @see IPlants
 */
trait PlantsTrait{
    /**
     * @override to process scheduling and repeating it if needs.
     * And remove tiles from plants that have fully-growth and this block is temponrary
     */
    public function onScheduledUpdate() : void{
        /** @var Block|IPlants $this */
        $world = $this->pos->getWorld();
        $plantsTile = $world->getTile($this->pos);
        if(!$plantsTile instanceof Plants){
            $plantsTile = new Plants($world, $this->pos);
            $world->addTile($plantsTile);
        }

        if($plantsTile->checkGrowth()){
            $this->pos->getWorld()->scheduleDelayedBlockUpdate($this->pos, Plants::$updateDelay);
        }elseif($this->getPlantsData()->isTemporary()){
            $plantsTile->close();
        }
    }

    /** @override to create tiles when block is placed */
    public function onPostPlace() : void{
        /** @var Block|IPlants $this */
        $plantsTile = $this->pos->getWorld()->getTile($this->pos);
        if(!$plantsTile instanceof Plants){
            $this->pos->getWorld()->addTile(new Plants($this->pos->getWorld(), $this->pos));
        }
    }

    /** @override to not perform block random ticking */
    public function ticksRandomly() : bool{
        return false;
    }

    /** @override to not perform block random ticking */
    public function onRandomTick() : void{
    }

    /** Returns the seconds it takes for this block to grow */
    public function getGrowSeconds() : float{
        /** @var Block|IPlants $this */
        return $this->getPlantsData()->getGrowSeconds();
    }
}