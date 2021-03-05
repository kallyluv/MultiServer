<?

namespace xjustjqy\MultiServer;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Loader extends PluginBase {

  /** @var self */
  private static $instance;
  /** @var SettingsManager */
  private static $settings_manager;

  public function onEnable() {
    self::$instance = $this;
    self::$settings_manager = new SettingsManager();
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

  public static function getSettings() : ?Config {
    return self::$settings_manager->fetchConfig();
  }

}
