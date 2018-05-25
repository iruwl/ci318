<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function display($view, $data = array())
    {
        // TWIG LIBRARY
        $view_paths = array();
        $module     = $this->load->getAppModule();

        list($path, $_view) = Modules::find($view, $module, 'views/');

        // module view path
        if ($path != false) {
            $view_paths[] = $path;
        }
        // default view path
        $view_paths[] = VIEWPATH;

        $config = array(
            'paths' => $view_paths,
        );
        $this->load->library('twig', $config);
        $this->twig->display($view, $data);
    }
}
