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

namespace kim\present\plantsplaner\tile;

use kim\present\plantsplaner\block\IPlants;
use kim\present\plantsplaner\Loader;
use pocketmine\block\tile\Tile;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\World;

use function microtime;

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
        if(!$block instanceof IPlants){
            Loader::getInstance()->getScheduler()->scheduleTask(new ClosureTask(function() : void{ $this->close(); }));
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
        if(!$block instanceof IPlants)
            return false;

        $this->timings->startTiming();
        $diffSeconds = microtime(true) - $this->lastTime;
        $growSeconds = $block->getGrowSeconds();

        $world = $this->pos->getWorld();
        $canGrow = true;
        while($canGrow && $diffSeconds > $growSeconds){
            $diffSeconds -= $growSeconds;
            if(!($canGrow = $block->growPlants()))
                break;

            $block = $world->getBlock($block->getPos());
            if($block instanceof IPlants){
                $growSeconds = $block->getGrowSeconds();
            }else{
                //HACK: for prevents errors that occur if tiles are destroyed as plants grow
                $canGrow = false;
                break;
            }
        }
        $this->lastTime = microtime(true) - $diffSeconds;
        $this->timings->stopTiming();

        return $canGrow;
    }

    public function getLastTime() : float{
        return $this->lastTime;
    }

    public function setLastTime(float $lastTime) : void{
        $this->lastTime = $lastTime;
    }
}