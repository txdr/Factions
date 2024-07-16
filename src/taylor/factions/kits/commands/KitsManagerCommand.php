<?php namespace taylor\factions\kits\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use taylor\factions\kits\commands\subcommands\CreateKitSubCommand;
use taylor\factions\kits\commands\subcommands\DeleteKitSubCommand;
use taylor\factions\kits\commands\subcommands\EditKitSubCommand;
use taylor\factions\Main;

class KitsManagerCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "kitsmgr", "Kits management command.");
        $this->setPermission("commands.kitsmanager");
    }

    public function prepare() : void {
        $this->registerSubCommand(new CreateKitSubCommand());
        $this->registerSubCommand(new DeleteKitSubCommand());
        $this->registerSubCommand(new EditKitSubCommand());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {

    }

}