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

class GiveCurrencySubCommand extends BaseSubCommand {

    private int $typeID;

    private string $typeStr;

    public function __construct(string $type, int $typeID) {
        parent::__construct(Main::getInstance(), $type, "Give " . $type . " to a player!");
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
        $amount = $args["amount"];
        $otherSession = SessionManager::getInstance()->getSession($player);
        $otherSession->addToBalance($amount, $this->typeID);
        $sender->sendMessage($session->getMessage("commands.pay.successInitializer", [["{amount}", "{type}", "{player}"], [FormatUtils::numberToSuffix($amount), $this->typeStr, $player->getName()]]));
    }

}