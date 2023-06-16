<?php

namespace MauticPlugin\CaWebexBundle\Form\Type;

use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\CaWebexBundle\Exception\ConfigurationException;
use MauticPlugin\CaWebexBundle\Integration\WebexIntegration;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MeetingsListType extends AbstractType
{
    private IntegrationHelper $integrationHelper;

    public function __construct(IntegrationHelper $integrationHelper)
    {
        $this->integrationHelper = $integrationHelper;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        /** @var WebexIntegration $integration */
        $integration = $this->integrationHelper->getIntegrationObject('Webex');
        if (!$integration || !$integration->getIntegrationSettings()->getIsPublished()) {
            throw new ConfigurationException();
        }

        $api = $integration->getApi();

        $resolver->setDefaults([
            'choices' => function (Options $options) use ($api) {
                $response = $api->getFutureMeetings();
                $choices = [];
                foreach ($response['items'] as $meetings) {
                    $choices[$meetings['title']] = $meetings['id'];
                }

                return $choices;
            },
            'label'             => 'cawebex.form.label.meetings_list',
            'label_attr'        => ['class' => 'control-label'],
            'multiple'          => false,
            'required'          => false,
            'return_entity'     => true,
        ]);
    }


    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'meeting';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

}