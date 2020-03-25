<?php

    namespace AppBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * IndicateurSpecIndicateur
     *
     * @ORM\Table(name="indicateur_spec_indicateur", indexes={@ORM\Index(name="fk_indicateur_spec_indicateur_indicateur1_idx", columns={"indicateur_id"}), @ORM\Index(name="fk_indicateur_spec_indicateur_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_indicateur_spec_indicateur_client1_idx", columns={"client_id"})})
     * @ORM\Entity(repositoryClass="AppBundle\Repository\IndicateurSpecIndicateurRepository")
     */
    class IndicateurSpecIndicateur
    {
        /**
         * @var integer
         *
         * @ORM\Column(name="id", type="integer")
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var \AppBundle\Entity\Indicateur
         *
         * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Indicateur")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="indicateur_id", referencedColumnName="id")
         * })
         */
        private $indicateur;

        /**
         * @var \AppBundle\Entity\Dossier
         *
         * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Dossier")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="dossier_id", referencedColumnName="id")
         * })
         */
        private $dossier;

        /**
         * @var \AppBundle\Entity\Client
         *
         * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Client")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="client_id", referencedColumnName="id")
         * })
         */
        private $client;



        /**
         * Get id
         *
         * @return integer
         */
        public function getId()
        {
            return $this->id;
        }

        /**
         * Set indicateur
         *
         * @param \AppBundle\Entity\Indicateur $indicateur
         *
         * @return IndicateurSpecIndicateur
         */
        public function setIndicateur(\AppBundle\Entity\Indicateur $indicateur = null)
        {
            $this->indicateur = $indicateur;

            return $this;
        }

        /**
         * Get indicateur
         *
         * @return \AppBundle\Entity\Indicateur
         */
        public function getIndicateur()
        {
            return $this->indicateur;
        }

        /**
         * Set dossier
         *
         * @param \AppBundle\Entity\Dossier $dossier
         *
         * @return IndicateurSpecIndicateur
         */
        public function setDossier(\AppBundle\Entity\Dossier $dossier = null)
        {
            $this->dossier = $dossier;

            return $this;
        }

        /**
         * Get dossier
         *
         * @return \AppBundle\Entity\Dossier
         */
        public function getDossier()
        {
            return $this->dossier;
        }

        /**
         * Set client
         *
         * @param \AppBundle\Entity\Client $client
         *
         * @return IndicateurSpecIndicateur
         */
        public function setClient(\AppBundle\Entity\Client $client = null)
        {
            $this->client = $client;

            return $this;
        }

        /**
         * Get client
         *
         * @return \AppBundle\Entity\Client
         */
        public function getClient()
        {
            return $this->client;
        }
    }
