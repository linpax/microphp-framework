<?php /** MicroAsset */

namespace Micro\Web;

use Micro\Base\Autoload;
use Micro\Base\Exception;
use Micro\Base\InjectorInterface;
use Micro\Base\KernelInjector;
use Micro\File\FileHelper;
use Micro\Mvc\Views\IView;

/**
 * Asset class file.
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Web
 * @version 1.0
 * @since 1.0
 */
class Asset
{
    /** @var string $sourcePath Full-path to source asset dir */
    public $sourcePath;

    /** @var bool $isHead Is a publish into head block */
    public $isHead = true;
    /** @var array $js JavaScript files links */
    public $js = [];
    /** @var array $css CSS files links */
    public $css = [];
    /** @var array $required Required assets */
    public $required = [];
    /** @var array $excludes Excludes extensions */
    public $excludes = [];

    /** @var IView $view View for install current asset */
    protected $view;
    /** @var string $hash Unique directory to publish into assets dir */
    protected $hash;
    /** @var string $publishPath Publish path */
    protected $publishPath;
    /** @var array $published Published required extends */
    private $published = [];


    /**
     * Constructor asset
     *
     * @access public
     *
     * @param IView $view
     *
     * @result void
     * @throws \Micro\Base\Exception
     */
    public function __construct(IView $view)
    {
        $this->view = $view;

        if (!$this->sourcePath) {
            $this->sourcePath = dirname(Autoload::getClassPath(get_class($this)));
        }

        $this->hash = md5($this->sourcePath);

        $this->publishPath = '/' . (($dir = (new InjectorInterface)->param('assetsDirName')) ? $dir : 'assets') . '/' . $this->hash;

        $web = (new KernelInjector)->build()->getWebDir();

        if (!file_exists($this->sourcePath)) {
            throw new Exception('Asset dir not exists: '.$this->sourcePath);
        }

        if (!is_dir($web.$this->publishPath) && (!mkdir($web.$this->publishPath, 0777) && !is_dir($web.$this->publishPath))) {
            throw new Exception('Could not access to publish dir: '.$this->publishPath);
        }

        FileHelper::recurseCopyIfEdited($this->sourcePath, $web.$this->publishPath, $this->excludes);
    }

    /**
     * Send asset into view
     *
     * @access public
     * @return void
     */
    public function publish()
    {
        foreach ($this->required AS $require) {
            if (!in_array($require, $this->published, true) && class_exists($require)) {
                $this->published[] = $require;

                /** @var Asset $require */
                $require = new $require($this->view);
                $require->publish();
            }
        }

        if ($this->js) {
            if (is_string($this->js)) {
                $this->js = [$this->js];
            }

            if (0 !== count($this->js)) {
                /** @noinspection ForeachSourceInspection */
                foreach ($this->js AS $script) {
                    $this->view->registerScriptFile($this->publishPath.$script, $this->isHead);
                }
            }
        }

        if ($this->css) {
            if (is_string($this->css)) {
                $this->css = [$this->css];
            }

            if (0 !== count($this->css)) {
                /** @noinspection ForeachSourceInspection */
                foreach ($this->css AS $style) {
                    $this->view->registerCssFile($this->publishPath.$style, $this->isHead);
                }
            }
        }
    }
}
