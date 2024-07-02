<?php namespace taylor\factions\sessions;

use pocketmine\player\Player;
use taylor\factions\utils\LanguageManager;

class PlayerSession {

    public const BALANCE_COINS = 0;
    public const BALANCE_MOBCOINS = 1;
    public const BALANCE_EXPERIENCE = 2;
    public const BALANCE_TO_STRING = [
        self::BALANCE_COINS => "Coins",
        self::BALANCE_MOBCOINS => "Mob Coins",
        self::BALANCE_EXPERIENCE => "Experience"
    ];

    private Player $player;

    private array $balances;

    private string $language;

    public function __construct(Player $player) {
        $this->player = $player;
        $this->balances = [0, 0, 0];
        $this->language = "en";
    }

    public function close() : void {

    }

    public function addToBalance(int $amount, int $type) : void {
        $this->balances[$type] += $amount;
    }

    public function takeFromBalance(int $amount, int $type) : void {
        $this->balances[$type] -= $amount;
    }

    public function getBalance(int $type) : int {
        return $this->balances[$type];
    }

    public function getMessage(string $name, array $replace = [[], []]) : string {
        return LanguageManager::getInstance()->getTranslation($this->language, $name, $replace);
    }

    public function getPlayer() : Player {
        return $this->player;
    }

}