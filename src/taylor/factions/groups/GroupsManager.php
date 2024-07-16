<?php namespace taylor\factions\groups;

use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use taylor\factions\groups\commands\GroupCommand;
use taylor\factions\groups\commands\PermissionCommand;
use taylor\factions\Main;

class GroupsManager {

    use SingletonTrait;

    /** @var array<string, Group> */
    private array $groups = [];

    public function __construct() {
        self::setInstance($this);

        foreach(Main::getInstance()->getConfig()->get("groups") as $name => $data) {
            $this->groups[$name] = new Group(
                $name,
                $data["fancyName"],
                $data["priority"],
                $data["permissions"]
            );
        }

        Server::getInstance()->getCommandMap()->registerAll("Factions", [
            new PermissionCommand(),
            new GroupCommand()
        ]);
    }

    public function getGroup(string $name) : ?Group { 
        return $this->groups[$name] ?? null;
    }

}