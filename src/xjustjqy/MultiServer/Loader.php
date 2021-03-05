<?

namespace xjustjqy\MultiServer;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Loader extends PluginBase {

  /** @var self */
  private static $instance;
  /** @var string */
  private static $dataFolder;
  /** @var SettingsManager */
  private static $settings_manager;

  public function onEnable() {
    self::$instance = $this;
    $arr = explode("plugins", $this->getFile());
    $folderName = $arr[0];
    self::$dataFolder = $folderName . "settings/";
    self::$settings_manager = new SettingsManager();
  }

  public static function getInstance() : ?self {
    return self::$instance;
  }

  public static function getConfigFolder() : ?string {
    return self::dataFolder;
  }

  public static function getSettings() : ?Config {
    return self::$settings_manager->fetchConfig();
  }

}
