<?

namespace xjustjqy\MultiServer;

use pocketmine\Server;
use pocketmine\utils\Config;

class SettingsManager {

    public function __construct() {
      @mkdir(Loader::getConfigFolder());
    }

    public function fetchConfig() : Config {
      return new Config(Loader::getConfigFolder() . "settings.yml", Config::YAML, []);
    }

    public function getServers() : array {
      return $this->fetchConfig()->get("servers");
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
