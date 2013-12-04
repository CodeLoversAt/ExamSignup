<?php
/**
 * @package rentorder
 *
 * @author Daniel Holzmann <d@velopment.at>
 * @date 06.09.13
 * @time 10:20
 */

namespace CodeLovers\UserBundle\Form\Type;


use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'label'              => 'form.email',
                'translation_domain' => 'FOSUserBundle',
                'attr'               => array(
                    'autocomplete' => 'false',
                    'autofocus'    => 'autofocus'
                )
            ))
            ->add('plainPassword', 'repeated', array(
                'type'            => 'password',
                'options'         => array(
                    'translation_domain' => 'FOSUserBundle',
                    'attr'               => array(
                        'autocomplete' => 'false',
                    )
                ),
                'first_options'   => array('label' => 'form.password'),
                'second_options'  => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('firstName', 'text', array(
                'label'              => 'form.firstName',
                'translation_domain' => 'FOSUserBundle'
            ))
            ->add('lastName', 'text', array(
                'label'              => 'form.lastName',
                'translation_domain' => 'FOSUserBundle'
            ))
            ->add('username', 'text', array(
                'label'              => 'form.username',
                'translation_domain' => 'FOSUserBundle',
                'attr'               => array(
                    'pattern' => '[Ss]{1}[0-9]{10}'
                )
            ))
        ;

    }

    public function getName()
    {
        return 'code_lovers_user_registration';
    }
}