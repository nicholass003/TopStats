<?php

declare(strict_types=1);

namespace nicholass003\topstats\libs\_8d917a7f9d1fddb3\DaPigGuy\libPiggyEconomy\providers;

use cooldogedev\BedrockEconomy\api\type\ClosureAPI;
use cooldogedev\BedrockEconomy\BedrockEconomy;
use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use cooldogedev\BedrockEconomy\currency\Currency;
use cooldogedev\BedrockEconomy\database\cache\GlobalCache;
use pocketmine\player\Player;
use pocketmine\Server;

class BedrockEconomyProvider extends EconomyProvider
{
    private ClosureAPI $api;
    private Currency $currency;

    public static function checkDependencies(): bool
    {
        $plugin = Server::getInstance()->getPluginManager()->getPlugin("BedrockEconomy");
        return $plugin !== null && version_compare($plugin->getDescription()->getVersion(), '4.0', '>=');
    }

    public function __construct()
    {
        $this->api = BedrockEconomyAPI::CLOSURE();
        $this->currency = BedrockEconomy::getInstance()->getCurrency();
    }

    public function getMonetaryUnit(): string
    {
        return $this->currency->symbol;
    }

    public function getMoney(Player $player, callable $callback): void
    {
        $entry = GlobalCache::ONLINE()->get($player->getName());
        $callback($entry ? (float)"{$entry->amount}.{$entry->decimals}" : (float)"{$this->currency->defaultAmount}.{$this->currency->defaultDecimals}");
    }

    public function giveMoney(Player $player, float $amount, ?callable $callback = null): void
    {
        $decimals = (int)(explode('.', strval($amount))[1] ?? 0);
        $this->api->add(
            $player->getXuid(),
            $player->getName(),
            (int)$amount,
            $decimals,
            fn () => $callback ? $callback(true) : null,
            fn () => $callback ? $callback(false) : null
        );
    }

    public function takeMoney(Player $player, float $amount, ?callable $callback = null): void
    {
        $decimals = (int)(explode('.', strval($amount))[1] ?? 0);
        $this->api->subtract(
            $player->getXuid(),
            $player->getName(),
            (int)$amount,
            $decimals,
            fn () => $callback ? $callback(true) : null,
            fn () => $callback ? $callback(false) : null
        );
    }

    public function setMoney(Player $player, float $amount, ?callable $callback = null): void
    {
        $decimals = (int)(explode('.', strval($amount))[1] ?? 0);
        $this->api->set(
            $player->getXuid(),
            $player->getName(),
            (int)$amount,
            $decimals,
            fn () => $callback ? $callback(true) : null,
            fn () => $callback ? $callback(false) : null
        );
    }
}