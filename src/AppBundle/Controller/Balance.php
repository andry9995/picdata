<?php

    namespace AppBundle\Controller;

    class Balance
    {
        public $comptes;
        public $debits;
        public $credits;
        public $soldes_debit;
        public $soldes_credit;
        public $exercices;
        public $comptes_str;

        public function __construct($comptes,$debits,$credits,$soldes_debit,$soldes_credit,$exercices,$comptes_str = null)
        {
            $this->comptes = $comptes;
            $this->debits = $debits;
            $this->credits = $credits;
            $this->soldes_debit = $soldes_debit;
            $this->soldes_credit = $soldes_credit;
            $this->exercices = $exercices;
            $this->comptes_str = $comptes_str;
        }
    }