<?php

if (!isset($app)) {
    throw new \Exception('App variable does not define');
}
use Ministra\Admin\Lib\Authentication\AccessMap;
use Ministra\Admin\Lib\EmptyTranslationExtension;
use Silex\Application;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactory;
$db = $app['db'];
$db->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
$app->boot();
$app['twig']->addExtension(new \Ministra\Admin\Lib\EmptyTranslationExtension($app['translator']));
$app->extend('form.factory', function (\Symfony\Component\Form\FormFactory $builder) {
    $builder->createNamed('text', \Symfony\Component\Form\Extension\Core\Type\TextType::class);
    $builder->createNamed('textarea', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class);
    $builder->createNamed('hidden', \Symfony\Component\Form\Extension\Core\Type\HiddenType::class);
    $builder->createNamed('choise', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class);
    $builder->createNamed('collection', \Symfony\Component\Form\Extension\Core\Type\CollectionType::class);
    $builder->createNamed('checkbox', \Symfony\Component\Form\Extension\Core\Type\CheckboxType::class);
    $builder->createNamed('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class);
    $builder->create('form', \Symfony\Component\Form\Extension\Core\Type\FormType::class);
    return $builder;
});
$app->offsetSet('access_map', function (\Silex\Application $app) {
    $token = $app['security.token_storage']->getToken();
    if (\null !== $token) {
        $user = $token->getUser();
    } else {
        $user = \null;
    }
    return new \Ministra\Admin\Lib\Authentication\AccessMap($app['db'], $user);
});
