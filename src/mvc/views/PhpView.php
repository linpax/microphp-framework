<?php /** MicroPhpView */

namespace Micro\Mvc\Views;

use Micro\Base\Autoload;
use Micro\Base\Exception;
use Micro\Base\KernelInjector;
use Micro\Logger\LoggerInjector;
use Micro\Web\Language;

/**
 * Class PhpView
 *
 * @author Oleg Lunegov <testuser@mail.linpax.org>
 * @link https://github.com/linpax/microphp-framework
 * @copyright Copyright (c) 2013 Oleg Lunegov
 * @license https://github.com/linpax/microphp-framework/blob/master/LICENSE
 * @package Micro
 * @subpackage Mvc/Views
 * @version 1.0
 * @since 1.0
 */
class PhpView extends View
{
    /** @var string Layout to render */
    public $layout;
    /** @var string $view View name */
    public $view;
    /** @var string $path Path to view */
    public $path;
    /** @var string $data Return data */
    public $data = '';


    /**
     * Render partial
     *
     * @access public
     *
     * @param string $view view name
     *
     * @return string
     * @throws Exception
     */
    public function renderPartial($view)
    {
        $lay = $this->layout;
        $wi = $this->view;

        $this->layout = null;
        $this->view = $view;
        $output = $this->render();
        $this->layout = $lay;
        $this->view = $wi;

        return $output;
    }

    /**
     * Render insert data into view
     *
     * @access protected
     *
     * @return string
     * @throws Exception
     */
    public function render()
    {
        if (!$this->view) {
            return false;
        }

        return $this->renderRawData(
            $this->data ?: $this->renderFile($this->getViewFile($this->view), $this->params)
        );
    }

    /**
     * Render raw data in layout
     *
     * @access public
     *
     * @param string $data arguments array
     *
     * @return string
     * @throws Exception
     */
    public function renderRawData($data = '')
    {
        $layoutPath = null;
        if ($this->layout && (!$layoutPath = $this->getLayoutFile((new KernelInjector)->build()->getAppDir(),
                $this->module))
        ) {
            (new LoggerInjector)->build()->send('error', 'Layout `'.$this->layout.'` not found');
        }

        if ($layoutPath) {
            $data = $this->insertStyleScripts($this->renderFile($layoutPath, ['content' => $data]));
        }

        return $data;
    }

    /**
     * Get layout path
     *
     * @access protected
     *
     * @param string $appDir path to base dir
     * @param string $module module name
     *
     * @return string
     * @throws Exception
     */
    protected function getLayoutFile($appDir, $module)
    {
        if ($module) {
            $module = strtolower(str_replace('\\', '/', $module));
            $module = substr($module, strpos($module, '/') + 1);
            $module = substr($module, 0, strrpos($module, '/'));
        }

        $layout = $appDir.'/'.($module ? $module.'/' : $module);
        $afterPath = 'views/layouts/'.ucfirst($this->layout).'.php';

        if (file_exists($layout.$afterPath)) {
            return $layout.$afterPath;
        }

        if (file_exists($appDir.'/'.$afterPath)) {
            return $appDir.'/'.$afterPath;
        }

        return false;
    }

    /**
     * Render file by path
     *
     * @access protected
     *
     * @param string $fileName file name
     * @param array $data arguments array
     *
     * @return string
     * @throws Exception widget not declared
     */
    protected function renderFile($fileName, array $data = [])
    {
        /** @noinspection OnlyWritesOnParameterInspection */
        /** @noinspection PhpUnusedLocalVariableInspection */
        $lang = new Language($fileName);
        extract($data, EXTR_PREFIX_SAME || EXTR_REFS, 'data');
        ob_start();

        /** @noinspection PhpIncludeInspection */
        include str_replace('\\', '/', $fileName);

        if (!empty($GLOBALS['widgetStack'])) {
            throw new Exception(count($GLOBALS['widgetStack']).' widgets not endings.');
        }

        return ob_get_clean();
    }

    /**
     * Get view file
     *
     * @access private
     *
     * @param string $view view file name
     *
     * @return string
     * @throws Exception
     */
    private function getViewFile($view)
    {
        $calledClass = $this->path;

        // Calculate path to view
        if (0 === strpos($calledClass, 'App')) {
            $path = (new KernelInjector)->build()->getAppDir();
        } else {
            $path = Autoload::getAlias('Micro');
        }

        $cl = strtolower(dirname(str_replace('\\', '/', $calledClass)));
        $cl = substr($cl, strpos($cl, '/'));

        if ($this->asWidget) {
            $path .= $cl.'/views/'.$view.'.php';
        } else {
            $className = str_replace('controller', '',
                strtolower(basename(str_replace('\\', '/', '/'.$this->path))));
            $path .= dirname($cl).'/views/'.$className.'/'.$view.'.php';
        }

        $path = str_replace('//', '/', $path);

        if (!file_exists($path)) {
            throw new Exception('View path `'.$path.'` not exists.');
        }

        return $path;
    }
}
