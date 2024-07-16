<?php namespace taylor\factions\sessions\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use taylor\factions\Main;
use taylor\factions\sessions\PlayerSession;
use taylor\factions\sessions\SessionManager;
use taylor\factions\utils\FormatUtils;

class MyCommand extends BaseCommand {

    private int $typeID;

    private string $typeStr;

    public function __construct(string $type, string $typeID) {
        parent::__construct(Main::getInstance(), "my" . $type, "Check your (or another players) balance!" . $type . "!");
        $this->setPermission("commands.my");
        $this->typeID = $typeID;
        $this->typeStr = PlayerSession::BALANCE_TO_STRING[$typeID];
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("player", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $session = SessionManager::getInstance()->getSession($sender);
        if (!$sender instanceof Player) {
            $sender->sendMessage($session->getMessage("commands.mustBeInGame"));
            return;
        }
        if (isset($args["player"])) {
            if (is_null($player = Server::getInstance()->getPlayerExact($args["player"]))) {
                $sender->sendMessage($session->getMessage("commands.cantFindPlayer"));
                return;
            }
            $playerSession = SessionManager::getInstance()->getSession($player);
            $sender->sendMessage($session->getMessage("commands.my.otherplayersbalance", [["{player}", "{amount}", "{type}"], [$player->getName(), FormatUtils::numberToSuffix($playerSession->getBalance($this->typeID)), $this->typeStr]]));
            return;
        }
        $sender->sendMessage($session->getMessage("commands.my.mybalance", [["{type}", "{amount}"], [$this->typeStr, FormatUtils::numberToSuffix($session->getBalance($this->typeID))]]));
    }

}