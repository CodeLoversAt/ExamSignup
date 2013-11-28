<?php
/**
 * @package examSignup
 *
 * @author Daniel Holzmann <d@velopment.at>
 * @date 28.11.13
 * @time 12:59
 */

namespace CodeLovers\ExamSignupBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ExamType extends AbstractType
{
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'exam';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CodeLovers\ExamSignupBundle\Entity\Exam'
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
                'label' => 'exam.label'
            ))
            ->add('dates', 'collection', array(
                'type'  => new ExamDateType(),
                'allow_add' =>  true,
                'allow_delete'  =>  true,
            ))
        ;
    }
} 