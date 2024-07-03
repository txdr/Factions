<?php namespace taylor\factions\kits\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use taylor\factions\Main;

class KitsManagerCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "kitsmgr", "Kits management command.");
        $this->setPermission("commands.kitsmanager");
    }

    public function prepare() : void {

    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {

    }

}