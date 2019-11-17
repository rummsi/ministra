<?php

namespace Ministra\Lib;

class CronForm
{
    private static $instance = null;
    private $cron_expression = '';
    private $cron_parts = array();
    private $form_data = array();
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new \Ministra\Lib\CronForm();
        }
        return self::$instance;
    }
    public function getExpression()
    {
        return $this->cron_expression;
    }
    public function getFormData()
    {
        return $this->form_data;
    }
    public function setFormData($data = array())
    {
        $this->form_data = $data;
        $this->createExpression();
        return $this;
    }
    private function createExpression()
    {
        $tmp = [];
        $repeating_interval = \array_key_exists('interval', $this->form_data) && \preg_match("/(\\d)\$/i", $this->form_data['interval'], $tmp) || \count($tmp) >= 2 ? (int) $tmp[1] : 0;
        $this->cron_parts[0] = isset($this->form_data['every_minute']) && \is_numeric($this->form_data['every_minute']) ? $this->form_data['every_minute'] : '*';
        $this->cron_parts[1] = isset($this->form_data['every_hour']) && \is_numeric($this->form_data['every_hour']) ? $this->form_data['every_hour'] : '*';
        $this->cron_parts[2] = isset($this->form_data['every_day']) && \is_numeric($this->form_data['every_day']) ? '*/' . $this->form_data['every_day'] : '*';
        if ($repeating_interval == 1 && !empty($this->form_data['month'])) {
            $this->cron_parts[3] = $this->form_data['month'];
        } elseif ($repeating_interval == 2 && !empty($this->form_data['every_month'])) {
            $this->cron_parts[3] = '*/' . $this->form_data['every_month'];
        } else {
            $this->cron_parts[3] = '*';
        }
        $this->cron_parts[4] = !empty($this->form_data['day_week']) ? \implode(',', $this->form_data['day_week']) : '*';
        $this->setExpression(\implode(' ', $this->cron_parts));
    }
    public function setExpression($expr_str = '')
    {
        $this->cron_expression = $expr_str;
        if (!empty($this->cron_expression) && $this->validateExpression()) {
            $this->fillFormData();
        } else {
            $this->cleanData();
        }
        return $this;
    }
    private function validateExpression()
    {
        if (!empty($this->cron_expression)) {
            $cronTab = new \Ministra\Lib\CronExpression($this->cron_expression, new \Cron\FieldFactory());
            $order = [5, 3, 2, 4, 1, 0];
            foreach (\array_reverse($order) as $position) {
                $this->cron_parts[$position] = $cronTab->getExpression($position);
                if (null === $this->cron_parts[$position] && $position <= 3) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
    private function fillFormData()
    {
        $this->setFormRepeatingInterval();
        $this->setFormDateType();
        $this->form_data['every_hour'] = $this->cron_parts[1];
        $this->form_data['every_minute'] = $this->cron_parts[0];
    }
    private function setFormRepeatingInterval()
    {
        $this->form_data['interval'] = 1;
        if (null !== $this->cron_parts[3]) {
            if (\is_numeric($this->cron_parts[3])) {
                $this->form_data['month'] = $this->cron_parts[3];
            } else {
                if (\count($tmp = \explode('/', $this->cron_parts[3])) == 2) {
                    $this->form_data['interval'] = 2;
                    $this->form_data['every_month'] = $tmp[1];
                } elseif (!empty($this->cron_parts[4]) && $this->cron_parts[4] != '*') {
                    $this->form_data['interval'] = 3;
                } else {
                    $this->form_data['interval'] = 4;
                }
            }
        }
    }
    private function setFormDateType()
    {
        $this->form_data['day_week'] = \explode(',', \str_replace('*', '', (string) $this->cron_parts[4]));
        $this->form_data['every_day'] = \str_replace(['*', '/'], '', (string) $this->cron_parts[2]);
        $this->form_data['date_type'] = 'day_week';
        if ($this->form_data['interval'] != 3 && !empty($this->form_data['every_day'])) {
            $this->form_data['date_type'] = 'day_number';
            unset($this->form_data['day_week']);
        } else {
            unset($this->form_data['every_day']);
        }
    }
    private function cleanData()
    {
        $this->cron_expression = '';
        $this->cron_parts = [];
        $this->form_data = [];
    }
}
