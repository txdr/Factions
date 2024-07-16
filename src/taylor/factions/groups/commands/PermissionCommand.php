<?php namespace taylor\factions\groups\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use taylor\factions\groups\commands\subcommands\RemovePermissionSubCommand;
use taylor\factions\Main;

class PermissionCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "permission", "Manage user permissions.");
        $this->setPermission("commands.permission");
    }

    public function prepare() : void {
        $this->registerSubCommand(new RemovePermissionSubCommand());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {

    }

}