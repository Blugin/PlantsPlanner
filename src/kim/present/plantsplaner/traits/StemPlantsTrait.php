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
use kim\present\plantsplaner\data\BearablePlantsData;
use kim\present\plantsplaner\data\PlantsData;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Stem;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\math\Facing;

use function shuffle;

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
                    $this->pos->getWorld()->setBlock($this->pos, $ev->getNewState());
                }
            }else{
                $world = $this->pos->getWorld();
                $grow = $this->getPlant();

                $facings = Facing::HORIZONTAL;
                shuffle($facings);
                foreach($facings as $face){
                    $side = $this->getSide($face);
                    $downId = $world->getBlock($side->pos->subtract(0, 1, 0))->getId();
                    if($side->canBeReplaced() && ($downId === BlockLegacyIds::FARMLAND || $downId === BlockLegacyIds::GRASS || $downId === BlockLegacyIds::DIRT)){
                        $ev = new BlockGrowEvent($side, $grow);
                        $ev->call();
                        if(!$ev->isCancelled()){
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
            $world = $this->pos->getWorld();
            $stemPlant = $this->getPlant();
            $hasSpace = false;
            foreach(Facing::HORIZONTAL as $face){
                $side = $this->getSide($face);
                if($side->isSameType($stemPlant))
                    return false;

                $downId = $world->getBlock($side->pos->subtract(0, 1, 0))->getId();
                if($side->canBeReplaced() && ($downId === BlockLegacyIds::FARMLAND || $downId === BlockLegacyIds::GRASS || $downId === BlockLegacyIds::DIRT)){
                    $hasSpace = true;
                    break;
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