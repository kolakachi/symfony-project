<?php

namespace App\Repository;

use App\Entity\InvoiceLines;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method InvoiceLines|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceLines|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceLines[]    findAll()
 * @method InvoiceLines[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceLinesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, InvoiceRepository $invoiceRepository, EntityManagerInterface $manager)
    {
        parent::__construct($registry, InvoiceLines::class);
        $this->invoiceRepository = $invoiceRepository;
        $this->manager = $manager;

    }

    public function saveInvoiceLine($data)
    {
        $invoice = $this->invoiceRepository->findOneBy(['id' => $data['invoice_id']]);
        $newInvoiceLine = new InvoiceLines;
        $newInvoiceLine->setInvoiceId($invoice)
            ->setDescription($data['description'])
            ->setQuantity($data['quantity'])
            ->setAmount($data['amount'])
            ->setVatAmount($data['vat_amount'])
            ->setTotalWithVat($data['total']);

        $this->manager->persist($newInvoiceLine);
        $this->manager->flush();
        return $newInvoiceLine->getId();
        
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(InvoiceLines $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function updateInvoiceLine(InvoiceLines $invoiceLine): InvoiceLines
    {
        $this->manager->persist($invoiceLine);
        $this->manager->flush();

        return $invoiceLine;
    }

    // /**
    //  * @return InvoiceLines[] Returns an array of InvoiceLines objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?InvoiceLines
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
