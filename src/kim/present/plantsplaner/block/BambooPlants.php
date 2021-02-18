<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\block;

use kim\present\plantsplaner\data\DefaultPlants;
use kim\present\plantsplaner\data\PlantsData;
use kim\present\plantsplaner\traits\StackablePlantsTrait;
use pocketmine\block\Bamboo;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\world\BlockTransaction;

final class BambooPlants extends Bamboo implements IPlants{
    use StackablePlantsTrait;

    public function getPlantsData() : PlantsData{
        return DefaultPlants::BAMBOO();
    }

    public function growPlants() : void{
        if($this->canGrow()){
            /** @var Bamboo[] $newBlocks */
            $newBlocks = [clone $this];

            $world = $this->pos->getWorld();
            for($y = 1; $y < $this->getMaxGrowth(); ++$y){
                $vec = $this->pos->add(0, $y, 0);
                if(!$world->isInWorld($vec->x, $vec->y, $vec->z))
                    break;

                $block = $world->getBlock($vec);
                if($block instanceof Bamboo){
                    $newBlocks[$y] = (clone $block)->setLeafSize(self::NO_LEAVES);
                    continue;
                }

                if($block->canBeReplaced()){
                    if($y < 2){
                        $block = (clone $this)->setLeafSize(self::NO_LEAVES);
                    }elseif($y < 4){
                        $block = (clone $this)->setLeafSize(self::SMALL_LEAVES);
                        $newBlocks[$y - 1]->setLeafSize(self::SMALL_LEAVES);
                    }else{
                        foreach($newBlocks as $newBlock){ //Make thick all stems
                            $newBlock->setThick(true);
                        }

                        if($y === 4){
                            $newBlocks[$y - 1]->setLeafSize(self::SMALL_LEAVES);
                        }else{
                            $newBlocks[$y - 2]->setLeafSize(self::SMALL_LEAVES);
                            $newBlocks[$y - 1]->setLeafSize(self::LARGE_LEAVES);
                        }
                        $block = (clone $this)->setLeafSize(self::LARGE_LEAVES)->setThick(true);
                    }
                    $newBlocks[$y] = $block;

                    $ev = new BlockGrowEvent($block, clone $this);
                    $ev->call();
                    if(!$ev->isCancelled()){
                        $tx = new BlockTransaction($this->pos->getWorld());
                        foreach($newBlocks as $newY => $newBlock){
                            $tx->addBlock($this->pos->add(0, $newY, 0), $newBlock);
                        }
                        $tx->apply();
                    }
                    break;
                }else{
                    break;
                }
            }
        }
    }
}