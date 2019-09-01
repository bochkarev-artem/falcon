<?php
namespace AppBundle\Service\Sape;

/**
 * Глобальные флаги
 */
class SAPE_globals
{
    protected function _get_toggle_flag($name, $toggle = false)
    {

        static $flags = array();

        if (!isset($flags[$name])) {
            $flags[$name] = false;
        }

        if ($toggle) {
            $flags[$name] = true;
        }

        return $flags[$name];
    }

    public function block_css_shown($toggle = false)
    {
        return $this->_get_toggle_flag('block_css_shown', $toggle);
    }

    public function block_ins_beforeall_shown($toggle = false)
    {
        return $this->_get_toggle_flag('block_ins_beforeall_shown', $toggle);
    }

    public function page_obligatory_output_shown($toggle = false)
    {
        return $this->_get_toggle_flag('page_obligatory_output_shown', $toggle);
    }
}
