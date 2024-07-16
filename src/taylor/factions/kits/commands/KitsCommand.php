<?php namespace taylor\factions\kits\commands;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use dktapps\pmforms\ModalForm;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use taylor\factions\kits\Kit;
use taylor\factions\kits\KitsManager;
use taylor\factions\Main;
use taylor\factions\sessions\SessionManager;
use taylor\factions\utils\FormatUtils;

class KitsCommand extends BaseCommand {

    public function __construct() {
        parent::__construct(Main::getInstance(), "kit", "Equip a kit!", ["kits"]);
        $this->setPermission("commands.kits");
    }

    /*** @throws ArgumentOrderException */
    public function prepare() : void {
        $this->registerArgument(0, new RawStringArgument("kit", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void {
        $session = SessionManager::getInstance()->getSession($sender);
        if (!$sender instanceof Player) {
            $sender->sendMessage($session->getMessage("commands.mustBeInGame"));
            return;
        }
        $km = KitsManager::getInstance();
        if (isset($args["kit"])) {
            if (is_null($kit = $km->getKit($args["kit"]))) {
                $sender->sendMessage($session->getMessage("commands.kits.cantfindkit"));
                return;
            }
            if (($coolDown = $session->getRemainingKitCoolDown($kit->getName(), $kit->getCoolDown())) > 0) {
                $sender->sendMessage($session->getMessage("module.kits.stillOnCoolDown", [["{time}"], [FormatUtils::numberToHumanReadable($coolDown)]]));
                return;
            }
            if ($kit->getPermission() !== "NO_PERMISSION" && !$sender->hasPermission($kit->getPermission())) {
                $sender->sendMessage($session->getMessage("commands.kits.noPermission"));
                return;
            }
            $kit->equip($sender);
            return;
        }
        $sender->sendForm(new MenuForm(
            "Kits",
            "Choose a category.",
            array_merge(array_map(fn(string $option) => new MenuOption($option . "\nTap to view."), KitsManager::VALID_GROUPS), [FormatUtils::getCloseButton()]),
            function(Player $player, int $selectedOption) use($km, $session) : void{
                if (is_null($group = KitsManager::VALID_GROUPS[$selectedOption] ?? null)) {
                    return;
                }
                $saveForm = null;
                $player->sendForm($saveForm = new MenuForm(
                    $group,
                    "Select a kit to equip.",
                    array_merge(array_map(fn(Kit $kit) => new MenuOption(TextFormat::colorize($kit->getFancyName() . "&r\n" . $kit->getFormSubTitle($player))), $kitsOfGroup = $km->getKitsOfGroup($group)), [FormatUtils::getCloseButton()]),
                    function(Player $player, int $selectedOption) use ($kitsOfGroup, &$saveForm, $session) : void {
                        if (is_null($kit = $kitsOfGroup[$selectedOption] ?? null)) {
                            return;
                        }
                        if ($kit->getPermission() !== "NO_PERMISSION" && !$player->hasPermission($kit->getPermission())) {
                            $player->sendForm(new ModalForm(
                                "No Permission",
                                "You do not have permission to use this kit.",
                                function(Player $player, bool $choice) use ($saveForm): void {
                                    if ($choice) {
                                        $player->sendForm($saveForm);
                                    }
                                },
                                "Return To Menu",
                                "Close Menu"
                            ));
                            return;
                        }
                        if (($remaining = $session->getRemainingKitCoolDown($kit->getName(), $kit->getCoolDown())) > 0) {
                            $player->sendForm(new ModalForm(
                                "On Cool Down",
                                join("\n", [
                                    "Your are still on cool down for this kit.",
                                    "Your cool down is: " . FormatUtils::numberToHumanReadable($remaining) . "."
                                ]),
                                function(Player $player, bool $choice) use ($saveForm): void {
                                    if ($choice) {
                                        $player->sendForm($saveForm);
                                    }
                                },
                                "Return To Menu",
                                "Close Menu"
                            ));
                            return;
                        }
                        $kit->equip($player);
                    }
                ));
            },
        ));
    }

}