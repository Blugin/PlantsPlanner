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

namespace kim\present\plantsplaner\block;

use kim\present\plantsplaner\data\DefaultPlants;
use kim\present\plantsplaner\data\PlantsData;
use kim\present\plantsplaner\traits\PlantsTrait;
use pocketmine\block\Sapling;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\item\VanillaItems;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;

final class SaplingPlants extends Sapling implements IPlants{
    use PlantsTrait;

    public function getPlantsData() : PlantsData{
        return DefaultPlants::SAPLING();
    }

    public function growPlants() : bool{
        if($this->isReady()){
            //HACK: Since the private property $treeType cannot be used, interact with bone meal.
            $this->onInteract(VanillaItems::BONE_MEAL(), Facing::UP, new Vector3(0, 0, 0));
        }else{
            $block = clone $this;
            $block->setReady(true);

            $ev = new BlockGrowEvent($this, $block);
            $ev->call();
            if(!$ev->isCancelled()){
                $this->pos->getWorld()->setBlock($this->pos, $ev->getNewState());
            }
        }
        return true;
    }

    public function canGrow() : bool{
        return true; //HACK: Continues to try to grow without checks.
    }
}