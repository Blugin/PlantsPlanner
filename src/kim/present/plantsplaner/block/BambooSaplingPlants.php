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
 * @noinspection PhpDocSignatureInspection
 */

declare(strict_types=1);

namespace kim\present\plantsplaner\block;

use kim\present\plantsplaner\data\DefaultPlants;
use kim\present\plantsplaner\data\PlantsData;
use kim\present\plantsplaner\traits\PlantsTrait;
use pocketmine\block\Bamboo;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockLegacyMetadata;
use pocketmine\block\Dirt;
use pocketmine\block\Flowable;
use pocketmine\block\Grass;
use pocketmine\block\Gravel;
use pocketmine\block\Mycelium;
use pocketmine\block\Podzol;
use pocketmine\block\Sand;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\item\Bamboo as ItemBamboo;
use pocketmine\item\Fertilizer;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

final class BambooSaplingPlants extends Flowable implements IPlants{
    use PlantsTrait;

    public function getPlantsData() : PlantsData{
        return DefaultPlants::BAMBOO();
    }

    private bool $ready = false;

    public function readStateFromData(int $id, int $stateMeta) : void{
        $this->ready = ($stateMeta & BlockLegacyMetadata::SAPLING_FLAG_READY) !== 0;
    }

    protected function writeStateToMeta() : int{
        return $this->ready ? BlockLegacyMetadata::SAPLING_FLAG_READY : 0;
    }

    public function getStateBitmask() : int{
        return 0b1000;
    }

    public function isReady() : bool{
        return $this->ready;
    }

    /** @return $this */
    public function setReady(bool $ready) : self{
        $this->ready = $ready;
        return $this;
    }

    private function canBeSupportedBy(Block $block) : bool{
        return $block instanceof Dirt
            || $block instanceof Grass
            || $block instanceof Gravel
            || $block instanceof Sand
            || $block instanceof Mycelium
            || $block instanceof Podzol;
    }

    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
        return $this->canBeSupportedBy($blockReplace->pos->getWorld()->getBlock($blockReplace->pos->subtract(0, 1, 0)))
            && parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
        if($item instanceof Fertilizer || $item instanceof ItemBamboo){
            $this->growPlants();

            $item->pop();
            return true;
        }
        return false;
    }

    public function onNearbyBlockChange() : void{
        if(!$this->canBeSupportedBy($this->pos->getWorld()->getBlock($this->pos->subtract(0, 1, 0)))){
            $this->pos->getWorld()->useBreakOn($this->pos);
        }
    }

    public function growPlants() : void{
        if($this->canGrow()){
            if($this->isReady()){
                $world = $this->pos->getWorld();

                /** @var BambooPlants $block */
                $block = BlockFactory::getInstance()->get(BlockLegacyIds::BAMBOO, 0);
                $ev = new BlockGrowEvent($this, $block);
                $ev->call();
                if(!$ev->isCancelled()){
                    $world->setBlock($this->pos, $ev->getNewState());

                    $upBlock = $world->getBlock($this->pos->add(0, 1, 0));
                    $bamboo = (clone $block)->setLeafSize(Bamboo::SMALL_LEAVES);
                    $ev2 = new BlockGrowEvent($upBlock, $bamboo);
                    $ev2->call();
                    if(!$ev2->isCancelled()){
                        $world->setBlock($upBlock->pos, $ev2->getNewState());
                    }
                }
            }else{
                $block = clone $this;
                $block->setReady(true);

                $ev = new BlockGrowEvent($this, $block);
                $ev->call();
                if(!$ev->isCancelled()){
                    $this->pos->getWorld()->setBlock($this->pos, $ev->getNewState());
                }
            }
        }
    }

    public function canGrow() : bool{
        if($this->isReady())
            return true;

        $world = $this->pos->getWorld();
        $up = $this->pos->add(0, 1, 0);
        return $world->isInWorld($up->x, $up->y, $up->z) && $world->getBlock($up)->canBeReplaced();
    }

    public function asItem() : Item{
        return VanillaBlocks::BAMBOO()->asItem();
    }
}