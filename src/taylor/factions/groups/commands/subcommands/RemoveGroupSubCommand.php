<?php namespace taylor\factions\groups\commands\subcommands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use taylor\factions\groups\GroupsManager;
use taylor\factions\Main;
use taylor\factions\sessions\SessionManager;

class RemoveGroupSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "remove", "Remove a group from a player.");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("player"));
        $this->registerArgument(1, new RawStringArgument("group"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $session = SessionManager::getInstance()->getSession($sender);
        if (is_null($player = Server::getInstance()->getPlayerExact($args["player"]))) {
            $sender->sendMessage($session->getMessage("commands.cantFindPlayer"));
            return;
        }
        if (is_null(GroupsManager::getInstance()->getGroup($group = $args["group"]))) {
            $sender->sendMessage($session->getMessage("commands.group.groupNotExists"));
            return;
        }
        $playerSession = SessionManager::getInstance()->getSession($player);
        if (!$playerSession->hasGroup($group)) {
            $sender->sendMessage($session->getMessage("commands.group.doesntHaveGroup"));
            return;
        }
        $playerSession->removeGroup($group);
        $sender->sendMessage($session->getMessage("commands.group.groupRemoved", [["{group}", "{player}"], [$group, $player->getName()]]));
    }

}