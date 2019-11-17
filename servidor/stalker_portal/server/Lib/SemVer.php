<?php

namespace Ministra\Lib;

use UnexpectedValueException;
class SemVer extends \Ministra\Lib\SemVerExpression
{
    private $version = '0.0.0';
    private $major = '0';
    private $minor = '0';
    private $patch = '0';
    private $build = '';
    private $prtag = '';
    public function __construct($version, $padZero = false)
    {
        parent::__construct($version);
        $version = (string) $version;
        $expression = \sprintf(parent::$dirty_regexp_mask, parent::$global_single_version);
        if (!\preg_match($expression, $version, $matches)) {
            throw new \Ministra\Lib\SemVerException('This is not a valid version');
        }
        parent::matchesToVersionParts($matches, $this->major, $this->minor, $this->patch, $this->build, $this->prtag, $padZero ? 0 : null);
        if ($this->build === '') {
            $this->build = null;
        }
        $this->version = parent::constructVersionFromParts($padZero, $this->major, $this->minor, $this->patch, $this->build, $this->prtag);
        if ($this->major === null) {
            $this->major = -1;
        }
        if ($this->minor === null) {
            $this->minor = -1;
        }
        if ($this->patch === null) {
            $this->patch = -1;
        }
        if ($this->build === null) {
            $this->build = -1;
        }
    }
    public static function cmp($v1, $cmp, $v2)
    {
        switch ($cmp) {
            case '==':
                return self::eq($v1, $v2);
            case '!=':
                return self::neq($v1, $v2);
            case '>':
                return self::gt($v1, $v2);
            case '>=':
                return self::gte($v1, $v2);
            case '<':
                return self::lt($v1, $v2);
            case '<=':
                return self::lte($v1, $v2);
            case '===':
                return $v1 === $v2;
            case '!==':
                return $v1 !== $v2;
            default:
                throw new \UnexpectedValueException('Invalid comparator');
        }
    }
    public static function eq($v1, $v2)
    {
        if (!$v1 instanceof \Ministra\Lib\SemVer) {
            $v1 = new \Ministra\Lib\SemVer($v1, true);
        }
        if (!$v2 instanceof \Ministra\Lib\SemVer) {
            $v2 = new \Ministra\Lib\SemVer($v2, true);
        }
        return $v1->getVersion() === $v2->getVersion();
    }
    public static function neq($v1, $v2)
    {
        return !self::eq($v1, $v2);
    }
    public static function gt($v1, $v2)
    {
        if (!$v1 instanceof \Ministra\Lib\SemVer) {
            $v1 = new \Ministra\Lib\SemVer($v1);
        }
        if (!$v2 instanceof \Ministra\Lib\SemVer) {
            $v2 = new \Ministra\Lib\SemVer($v2);
        }
        $ma1 = $v1->getMajor();
        $ma2 = $v2->getMajor();
        if ($ma1 < 0 && $ma2 >= 0) {
            return false;
        }
        if ($ma1 >= 0 && $ma2 < 0) {
            return true;
        }
        if ($ma1 > $ma2) {
            return true;
        }
        if ($ma1 < $ma2) {
            return false;
        }
        $mi1 = $v1->getMinor();
        $mi2 = $v2->getMinor();
        if ($mi1 < 0 && $mi2 >= 0) {
            return false;
        }
        if ($mi1 >= 0 && $mi2 < 0) {
            return true;
        }
        if ($mi1 > $mi2) {
            return true;
        }
        if ($mi1 < $mi2) {
            return false;
        }
        $p1 = $v1->getPatch();
        $p2 = $v2->getPatch();
        if ($p1 < 0 && $p2 >= 0) {
            return false;
        }
        if ($p1 >= 0 && $p2 < 0) {
            return true;
        }
        if ($p1 > $p2) {
            return true;
        }
        if ($p1 < $p2) {
            return false;
        }
        $b1 = $v1->getBuild();
        $b2 = $v2->getBuild();
        if ($b1 < 0 && $b2 >= 0) {
            return false;
        }
        if ($b1 >= 0 && $b2 < 0) {
            return true;
        }
        if ($b1 > $b2) {
            return true;
        }
        if ($b1 < $b2) {
            return false;
        }
        $t1 = $v1->getTag();
        $t2 = $v2->getTag();
        if ($t1 === $t2) {
            return false;
        }
        if ($t1 === '' && $t2 !== '') {
            return true;
        }
        if ($t1 !== '' && $t2 === '') {
            return false;
        }
        $array = [$t1, $t2];
        \natsort($array);
        return \reset($array) === $t2;
    }
    public function getMajor()
    {
        return (int) $this->major;
    }
    public function getMinor()
    {
        return (int) $this->minor;
    }
    public function getPatch()
    {
        return (int) $this->patch;
    }
    public function getBuild()
    {
        return (int) $this->build;
    }
    public function getTag()
    {
        return (string) $this->prtag;
    }
    public static function gte($v1, $v2)
    {
        return self::gt($v1, $v2) || self::eq($v1, $v2);
    }
    public static function lt($v1, $v2)
    {
        return self::gt($v2, $v1);
    }
    public static function lte($v1, $v2)
    {
        return self::lt($v1, $v2) || self::eq($v1, $v2);
    }
    public static function rcompare($v1, $v2)
    {
        return self::compare($v2, $v1);
    }
    public static function compare($v1, $v2)
    {
        if (self::eq($v1, $v2)) {
            return 0;
        }
        if (self::gt($v1, $v2)) {
            return 1;
        }
        return -1;
    }
    public static function satisfiesRange($version, $range)
    {
        if (!$version instanceof \Ministra\Lib\SemVer) {
            $version = new \Ministra\Lib\SemVer($version, true);
        }
        if (!$range instanceof \Ministra\Lib\SemVerExpression) {
            $range = new \Ministra\Lib\SemVerExpression($range);
        }
        return $version->satisfies($range);
    }
    public function satisfies(\Ministra\Lib\SemVerExpression $versions)
    {
        return $versions->satisfiedBy($this);
    }
    public function valid()
    {
        return $this->getVersion();
    }
    public function getVersion()
    {
        return (string) $this->version;
    }
    public function inc($what)
    {
        if ($what == 'major') {
            return new \Ministra\Lib\SemVer($this->major + 1 . '.0.0');
        }
        if ($what == 'minor') {
            return new \Ministra\Lib\SemVer($this->major . '.' . ($this->minor + 1) . '.0');
        }
        if ($what == 'patch') {
            return new \Ministra\Lib\SemVer($this->major . '.' . $this->minor . '.' . ($this->patch + 1));
        }
        if ($what == 'build') {
            if ($this->build == -1) {
                return new \Ministra\Lib\SemVer($this->major . '.' . $this->minor . '.' . $this->patch . '-1');
            }
            return new \Ministra\Lib\SemVer($this->major . '.' . $this->minor . '.' . $this->patch . '-' . ($this->build + 1));
        }
        throw new \Ministra\Lib\SemVerException('Invalid increment value given', $what);
    }
    public function __toString()
    {
        return $this->getVersion();
    }
}
