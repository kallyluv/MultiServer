<?

namespace xjustjqy\MultiServer;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use xjustjqy\MultiServer\classmap\Server;
use const DIRECTORY_SEPARATOR;

class Loader extends PluginBase {

  /** @var self */
  private static $instance;
  /** @var SettingsManager */
  private static $settings_manager;
  /** @var array */
  private static $servers = [];

  public function onEnable() {
    self::$instance = $this;
    self::$settings_manager = new SettingsManager();
    $this->initServers();
    $this->initPlugins();
  }
  
  private function initServers() {
   foreach(self::$settings_manager->getServers() as $server) {
     self::$servers[] = new Server($this->getServer(), $server["name"], count(self::$servers));
   }
  }
  
  private function initPlugins() {
    foreach(self::$servers as $server) {
      (new PluginLoader(self::getServersFolder() . $server->getName() . DIRECTORY_SEPARATOR, $this->getServer()->getCommandMap(), self::getServersFolder() . $server->getName() . "/plugin_data/"))->loadPlugins(); 
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
  
  public static function getServer(int $id) : ?Server {
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
    $arr = explode("plugins", Server::getInstance()->getPluginManager()->getPlugin("MultiServer")->getFile());
    $folderName = $arr[0];
    $dataFolder = $folderName . "MultiServer/";
    return $dataFolder;
  }
  
  public static function getServersFolder() : ?string {
   return self::getConfigFolder() . "servers/"; 
  }

  public static function getSettings() : ?Config {
    return self::$settings_manager->fetchConfig();
  }

}
