<?php

namespace App\Repository;

use App\Entity\Invoice;
use App\Entity\InvoiceLines;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Invoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invoice[]    findAll()
 * @method Invoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Invoice::class);
        $this->manager = $manager;
    }

    public function saveInvoice($data)
    {
        $newInvoice = new Invoice();
        $invoiceLines = $data['invoice_lines'];

        $newInvoice
            ->setInvoiceDate(\DateTime::createFromFormat('Y-m-d', $data['invoice_date']))
            ->setInvoiceNumber($data['invoice_number'])
            ->setCustomerId($data['customer_id']);
        $this->manager->persist($newInvoice);
        $this->manager->flush();
        
        foreach($invoiceLines as $line){
            $line = (array)$line;
            $newInvoiceLine = new InvoiceLines;
            // $total = (($line['quantity'] * $line['amount']) * 10) + ($line['vat_amount'] * 10);
            // $total = number_format($total/10, 1, '.', '');
            
            $newInvoiceLine->setInvoiceId($newInvoice)
                ->setDescription($line['description'])
                ->setQuantity($line['quantity'])
                ->setAmount($line['amount'])
                ->setVatAmount($line['vat_amount'])
                ->setTotalWithVat($line['total']);

            // $newInvoice->addInvoiceLine($newInvoiceLine);
            $this->manager->persist($newInvoiceLine);
            $this->manager->flush();

        }

        
    }

    public function removeInvoice(Invoice $invoice)
    {
        $this->manager->remove($invoice);
        $this->manager->flush();
    }

    public function updateInvoice(Invoice $invoice): Invoice
    {
        $this->manager->persist($invoice);
        $this->manager->flush();

        return $invoice;
    }

    // /**
    //  * @return Invoice[] Returns an array of Invoice objects
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
    public function findOneBySomeField($value): ?Invoice
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
