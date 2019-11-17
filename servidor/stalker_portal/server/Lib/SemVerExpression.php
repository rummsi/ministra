<?php

namespace Ministra\Lib;

class SemVerExpression
{
    protected static $global_single_version = '(([0-9]+)(\\.([0-9]+)(\\.([0-9]+)(-([0-9]+))?(-?([a-zA-Z-+][a-zA-Z0-9\\.\\-:]*)?)?)?)?)';
    protected static $global_single_xrange = '(([0-9]+|[xX*])(\\.([0-9]+|[xX*])(\\.([0-9]+|[xX*])(-([0-9]+))?(-?([a-zA-Z-+][a-zA-Z0-9\\.\\-:]*)?)?)?)?)';
    protected static $global_single_comparator = '([<>]=?)?\\s*';
    protected static $global_single_spermy = '(~?)>?\\s*';
    protected static $global_single_caret = '(\\^?)>?\\s*';
    protected static $range_mask = '%1$s\\s+-\\s+%1$s';
    protected static $regexp_mask = '/%s/';
    protected static $dirty_regexp_mask = '/^[v= ]*%s$/';
    private $chunks = array();
    public function __construct($versions)
    {
        $versions = \preg_replace(\sprintf(self::$dirty_regexp_mask, self::$global_single_comparator . '(\\s+-\\s+)?' . self::$global_single_xrange), '$1$2$3', $versions);
        $versions = \preg_replace('/\\s+/', ' ', $versions);
        $versions = \str_replace(['*', 'X'], 'x', $versions);
        if (\strstr($versions, ' - ')) {
            $versions = self::rangesToComparators($versions);
        }
        if (\strstr($versions, '~')) {
            $versions = self::spermiesToComparators($versions);
        }
        if (\strstr($versions, '^')) {
            $versions = self::caretToComparators($versions);
        }
        if (\strstr($versions, 'x') && (\strstr($versions, '<') || \strstr($versions, '>'))) {
            $versions = self::compAndxRangesToComparators($versions);
        }
        if (\strstr($versions, 'x')) {
            $versions = self::xRangesToComparators($versions);
        }
        $or = \explode('||', $versions);
        foreach ($or as &$orchunk) {
            $and = \explode(' ', \trim($orchunk));
            foreach ($and as $order => &$achunk) {
                $achunk = self::standardizeSingleComparator($achunk);
                if (\strstr($achunk, ' ')) {
                    $pieces = \explode(' ', $achunk);
                    unset($and[$order]);
                    $and = \array_merge($and, $pieces);
                }
            }
            $orchunk = $and;
        }
        $this->chunks = $or;
    }
    protected static function rangesToComparators($range)
    {
        $range_expression = \sprintf(self::$range_mask, self::$global_single_version);
        $expression = \sprintf(self::$regexp_mask, $range_expression);
        if (!\preg_match($expression, $range)) {
            throw new \Ministra\Lib\SemVerException('Invalid range given', $range);
        }
        $versions = \preg_replace($expression, '>=$1 <=$11', $range);
        $versions = self::standardizeMultipleComparators($versions);
        return $versions;
    }
    protected static function standardizeMultipleComparators($versions)
    {
        $versions = \preg_replace('/' . self::$global_single_comparator . self::$global_single_xrange . '/', '$1$2', $versions);
        $versions = \preg_replace('/\\s+/', ' ', $versions);
        $or = \explode('||', $versions);
        foreach ($or as &$orchunk) {
            $orchunk = \trim($orchunk);
            $and = \explode(' ', $orchunk);
            foreach ($and as &$achunk) {
                $achunk = self::standardizeSingleComparator($achunk);
            }
            $orchunk = \implode(' ', $and);
        }
        $versions = \implode('||', $or);
        return $versions;
    }
    protected static function standardizeSingleComparator($version)
    {
        $expression = \sprintf(self::$regexp_mask, self::$global_single_comparator . self::$global_single_version);
        if (!\preg_match($expression, $version, $matches)) {
            throw new \Ministra\Lib\SemVerException('Invalid version string given', $version);
        }
        $comparators = $matches[1];
        $version = $matches[2];
        $hasComparators = true;
        if ($comparators === '') {
            $hasComparators = false;
        }
        $version = self::standardize($version, $hasComparators);
        return $comparators . $version;
    }
    public static function standardize($version, $padZero = false)
    {
        $expression = \sprintf(self::$dirty_regexp_mask, self::$global_single_version);
        if (!\preg_match($expression, $version, $matches)) {
            throw new \Ministra\Lib\SemVerException('Invalid version string given', $version);
        }
        if ($padZero) {
            self::matchesToVersionParts($matches, $major, $minor, $patch, $build, $prtag, null);
            if ($build === '') {
                $build = null;
            }
            if ($prtag === '') {
                $prtag = null;
            }
            return self::constructVersionFromParts(false, $major, $minor, $patch, $build, $prtag);
        }
        self::matchesToVersionParts($matches, $major, $minor, $patch, $build, $prtag, 'x');
        if ($build === '') {
            $build = null;
        }
        if ($prtag === '') {
            $prtag = null;
        }
        $version = self::constructVersionFromParts(false, $major, $minor, $patch, $build, $prtag);
        return self::xRangesToComparators($version);
    }
    protected static function matchesToVersionParts($matches, &$major, &$minor, &$patch, &$build, &$prtag, $default = 0, $offset = 2)
    {
        $major = $minor = $patch = $default;
        $build = '';
        $prtag = '';
        switch (\count($matches)) {
            default:
            case $offset + 8:
                $prtag = $matches[$offset + 7];
            case $offset + 7:
                $build = $matches[$offset + 6];
            case $offset + 6:
            case $offset + 5:
                $patch = $matches[$offset + 4];
            case $offset + 4:
            case $offset + 3:
                $minor = $matches[$offset + 2];
            case $offset + 2:
            case $offset + 1:
                $major = $matches[$offset];
            case $offset:
            case 0:
        }
        if (\is_numeric($build) && \strpos($build, '0') !== 0) {
            $build = (int) $build;
        }
        if (\is_numeric($patch) && \strpos($patch, '0') !== 0) {
            $patch = (int) $patch;
        }
        if (\is_numeric($minor) && \strpos($minor, '0') !== 0) {
            $minor = (int) $minor;
        }
        if (\is_numeric($major) && \strpos($major, '0') !== 0) {
            $major = (int) $major;
        }
    }
    protected static function constructVersionFromParts($padZero = true, $ma = null, $mi = null, $p = null, $b = null, $t = null)
    {
        if ($padZero) {
            if ($ma === null) {
                return '0.0.0';
            }
            if ($mi === null) {
                return $ma . '.0.0';
            }
            if ($p === null) {
                return $ma . '.' . $mi . '.0';
            }
            if ($b === null && $t === null) {
                return $ma . '.' . $mi . '.' . $p;
            }
            if ($b !== null && $t === null) {
                return $ma . '.' . $mi . '.' . $p . '-' . $b;
            }
            if ($b === null && $t !== null) {
                return $ma . '.' . $mi . '.' . $p . $t;
            }
            if ($b !== null && $t !== null) {
                return $ma . '.' . $mi . '.' . $p . '-' . $b . $t;
            }
        } else {
            if ($ma === null) {
                return '';
            }
            if ($mi === null) {
                return $ma . '';
            }
            if ($p === null) {
                return $ma . '.' . $mi . '';
            }
            if ($b === null && $t === null) {
                return $ma . '.' . $mi . '.' . $p;
            }
            if ($b !== null && $t === null) {
                return $ma . '.' . $mi . '.' . $p . '-' . $b;
            }
            if ($b === null && $t !== null) {
                return $ma . '.' . $mi . '.' . $p . $t;
            }
            if ($b !== null && $t !== null) {
                return $ma . '.' . $mi . '.' . $p . '-' . $b . $t;
            }
        }
    }
    protected static function xRangesToComparators($ranges)
    {
        $expression = \sprintf(self::$regexp_mask, self::$global_single_xrange);
        return \preg_replace_callback($expression, ['self', 'xRangesToComparatorsCallback'], $ranges);
    }
    protected static function spermiesToComparators($spermies)
    {
        $expression = \sprintf(self::$regexp_mask, self::$global_single_spermy . self::$global_single_xrange);
        return \preg_replace_callback($expression, ['self', 'spermiesToComparatorsCallback'], $spermies);
    }
    protected static function caretToComparators($caret)
    {
        $expression = \sprintf(self::$regexp_mask, self::$global_single_caret . self::$global_single_xrange);
        return \preg_replace_callback($expression, ['self', 'caretToComparatorsCallback'], $caret);
    }
    private static function compAndxRangesToComparators($versions)
    {
        $regex = \sprintf(self::$regexp_mask, self::$global_single_comparator . self::$global_single_xrange);
        return \preg_replace_callback($regex, ['self', 'compAndxRangesToComparatorsCallback'], $versions);
    }
    public static function standarize($version, $padZero = false)
    {
        return self::standardize($version, $padZero);
    }
    private static function xRangesToComparatorsCallback($matches)
    {
        self::matchesToVersionParts($matches, $major, $minor, $patch, $build, $prtag, 'x');
        if ($build !== '') {
            $build = '-' . $build;
        }
        if ($major === 'x') {
            return '>=0';
        }
        if ($minor === 'x') {
            return '>=' . $major . ' <' . ($major + 1) . '.0.0-';
        }
        if ($patch === 'x') {
            return '>=' . $major . '.' . $minor . ' <' . $major . '.' . ($minor + 1) . '.0-';
        }
        return $major . '.' . $minor . '.' . $patch . $build . $prtag;
    }
    private static function caretToComparatorsCallback($matches)
    {
        self::matchesToVersionParts($matches, $major, $minor, $patch, $build, $prtag, 'x', 3);
        if ($build !== '') {
            $build = '-' . $build;
        }
        if ($major === 'x') {
            return '>=0';
        }
        if ($minor === 'x') {
            return '>=' . $major . ' <' . ($major + 1) . '.0.0-';
        }
        if ($patch === 'x') {
            return '>=' . $major . '.' . $minor . ' <' . $major . '.' . ($minor + 1) . '.0-';
        }
        return '>=' . $major . '.' . $minor . '.' . $patch . $build . $prtag . ' <' . ($major >= 1 ? $major + 1 : 0) . '.' . ($major == 0 && $minor != 0 ? $minor + 1 : 0) . '.' . ($major == 0 && $minor == 0 ? $patch + 1 : 0) . '-';
    }
    private static function spermiesToComparatorsCallback($matches)
    {
        self::matchesToVersionParts($matches, $major, $minor, $patch, $build, $prtag, 'x', 3);
        if ($build !== '') {
            $build = '-' . $build;
        }
        if ($major === 'x') {
            return '>=0';
        }
        if ($minor === 'x') {
            return '>=' . $major . ' <' . ($major + 1) . '.0.0-';
        }
        if ($patch === 'x') {
            return '>=' . $major . '.' . $minor . ' <' . $major . '.' . ($minor + 1) . '.0-';
        }
        return '>=' . $major . '.' . $minor . '.' . $patch . $build . $prtag . ' <' . $major . '.' . ($minor + 1) . '.0-';
    }
    private static function compAndxRangesToComparatorsCallback($matches)
    {
        $comparators = $matches[1];
        self::matchesToVersionParts($matches, $major, $minor, $patch, $build, $prtag, 'x', 3);
        if ($comparators[0] === '<') {
            if ($major === 'x') {
                return $comparators . '0';
            }
            if ($minor === 'x') {
                return $comparators . $major . '.0';
            }
            if ($patch === 'x') {
                return $comparators . $major . '.' . $minor . '.0';
            }
            return $comparators . self::constructVersionFromParts(false, $major, $minor, $patch, $build, $prtag);
        } elseif ($comparators[0] === '>') {
            return $comparators . self::constructVersionFromParts(false, $major === 'x' ? 0 : $major, $minor === 'x' ? 0 : $minor, $patch === 'x' ? 0 : $patch, $build, $prtag);
        }
    }
    public function satisfiedBy(\Ministra\Lib\SemVer $version)
    {
        $version1 = $version->getVersion();
        $expression = \sprintf(self::$regexp_mask, self::$global_single_comparator . self::$global_single_version);
        $ok = false;
        foreach ($this->chunks as $orblocks) {
            foreach ($orblocks as $ablocks) {
                $matches = [];
                \preg_match($expression, $ablocks, $matches);
                $comparators = $matches[1];
                $version2 = $matches[2];
                if ($comparators === '') {
                    $comparators = '==';
                }
                if (!\Ministra\Lib\SemVer::cmp($version1, $comparators, $version2)) {
                    $ok = false;
                    break;
                }
                $ok = true;
            }
            if ($ok) {
                return true;
            }
        }
        return false;
    }
    public function __toString()
    {
        return $this->getString();
    }
    public function getString()
    {
        $or = $this->chunks;
        foreach ($or as &$orchunk) {
            $orchunk = \implode(' ', $orchunk);
        }
        return \implode('||', $or);
    }
    public function validRange()
    {
        return $this->getString();
    }
    public function maxSatisfying($versions)
    {
        if (!\is_array($versions)) {
            $versions = [$versions];
        }
        \usort($versions, __NAMESPACE__ . '\\version::rcompare');
        foreach ($versions as $version) {
            try {
                if (!\is_a($version, 'SemVer')) {
                    $version = new \Ministra\Lib\SemVer($version);
                }
            } catch (\Ministra\Lib\SemVerException $e) {
                continue;
            }
            if ($version->satisfies($this)) {
                return $version;
            }
        }
    }
}
