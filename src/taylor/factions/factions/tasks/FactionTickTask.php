<?php namespace taylor\factions\factions\tasks;

use pocketmine\scheduler\Task;
use taylor\factions\factions\FactionsManager;

class FactionTickTask extends Task {

    public function onRun() : void {
        foreach(FactionsManager::getInstance()->getFactions() as $faction) {
            $faction->getInvitesManager()->tickInvites();
        }
    }

}