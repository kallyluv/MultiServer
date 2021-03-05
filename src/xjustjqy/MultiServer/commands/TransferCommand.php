<?php

namespace xjustjqy\MultiServer\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Server as DFServer;
use pocketmine\Player as DFPlayer;
use pocketmine\level\Position;
use xjustjqy\MultiServer\Loader as API;
use xjustjqy\MultiServer\events\player\PlayerTransferEvent;
use xjustjqy\MultiServer\classmap\Server;
use xjustjqy\MultiServer\worlds\WorldManager;

class TransferCommand extends Command
{

    public function __construct()
    {
        parent::__construct("transfer", "Transfer between servers!", "/transfer <name>", []);
        $this->setDescription("Transfer between servers!");
        $this->setPermission("transfer");
    }

    public function execute(CommandSender $sender, string $label, array $args): bool
    {
        if (count($args) < 1 || !$sender instanceof DFPlayer) return false;
        $server = API::getInstance()->getServerByName($args[0]);
        if ($server !== null) {
            $level = $server->getDefaultWorld();
            if ($level === null) {
                try {
                    $level = ($server->getLevels())[0];
                } catch (Exception $e) {
                    $level = DFServer::getInstance()->getDefaultLevel();
                }
            }
        } else {
            $server = WorldManager::getDefaultServer();
            if ($server !== null) {
                $level = $server->getDefaultWorld();
                if ($level === null) {
                    try {
                        $level = ($server->getLevels())[0];
                    } catch (Exception $e) {
                        $level = DFServer::getInstance()->getDefaultLevel();
                    }
                }
            }else{
                $server = new Server(DFServer::getInstance(), "default", 9999, true);
                $level = DFServer::getInstance()->getDefaultLevel();
            }
        }
        $ev = new PlayerTransferEvent($sender, new Server(DFServer::getInstance(), "defaultServerInstance", 999, true), $server, $level);
        $ev->call();
        $ev->onRun();
        return true;
    }
}
