<?php namespace taylor\factions\utils;

use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use taylor\factions\Main;

class LanguageManager {

    use SingletonTrait;

    /** @var array<string, array<string>> */
    private array $messages = ["en" => []];

    public function __construct() {
        self::setInstance($this);
        $main = Main::getInstance();
        foreach(array_keys($this->messages) as $key) {
            $main->saveResource($name = "messages_" . $key . ".yml", true);
            $this->messages[$key] = (new Config($main->getDataFolder() . $name, Config::YAML))->getAll();
        }
    }

    public function getTranslation(string $language, string $location, array $replace) : string {
        return TextFormat::colorize(str_replace($replace[0], $replace[1], $this->messages[$language][$location]));
    }

    public function getLanguages() : array {
        return array_keys($this->messages);
    }

}