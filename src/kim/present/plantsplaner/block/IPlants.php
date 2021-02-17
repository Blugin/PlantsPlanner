<?php
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

    /** Proccess block growing */
    public function grow() : void;
}