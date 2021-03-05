<?

namespace xjustjqy\MultiServer;

use pocketmine\Server;
use pocketmine\utils\Config;

class SettingsManager {

    public function __construct() {
      @mkdir(Loader::getConfigFolder());
      @mkdir(Loader::getConfigFolder() . "settings/");
    }

    public function fetchConfig() : Config {
      @mkdir(Loader::getConfigFolder());
      @mkdir(Loader::getConfigFolder() . "settings/");
      return new Config(Loader::getConfigFolder() . "settings/settings.yml", Config::YAML, [
        "servers" => [
          "exampleServer" => [
            "levels" => [
              [
                "name" => "example",
                "seed" => 0,
                "generator_type" => "default",
                "options" => []
              ]
            ]
          ]
        ]
      ]);
    }

    public function getServers() : array {
      return ($this->fetchConfig()->get("servers") !== false) ? $this->fetchConfig()->get("servers") : [];
    }

    public function get(string $which) {
      return $this->fetchConfig()->get($which);
    }

    public function set(string $which, $value) {
      return $this->fetchConfig()->set($which, $value);
    }

    public function fetch() : array {
      return ($this->fetchConfig())->getAll();
    }

 }
