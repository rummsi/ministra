<?php

namespace Ministra\Admin\Lib;

class Theme extends \Ministra\Lib\Theme
{
    private $ext_variables = array();
    private $valid_values = array('logoAlign' => array('left', 'center', 'right'));
    public function __construct($alias)
    {
        parent::__construct($alias);
    }
    public function getThemeVar($var_name, $default = false)
    {
        if (empty($this->ext_variables)) {
            $this->ext_variables = $this->getVariables();
        }
        if (\array_key_exists($var_name, $this->ext_variables) && \is_array($this->ext_variables[$var_name]) && \array_key_exists('value', $this->ext_variables[$var_name])) {
            $this->ext_variables[$var_name] = $this->ext_variables[$var_name]['value'];
        }
        if (!\array_key_exists($var_name, $this->ext_variables) || \array_key_exists($var_name, $this->valid_values) && !\in_array($this->ext_variables[$var_name], $this->valid_values[$var_name])) {
            $this->ext_variables[$var_name] = $default;
        }
        return $this->ext_variables[$var_name];
    }
}
