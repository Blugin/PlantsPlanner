<?php
declare(strict_types=1);

namespace kim\present\tiledplants\traits;

use kim\present\tiledplants\block\ITiledPlant;
use kim\present\tiledplants\tile\Plants;
use pocketmine\block\Crops;
use pocketmine\event\block\BlockGrowEvent;

/**
 * This trait provides a implementation for `Crops` and `ITiledPlant` to reduce boilerplate.
 *
 * @see Crops, ITiledPlant
 */
trait TiledCropsTrait{
    use TiledPlantsTrait;

    public function grow() : void{
        /** @var Crops|ITiledPlant $this */
        if(!$this->isRipe()){
            $block = clone $this;
            ++$block->age;
            $ev = new BlockGrowEvent($this, $block);
            $ev->call();
            if(!$ev->isCancelled()){
                $this->pos->getWorld()->setBlock($this->pos, $ev->getNewState());
                $this->onGrow();
            }
        }
    }

    public function isRipe() : bool{
        /** @var Crops|ITiledPlant $this */
        return $this->age >= 7;
    }
}