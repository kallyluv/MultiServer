<?

namespace xjustjqy\MultiServer;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use xjustjqy\MultiServer\classmap\Server;
use xjustjqy\MultiServer\commands\TransferCommand;
use pocketmine\Server as DFServer;
use const DIRECTORY_SEPARATOR;

class Loader extends PluginBase {

  /** @var self */
  private static $instance;
  /** @var SettingsManager */
  private static $settings_manager;
  /** @var array */
  private static $servers = [];
  /** @var PlayerManager */
  private static $player_manager;

  public function onEnable() {
    self::$instance = $this;
    self::$settings_manager = new SettingsManager();
    self::$player_manager = new PlayerManager();
    $this->initServers();
    $this->initPlugins();
    $this->getServer()->getCommandMap()->registerAll("multiserver", [
      new TransferCommand()
    ]);
  }
  
  private function initServers() {
   foreach(self::$settings_manager->getServers() as $name => $server) {
     self::$servers[] = new Server($this->getServer(), $name, count(self::$servers));
   }
  }
  
  private function initPlugins() {
    foreach(self::$servers as $server) {
      (new PluginLoader(self::getServersFolder() . $server->getName() . DIRECTORY_SEPARATOR, $server, $this->getServer()->getCommandMap(), self::getServersFolder() . $server->getName() . "/plugin_data/"))->loadPlugins(); 
    }
  }
  
  public static function getServerByName(string $name) : ?Server {
    $target = null;
    foreach(self::$servers as $s) {
      if($s->getName() === $name) {
       $target = $s; 
      }
    }
    return $target;
  }
  
  public static function get(int $id) : ?Server {
   $target = null;
    foreach(self::$servers as $s) {
     if($s->getId() === $id) {
      $target = $s; 
     }
    }
    return $target;
  }

  public static function getInstance() : ?self {
    return self::$instance;
  }

  public static function getConfigFolder() : ?string {
    $arr = explode("plugins", DFServer::getInstance()->getPluginManager()->getPlugin("MultiServer")->getFile());
    $folderName = $arr[0];
    $dataFolder = $folderName . "MultiServer/";
    return $dataFolder;
  }
  
  public static function getServersFolder() : ?string {
   return self::getConfigFolder() . "servers/"; 
  }

  public static function getSettings() : ?SettingsManager {
    return self::$settings_manager;
  }

  public static function getPlayerManager() : PlayerManager {
    return self::$player_manager;
  }

}
