<?php

namespace xjustjqy\MultiServer;

use xjustjqy\MultiServer\classmap\Player;
use xjustjqy\MultiServer\classmap\Server;
use pocketmine\Player as DFPlayer;

class PlayerManager
{

    /** @var Player[] */
    private $players;
    /** @var self */
    private static $self;

    public function __construct()
    {
        self::$self = $this;
    }

    public function register(DFPlayer $who, Server $server)
    {
        if (in_array($who->getId(), array_values($this->players))) return;
        $this->players[$who->getId()] = [
            "player" => $who,
            "server" => $server
        ];
    }

    public function unregister(DFPlayer $who)
    {
        if(!is_array($this->players)) $this->players = [];
        if (!in_array($who->getId(), array_values($this->players))) return;
        array_splice($this->players, $who->getId());
    }

    public function reregister(DFPlayer $who, Server $server)
    {
        $this->unregister($who);
        $this->register($who, $server);
    }

    public function getPlayer(DFPlayer $who): ?Player
    {
        if (!in_array($who->getId(), array_values($this->players))) return null;
        return $this->players[$who->getId()]["player"];
    }

    public static function fetch(): self
    {
        return self::$self;
    }
}
