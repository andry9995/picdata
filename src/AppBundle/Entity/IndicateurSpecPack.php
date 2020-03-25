<?php

    namespace AppBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;

    /**
     * IndicateurSpecPack
     *
     * @ORM\Table(name="indicateur_spec_pack", indexes={@ORM\Index(name="fk_indicateur_spec_pack_indicateur_pack1_idx", columns={"indicateur_pack_id"}), @ORM\Index(name="fk_indicateur_spec_pack_dossier1_idx", columns={"dossier_id"}), @ORM\Index(name="fk_indicateur_spec_pack_client1_idx", columns={"client_id"})})
     * @ORM\Entity(repositoryClass="AppBundle\Repository\IndicateurSpecPackRepository")
     */
    class IndicateurSpecPack
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
         * @var \AppBundle\Entity\IndicateurPack
         *
         * @ORM\ManyToOne(targetEntity="AppBundle\Entity\IndicateurPack")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="indicateur_pack_id", referencedColumnName="id")
         * })
         */
        private $indicateurPack;

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
         * Set indicateurPack
         *
         * @param \AppBundle\Entity\IndicateurPack $indicateurPack
         *
         * @return IndicateurSpecPack
         */
        public function setIndicateurPack(\AppBundle\Entity\IndicateurPack $indicateurPack = null)
        {
            $this->indicateurPack = $indicateurPack;

            return $this;
        }

        /**
         * Get indicateurPack
         *
         * @return \AppBundle\Entity\IndicateurPack
         */
        public function getIndicateurPack()
        {
            return $this->indicateurPack;
        }

        /**
         * Set dossier
         *
         * @param \AppBundle\Entity\Dossier $dossier
         *
         * @return IndicateurSpecPack
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
         * @return IndicateurSpecPack
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
