<?php namespace taylor\factions;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use taylor\factions\sessions\SessionManager;

class EventListener implements Listener {

    public function onLogin(PlayerLoginEvent $event) : void {
        SessionManager::getInstance()->createSession($event->getPlayer());
    }

    public function onQuit(PlayerQuitEvent $event) : void {
        $player = $event->getPlayer();
        SessionManager::getInstance()->closeSession($player);
    }

}