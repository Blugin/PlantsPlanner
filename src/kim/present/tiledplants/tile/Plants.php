<?php
declare(strict_types=1);

namespace kim\present\tiledplants\tile;

use kim\present\tiledplants\block\ITiledPlant;
use pocketmine\block\tile\Tile;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class Plants extends Tile{
    public static int $updateDelay = 60 * 20;

    public const TAG_LAST_TIME = "LastTime";

    protected float $lastTime;

    public function __construct(World $world, Vector3 $pos){
        parent::__construct($world, $pos);

        $this->lastTime = microtime(true);
        $this->pos->getWorld()->scheduleDelayedBlockUpdate($this->pos, self::$updateDelay);
    }

    public function readSaveData(CompoundTag $nbt) : void{
        $this->lastTime = $nbt->getFloat(self::TAG_LAST_TIME, microtime(true));
        $this->onUpdate();
    }

    protected function writeSaveData(CompoundTag $nbt) : void{
        $nbt->setFloat(self::TAG_LAST_TIME, $this->lastTime);
    }

    /** @override for grow up plant */
    public function onUpdate() : bool{
        if($this->closed)
            return false;

        $block = $this->getBlock();
        if(!$block instanceof ITiledPlant || $block->isRipe())
            return false;

        $this->timings->startTiming();
        $diffSeconds = microtime(true) - $this->lastTime;
        $growSeconds = $block->getPlantData()->getGrowSeconds();
        while(!$block->isRipe() && $diffSeconds > $growSeconds){
            $diffSeconds -= $growSeconds;
            $block->grow();

            $block = $this->getBlock();
            if(!$block instanceof ITiledPlant)
                return false;
        }
        $this->lastTime -= $diffSeconds;
        $this->timings->stopTiming();

        return !$block->isRipe();
    }

    public function getLastTime() : float{
        return $this->lastTime;
    }

    public function setLastTime(float $lastTime) : void{
        $this->lastTime = $lastTime;
    }
}