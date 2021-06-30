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
use kim\present\plantsplaner\data\StackablePlantsData;
use pocketmine\block\Block;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\world\World;

/**
 * This trait provides a implementation for stackable `IPlants` to reduce boilerplate.
 *
 * @see IPlants
 */
trait StackablePlantsTrait{
    use PlantsTrait;

    /** @inheritDoc */
    public function growPlants() : bool{
        /** @var Block|IPlants $this */
        $world = $this->pos->getWorld();

        //Check if above block is replaceable block and this plant is the top block
        $upY = $this->pos->y + 1;
        if(!$world->isInWorld($this->pos->x, $upY, $this->pos->z))
            return false;

        $upBlock = $world->getBlockAt($this->pos->x, $upY, $this->pos->z);
        if(!$upBlock->canBeReplaced() || $upBlock->isSameType($this))
            return false;

        //Check if this plant is shorter than the maximum length
        $minY = $this->pos->y - $this->getMaxGrowth();
        for($y = $this->pos->y - 1; $y > $minY; --$y){
            if(
                !$world->isInWorld($this->pos->x, $y, $this->pos->z) ||
                !$world->getBlockAt($this->pos->x, $y, $this->pos->z)->isSameType($this)
            ){
                $ev = new BlockGrowEvent($upBlock, clone $this);
                $ev->call();
                if(!$ev->isCancelled()){
                    $world->setBlock($upBlock->pos, $ev->getNewState());
                    return false;
                }
                break;
            }
        }
        return true;
    }

    /** @inheritDoc */
    public function canGrow() : bool{
        /** @var Block|IPlants $this */
        $world = $this->pos->getWorld();

        //Check if above block is replaceable block and this plant is the top block
        $upY = $this->pos->y + 1;
        if(!$world->isInWorld($this->pos->x, $upY, $this->pos->z))
            return false;

        $upBlock = $world->getBlockAt($this->pos->x, $upY, $this->pos->z);
        if(!$upBlock->canBeReplaced() || $upBlock->isSameType($this))
            return false;

        //Check if this plant is shorter than the maximum length
        $minY = $this->pos->y - $this->getMaxGrowth();
        for($y = $this->pos->y - 1; $y > $minY; --$y){
            if(
                !$world->isInWorld($this->pos->x, $y, $this->pos->z) ||
                !$world->getBlockAt($this->pos->x, $y, $this->pos->z)->isSameType($this)
            ){
                return true;
            }
        }
        return false;
    }

    /** @see StackablePlantsData::getMaxGrowth() */
    public function getMaxGrowth() : int{
        return $this->getPlantsData()->getMaxGrowth();
    }

    /**
     * @see Block::getAffectedBlocks()
     * @override to destroyed at once
     */
    public function getAffectedBlocks() : array{
        /** @var Block|IPlants $this */
        $blocks = [$this];

        $world = $this->pos->getWorld();
        for($y = $this->pos->y + 1; $y <= World::Y_MAX; ++$y){
            $block = $world->getBlockAt($this->pos->x, $y, $this->pos->z);
            if($block->isSameType($this)){
                $blocks[] = $block;
            }else{
                break;
            }
        }
        return $blocks;
    }
}