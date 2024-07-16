<?php namespace taylor\factions\factions\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use taylor\factions\factions\FactionsManager;
use taylor\factions\Main;
use taylor\factions\sessions\SessionManager;

class CreateFactionSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "create", "Create a faction.");
    }

    public function prepare() : void {}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $session = SessionManager::getInstance()->getSession($sender);
        if (!$sender instanceof Player) {
            $sender->sendMessage($session->getMessage("commands.mustBeInGame"));
            return;
        }
        if ($session->isInFaction()) {
            $sender->sendMessage($session->getMessage("commands.faction.alreadyInFaction"));
            return;
        }
        $sender->sendForm(new CustomForm("Faction Creation", [
            new Input("name", "Faction name", "cool name")
        ], function(Player $player, CustomFormResponse $data) use($session) : void {
            $fm = FactionsManager::getInstance();
            $name = $data->getString("name");
            if (!is_null($fm->getFaction($name))) {
                $player->sendMessage($session->getMessage("commands.faction.factionExists"));
                return;
            }
            if (!ctype_alnum($name)) {
                $player->sendMessage($session->getMessage("commands.faction.invalidChars"));
                return;
            }
            if (strlen($name) < 3 || strlen($name) > 16) {
                $player->sendMessage($session->getMessage("commands.faction.nameLength"));
                return;
            }
            $fm->createFaction($player, $name);
            $session->setFaction($name);
            $player->sendMessage($session->getMessage("commands.faction.justCreated"));
        }));
    }

}