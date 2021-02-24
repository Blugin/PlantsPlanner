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
use pocketmine\block\Crops;
use pocketmine\event\block\BlockGrowEvent;

/**
 * This trait provides a implementation for `Crops` and `IPlants` to reduce boilerplate.
 *
 * @see Crops, IPlants
 */
trait CropsPlantsTrait{
    use PlantsTrait;

    /** @inheritDoc */
    public function growPlants() : void{
        /** @var Crops|IPlants $this */
        if($this->canGrow()){
            $block = clone $this;
            ++$block->age;

            $ev = new BlockGrowEvent($this, $block);
            $ev->call();
            if(!$ev->isCancelled()){
                $this->pos->getWorld()->setBlock($this->pos, $ev->getNewState());
            }
        }
    }

    /** @inheritDoc */
    public function canGrow() : bool{
        /** @var Crops|IPlants $this */
        return $this->age < 7;
    }
}