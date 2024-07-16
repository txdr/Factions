<?php namespace taylor\factions\groups\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use taylor\factions\groups\commands\subcommands\AddGroupSubCommand;
use taylor\factions\groups\commands\subcommands\RemoveGroupSubCommand;
use taylor\factions\Main;

class GroupCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "group", "Manage user groups.");
        $this->setPermission("commands.group");
    }

    public function prepare() : void {
        $this->registerSubCommand(new AddGroupSubCommand());
        $this->registerSubCommand(new RemoveGroupSubCommand());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {

    }

}