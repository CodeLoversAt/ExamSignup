<?php
/**
 * this service will listen for document removals
 * and make sure the associated ACL will be removed as well
 *
 * @package rentorder
 *
 * @author Daniel Holzmann <d@velopment.at>
 * @date 18.09.13
 * @time 11:01
 */

namespace CodeLovers\ExamSignupBundle\Acl;


use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AclCleaner implements EventSubscriber
{
    /**
     * @var AclHandlerInterface
     */
    private $aclHandler = null;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, Logger $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'preRemove'
        );
    }

    /**
     * @param LifecycleEventArgs $args
     *
     */
    public function preRemove(LifecycleEventArgs $args)
    {
        if (null === $this->aclHandler) {
            $this->aclHandler = $this->container->get('aclHandler');
        }

        $this->logger->addInfo('preRemove');

        $context = array();
        $document = $args->getDocument();

        if (method_exists($document, 'getId')) {
            $context['id'] = $document->getId();
        }
        if (method_exists($document, '__toString')) {
            $context['document'] = (string) $document;
        }
        try {
            $this->aclHandler->deleteAcl($document);
            $this->logger->addInfo(sprintf("removed ACL for %s", get_class($document)), $context);
        } catch (\Exception $e) {
            $this->logger->addInfo(sprintf("could not remove ACL for %s", get_class($document)), $context);
        }
    }
}