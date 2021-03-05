<?php

namespace xjustjqy\MultiServer\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Server;
use pocketmine\level\Position;
use xjustjqy\MultiServer\Loader as API;

class TransferCommand extends Command {

    public function __construct() {
        parent::__construct("transfer", "Transfer between servers!", "/transfer <name>", []);
        $this->setDescription("Transfer between servers!");
        $this->setPermission("transfer");
    }

    public function execute(CommandSender $sender, string $label, array $args) : bool {
        if(count($args) < 1) return false;
        $server = API::getInstance()->getServerByName($args[0]);
        if($server !== null) {
            $level = $server->getDefaultWorld();
            if($level === null) {
                try {
                $level = ($server->getLevels())[0];
                } catch (Exception $e) {
                    $level = Server::getInstance()->getDefaultLevel();
                }
            }
            $sender->teleport($level->getSafeSpawn());
            $sender->sendMessage("Sent to server: " . $server->getName());
        }else{
            $sender->sendMessage("That server doesn't exist!");
        }
        return true;
    }

}