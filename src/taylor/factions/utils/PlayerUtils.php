<?php namespace taylor\factions\utils;

use pocketmine\item\Item;
use pocketmine\player\Player;

class PlayerUtils {

    public static function safeGive(Player $player, Item $item) : bool {
        if ($player->getInventory()->canAddItem($item)) {
            $player->getInventory()->addItem($item);
            return true;
        }
        $player->getWorld()->dropItem($player->getPosition()->asVector3(), $item);
        return false;
    }

}