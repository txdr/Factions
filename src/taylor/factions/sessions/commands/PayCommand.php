<?php namespace taylor\factions\sessions\commands;

use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use taylor\factions\Main;
use taylor\factions\sessions\commands\subcommands\PaymentSubCommand;
use taylor\factions\sessions\PlayerSession;

class PayCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "pay", "Pay another player.");
        $this->setPermission("commands.pay");
    }

    public function prepare() : void {
        $this->registerSubCommand(new PaymentSubCommand("coins", PlayerSession::BALANCE_COINS));
        $this->registerSubCommand(new PaymentSubCommand("mobcoins", PlayerSession::BALANCE_MOBCOINS));
        $this->registerSubCommand(new PaymentSubCommand("exp", PlayerSession::BALANCE_EXPERIENCE));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {

    }

}