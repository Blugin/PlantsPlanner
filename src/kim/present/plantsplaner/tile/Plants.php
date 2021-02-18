<?php
declare(strict_types=1);

namespace kim\present\plantsplaner\tile;

use kim\present\plantsplaner\block\IPlants;
use kim\present\plantsplaner\Loader;
use pocketmine\block\tile\Tile;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\world\World;

/**
 * Tile class created to grow in the precise time and save data in the worlds,
 * Save the last time check time, so that the plant will grow even if the chunk is not loaded.
 */
class Plants extends Tile{
    public const TAG_LAST_TIME = "LastTime";

    /** Last time to checked plant growth */
    protected float $lastTime;

    /** @override to register scehduling */
    public function __construct(World $world, Vector3 $pos){
        parent::__construct($world, $pos);

        $this->lastTime = microtime(true);

        $block = $this->getBlock();
        if($block instanceof IPlants){
            self::schedulePlants($this, $block);
        }else{
            Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() : void{ $this->close(); }), 1);
        }
    }

    /** @override to read last-time from nbt and run check-growth */
    public function readSaveData(CompoundTag $nbt) : void{
        $this->lastTime = $nbt->getFloat(self::TAG_LAST_TIME, microtime(true));
        $this->checkGrowth();
    }

    /** @override to write last-time to nbt */
    protected function writeSaveData(CompoundTag $nbt) : void{
        $nbt->setFloat(self::TAG_LAST_TIME, $this->lastTime);
    }

    /**
     * Check whether the plants is growing.
     * If returns false, Scheduling ends.
     */
    public function checkGrowth() : bool{
        if($this->closed)
            return false;

        $block = $this->getBlock();
        if(!$block instanceof IPlants || !$block->canGrow())
            return false;

        $this->timings->startTiming();
        $diffSeconds = microtime(true) - $this->lastTime;
        $growSeconds = $block->getGrowSeconds();
        while($block->canGrow() && $diffSeconds > $growSeconds){
            $diffSeconds -= $growSeconds;
            $block->growPlants();

            //HACK: for prevents errors that occur if tiles are destroyed as plants grow
            $block = $block->getSide(0, 0);
            if(!$block instanceof IPlants)
                return false;
        }
        $this->lastTime = microtime(true) - $diffSeconds;
        $this->timings->stopTiming();

        return $block->canGrow();
    }

    public function getLastTime() : float{
        return $this->lastTime;
    }

    public function setLastTime(float $lastTime) : void{
        $this->lastTime = $lastTime;
    }

    public static function schedulePlants(Plants $tile, IPlants $block) : void{
        $delay = ($block->getGrowSeconds() - (microtime(true) - $tile->getLastTime()));

        $tile->pos->getWorld()->scheduleDelayedBlockUpdate($tile->pos, (int) max(1, $delay * 20 + 1));
    }
}