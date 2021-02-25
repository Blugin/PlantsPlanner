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

use kim\present\plantsplaner\data\PlantsData;
use kim\present\plantsplaner\tile\Plants;

/**
 * Interface of blocks defined so that growth is handled on Plants tiles.
 *
 * @see Plants, PlantsData
 */
interface IPlants{
    /** Returns this block's PlantsData */
    public function getPlantsData() : PlantsData;

    /** Returns the seconds it takes for this block to grow */
    public function getGrowSeconds() : float;

    /** Returns whether the block can grow */
    public function canGrow() : bool;

    /**
     * Proccess block growing
     * Returns whether the block can grow more
     */
    public function growPlants() : bool;
}