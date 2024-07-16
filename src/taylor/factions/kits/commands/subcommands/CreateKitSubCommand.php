<?php namespace taylor\factions\kits\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use dktapps\pmforms\element\Input;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use taylor\factions\kits\KitsManager;
use taylor\factions\Main;
use taylor\factions\sessions\SessionManager;

class CreateKitSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "createkit", "Create a kit!");
    }

    public function prepare() : void {

    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $session = SessionManager::getInstance()->getSession($sender);
        if (!$sender instanceof Player) {
            $sender->sendMessage($session->getMessage("commands.mustBeInGame"));
            return;
        }
        $km = KitsManager::getInstance();
        $sender->sendForm(new CustomForm(
            "Kit Creation",
            [
                new Input("name", "Kit Name", "cool kit name"),
                new Input("fancyName", "Fancy Kit Name", "&r&l&5EPIC KIT"),
                new Input("kitPermission", "Kit Permission (keep blank for none)"),
                new Input("kitCoolDown", "Kit CoolDown (seconds)", "86400 (one day)"),
                new Dropdown("kitGroup", "Kit Group", KitsManager::VALID_GROUPS)
            ],
            function(Player $player, CustomFormResponse $data) use ($km, $session) : void {
                $name = $data->getString("name");
                if (!is_null($km->getKit($name))) {
                    $player->sendMessage($session->getMessage("command.kitsmgr.kitAlreadyExists"));
                    return;
                }
                $perm = $data->getString("kitPermission");
                if ($perm == "") {
                    $perm = "NO_PERMISSION";
                }
                $km->insertKit(
                    $name,
                    $data->getString("fancyName"),
                    $perm,
                    (int)$data->getString("kitCoolDown"),
                    KitsManager::VALID_GROUPS[$data->getInt("kitGroup")]
                );
                $player->sendMessage($session->getMessage("command.kitsmgr.kitCreated"));
            }
        ));
    }

}