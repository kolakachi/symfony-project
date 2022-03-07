<?php

namespace App\Controller;

use App\Entity\Invoice;
use App\Repository\InvoiceLinesRepository;
use App\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class InvoiceController extends AbstractController
{

    private $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepository, InvoiceLinesRepository $invoiceLinesRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceLinesRepository = $invoiceLinesRepository;
    }

    /**
     * @Route("/", name="app_invoice")
     */
    public function index(): Response
    {
        $invoices = $this->invoiceRepository->findAll();
        $data = [];

        foreach ($invoices as $invoice) {
            $data[] = [
                'id' => $invoice->getId(),
                'invoice_date' => $invoice->getInvoiceDate()->format('Y-m-d'),
                'invoice_number' => $invoice->getInvoiceNumber(),
                'customer_id' => $invoice->getCustomerId(),
            ];
        }
        return $this->render('invoice/index.html.twig', [
            'data' => json_encode($data)
        ]);
    }

    /**
     * @Route("/create", name="create_invoice")
     */
    public function create(Request $request): Response
    {
       return $this->render('invoice/create-form.html.twig');
    }

    /**
     * @Route("/add", name="add_invoice")
     */
    public function add(Request $request): JsonResponse
    {
        try{
            $data = $request->request->all();
            $data['invoice_lines'] = json_decode($data['invoice_lines']);
            $this->invoiceRepository->saveInvoice($data);
            return new JsonResponse(['message' => 'Invoice created!', 'data' => $data], Response::HTTP_CREATED);
        }catch(\Exception $error){
            return new JsonResponse(['message' => $error->getMessage()], 500);
        }
        
    }

    /**
     * @Route("/delete/{id}", name="delete_invoice", methods={"DELETE"})
     */
    public function delete($id): JsonResponse
    {
        $invoice = $this->invoiceRepository->findOneBy(['id' => $id]);

        $this->invoiceRepository->removeInvoice($invoice);

        return new JsonResponse(['message' => 'Invoice deleted'], Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/edit/{id}", name="get_invoice", methods={"GET"})
     */
    public function get($id): Response
    {
        $invoice = $this->invoiceRepository->findOneBy(['id' => $id]);
        $invoiceLines = $this->invoiceLinesRepository->findBy(['invoice_id' => $id]);
        $lines = [];
        foreach($invoiceLines as $line){
            $lines[] = [
                'id' => $line->getId(),
                'invoice_id'  =>  $line->getInvoiceId(),
                'description' =>  $line->getDescription(),
                'quantity' =>  $line->getQuantity(),
                'amount' =>  $line->getAmount(),
                'vat_amount' =>  $line->getVatAmount(),
                'total' => $line->getTotalWithVat()
            ];
        }
        
        $data = [
            'id' => $invoice->getId(),
            'invoice_date' => $invoice->getInvoiceDate()->format('Y-m-d'),
            'invoice_number' => $invoice->getInvoiceNumber(),
            'customer_id' => $invoice->getCustomerId(),
            'invoice_lines' => $lines,
        ];
        return $this->render('invoice/edit-form.html.twig', [
            'data' => json_encode($data)
        ]);
    }

    /**
     * @Route("/update/{id}", name="update_invoice", methods={"POST"})
     */
    public function update($id, Request $request): JsonResponse
    {
        $invoice = $this->invoiceRepository->findOneBy(['id' => $id]);
        $data = $request->request->all();
        $invoice
            ->setInvoiceDate(\DateTime::createFromFormat('Y-m-d', $data['invoice_date']))
            ->setInvoiceNumber($data['invoice_number'])
            ->setCustomerId($data['customer_id']);

        $updatedInvoice = $this->invoiceRepository->updateInvoice($invoice);

        return new JsonResponse(
            [ 
                'message' => 'Invoice updated' 
            ],
             Response::HTTP_OK
        );
    }

    /**
     * @Route("/add/line", name="add_invoice_line")
     */
    public function addLine(Request $request): JsonResponse
    {
        try{
            $data = $request->request->all();
            $lineId = $this->invoiceLinesRepository->saveInvoiceLine($data);
            return new JsonResponse([
                'message' => 'Invoice created!', 'lineId' => $lineId], Response::HTTP_CREATED);
        }catch(\Exception $error){
            return new JsonResponse(['message' => $error->getMessage()], 500);
        }
        
    }

    /**
     * @Route("/update/line/{id}", name="update_invoice_line", methods={"POST"})
     */
    public function updateLine($id, Request $request): JsonResponse
    {
        $invoice = $this->invoiceLinesRepository->findOneBy(['id' => $id]);
        $data = $request->request->all();
        $invoice
            ->setDescription($data['description'])
            ->setQuantity($data['quantity'])
            ->setAmount($data['amount'])
            ->setVatAmount($data['vat_amount'])
            ->setTotalWithVat($data['total']);

        $updatedInvoice = $this->invoiceLinesRepository->updateInvoiceLine($invoice);

        return new JsonResponse(
            [ 
                'message' => 'Invoice Line updated' 
            ],
             Response::HTTP_OK
        );
    }


    /**
     * @Route("/delete/line/{id}", name="delete_invoice_line", methods={"DELETE"})
     */
    public function deleteLine($id): JsonResponse
    {
        $invoice = $this->invoiceLinesRepository->findOneBy(['id' => $id]);

        $this->invoiceLinesRepository->remove($invoice);

        return new JsonResponse(['message' => 'Invoice Line deleted'], Response::HTTP_NO_CONTENT);
    }
}
