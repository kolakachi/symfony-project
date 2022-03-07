<?php

namespace App\Entity;

use App\Repository\InvoiceLinesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvoiceLinesRepository::class)
 */
class InvoiceLines
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Invoice::class, inversedBy="invoiceLines")
     * @ORM\JoinColumn(nullable=false)
     */
    private $invoice_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private $amount;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=1)
     */
    private $vat_amount;

    /**
     * @ORM\Column(type="decimal", precision=3, scale=2)
     */
    private $total_with_vat;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvoiceId(): ?Invoice
    {
        return $this->invoice_id;
    }

    public function setInvoiceId(?Invoice $invoice_id): self
    {
        $this->invoice_id = $invoice_id;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getVatAmount(): ?string
    {
        return $this->vat_amount;
    }

    public function setVatAmount(string $vat_amount): self
    {
        $this->vat_amount = $vat_amount;

        return $this;
    }

    public function getTotalWithVat(): ?int
    {
        return $this->total_with_vat;
    }

    public function setTotalWithVat(string $total_with_vat): self
    {
        $this->total_with_vat = $total_with_vat;

        return $this;
    }
}
