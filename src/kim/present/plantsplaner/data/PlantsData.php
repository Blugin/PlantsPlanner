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

namespace kim\present\plantsplaner\data;

use kim\present\plantsplaner\tile\Plants;

/**
 * Plants handles growth based on PlantsData.
 * It has setting values for the time it takes for the block to grow and many other options.
 *
 * @see Plants
 */
class PlantsData{
    /** Seconds it takes to grow. (It means grow to the next level, not full-growth time) */
    private float $growSeconds;

    public function __construct(float $growSeconds){
        $this->growSeconds = $growSeconds;
    }

    public function getGrowSeconds() : float{
        return $this->growSeconds;
    }

    /** Returns a PlantsData generated as values in an array. */
    public static function fromArray(array $array) : PlantsData{
        return new PlantsData((float) ($array["grow-seconds"] ?? PHP_INT_MAX));
    }
}