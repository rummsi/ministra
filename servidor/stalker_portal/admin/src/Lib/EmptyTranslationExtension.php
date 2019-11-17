<?php

namespace Ministra\Admin\Lib;

use Symfony\Bridge\Twig\Extension\TranslationExtension;
class EmptyTranslationExtension extends \Symfony\Bridge\Twig\Extension\TranslationExtension
{
    public function trans($message, array $arguments = array(), $domain = null, $locale = null)
    {
        $value = parent::trans($message, $arguments, $domain, $locale);
        if (\strlen((string) $value) === 0) {
            $fallback_locales = $this->getTranslator()->getFallbackLocales();
            foreach ($fallback_locales as $fallback_locale) {
                $value = parent::trans($message, $arguments, $domain, $fallback_locale);
                if (\strlen((string) $value) !== 0) {
                    break;
                }
            }
        }
        return $value;
    }
    public function transchoice($message, $count, array $arguments = array(), $domain = null, $locale = null)
    {
        $value = parent::transchoice($message, $count, \array_merge(['%count%' => $count], $arguments), $domain, $locale);
        if (\strlen((string) $value) === 0) {
            $fallback_locales = $this->getTranslator()->getFallbackLocales();
            foreach ($fallback_locales as $fallback_locale) {
                $value = parent::transchoice($message, $count, \array_merge(['%count%' => $count], $arguments), $domain, $fallback_locale);
                if (\strlen((string) $value) !== 0) {
                    break;
                }
            }
        }
        return $value;
    }
}
