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

namespace kim\present\plantsplaner\block;

use kim\present\plantsplaner\data\DefaultPlants;
use kim\present\plantsplaner\data\PlantsData;
use kim\present\plantsplaner\traits\StackablePlantsTrait;
use pocketmine\block\Bamboo;
use pocketmine\block\Block;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\world\BlockTransaction;

final class BambooPlants extends Bamboo implements IPlants{
    use StackablePlantsTrait;

    public function getPlantsData() : PlantsData{
        return DefaultPlants::BAMBOO();
    }

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

        /** @var Bamboo[] $newBlocks */
        $newBlocks = [clone $this];
        $max = $this->getMaxGrowth();
        for($i = 1; $i <= $max; ++$i){
            if(!$world->isInWorld($this->pos->x, $upY - $i, $this->pos->z))
                break;

            $block = $world->getBlockAt($this->pos->x, $upY - $i, $this->pos->z);
            if($block->isSameType($this)){
                $newBlocks[$i] = clone $block;
            }else{
                break;
            }
        }

        //Check if this plant is shorter than the maximum length
        if(isset($newBlocks[$max + 1]))
            return false;

        foreach($newBlocks as $newBlock){
            $newBlock->setLeafSize(self::NO_LEAVES); //Remove leaves all stems
            if(isset($newBlocks[3])){
                $newBlock->setThick(true); //Make thick all stems
            }
        }
        if(!isset($newBlocks[2])){
            $newBlocks[0]->setLeafSize(self::SMALL_LEAVES);
            $newBlocks[1]->setLeafSize(self::SMALL_LEAVES);
        }elseif(!isset($newBlocks[3])){
            $newBlocks[0]->setLeafSize(self::LARGE_LEAVES);
            $newBlocks[1]->setLeafSize(self::SMALL_LEAVES);
        }else{
            $newBlocks[0]->setLeafSize(self::LARGE_LEAVES);
            $newBlocks[1]->setLeafSize(self::LARGE_LEAVES);
            $newBlocks[2]->setLeafSize(self::SMALL_LEAVES);
        }

        $ev = new BlockGrowEvent($upBlock, $newBlocks[0]);
        $ev->call();
        if(!$ev->isCancelled()){
            $newBlocks[0] = $ev->getNewState();

            $tx = new BlockTransaction($world);
            foreach($newBlocks as $i => $newBlock){
                $tx->addBlockAt($this->pos->x, $upY - $i, $this->pos->z, $newBlock);
            }
            $tx->apply();
        }
        return isset($newBlocks[$max]);
    }
}