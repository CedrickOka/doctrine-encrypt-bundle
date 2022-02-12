<?php

namespace Oka\Doctrine\EncryptBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Oka\Doctrine\EncryptBundle\Model\AbstractDoctrineListener;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class DoctrineORMListener extends AbstractDoctrineListener
{
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $unitOfWork = $em->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            if (true === $this->encryptOnFlush($entity)) {
                $unitOfWork->recomputeSingleEntityChangeSet($em->getClassMetadata($entity::class), $entity);
            }
        }

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if (true === $this->encryptOnFlush($entity)) {
                $unitOfWork->recomputeSingleEntityChangeSet($em->getClassMetadata($entity::class), $entity);
            }
        }
    }
}
