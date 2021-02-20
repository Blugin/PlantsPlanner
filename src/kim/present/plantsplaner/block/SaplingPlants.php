<?php
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

    public function growPlants() : void{
        if($this->canGrow()){
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
        }
    }

    public function canGrow() : bool{
        return true; //HACK: Continues to try to grow without checks.
    }
}