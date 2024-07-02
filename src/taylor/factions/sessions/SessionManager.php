<?php namespace taylor\factions\sessions;

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

    public function __construct() {
        self::setInstance($this);

        Server::getInstance()->getCommandMap()->registerAll("Factions", [
            new PayCommand(),
            new GiveCurrencyCommand(),
            new MyCommand("coins", PlayerSession::BALANCE_COINS)
        ]);
    }

    public function getSession(Player $player) : PlayerSession {
        return $this->sessions[$player->getName()];
    }

    public function createSession(Player $player) : void {
        $this->sessions[$player->getName()] = new PlayerSession($player);
    }

    public function closeSession(Player $player) : void {
        unset($this->sessions[$player->getName()]);
    }

}