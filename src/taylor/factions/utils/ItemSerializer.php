<?php
declare(strict_types=1);

namespace taylor\factions\utils;

use Exception;
use JetBrains\PhpStorm\ArrayShape;
use pocketmine\color\Color;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\item\Tool;
use pocketmine\item\VanillaItems;
use pocketmine\lang\Translatable;
use pocketmine\nbt\JsonNbtParser;
use pocketmine\nbt\LittleEndianNbtSerializer;
use pocketmine\nbt\TreeRoot;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

// This is not my code - Taylor

final class ItemSerializer {

    /**
     * @param Item $item
     * @return array
     */
    #[ArrayShape(["item" => "string", "count" => "int", "nbt_json" => "string", "enchantments" => "string[]", "color" => "int[]", "damage" => "mixed", "lore" => "string[]", "customName" => "string"])]
    public static function serializeItem(Item $item): array {
        $namespaceId = "air";
        (function () use (&$namespaceId, $item): void {
            $namespaceId = array_key_first($this->reverseMap[$item->getStateId()]);
        })->call(StringToItemParser::getInstance());

        $data = [
            "item" => $namespaceId,
            "count" => $item->getCount()
        ];

        if ($item->hasCustomName()) {
            $data["customName"] = $item->getName();
        }
        if (!empty($item->getLore())) {
            $data["lore"] = $item->getLore();
        }
        if ($item instanceof Tool) {
            $data["damage"] = $item->getDamage();
        }
        if ($item instanceof Armor) {
            if (($color = $item->getCustomColor()) !== null) {
                $data["color"] = [
                    $color->getR(),
                    $color->getG(),
                    $color->getB(),
                    $color->getA()
                ];
            }
        }
        if ($item->hasEnchantments()) {
            $data["enchantments"] = [];

            foreach ($item->getEnchantments() as $enchantment) {
                $name = $enchantment->getType()->getName();
                if ($name instanceof Translatable) {
                    $name = Server::getInstance()->getLanguage()->translate($name);
                }

                $name = mb_strtolower(str_replace(" ", "_", $name));

                $data["enchantments"][] = $name . " " . $enchantment->getLevel();
            }
        }

        if ($item->hasNamedTag()) {
            $var = new LittleEndianNbtSerializer();

            $data["nbt_b64"] = base64_encode($var->write(
                new TreeRoot($item->getNamedTag())
            ));
        }

        return $data;
    }

    /**
     * @param array $data
     * @return Item
     */
    public static function parseItem(array $data): Item {
        if (!isset($data["item"]) or ($data["item"] === "air")) {
            return VanillaItems::AIR();
        }

        try {
            $item = StringToItemParser::getInstance()->parse($data["item"]);
            if ($item == null) {
                return VanillaItems::AIR();
            }

            if (isset($data["count"])) {
                if (is_string($data["count"])) {
                    $item->setCount(eval($data["count"]));
                } else if (is_int($data["count"])) {
                    $item->setCount((int)$data["count"]);
                }

                if ($item->getCount() === 0) { // null item
                    return VanillaItems::AIR();
                }
            }

            if ($item instanceof Tool) {
                $item->setDamage($data["damage"] ?? 0);
            }
            if ($item instanceof Armor) {
                $rgb = $data["color"] ?? null;

                if ($rgb !== null) {
                    $item->setCustomColor(new Color($rgb[0], $rgb[1], $rgb[2], $rgb[3] ?? 255));
                }
            }
            if (isset($data["customName"])) {
                $item->setCustomName(
                    TextFormat::colorize($data["customName"])
                );
            }
            if (isset($data["lore"])) {
                $lore = [];
                foreach ($data["lore"] as $line) {
                    $lore[] = TextFormat::colorize($line);
                }

                $item->setLore($lore);
            }
            if (isset($data["enchantments"])) {
                foreach ($data["enchantments"] as $str) {
                    $en = explode(" ", $str);

                    try {
                        $enchantment = StringToEnchantmentParser::getInstance()->parse($en[0]) ?? EnchantmentIdMap::getInstance()->fromId((int)$en[0]);

                        if ($enchantment !== null) {
                            $item->addEnchantment(new EnchantmentInstance($enchantment, (int)$en[1]));
                        }
                    } catch (Exception) {
                        // ignore
                    }
                }
            }
            if (isset($data["nbt_json"])) {
                try {
                    $nbt = JsonNbtParser::parseJson($data["nbt_json"]);
                    $root = $item->getNamedTag();

                    foreach ($nbt->getValue() as $name => $tag) {
                        $root->setTag($name, $tag);
                    }

                    $item->setNamedTag($root);
                } catch (Exception) {
                    // nothing to do here, ignore again
                }
            }
            if (isset($data["nbt_b64"])) {
                try {
                    $var = new LittleEndianNbtSerializer();

                    $root = $item->getNamedTag();
                    $nbt = $var->read(base64_decode($data["nbt_b64"]))->mustGetCompoundTag();

                    foreach ($nbt->getValue() as $name => $tag) {
                        $root->setTag($name, $tag);
                    }

                    $item->setNamedTag($root);
                } catch (Exception) {
                    // nothing to do here, ignore again
                }
            }

            return $item;
        } catch (Exception) {
            return VanillaItems::AIR();
        }
    }
}