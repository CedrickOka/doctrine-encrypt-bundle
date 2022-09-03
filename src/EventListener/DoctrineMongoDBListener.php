<?php

namespace Oka\Doctrine\EncryptBundle\EventListener;

use Doctrine\ODM\MongoDB\Event\OnFlushEventArgs;
use Oka\Doctrine\EncryptBundle\Model\AbstractDoctrineListener;

/**
 * @author Cedrick Oka Baidai <okacedrick@gmail.com>
 */
class DoctrineMongoDBListener extends AbstractDoctrineListener
{
    public function onFlush(OnFlushEventArgs $args)
    {
        /** @var \Doctrine\ODM\MongoDB\DocumentManager $dm */
        $dm = $args->getObjectManager();
        $unitOfWork = $dm->getUnitOfWork();

        foreach ($unitOfWork->getScheduledDocumentInsertions() as $document) {
            if (true === $this->encryptOnFlush($document)) {
                $unitOfWork->recomputeSingleDocumentChangeSet($dm->getClassMetadata($document::class), $document);
            }
        }

        foreach ($unitOfWork->getScheduledDocumentUpdates() as $document) {
            if (true === $this->encryptOnFlush($document)) {
                $unitOfWork->recomputeSingleDocumentChangeSet($dm->getClassMetadata($document::class), $document);
            }
        }

        foreach ($unitOfWork->getScheduledDocumentUpserts() as $document) {
            if (true === $this->encryptOnFlush($document)) {
                $unitOfWork->recomputeSingleDocumentChangeSet($dm->getClassMetadata($document::class), $document);
            }
        }
    }
}
