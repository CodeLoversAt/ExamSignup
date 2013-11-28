<?php
/**
 * @package examSignup
 *
 * @author Daniel Holzmann <d@velopment.at>
 * @date 28.11.13
 * @time 13:25
 */

namespace CodeLovers\ExamSignupBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ExamDateType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'examDate';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CodeLovers\ExamSignupBundle\Entity\ExamDate'
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('location', 'text', array(
            'label' => 'examDate.location'
        ))
        ->add('date', 'datetime', array(
                'label' =>  'examDate.date',
                'date_widget'   =>  'single_text',
                'format'    =>  'yyyy-MM-dd',
                'time_widget'   =>  'single_text',
                'with_seconds'  =>  false
            ))
        ;
    }
} 