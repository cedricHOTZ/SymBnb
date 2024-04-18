<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $personneResa = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Annonces $annonce = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPersonneResa(): ?User
    {
        return $this->personneResa;
    }

    public function setPersonneResa(?User $personneResa): static
    {
        $this->personneResa = $personneResa;

        return $this;
    }

    public function getAnnonce(): ?Annonces
    {
        return $this->annonce;
    }

    public function setAnnonce(?Annonces $annonce): static
    {
        $this->annonce = $annonce;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    #[ORM\PrePersist]
    public function prepersist()
    {
        if (empty($this->createdAt)) {
            $this->createdAt = new \DateTime();
        }
        if (empty($this->prix)) {
            $this->prix = $this->annonce->getPrice() * $this->getDuration();
        }
    }

    public function getDuration()
    {

        $diff = $this->endDate->diff($this->startDate);
        return $diff->days;
    }
    public function datePossible(){

        // 1. Il faut connaitre les dates non réservables
        $notAvailableDays = $this->annonce->jourIndisponible();
        // 2. Il faut comparer avec les dates choisies
        $bookingDays = $this->getDays();

        $formatDay = function (\DateTime $day) {
            return $day->format('Y-m-d');
        };

        $notAvailable = array_map($formatDay, $notAvailableDays);
        $days = array_map($formatDay, $bookingDays);

        return !(bool) array_intersect($notAvailable, $days);
    
    }

    public function getDays()
    {
        // Intervalle de date d'une journée
        $dateInterval = new \DateInterval('P1D');

        $period = new \DatePeriod(
            $this->startDate,
            $dateInterval,
            $this->endDate->add($dateInterval)
        );
        return iterator_to_array($period);
    } // End function getDays

}
