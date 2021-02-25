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
 * @noinspection DuplicatedCode
 */

declare(strict_types=1);

namespace kim\present\plantsplaner\traits;

use kim\present\plantsplaner\block\IPlants;
use kim\present\plantsplaner\data\BearablePlantsData;
use kim\present\plantsplaner\data\PlantsData;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Crops;
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
    use PlantsTrait;

    /** @inheritDoc */
    public function growPlants() : bool{
        /** @var Stem|IPlants $this */
        if($this->age < 7){
            $block = clone $this;
            ++$block->age;

            $ev = new BlockGrowEvent($this, $block);
            $ev->call();
            if(!$ev->isCancelled()){
                $newBlock = $ev->getNewState();
                $this->pos->getWorld()->setBlock($this->pos, $newBlock);
                return $newBlock instanceof Crops && $newBlock->age < 7;
            }
            return true;
        }else{
            $world = $this->pos->getWorld();
            $stemPlant = $this->getPlant();
            $place = null;

            $facings = Facing::HORIZONTAL;
            shuffle($facings);
            foreach($facings as $face){
                $side = $this->getSide($face);
                if($side->isSameType($stemPlant))
                    return false;

                if(!$side->canBeReplaced())
                    continue;

                $downId = $world->getBlockAt($side->pos->x, $side->pos->y - 1, $side->pos->z)->getId();
                if(
                    $downId === BlockLegacyIds::FARMLAND ||
                    $downId === BlockLegacyIds::GRASS ||
                    $downId === BlockLegacyIds::DIRT
                ){
                    $place = $side;
                }
            }

            if($place !== null){
                $ev = new BlockGrowEvent($place, $stemPlant);
                $ev->call();
                if(!$ev->isCancelled()){
                    $world->setBlock($place->pos, $ev->getNewState());
                }else{
                    return true;
                }
            }
            return false;
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

                if(!$side->canBeReplaced())
                    continue;

                $downId = $world->getBlockAt($side->pos->x, $side->pos->y - 1, $side->pos->z)->getId();
                if(
                    $downId === BlockLegacyIds::FARMLAND ||
                    $downId === BlockLegacyIds::GRASS ||
                    $downId === BlockLegacyIds::DIRT
                ){
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