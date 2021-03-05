<?

namespace xjustjqy\MultiServer;

use pocketmine\plugin\PluginBase;

class Loader extends PluginBase {

  /** @var self */
  private $instance;

  public function onEnable() {
    self::$instance = $this;
  }

  public static function getInstance() : self {
    return self::$instance;
  }

}
