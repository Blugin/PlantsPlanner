<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\traits;

use kim\present\plantsplaner\block\IPlants;
use kim\present\plantsplaner\tile\Plants;
use pocketmine\block\Block;

/**
 * This trait provides a implementation for `IPlants` to reduce boilerplate.
 *
 * @see IPlants
 */
trait PlantsTrait{
    public function onScheduledUpdate() : void{
        /** @var Block|IPlants $this */
        $plantsTile = $this->pos->getWorld()->getTile($this->pos);
        if($plantsTile instanceof Plants){
            if($plantsTile->onUpdate()){
                $this->pos->getWorld()->scheduleDelayedBlockUpdate($this->pos, Plants::$updateDelay);
            }elseif($this->getPlantsData()->isTemporary()){
                $plantsTile->close();
            }
        }
    }

    public function onPostPlace() : void{
        /** @var Block|IPlants $this */
        $plantsTile = $this->pos->getWorld()->getTile($this->pos);
        if(!$plantsTile instanceof Plants){
            $this->pos->getWorld()->addTile(new Plants($this->pos->getWorld(), $this->pos));
        }
    }

    public function ticksRandomly() : bool{
        return false;
    }

    public function onRandomTick() : void{
    }

    public function getGrowSeconds() : float{
        /** @var Block|IPlants $this */
        return $this->getPlantsData()->getGrowSeconds();
    }
}