<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Functions\CustomPdoConnection;
use AppBundle\Controller\Boost;
use AppBundle\Entity\JournalModel;


class JournalRepository extends EntityRepository
{
	public function saveJournalEdit($data)
	{
		
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $update = '
            UPDATE journal
                SET code = :code,
                	libelle = :libelle

            WHERE id = :id
        ';

        $prep = $pdo->prepare($update);

        return $prep->execute(array(
            'code' => $data['code'],
            'libelle' => $data['libelle'],
            'id' => $data['id']
        ));
	}

	public function getJournalDossier($client)
	{
		$con = new CustomPdoConnection();
        $pdo = $con->connect();

		$client = Boost::deboost($client,$this);

		$query = '	select d.nom as dossier, jd.libelle as journal_dossier, j.libelle as type_journal, "<i class=\'fa fa-edit icon-action js-save-button edit-journal-dossier-param\'></i><i class=\'fa fa-recycle icon-action js-save-button restore-journal-dossier-param\'></i>" as action, jd.code_str, j.id as journal_id, jd.id
					from dossier d
					inner join site s on (d.site_id=s.id)
					inner join client c on (s.client_id=c.id)
					inner join journal_dossier jd on (d.id = jd.dossier_id)
					inner join journal j on (jd.journal_id=j.id)
					where c.id = :client
					order by d.nom;
					';

		$params = array(
			'client' => $client
		);

        $prep = $pdo->prepare($query);

        $prep->execute($params);

        return $prep->fetchAll();

	}

	public function saveJournalDossier($data)
    {
    	$count = 0;
        $con = new CustomPdoConnection();
        $pdo = $con->connect();

        $select = '
            SELECT id
            FROM journal
            WHERE code = :journal_code
        ';

        $params = array(
            'journal_code' => $data['journal_code']
        );

        $prep = $pdo->prepare($select);
        $prep->execute($params);
        $journal_id = $prep->fetchAll()[0]->id;

        $q_old = "	select libelle, code_str
        			from journal_dossier
        			where id = :id";

        $prep = $pdo->prepare($q_old);

        $params = array(
            'id' => $data['id']
        );

        $prep->execute($params);

        $jd_old = $prep->fetchAll()[0];

        $update = '
            UPDATE journal_dossier
                SET journal_id = :journal_id
            WHERE id = :id
        ';

        $prep = $pdo->prepare($update);

        $up = $prep->execute(array(
			'journal_id' => $journal_id,
			'id'         => $data['id']
        ));

        if ($up) {
        	$count = 1;
        }


        // var_dump("expression");die();

        $jmRepository = $this->getEntityManager()->getRepository('AppBundle:JournalModel');

        $jlRepository = $this->getEntityManager()->getRepository('AppBundle:Journal');

        $journal = $jlRepository->find($journal_id);

        $modelExist = $jmRepository->findOneBy(array(
        				// 'code' => $jd_old->code_str,
        				'libelle' => $jd_old->libelle
        			  ));

        if (!$modelExist) {
        	$model = new JournalModel();
        } else {
        	$model = $modelExist;
        }

    	$model->setCode($jd_old->code_str);
    	$model->setLibelle($jd_old->libelle);
    	$model->setJournal($journal);

    	$em = $this->getEntityManager();
    	$em->persist($model);
    	$em->flush();
    	
    	return ($this->updateAllJournal($model)) + $count;

    }


    public function updateAllJournal($model)
    {

    	$con = new CustomPdoConnection();
        $pdo = $con->connect();

    	$query = "	select id, journal_id 
					from journal_dossier
					where (code_str like '". $model->getCode() ."'
					and libelle like '". $model->getLibelle() ."%')
					or libelle like '". $model->getLibelle() ."%' ";

        $prep = $pdo->prepare($query);

        $prep->execute();

        $list = $prep->fetchAll();

        $jdRepository = $this->getEntityManager()->getRepository('AppBundle:JournalDossier');

        $count = 0;

        foreach ($list as $jd) {

        	if ($jd->journal_id != $model->getJournal()->getId()) {
        		
        		$journal_dossier = $jdRepository->find($jd->id);

        		$journal_dossier->setJournal($model->getJournal());

        		$em = $this->getEntityManager();
		    	$em->persist($journal_dossier);
		    	$em->flush();

		    	$count += 1;

        	}

        }

        return $count;

        // return true;

    }

    public function syncJM()
    {

    	$count = 0;

        $jmRepository = $this->getEntityManager()->getRepository('AppBundle:JournalModel');

        $models = $jmRepository->findAll();

        foreach ($models as $model) {
        	$res = $this->updateAllJournal($model);

        	$count += $res;
        }

        return $count;
    }

}