<?php namespace taylor\factions\sessions;

use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use taylor\factions\sessions\commands\GiveCurrencyCommand;
use taylor\factions\sessions\commands\MyCommand;
use taylor\factions\sessions\commands\PayCommand;

class SessionManager {

    use SingletonTrait;

    /** @var array<string, PlayerSession> */
    private array $sessions = [];

    /**
     * @var PlayerSession
     * This is used for sending messages to console with correct language.
     */
    private PlayerSession $consoleSession;

    public function __construct() {
        self::setInstance($this);

        $this->consoleSession = new PlayerSession(null);
        Server::getInstance()->getCommandMap()->registerAll("Factions", [
            new PayCommand(),
            new GiveCurrencyCommand(),
            new MyCommand("coins", PlayerSession::BALANCE_COINS),
            new MyCommand("mobcoins", PlayerSession::BALANCE_MOBCOINS),
            new MyCommand("exp", PlayerSession::BALANCE_EXPERIENCE)
        ]);
    }

    public function getSession(Player|CommandSender|null $player) : ?PlayerSession {
        if (is_null($player)) {
            return null;
        }
        if ($player instanceof ConsoleCommandSender) {
            return $this->consoleSession;
        }
        return $this->sessions[$player->getName()] ?? null;
    }

    public function createSession(Player $player) : void {
        $this->sessions[$player->getName()] = new PlayerSession($player);
    }

    public function closeSession(Player $player) : void {
        unset($this->sessions[$player->getName()]);
    }

}