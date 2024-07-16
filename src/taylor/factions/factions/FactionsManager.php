<?php namespace taylor\factions\factions;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use taylor\factions\factions\commands\FactionCommand;
use taylor\factions\factions\tasks\FactionTickTask;
use taylor\factions\Main;

class FactionsManager {

    use SingletonTrait;

    /** @var array<string, Faction> */
    private array $factions = [];

    public function __construct() {
        self::setInstance($this);

        Server::getInstance()->getCommandMap()->register("Factions", new FactionCommand());
        Main::getInstance()->getScheduler()->scheduleRepeatingTask(new FactionTickTask(), 20);
    }

    /*** @return array<Faction> */
    public function getFactions() : array {
        return array_values($this->factions);
    }

    public function createFaction(Player $owner, string $name) : void {
        $this->factions[$name] = new Faction(
            $name,
            "Default faction description",
            "",
            date('d/m/Y h:i A'),
            $owner->getName(),
            "",
            "",
            "",
            true, $owner
        );

    }

    public function getFaction(string $name) : ?Faction {
        return $this->factions[$name] ?? null;
    }

    public function getInvitesForPlayer(Player $player) : array {
        return array_map(
            fn(Faction $faction) => $faction->getName(),
            array_filter(
                $this->getFactions(),
                fn(Faction $faction) => !is_null($faction->getInvitesManager()->getInvite($player))
            ));
    }

}