<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }

    protected function display($view, $data = array())
    {
        // view path
        $view_paths = array();
        $module     = $this->load->getAppModule();

        list($path, $_view) = Modules::find($view, $module, 'views/');

        // module view path
        if ($path != false) {
            $view_paths[] = $path;
        }
        // default view path
        $view_paths[] = VIEWPATH;

        // load twig template
        $config = array(
            'paths' => $view_paths,
            'cache' => APPPATH . 'cache/twig',
        );
        $this->load->library('twig', $config);

        // minify
        $this->_minify($path, $view, $this->twig->getTwig());

        // display view
        $this->twig->display($view, $data);
    }

    private function _minify($path, $view, $twig)
    {
        // minify global css/js
        $this->load->library('minify');
        $this->minify->js('global.js');
        $this->minify->css('global.css');
        $twig->addGlobal('global_css', $this->minify->deploy_css());
        $twig->addGlobal('global_js', $this->minify->deploy_js());

        // minify view css/js
        $path     = $path ?: VIEWPATH;
        $css_path = $path . 'css';
        $js_path  = $path . 'js';

        $minify_module_cfg = array(
            'css_dir' => $css_path,
            'js_dir'  => $js_path,
        );
        $minify = new Minify($minify_module_cfg);

        $css_file = "$css_path/$view.css";
        if (file_exists($css_file) && filesize($css_file)) {
            $minify->css("$view.css");
            $twig->addGlobal('view_css', $minify->deploy_css());
        }

        $js_file = "$js_path/$view.js";
        if (file_exists($js_file) && filesize($js_file)) {
            $minify->js("$view.js");
            $twig->addGlobal('view_js', $minify->deploy_js());
        }
    }
}
