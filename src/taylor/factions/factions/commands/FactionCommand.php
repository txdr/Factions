<?php namespace taylor\factions\factions\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use taylor\factions\factions\commands\subcommands\CreateFactionSubCommand;
use taylor\factions\factions\commands\subcommands\InviteSubCommand;
use taylor\factions\factions\commands\subcommands\JoinFactionSubCommand;
use taylor\factions\Main;

class FactionCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "faction", "Base Factions command.", ["f", "fac", "factions"]);
        $this->setPermission("commands.faction");
    }

    public function prepare() : void {
        $this->registerSubCommand(new CreateFactionSubCommand());
        $this->registerSubCommand(new InviteSubCommand());
        $this->registerSubCommand(new JoinFactionSubCommand());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {

    }

}