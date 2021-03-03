<?php

/**
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://www.gnu.org/licenses/lgpl-3.0 LGPL-3.0 License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 *
 * @noinspection PhpIllegalPsrClassPathInspection
 * @noinspection SpellCheckingInspection
 */

declare(strict_types=1);

namespace kim\present\plantsplaner\traits;

use kim\present\plantsplaner\block\IPlants;
use kim\present\plantsplaner\tile\Plants;
use pocketmine\block\Block;

use function max;
use function microtime;

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
            if($this->canGrow()){
                $plantsTile = new Plants($world, $this->pos);
                $world->addTile($plantsTile);
            }
            return;
        }

        if($plantsTile->checkGrowth()){
            $growSeconds = $this->getGrowSeconds();
            if($growSeconds > 0xffffff) //If the growth seconds is too large, it will not grow.
                return;

            $diffSeconds = (microtime(true) - $plantsTile->getLastTime());
            if($diffSeconds > 0.1){ //If the difference from the last update time is too short, it is not calculated.
                $growSeconds -= $diffSeconds;
            }
            $plantsTile->getPos()->getWorld()->scheduleDelayedBlockUpdate($plantsTile->getPos(), (int) max(1, $growSeconds * 20 + 1));
        }else{
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

    /**
     * @override to register scheduling when near block changed
     * @noinspection PhpUndefinedClassInspection
     */
    public function onNearbyBlockChange() : void{
        /** @var Block|IPlants $this */
        parent::onNearbyBlockChange();

        if(!$this->canGrow())
            return;

        $world = $this->pos->getWorld();
        $plantsTile = $world->getTile($this->pos);
        if(!$plantsTile instanceof Plants){
            $plantsTile = new Plants($world, $this->pos);
            $world->addTile($plantsTile);
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