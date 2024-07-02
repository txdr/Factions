<?php namespace taylor\factions\sessions\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use taylor\factions\Main;
use taylor\factions\sessions\commands\subcommands\GiveCurrencySubCommand;
use taylor\factions\sessions\PlayerSession;

class GiveCurrencyCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "givecurrency", "Give currency to a player.");
        $this->setPermission("commands.givecurrency");
    }

    public function prepare() : void {
        $this->registerSubCommand(new GiveCurrencySubCommand("coins", PlayerSession::BALANCE_COINS));
        $this->registerSubCommand(new GiveCurrencySubCommand("mobcoins", PlayerSession::BALANCE_MOBCOINS));
        $this->registerSubCommand(new GiveCurrencySubCommand("exp", PlayerSession::BALANCE_EXPERIENCE));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {

    }

}