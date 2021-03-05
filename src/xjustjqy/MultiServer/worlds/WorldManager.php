<?

namespace xjustjqy\MultiServer\worlds;

use pocketmine\level\Level as DFLevel;
use xjustjqy\MultiServer\classmap\Server;
use xjustjqy\MultiServer\Loader as API;

class WorldManager {
 
  /** @var Level[] */
  private static $levels = [];
  /** @var Server[] */
  private static $servers = [];
  
  public function __construct() {
    $servers = API::getInstance()->getSettings()->getServers();
    foreach($servers as $name) {
      $server = API::getInstance()->getServerByName($name);
      foreach($server["levels"] as $level) {
        self::$levels[] = $level;
      }
      self::$servers[] = $server;
    }
  }

  public static function getDefaultServer(): ?Server {
    $return = null;
    foreach (self::$servers as $server) {
      if(isset($server["default"]) && $server["default"] === true) {
        $return = $server;
      }
    }
    return $return;
  }
  
}
