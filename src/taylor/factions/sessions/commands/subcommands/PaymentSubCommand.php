<?php namespace taylor\factions\sessions\commands\subcommands;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use taylor\factions\Main;
use taylor\factions\sessions\PlayerSession;
use taylor\factions\sessions\SessionManager;
use taylor\factions\utils\FormatUtils;

class PaymentSubCommand extends BaseSubCommand {

    private int $typeID;

    private string $typeStr;

    public function __construct(string $type, int $typeID) {
        parent::__construct(Main::getInstance(), $type, "Pay " . $type . " to a player!");
        $this->typeID = $typeID;
        $this->typeStr = PlayerSession::BALANCE_TO_STRING[$typeID];
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("player"));
        $this->registerArgument(1, new IntegerArgument("amount"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        if (!$sender instanceof Player) {
            $sender->sendMessage("You must be a player to use this command.");
            return;
        }
        $session = SessionManager::getInstance()->getSession($sender);
        if (is_null($player = Server::getInstance()->getPlayerExact($args["player"]))) {
            $sender->sendMessage($session->getMessage("commands.cantFindPlayer"));
            return;
        }
        if ($player->getId() == $sender->getId()) {
            $sender->sendMessage($session->getMessage("commands.pay.cantPaySelf"));
            return;
        }
        $amount = abs($args["amount"]);
        if ($amount > $session->getBalance($this->typeID)) {
            $sender->sendMessage($session->getMessage("commands.pay.notEnoughMoney", [["{type}"], [$this->typeStr]]));
            return;
        }
        $session->takeFromBalance($amount, $this->typeID);
        $otherSession = SessionManager::getInstance()->getSession($player);
        $otherSession->addToBalance($amount, $this->typeID);
        $formatted = FormatUtils::numberToSuffix($amount);
        $sender->sendMessage($session->getMessage("commands.pay.successInitializer", [["{amount}", "{type}", "{player}"], [$formatted, $this->typeStr, $player->getName()]]));
        $player->sendMessage($otherSession->getMessage("commands.pay.successReceiver", [["{amount}", "{type}", "{player}"], [$formatted, $this->typeStr, $sender->getName()]]));
    }

}