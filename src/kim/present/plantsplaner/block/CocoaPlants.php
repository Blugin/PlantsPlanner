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
use pocketmine\block\CocoaBlock;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\item\Fertilizer;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

final class CocoaPlants extends CocoaBlock implements IPlants{
    use PlantsTrait;

    public function getPlantsData() : PlantsData{
        return DefaultPlants::COCOA();
    }

    /** @override to call BlockGrowEvent, I don't know why, but PMMP doesn't call Cocoa's events. */
    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
        if($this->age < 2 && $item instanceof Fertilizer){
            $block = clone $this;
            ++$block->age;

            $ev = new BlockGrowEvent($this, $block);
            $ev->call();
            if(!$ev->isCancelled()){
                $this->pos->getWorld()->setBlock($this->pos, $ev->getNewState());
            }

            $item->pop();

            return true;
        }

        return false;
    }

    public function growPlants() : bool{
        if($this->age < 2){
            $block = clone $this;
            ++$block->age;

            $ev = new BlockGrowEvent($this, $block);
            $ev->call();
            if(!$ev->isCancelled()){
                /** @var CocoaPlants $newBlock */
                $newBlock = $ev->getNewState();
                $this->pos->getWorld()->setBlock($this->pos, $newBlock);
                return $newBlock->age < 2;
            }
            return true;
        }
        return false;
    }

    public function canGrow() : bool{
        return $this->age < 2;
    }
}