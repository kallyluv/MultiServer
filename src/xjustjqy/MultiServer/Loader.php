<?

namespace xjustjqy\MultiServer;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Loader extends PluginBase {

  /** @var self */
  private static $instance;
  /** @var string */
  private static $dataFolder;

  public function onEnable() {
    self::$instance = $this;
    self::$dataFolder = str_replace("plugins", "", $this->getFile());
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
