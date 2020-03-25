select i.nom,l.date_scan, cas.libelle, ca.libelle 
from image i 
left join code_analytique ca on (i.code_analytique_id = ca.id)
left join code_analytique_section cas on (ca.code_analytique_section_id = cas.id)
join lot l on (l.id = i.lot_id)
join dossier d on (d.id = l.dossier_id) 
where d.id = 19482 and l.date_scan >= '2019-10-01' limit 100000;